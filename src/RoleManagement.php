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

use src\exceptions\roles\EmptyRoleNameException;
use src\exceptions\roles\RoleNotFoundException;
use src\exceptions\roles\RoleSaveException;
use src\exceptions\roles\UsedRoleNameException;
use src\exceptions\roles\RoleNotSelectedException;
use src\exceptions\roles\RoleAssignmentException;
use src\exceptions\roles\RoleNotAssignedException;
use src\exceptions\users\UserNotFoundException;

use src\models\PermissionRole as PermissionRole;
use src\models\UserRole as UserRole;
use src\models\User;
use src\models\Role;

/**
 * Class RoleManagement
 *
 * This class allows you to handle complete role administration.
 * Create, edit and delete roles but also allow you to assign and remove
 * roles to users.
 *
 * @package src
 *
 * @since 1.0
 */
class RoleManagement
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
     * Constructor of the RoleManagement class where the models are started.
     *
     * @param \src\models\User $userModel User model.
     * @param \src\models\Role $roleModel Role model.
     */
    public function __construct(User $userModel, Role $roleModel)
    {
        $this->userModel = $userModel;
        $this->roleModel = $roleModel;
    }

    /**
     * Create a new role with the name passed as a parameter.
     *
     * @param string $name New name for the role.
     *
     * @return bool True if the role is created successfully.
     *
     * @throws \src\exceptions\roles\EmptyRoleNameException If the new role name is empty.
     * @throws \src\exceptions\roles\UsedRoleNameException If the new role name is already in use by another role.
     * @throws \src\exceptions\roles\RoleSaveException If there was an error saving the role edit.
     */
    public function createRole(string $name): bool
    {
        // Validate that the name is not empty and that there is no other role with the same name
        if (empty($name)) {
            throw new EmptyRoleNameException();
        }
        if ($this->roleModel::where('name', $name)->exists()) {
            throw new UsedRoleNameException();
        }

        // Create new role
        $role = new $this->roleModel();
        $role->name = $name;

        if ($role->save()) {
            return true;
            //return $role; // Optional: Return the created role.
        } else {
            throw new RoleSaveException();
        }
    }

    /**
     * Edit the name of a role.
     *
     * @param int $roleId ID of the role to edit.
     * @param string $newName New name for the role.
     *
     * @return bool True if the role is successfully edited.
     *
     * @throws \src\exceptions\roles\RoleNotFoundException If the role is not found.
     * @throws \src\exceptions\roles\EmptyRoleNameException If the new role name is empty.
     * @throws \src\exceptions\roles\UsedRoleNameException If the new role name is already in use by another role.
     * @throws \src\exceptions\roles\RoleSaveException If there was an error saving the role edit.
     */
    public function editRole(int $roleId, string $newName): bool
    {

        $role = $this->roleModel::find($roleId);

        if (!$role) {
            throw new RoleNotFoundException();
        }

        if (empty($newName)) {
            throw new EmptyRoleNameException();
        }

        $existingRole = $this->roleModel::where('name', $newName)->first();
        if ($existingRole && $existingRole->id !== $roleId) {
            throw new UsedRoleNameException();
        }

        $role->name = $newName;

        if ($role->save()) {
            return true;
            //return $role;
        } else {
            throw new RoleSaveException('Error editing role.');
        }
    }

    /**
     * Method to delete an existing role and reassign users to a default role.
     *
     * @param int $roleId ID of the role to delete.
     *
     * @return bool True if the role is successfully deleted and users are reassigned.
     *
     * @throws \src\exceptions\roles\RoleNotFoundException If the role does not exist or has already been deleted.
     */
    public function deleteRole(int $roleId): bool
    {
        // Start a transaction
        DB::beginTransaction();

        try {
            // Check if the role exists.
            $role = $this->roleModel::find($roleId);

            if ($role) {
                // Get the ID of the "User" role (or other default role) that will be used as a replacement.
                // For now the "User" role is ALWAYS used
                $newRoleId = $this->roleModel::where('name', 'User')->value('id');

                // Delete the relationships in the 'user_roles' table and assign the new role to the users.
                // This does not delete the role itself, but what it does is an update.
                // Updates the 'role_id' of the users who had the role removed so that they have the new role.
                // Therefore, the records related to the deleted role are updated,
                // which is an effective way to reassign roles to users.
                UserRole::where('role_id', $roleId)->update(['role_id' => $newRoleId]);

                // Removes associations in the 'permission_roles' table for the role.
                PermissionRole::where('role_id', $roleId)->delete();

                // Finally, delete the role.
                $role->delete();

                // From here, it is understood that the role was successfully deleted,
                // the new role was assigned to the users and the associations/relationships were deleted.
                return true;
            } else {
                throw new RoleNotFoundException('The role does not exist or has already been deleted.');
            }

            // Confirm the transaction if everything has been executed correctly
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Reverts the transaction in case of error
            DB::rollBack();
            throw new \Exception('Error deleting the role: ' . $e->getMessage());
        }
    }

    /**
     * It is responsible for assigning roles to a specific user.
     *
     * @param int $userId ID of the user to whom the roles will be assigned.
     * @param array $roles Array of role identities to assign to the user.
     *
     * @return bool True if at least one role is successfully assigned.
     *
     * @throws \src\exceptions\roles\RoleNotSelectedException If at least one role is not selected.
     * @throws \src\exceptions\users\UserNotFoundException If the user is not found.
     * @throws \src\exceptions\roles\RoleAssignmentException If an error occurred while assigning the role to the user.
     */
    public function assignRoleToUser(int $userId, array $roles): bool
    {
        // Validate that at least one role has been selected
        if (empty($roles)) {
            throw new RoleNotSelectedException('You must select at least one role to assign to the user.');
        }

        // Get user
        $user = $this->userModel::find($userId);

        // Check if the user exists
        if (!$user) {
            throw new UserNotFoundException();
        }
        // Assign roles to the user
        $success = false; // Variable to track whether at least one role has been assigned successfully

        foreach ($roles as $roleId) {
            try {
                // Check if the user already has the role before assigning it
                if (!$user->roles->contains($roleId)) {
                    // The user does not have the role, therefore assign it
                    $user->roles()->attach($roleId);
                    $success = true; // At least one role has been successfully assigned
                }
            } catch (\RuntimeException $ex) {
                Logger::error('Error assigning role: ' . $ex->getMessage(), $ex->getTraceAsString());
                throw new RoleAssignmentException();
            }
        }

        return $success;
    }

    /**
     * Remove roles from a user.
     *
     * @param mixed $userId ID of the user to remove roles from.
     * @param array $roles Array of role identities that are removed from the user.
     *
     * @return bool True if at least one role is successfully removed.
     *
     * @throws \src\exceptions\roles\RoleNotSelectedException If at least one role is not selected
     * or the user does not have the role.
     * @throws \src\exceptions\users\UserNotFoundException If the user is not found.
     * @throws \src\exceptions\roles\RoleNotAssignedException If the user does not have the role.
     */
    public function removeRoleToUser(int $userId, array $roles): bool
    {
        // Check that at least one role has been selected
        if (empty($roles)) {
            throw new RoleNotSelectedException('You must select at least one role to delete.');
        }

        // Get user
        $user = $this->userModel::find($userId);

        // Check if the user exists
        if (!$user) {
            throw new UserNotFoundException('User not found.');
        }

        // Delete user roles
        $success = false; // Variable to track whether at least one role has been successfully deleted

        foreach ($roles as $roleId) {

            // Check if the user has the role before removing it
            if ($user->roles->contains($roleId)) {
                // The user has the role, therefore remove it
                $user->roles()->detach($roleId);
                $success = true; // At least one role has been successfully deleted
            } else {
                throw new RoleNotAssignedException("The user does not have the role with ID: $roleId.");
            }
        }

        return $success;
    }
}
