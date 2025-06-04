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

use src\Logger;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as DB;

use src\exceptions\users\CreateUserException;
use src\exceptions\users\DuplicateEmailException;
use src\exceptions\users\InvalidPasswordException;
use src\exceptions\users\EditUserException;
use src\exceptions\users\UserNotFoundException;
use src\exceptions\users\ArrayOptionsUserException;
use src\exceptions\users\LimitChangesPasswordException;

/**
 * Model that inherits from the 'Eloquent Model' (\Illuminate\Database\Eloquent\Model).
 * This model represents the users in the database.
 *
 * @package src\models
 *
 * @since 1.0
 */
class User extends Model
{
    /**
     * Name of the table in the database that stores registered users.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Primary key of the 'users' table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * All fields that can be mass assignment.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'verified', 'visibility'];

    /**
     * Establishes a "many-to-many" relationship between
     * this model ('User') and the 'Role' model.
     *
     * Uses the 'user_roles' pivot table. This allows a user to have multiple
     * roles, and roles can be associated with multiple users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Defines the relationship of a user to their account verification information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function accountVerification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccountVerification::class);
    }

    /**
     * Establishes a "many-to-many" relationship between
     * this model ('User') and the 'PasswordReset' model.
     * A user can have multiple password reset requests...
     */
    public function passwordReset(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PasswordReset::class, 'password_resets', 'user_id');
    }

    /**
     * Gets all users in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\src\models\User[]
     */
    public function showUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return self::all();
    }

    /**
     * Checks if a user with the given ID exists in the database.
     *
     * @param int $userId ID of the user to check.
     *
     * @return bool true if the user exists, false otherwise.
     */
    public static function userIdExists(int $userId): bool
    {
        // The user is searched by their primary key.
        // It is more concise and can be more efficient since
        // it is optimized for primary key searching.
        return self::find($userId) !== null;
    }

    /**
     * Through the email passed as a parameter, it is checked if a user exists
     * in the database.
     *
     * @param string $email User email of the user you want to check.
     *
     * @return bool true if the email exists, false otherwise.
     */
    public static function userEmailExists(string $email): bool
    {
        return self::where('email', $email)->first() !== null;
    }

    /**
     * Gets the data of a user with the provided ID or email.
     *
     * @param int|string $identifier ID or email of the user from whom you want
     * to obtain the data.
     *
     * @return \src\models\User|null Instance of the User model if the user
     * exists, null otherwise.
     */
    public function dataUser(int|string $identifier): ?User
    {
        $user = self::where(function ($query) use ($identifier) {
            return $query->where('id', $identifier)
                ->orWhere('email', $identifier);
        })->first();

        return $user ?? null;
    }

    /**
     * Gets the roles associated with a user with the user ID
     * passed as a parameter.
     *
     * @param int $userId ID of the user from whom you want to obtain the roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array List of roles
     * associated with the user.
     *
     * @throws \src\exceptions\users\UserNotFoundException
     * If the user ID is not found (does not exist).
     */
    public function getUserRoles(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        $user = self::find($userId);

        if ($user) {
            return $user->roles;
        } else {
            //return [];
            throw new UserNotFoundException('User ID not found (does not exist)');
        }
    }

    /**
     * Edits a user's data with the user ID passed as a parameter.
     *
     * @param int $userId   ID of the user to edit.
     * @param array $options Associative array of options with the new user values.
     *
     * Example: editUser(1, [
     *  'name' => 'Jhon',
     *  'email' => 'jhon@demo.test',
     *  'country' => 'Spain'
     * ])
     *
     * @return bool true if the edit was successful, false otherwise.
     *
     * @throws \src\exceptions\users\EditUserException
     * If there is a problem editing the user.
     * @throws \src\exceptions\users\ArrayOptionsUserException
     * if "array options" is empty.
     * @throws \src\exceptions\users\UserNotFoundException
     * If the user ID is not found (does not exist).
     */
    public function editUser(int $userId, array $options): bool
    {
        $user = self::find($userId);

        if ($user) {
            if (isset($options) && (!empty($options))) {
                // Runs through options of array and set the corresponding values in the model
                foreach ($options as $key => $value) {
                    $user->{$key} = $value;
                }

                // try to save the changes and check if the operation was successful
                try {
                    $saveResult = $user->save();

                    if ($saveResult) {
                        return true;
                    } else {
                        throw new EditUserException('There was a problem editing the user.');
                    }
                } catch (\Exception $ex) {
                    // Catches and handles any exceptions that may happen when trying to save
                    Logger::error($ex->getMessage(), $ex->getTraceAsString());
                    //echo $ex->getMessage(); It may display sensitive information from the database that we are not interested in.
                }
            } else {
                throw new ArrayOptionsUserException();
            }
        } else {
            throw new UserNotFoundException('User ID not found (does not exist).');
        }

        return false;
    }

    /**
     * This method creates a new user in the database. Of course, it is designed
     * to be used through an 'administrator' role.
     * See the documentation (readme) for more information.
     *
     * @param string $name     Name of the new user.
     * @param string $email    Email of the new user.
     * @param string $password Password of the new user.
     * @param array $options   Additional options for the new user.
     *
     * @return bool true if user creation was successful, false otherwise.
     *
     * @throws \src\exceptions\users\DuplicateEmailException
     * If the email already exists in the database.
     * @throws \src\exceptions\users\CreateUserException
     * If there is a problem creating the user.
     */
    public function createUser(string $name, string $email, string $password, array $options = []): bool
    {
        // Check if the email already exists in the database
        $existingUser = self::where('email', $email)->first();
        if ($existingUser) {
            // Email already exists in database, throw exception
            throw new DuplicateEmailException();
        } else {
            $this->name = $name;
            $this->email = $email;
            $this->password = password_hash($password, PASSWORD_BCRYPT);
            $this->verified = $options['verified'] ?? DULCE_AUTH_VERIFIED;
            $this->visibility = $options['visibility'] ?? DULCE_AUTH_DEFAULT_VISIBILITY;

            // Runs through options of array and set the corresponding values in the model
            foreach ($options as $key => $value) {
                // If 'verified' or 'visibility' has already been set, do not set it again
                if ($key !== 'verified' && $key !== 'visibility') {
                    $this->{$key} = $value;
                }
            }

            if ($this->save()) {
                // The "User" role is assigned to the newly registered user
                $userRole = Role::where('name', 'User')->first();
                if ($userRole) {
                    $this->roles()->attach($userRole->id);
                }
                return true;
            } else {
                throw new CreateUserException();
            }
        }
    }

    /**
     * Deletes a user and their associated relationships from the database.
     *
     * @param int $userId ID of the user to delete.
     *
     * @return bool true if the user and its relationships were successfully
     * deleted, false otherwise.
     *
     * @throws \src\exceptions\users\UserNotFoundException If the user ID is not found
     * (does not exist).
     * @throws \Exception When there is a problem deleting the user or
     * its relationships.
     */
    public function deleteUser(int $userId): bool
    {
        // Search for the user by their ID
        $user = self::find($userId);

        if ($user) {
            try {
                // Eliminate role relationships (assuming a many-to-many relationship)
                $user->roles()->detach();
                $user->accountVerification()->delete();
                $user->passwordReset()->detach();

                // Here you could eliminate any other intermediate relationship, such as permissions.

                // Finally, delete the user
                $user->delete();

                return true;
            } catch (\Illuminate\Database\QueryException $e) {
                throw new \Exception('Error deleting the user (some problem with
                 detach() or delete()):' . $e->getMessage());
            }
        } else {
            throw new UserNotFoundException('User ID not found (does not exist).');
        }
    }

    /**
     * Method to change a user's password and record the change.
     *
     * @param int $userId ID of the user whose password will be changed.
     * @param string $currentPassword User current password.
     * @param string $newPassword     New password for the user.
     *
     * @return bool true if the password change was successful, false otherwise.
     *
     * @throws \src\exceptions\users\UserNotFoundException If the user ID is not found
     * (does not exist).
     * @throws \src\exceptions\users\InvalidPasswordException
     * If the current password is not valid.
     * @throws \src\exceptions\users\LimitChangesPasswordException
     * When the limit of password changes per year is exceeded.
     * @throws \Exception When there is a problem changing the password.
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        // Start a transaction
        DB::beginTransaction();

        try {
            // Get user
            $user = self::find($userId);

            // Check if the user exists
            if (!$user) {
                throw new UserNotFoundException("User not found.");
            }

            // Verify the user's current password
            if (!password_verify($currentPassword, $user->password)) {
                throw new InvalidPasswordException();
            }

            // Gets the most recent password change record for the user
            $latestChange = PasswordChange::where('user_id', $userId)
                ->orderByDesc('created_at')
                ->first();

            // Gets the current date
            $currentDate = new \DateTime();

            // Checks if a previous change record exists, that is,
            // checks if a change has already been attempted before
            if ($latestChange) {
                // Gets the date of the last change
                $lastChangeDate = new \DateTime($latestChange->last_change_date);

                // Check if at least 1 year has passed since the last change
                $oneYearAgo = $lastChangeDate->modify('+1 year');
                if ($currentDate > $oneYearAgo) {
                    // Resets the change counter and updates the date of the last change
                    $latestChange->changes_count = 0;
                    $latestChange->last_change_date = $currentDate->format('Y-m-d');
                }
            } else {
                // Create a new change record if a previous one does not exist
                $latestChange = new PasswordChange();
                $latestChange->user_id = $userId;
                $latestChange->last_change_date = $currentDate->format('Y-m-d');
            }

            // Checks if the user has reached the limit of password changes per year
            if ($latestChange->changes_count < DULCE_AUTH_MAX_PASSWORD_CHANGES) {
                // Encrypt or hash passwords
                $oldPasswordHash = password_hash($currentPassword, PASSWORD_BCRYPT);
                $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

                // Store passwords in the change log
                $latestChange->old_password = $oldPasswordHash;
                $latestChange->new_password = $newPasswordHash;

                // Change the password in 'users' and update the change counter
                $user->password = $newPasswordHash;
                $user->save();

                $latestChange->changes_count++;
                $latestChange->save();

                // Confirm the transaction if everything has been executed correctly
                DB::commit();
                return true;
            } else {
                throw new LimitChangesPasswordException();
            }
        } catch (\Exception $e) {
            // Reverts the transaction in case of error
            DB::rollBack();
            throw new \Exception("Error changing password: " . $e->getMessage());
        }
    }
}
