<?php

/**
 * dulceAuth: Library that allows user management. It facilitates registration
 * and authentication, as well as the administration of users with roles and
 * permissions.
 *
 * @link https://github.com/odevnet/dulceAuth/
 *
 * @license https://github.com/odevnet/dulceAuth/blob/main/LICENSE (MIT License)
 */

namespace src;

use Illuminate\Database\Capsule\Manager as DB;

use src\models\User;
use src\models\Role;

use src\exceptions\users\AccountValidationException;
use src\exceptions\users\DuplicateEmailException;
use src\exceptions\users\RegisterException;

/**
 * Auth class
 *
 * This class provides authentication related functionality such as login and
 * user registration.
 *
 * @package src
 *
 * @since 1.0
 */
class Auth
{

    /**
     * Represents the user model.
     *
     *  @var \src\models\User
     */
    public User $userModel;

    /**
     * Represents the role model.
     *
     *  @var \src\models\Role
     */
    public Role $roleModel;

    /**
     * Session handler.
     *
     *  @var \src\Session
     */
    public Session $session;

    /**
     * Constructor of the Auth class.
     *
     * @param \src\models\User $userModel User model.
     * @param \src\models\Role $roleModel Role model.
     * @param \src\Session $session Session handler.
     */
    public function __construct(User $userModel, Role $roleModel, Session $session)
    {
        $this->userModel = $userModel;
        $this->roleModel = $roleModel;
        $this->session = $session;
    }

