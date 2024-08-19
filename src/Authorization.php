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

use src\models\User;

/**
 * Authorization class
 *
 * Authorization class that provides functions to verify user roles and permissions.
 * This class uses a system of roles and permissions to determine if a user has
 * certain privileges in the application.
 *
 * @package src
 *
 * @since 1.0
 */
class Authorization
{
    /**
     * Instance of the Auth class to manage user authentication.
     *
     * @var \src\Auth
     */
    protected Auth $auth;

    /**
     * User model instance to access user information.
     *
     * @var \src\models\User
     */
    protected User $userModel;

    /**
     * Constructor of the Authorization class.
     *
     * @param \src\Auth $auth Instance of the Auth class to manage user authentication.
     * @param \src\models\User $userModel User model instance to access user information.
     */
    public function __construct(Auth $auth, User $userModel)
    {
        $this->auth = $auth;
        $this->userModel = $userModel;
    }

    /**
     * Method to check if a user has a specific role.
     *
     * @param string $roleName Name of the role to verify.
     * @param int|null $userId (Optional) User ID. If not provided,
     * the ID of the currently authenticated user is used.
     *
     * @return bool True if the user has the specified role, false otherwise.
     */
    public function hasRole(string $roleName, ?int $userId = null): bool
    {
        // If no user ID is provided, get the ID of the currently authenticated user
        $userId = $userId ?: ($this->auth->isLoggedIn() ? $this->auth->currentUser()->id : null);

        if ($userId) {
            $userRoles = $this->userModel::find($userId)->roles;

            foreach ($userRoles as $role) {
                if ($role->name === $roleName) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Method to verify if a logged in user has a specific permission.
     *
     * @param string $permissionName The name of the permission to check.
     *
     * @return bool True if the logged in user has the specified permission, false otherwise.
     */
    public function hasPermission(string $permissionName): bool
    {
        if ($this->auth->isLoggedIn()) {
            $user = $this->userModel::find($this->auth->currentUser()->id);

            if ($user) {
                $userRoles = $user->roles;

                foreach ($userRoles as $role) {
                    $rolePermissions = $role->permissions;

                    foreach ($rolePermissions as $permission) {
                        if ($permission->name === $permissionName) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
}
