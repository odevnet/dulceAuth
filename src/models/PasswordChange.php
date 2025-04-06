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

use src\exceptions\users\LimitChangesPasswordException;

/**
 * Model that inherits from the 'Eloquent Model' (\Illuminate\Database\Eloquent\Model).
 * Represents the history of password changes made by users.
 *
 * @package src\models
 *
 * @since 1.0
 */
class PasswordChange extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'password_changes';

    /**
     * Primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * All fields that can be mass assignment.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'old_password', 'new_password', 'changes_count', 'last_change_date'];

    /**
     * Method to establish the relationship with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Gets the last password change made by the user.
     *
     * @param int $userId User ID.
     *
     * @return \src\models\PasswordChange|null Instance of 'PasswordChange' if found
     * a password change, or null if no password change exists
     * for the indicated user.
     *
     * @throws \src\exceptions\users\LimitChangesPasswordException
     * It is "launched" when it finds no change password for the indicated user.
     */
    public static function latestChange(int $userId): ?PasswordChange
    {
        $latestChange = self::where('user_id', $userId)->latest()->first();

        if ($latestChange === null) {
            throw new LimitChangesPasswordException("No password change found for user with ID: $userId");
        }
        return $latestChange;
    }
}
