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
            // Create the session and register it with your user id later
            $this->session->start();
            // Renew session ID after successful login
            session_regenerate_id(true);
            $this->session->set('userId', $userId);

            // Set the expiration time variable
            $this->session->set('expire_time', time() + SESSION_EXPIRATION);
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
                $this->userModel->name = $name;
                $this->userModel->email = $email;
                $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);

                // Set default values for 'verified' and 'visibility' (see config file)
                $this->userModel->verified = $options['verified'] ?? VERIFIED;
                $this->userModel->visibility = $options['visibility'] ?? DEFAULT_VISIBILITY;

                // Loop through the array of options and set the corresponding values in the model
                foreach ($options as $key => $value) {
                    // If 'verified' or 'visibility' was already set, it is not set again
                    if ($key !== 'verified' && $key !== 'visibility') {
                        $this->userModel->{$key} = $value;
                    }
                }

                if ($this->userModel->save()) {
                    // Assign the "User" role to the newly registered user
                    $userRole = $this->roleModel::where('name', 'User')->first();
                    if ($userRole) {
                        $this->userModel->roles()->attach($userRole->id);
                    }

                    if ($options['verified'] ?? VERIFIED === '1') {
                        // If account is verified, login
                        $this->login($email, $password);
                    } else {
                        // If the new registration requires validation,
                        // this same method will register a verification token
                        // with an expiration time and will return the 'userid'
                        // and 'the token' to later be able to use it in an email.
                        // See readme file for more detailed information...
                        $token = bin2hex(random_bytes(32)); // Generate a random token
                        $this->userModel->accountVerification()->create([
                            'token' => $token,
                            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day')), // Here you can adjust the expiration time according to your needs
                        ]);

                        // If the account is not verified, send verification email
                        $verificationMail = new DulceMail();
                        $verificationMail->from(EMAIL_FROM);
                        $verificationMail->sendVerificationEmail($email, $token, $this->userModel->id, true);
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
}