    /**
     * Checks if the user is currently authenticated.
     *
     * @return bool Returns true if the user is authenticated; otherwise false.
     */
    public function isLoggedIn(): bool
    {
        if ($this->session->has('userId') === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the data of the currently authenticated user.
     *
     * @return \src\models\User|null Returns the current user or null if not authenticated.
     */
    public function currentUser(): ?User
    {
        if (!$this->isLoggedIn()) {
            return null;
        } else {
            $userId = $this->session->get('userId');
            return $this->userModel->dataUser($userId);
        }
    }

    /**
     * Method to login with the provided credentials.
     *
     * @param string $email User email
     * @param string $password User password.
     * @return bool True if the login is successful; otherwise false.
     *
     * @throws \src\exceptions\users\AccountValidationException
     * When the account is not yet verified.
     */
    public function login(string $email, string $password): bool
    {
        // Check the database to verify if the email exists
        $user = $this->userModel::where('email', $email)->first();
        // If exists but your account is not yet verified...
        if ($user && $user->verified === 0) {
            throw new AccountValidationException();
        }

        if ($user && password_verify($password, $user->password)) {
            // Credentials are valid, user is authenticated
            // Access the user ID
            $userId = $user->id;

            if (defined('DULCE_AUTH_EMAIL_2FA') && DULCE_AUTH_EMAIL_2FA === true) {
                $this->session->start();
                session_regenerate_id(true);

                $code = $this->generateOtpCode();

                $this->session->set('pending_2fa', true);
                $this->session->set('pending_userId', $userId);
                $this->session->set('otp_code', $code);
                $this->session->set('otp_expires', time() + 600); // 10 min
                $this->session->set('otp_sent_count', 1);

                try {
                    $sendOtpEmail = new DulceMail();
                    $sendOtpEmail->from(DULCE_AUTH_EMAIL_FROM);
                    $sendOtpEmail->sendOtpEmail($user->email, $code, $userId, true);
                } catch (\Throwable $e) {
                    // Limpieza si falla el envío
                    $this->session->remove('pending_2fa');
                    $this->session->remove('pending_userId');
                    $this->session->remove('otp_code');
                    $this->session->remove('otp_expires');
                    $this->session->remove('otp_sent_count');
                    throw $e;
                }

                // Credenciales válidas pero login pendiente: primer paso completado
                return true;
            }

            // Normal flow (without 2FA):
            // Create the session and register it with your user id later
            $this->session->start();
            // Renew session ID after successful login
            session_regenerate_id(true);
            $this->session->set('userId', $userId);
            // Set the expiration time variable
            $this->session->set('expire_time', time() + DULCE_AUTH_SESSION_EXPIRATION);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Register a new user in the system with the information provided.
     *
     * @param string $name Username.
     * @param string $email User email.
     * @param string $password User password.
     * @param array $options Additional options for user creation.
     *
     * @throws \src\exceptions\users\DuplicateEmailException If the email is already registered.
     * @throws \src\exceptions\users\RegisterException If there is an error during registration.
     */
    public function register(string $name, string $email, string $password, array $options = [])
    {
        // Start a transaction
        DB::beginTransaction();

        try {
            // Check the database to verify if the email exists
            $existingUser = $this->userModel::where('email', $email)->first();
            if ($existingUser) {
                // If it exists, the exception is thrown
                throw new DuplicateEmailException();
            } else {
                $user = new $this->userModel;
                $user->name = $name;
                $user->email = $email;
                $user->password = password_hash($password, PASSWORD_BCRYPT);
                // Set default values for 'verified' and 'visibility' (see config file)
                $user->verified = $options['verified'] ?? DULCE_AUTH_VERIFIED;
                $user->visibility = $options['visibility'] ?? DULCE_AUTH_DEFAULT_VISIBILITY;

                // Loop through the array of options and set the corresponding values in the model
                foreach ($options as $key => $value) {
                    // If 'verified' or 'visibility' was already set, it is not set again
                    if ($key !== 'verified' && $key !== 'visibility') {
                        $user->{$key} = $value;
                    }
                }

                if ($user->save()) {
                    // Assign the "User" role to the newly registered user
                    $userRole = $this->roleModel::where('name', 'User')->first();
                    if ($userRole) {
                        $user->roles()->attach($userRole->id);
                    }

                    if ($options['verified'] ?? DULCE_AUTH_VERIFIED === '1') {
                        // If account is verified, login
                        $this->login($email, $password);
                    } else {
                        // If the new registration requires validation,
                        // this same method will register a verification token
                        // with an expiration time and will return the 'userid'
                        // and 'the token' to later be able to use it in an email.
                        // See readme file for more detailed information...
                        $token = bin2hex(random_bytes(32)); // Generate a random token
                        $user->accountVerification()->create([
                            'token' => $token,
                            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day')), // Here you can adjust the expiration time according to your needs
                        ]);

                        // If the account is not verified, send verification email
                        $verificationMail = new DulceMail();
                        $verificationMail->from(DULCE_AUTH_EMAIL_FROM);
                        $verificationMail->sendVerificationEmail($email, $token, $user->id, true);
                    }
                    // Confirm the transaction if everything has been executed correctly
                    DB::commit();
                } else {
                    throw new RegisterException();
                }
            }
        } catch (\Exception $e) {
            // Reverts the transaction in case of error
            DB::rollBack();
            throw new \Exception("Error registering user: " . $e->getMessage());
        }
    }

    /**
     * Close the currently active session.
     */
    public function logout(): void
    {
        $this->session->destroy();
    }

    /**
     * Generate a new One-Time Password (OTP) code.
     *
     * @return string A randomly generated 6-digit OTP code (e.g., "483920").
     *
     * @since 2.1.0
     */
    private function generateOtpCode(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify the One-Time Password (OTP) provided by the user and complete
     * the authentication process if the code is valid.
     *
     * This method validates the OTP code previously generated during the
     * login process. If the code is correct and has not expired, the user
     * session is finalized and the temporary OTP data is removed.
     *
     * @param string $code The OTP code entered by the user.
     *
     * @return bool Returns true if the OTP is valid and the user is
     *              successfully authenticated, false otherwise.
     *
     * @throws \Exception If an unexpected error occurs during verification.
     *
     * @since 2.1.0
     */
    public function verifyOtp(string $code): bool
    {
        $this->session->start();

        $pending = $this->session->get('pending_2fa');
        $pendingUser = $this->session->get('pending_userId');
        $storedCode = $this->session->get('otp_code');
        $expires = $this->session->get('otp_expires');

        if (empty($pending) || empty($pendingUser) || empty($storedCode) || empty($expires)) {
            return false;
        }

        if (time() > (int)$expires) {
            // Limpieza
            $this->session->remove('pending_2fa');
            $this->session->remove('pending_userId');
            $this->session->remove('otp_code');
            $this->session->remove('otp_expires');
            return false;
        }

        if (hash_equals((string)$storedCode, (string)$code)) {
            // Código correcto: completar login
            $this->session->set('userId', $pendingUser);
            $this->session->set('expire_time', time() + DULCE_AUTH_SESSION_EXPIRATION);

            // Borrar datos temporales
            $this->session->remove('pending_2fa');
            $this->session->remove('pending_userId');
            $this->session->remove('otp_code');
            $this->session->remove('otp_expires');
            $this->session->remove('otp_sent_count');

            return true;
        }

        return false;
    }

    /**
     * Generate and send a new One-Time Password (OTP) code to the user.
     *
     * This method creates a new OTP code for a pending authentication
     * request, updates its expiration time and sends it to the user's
     * registered email address.
     *
     * @return bool Returns true if the OTP was successfully generated
     *              and sent, false otherwise.
     *
     * @throws \Exception If an unexpected error occurs during the
     *                    OTP generation or email delivery process.
     *
     * @since 2.1.0
     */
    public function resendOtp(): bool
    {
        $this->session->start();
        $pendingUser = $this->session->get('pending_userId');

        if (empty($pendingUser)) {
            return false;
        }

        $code = $this->generateOtpCode();
        $this->session->set('otp_code', $code);
        $this->session->set('otp_expires', time() + 600); // 10 minutes

        $sentCount = (int)$this->session->get('otp_sent_count', 0);
        $this->session->set('otp_sent_count', $sentCount + 1);

        try {
            $user = $this->userModel::find($pendingUser);
            $userId = $user->id;
            if (!$user) {
                return false;
            }
            $sendOtpEmail = new DulceMail();
            $sendOtpEmail->from(DULCE_AUTH_EMAIL_FROM);
            $sendOtpEmail->sendOtpEmail($user->email, $code, $userId, true);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}
