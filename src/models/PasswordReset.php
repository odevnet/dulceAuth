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

namespace src\models;

use Illuminate\Database\Eloquent\Model;

use src\exceptions\tokens\TokenSaveException;
use src\exceptions\tokens\TokenException;
use src\exceptions\tokens\RelationShipTokenException;
use src\exceptions\tokens\TokenExpiredException;
use src\exceptions\tokens\TokenNotFoundException;
use src\exceptions\users\UserNotFoundException;

/**
 * Model that inherits from the 'Eloquent Model' (\Illuminate\Database\Eloquent\Model).
 * This class represents everything related to password reset.
 *
 * @package src\models
 *
 * @since 1.0
 */
class PasswordReset extends Model
{
    /**
     * Name of the database table where passwords are stored.
     *
     * @var string
     */
    protected $table = 'password_resets';

    /**
     * Primary key of the 'password_resets' table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id'; // !!

    /**
     * All fields that can be mass assignment.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'token', 'expires_at'];

    /**
     * "Eloquent timestamps".
     * If 'false' is set, we tell it not to handle them automatically, that is,
     * it will stop looking for the created_at and updated_at columns in the table.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Represents the user model.
     *
     *  @var \src\models\User
     */
    public User $userModel;

    /**
     * Constructor of PasswordReset.
     *
     * @param \src\models\User|null $user User instance to associate with the
     * password reset.
     */
    public function __construct(User $user = null)
    {
        $this->userModel = $user ?: new User();
        parent::__construct();
    }

    /**
     * Checks if a token exists in the 'password_resets' table.
     *
     * @param string $token Token to verify.
     *
     * @return bool True if the token exists, false otherwise.
     */
    public static function checkToken(string $token): bool
    {
        // Find the corresponding entry in the password_resets table
        $passwordReset = self::where('token', $token)->first();

        if ($passwordReset) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method in charge of the password reset process for the user
     * that matches the email passed as a parameter.
     *
     * @param string $email Email of user.
     *
     * @param bool $send Optional. Whether to send a reset email or
     * return an array with the user ID and token. Default is true.
     *
     * @return array|null Returns an array with 'userId' and 'token' if $send
     * is false, otherwise null.
     *
     * @throws \src\exceptions\tokens\TokenSaveException
     * If there is a problem generating or saving the token.
     * @throws \src\exceptions\users\UserNotFoundException
     * If a user with the provided email is not found.
     */
    public function forgotPassword(string $email, $send = true)
    {
        $exist = $this->userModel->userEmailExists($email);

        if ($exist) {
            // If the user exists, get his id
            $userId = $this->userModel->dataUser($email)->id;
            $token = bin2hex(random_bytes(32)); //generate token

            // Calculate expiration date (1 hour after creation)
            $expiresAt = (new \DateTime())->modify('+1 hour');
            // Store the token and expiration date in the 'password_resets' table
            $this->user_id = $userId;
            $this->token = $token;
            $this->expires_at = $expiresAt;

            if ($this->save()) {
                //return true;
                //return ['userId' => $userId, 'token' => $token];
                if ($send) {
                    $verificationMail = new \src\DulceMail();
                    $verificationMail->from(EMAIL_FROM);
                    $verificationMail->sendForgotPasswordEmail($email, $token, $userId, true);
                } else {
                    return ['userId' => $userId, 'token' => $token];
                }
            } else {
                throw new TokenSaveException();
            }
        } else {
            throw new UserNotFoundException('There is no user with that email.');
        }
    }

    /**
     * Validates a password reset token.
     *
     * @param string $token  Token to verify.
     * @param int    $userId ID of the user associated with the token.
     *
     * @return bool True if the token is valid, false otherwise.
     *
     * @throws \src\exceptions\tokens\RelationShipTokenException
     * If the token does not match, that is, it does not belong to the user.
     * @throws \src\exceptions\tokens\TokenExpiredException
     * Occurs if token has expired.
     * @throws \src\exceptions\tokens\TokenNotFoundException
     * Token don't exist.
     */
    public function validateTokenPassword(string $token, int $userId): bool
    {
        // checks if the token matches the one in the database
        if (self::checkToken($token)) {
            // Find the corresponding entry in the password_resets table
            // Check if the token has expired.
            // If the token is greater than the current date, it is understood that it has NOT expired
            $passwordReset = self::where('token', $token)
                ->where('expires_at', '>', new \DateTime())
                ->first();
            if ($passwordReset) {
                // It checks if the user ID also matches
                if ($passwordReset->user_id == $userId) {
                    return true;
                } else {
                    throw new RelationShipTokenException('There is no relationship between the submitted user id and the token.');
                }
            } else {
                throw new TokenExpiredException('The token has already expired.');
            }
        } else {
            throw new TokenNotFoundException('The token does not match or does not exist.');
        }
    }

    /**
     * Creates a new password for the user associated with the token.
     *
     * @param string $password New password for the user.
     * @param int    $userId   ID of the user associated with the token.
     *
     * @throws \src\exceptions\tokens\TokenException
     * If there is a problem inserting the new password.
     */
    public function insertNewPassword(string $password, int $userId): void
    {
        $passwordReset = self::find($userId);
        $user = $this->userModel::find($userId);

        if ($passwordReset && $user) {
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->save();
            $passwordReset = self::where('user_id', $userId);
            $passwordReset->delete();
        } else {
            // the exception is thrown since the user id does not exist in password_resets
            throw new TokenException('Cannot execute insertNewPassword() method
            directly without first having generated a token...');
        }
    }
}
