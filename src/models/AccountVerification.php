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

use src\exceptions\tokens\RelationShipTokenException;
use src\exceptions\tokens\TokenException;
use src\exceptions\tokens\TokenExpiredException;
use src\exceptions\tokens\TokenNotFoundException;
use src\exceptions\tokens\TokenSaveException;
use src\exceptions\users\UserNotFoundException;

/**
 * Model that inherits from the 'Eloquent Model' (\Illuminate\Database\Eloquent\Model)
 * and represents everything related to account verification.
 *
 * @package src\models
 *
 * @since 1.0
 */
class AccountVerification extends Model
{
    /**
     * Name of the database table where the accounts pending validation are stored.
     *
     * @var string
     */
    protected $table = 'account_verifications';

    /**
     * Primary key of the 'account verifications' table.
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
     * Constructor of AccountVerification.
     *
     * @param \src\models\User|null $user 'User' instance.
     */
    public function __construct(?User $user = null)
    {
        $this->userModel = $user ?: new User();
        parent::__construct();
    }

    /**
     * It is responsible for establishing the "belongs to" relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Method in charge of verifying if a token exists in the 'account_verifications' table.
     *
     * @param string $token Token to verify.
     *
     * @return bool True if the token exists, false otherwise.
     */
    public static function checkToken(string $token): bool
    {
        $check = self::where('token', $token)->first();

        if ($check) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method to validate a token with its corresponding user.
     *
     * @param string $token  Token to verify.
     * @param int    $userId ID of the user associated with the token.

     * @return bool True if the token is valid, false otherwise.
     *
     * @throws \src\exceptions\tokens\RelationShipTokenException
     * If the token does not match, that is, it does not belong to the user.
     * @throws \src\exceptions\tokens\TokenExpiredException
     * In the event that the token has expired.
     * @throws \src\exceptions\tokens\TokenNotFoundException
     * Token don't exist.
     * @throws \src\exceptions\tokens\TokenException
     * If there is a problem with token validation.
     */
    public function validateTokenAccount(string $token, int $userId): bool
    {

        if (self::checkToken($token)) {
            // Check if the token has expired
            // If the token is greater than the current date, it is understood that it has NOT expired
            $verification = self::where('token', $token)
                ->where('expires_at', '>', new \DateTime())
                ->first();
            if ($verification) {
                // It has not expired, that is, it matches and is greater than the current date
                // It checks if the user ID also matches
                if ($verification->user_id == $userId) {
                    return true;
                } else {
                    throw new RelationShipTokenException();
                }
            } else {
                throw new TokenExpiredException();
            }
        } else {
            throw new TokenNotFoundException('The token does not match or does not exist.');
        }
    }

    /**
     * Method responsible for verifying a user and deleting its corresponding entry in the table.
     *
     * @param int $userId ID of the user to verify.
     *
     * @throws \src\exceptions\tokens\TokenException
     * If there is a problem verifying the user.
     */
    public function verified(int $userId): void
    {
        $accountVerifications = self::find($userId);
        $user = $this->userModel::find($userId);

        if ($accountVerifications && $user) {
            $user->verified = 1;
            $user->save();
            $accountVerifications = self::where('user_id', $userId);
            $accountVerifications->delete();
        } else {
            throw new TokenException('Cannot execute verified() method
            directly without first having generated a token...');
        }
    }

    /**
     * Generate a verification token for the user with the provided email.
     *
     * @param string $email User email.
     *
     * @return array Returns an array with the user ID and the generated token.
     *
     * @throws \src\exceptions\users\UserNotFoundException
     * If the user does not exist.
     * @throws \src\exceptions\tokens\TokenException
     * If the account user is already verified o also if the token cannot
     * be generated.
     * @throws \src\exceptions\tokens\TokenSaveException
     * If the token could not be generated or there is a problem saving it.
     */
    public function generateVerificationToken(string $email, $send = true)
    {
        // First of all look if for the email to exist
        $user = $this->userModel::where('email', $email)->first();

        if (!$user) {
            throw new UserNotFoundException('The email does not exist.');
        }

        // Check if the account is already verified
        if ($user->verified === 1) {
            throw new TokenException('The account is already verified.');
        }

        // Generate a random token
        $token = bin2hex(random_bytes(32));

        // Set the token expiration date (for example, 1 day later)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));


        // Store the token and expiration date in your table (account_verifications)
        $this->user_id = $user->id;
        $this->token = $token;
        $this->expires_at = $expiresAt;
        if ($this->save()) {
            //return true;
            //return ['userId' => $user_id, 'token' => $token];
            if ($send) {
                $verificationMail = new \src\DulceMail();
                $verificationMail->from(EMAIL_FROM);
                $verificationMail->sendVerificationEmail($email, $token, $user->id, true);
            } else {
                return ['userId' => $user->id, 'token' => $token];
            }
        } else {
            throw new TokenSaveException();
        }
    }
}
