<?php

namespace src;

/**
 * dulceAuth: Library that allows user management. It facilitates registration
 * and authentication, as well as the administration of users with roles and
 * permissions.
 *
 * @link https://github.com/odevnet/dulceAuth/
 *
 * @license https://github.com/odevnet/dulceAuth/blob/main/LICENSE (MIT License)
 */

use src\Configuration;
use src\DulceContainer;

/**
 * DulceAuth class
 *
 * This class serves as an abstraction layer for authentication methods,
 * user, role and permission management, as well as account verification
 * and session management.
 *
 * @package src
 *
 * @since 1.0
 */
class DulceAuth
{

    /**
     * Dependency container.
     *
     * @var \src\DulceContainer
     */
    public $dulce;

    /**
     * Authentication 'Service'.
     *
     * @var \src\Auth
     */
    private $auth;

    /**
     * Represents the account verification 'service'.
     *
     * @var \src\models\AccountVerification
     */
    private $accountVerification;

    /**
     * Role model.
     *
     * @var \src\Role
     */
    private $roleModel;

    /**
     * Permission model.
     *
     * @var \src\models\Permission
     */
    private $permissionModel;

    /**
     * User model.
     *
     * @var \src\models\UserUser
     */
    private $userModel;

    /**
     * Represents the model for changing password.
     *
     * @var \src\models\PasswordChange
     */
    private $passwordChangeModel;

    /**
     * Model for password reset.
     *
     * @var \src\models\PasswordReset
     */
    private $passwordResetModel;

    /**
     * Session management 'service'.
     *
     * @var \src\Session
     */
    private $session;

    /**
     * Authorization 'Service'.
     *
     * @var \src\Authorization
     */
    private $authorization;

    /**
     * Role management 'service'.
     *
     * @var \src\RoleManagement
     */
    private $roleManagement;

    /**
     * Permission management 'service'.
     *
     * @var \src\PermissionManagement
     */
    private $permissionManagement;

    /**
     * Role permission management 'service'.
     *
     * @var \src\RolePermissionManagement
     */
    private $rolePermissionManagement;

    /**
     * Represents the email 'service'.
     *
     * @var \src\DulceMail
     */
    private $dulceMail;

    /**
     * Represents the Configuration class
     *
     * @var \src\Configuration
     */
    private $config;

    /**
     * Constructor that initializes services from the dependency container.
     *
     * @param array|string $customConfig Configuration file(s). It can be a string (single file)
     *                                    or an array of file paths (multiple files).
     * @param \src\DulceContainer $container Dependency container (optional).
     */
    public function __construct($customConfig = [], $container = null)
    {
        $this->config = new Configuration();

        // Asegurar que $customConfig sea siempre un array
        $files = is_array($customConfig) ? $customConfig : [$customConfig];

        // Cargar todos los archivos de configuración
        foreach ($files as $file) {
            $this->config->load($file);
        }

        // Inicializar el contenedor de dependencias (por defecto, uno nuevo)
        $this->dulce = $container ?? new DulceContainer();

        // Inicializar Bootstrap con la configuración y el contenedor
        $bootstrap = new Bootstrap($this->config, $this->dulce);

        $this->auth = $this->dulce->get('Auth');
        $this->accountVerification = $this->dulce->get('AccountVerification');
        $this->session = $this->dulce->get('Session');
        $this->roleModel = $this->dulce->get('Role');
        $this->permissionModel = $this->dulce->get('Permission');
        $this->passwordChangeModel = $this->dulce->get('PasswordChange');
        $this->passwordResetModel = $this->dulce->get('PasswordReset');
        $this->userModel = $this->dulce->get('User');
        $this->authorization = $this->dulce->get('Authorization');
        $this->roleManagement = $this->dulce->get('RoleManagement');
        $this->permissionManagement = $this->dulce->get('PermissionManagement');
        $this->rolePermissionManagement = $this->dulce->get('RolePermissionManagement');
        $this->dulceMail = $this->dulce->get('DulceMail');
    }
    /**
     * Gets a value from the configuration.
     *
     * @param string $key The key for the configuration value.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The value of the configuration or the default value.
     *
     * @return mixed
     *
     * @see src\Configuration::get
     */
    public function getConfig($key, $default = null)
    {
        return $this->config->get($key, $default);
    }
    /**
     * Returns the email service to make full use of the email class.
     *
     * @return \src\DulceMail
     */
    public function dulceMail()
    {
        return $this->dulceMail;
    }

    /**
     * Returns the session management service.
     *
     * @return \src\Session
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * Sign in with the provided credentials.
     * Delegates authentication to the 'login' method of the 'Auth' class.
     *
     * @param string $email Email of the user.
     * @param string $password User password.
     *
     * @return bool True if the login is successful; otherwise false.
     *
     * @see Auth::login
     *
     */
    public function login(string $email, string $password)
    {
        return $this->auth->login($email, $password);
    }
    /**
     * Register a new user delegating the registration process to the method
     * 'register' of class 'Auth'.
     *
     * @param string $name Name of the user.
     * @param string $email Email of the user.
     * @param string $password User password.
     * @param array $options Additional options.
     *
     * @return mixed
     *
     * @see src\Auth::register
     */
    public function register(string $name, string $email, string $password, array $options = [])
    {
        return $this->auth->register($name, $email, $password, $options = []);
    }
    /**
     * Delegates the verification of if a user is authenticated or not
     * to the 'isLoggedIn' method of the 'Auth' class.
     *
     * @return bool
     *
     * @see Auth::isLoggedIn
     */
    public function isLoggedIn()
    {
        return $this->auth->isLoggedIn();
    }

    /**
     * Log out the user.
     *
     * @return void
     *
     * @see Auth::logout
     */
    public function logout()
    {
        return $this->auth->logout();
    }
    /**
     * Delegates getting the currently authenticated user to the method
     * 'currentUser' from class 'Auth'.
     *
     * @return \src\models\User|null
     *
     * @see Auth::currentUser
     */
    public function currentUser()
    {
        return $this->auth->currentUser();
    }

    /**
     * Validates the account verification token.
     * Delegates the process to the 'validateTokenAccount' method of
     * 'AccountVerification' model.
     *
     * @param string $token Verification token.
     * @param int $userId User ID.
     *
     * @return bool
     *
     * @see AccountVerification::validateTokenAccount
     */
    public function validateTokenAccount(string $token, int $userId)
    {
        return $this->accountVerification->validateTokenAccount($token, $userId);
    }
    /**
     * Set the user's account as verified.
     *
     * @param int $userId User ID.
     *
     * @return void
     *
     * @see \src\models\AccountVerification::verified
     */
    public function verified(int $userId)
    {
        return $this->accountVerification->verified($userId);
    }
    /**
     * Generates a verification token for the provided email.
     *
     * @param string $email Email of the user.
     *
     * @return array
     *
     * @see src\models\AccountVerification::generateVerificationToken
     */
    public function generateVerificationToken(string $email, bool $send = true)
    {
        return $this->accountVerification->generateVerificationToken($email, $send);
    }

    /**
     * Checks if the user has a specific role.
     * Delegates to the 'hasRole' method of the 'Authorization' class.
     *
     * @param string $roleName Role name.
     * @param int|null $userId Optional user ID.
     * @return bool
     *
     * @see src\Authorization::hasRole
     */
    public function hasRole(string $roleName, $userId = null)
    {
        return $this->authorization->hasRole($roleName, $userId);
    }

    /**
     * Checks if the user has a specific permission.
     *
     * @param string $permissionName Permission name.
     * @return bool
     *
     * @see src\Authorization::hasPermission
     */
    public function hasPermission(string $permissionName)
    {
        return $this->authorization->hasPermission($permissionName);
    }
    /**
     * Delegates to the 'showUsers' method of the 'User' model to return
     * a list of all users.
     *
     * @return array
     *
     * @see src\models\User::showUsers
     */
    public function showUsers()
    {
        return $this->userModel->showUsers();
    }

    /**
     * Returns a list of IDs of all users.
     *
     * @return array
     *
     * @see src\models\User::showUsers
     */
    public function showUsersById()
    {
        $userListById = [];

        foreach ($this->userModel->showUsers() as $userList) {
            $userListById[] = $userList->id;
        }

        return $userListById;
    }

    /**
     * Returns a list of names of all users.
     *
     * @return array
     *
     * @see src\models\User::showUsers
     */
    public function showUsersByName()
    {
        $userListByName = [];

        foreach ($this->userModel->showUsers() as $userList) {
            $userListByName[] = $userList->name;
        }

        return $userListByName;
    }

    /**
     * Returns a list of emails of all users.
     *
     * @return array
     *
     * @see src\models\User::showUsers
     */
    public function showUsersByEmail()
    {
        $userListByEmail = [];

        foreach ($this->userModel->showUsers() as $userList) {
            $userListByEmail[] = $userList->email;
        }

        return $userListByEmail;
    }

    /**
     * Checks if a user ID exists.
     *
     * @param int $userId User ID.
     *
     * @return bool
     *
     * @see src\models\User::userIdExists
     */
    public function userIdExists(int $userId)
    {
        return $this->userModel->userIdExists($userId);
    }

    /**
     * Checks if a user email exists.
     *
     * @param string $email User email.
     *
     * @return bool
     *
     * @see src\models\User::showUsers
     */
    public function userEmailExists(string $email)
    {
        return $this->userModel->userEmailExists($email);
    }

    /**
     * Returns the roles of a user by their ID.
     *
     * @param int $userId User ID.
     *
     * @return array
     *
     * @see src\models\User::getUserRoles
     */
    public function userRoles(int $userId)
    {
        return $this->userModel->getUserRoles($userId);
    }

    /**
     * Returns a list of a user's role IDs.
     *
     * @param int $userId User ID.
     *
     * @return array
     *
     * @see src\models\User::showUsers
     */
    public function userRolesById(int $userId)
    {
        $roleId = [];

        foreach ($this->userModel->getUserRoles($userId) as $role) {
            $roleId[] = $role->id;
        }

        return $roleId;
    }

    /**
     * Returns a list of role names for a user.
     *
     * @param int $userId ID del usuario.
     *
     * @return array
     *
     * @see src\models\User::showUsers
     */
    public function userRolesByName(int $userId)
    {
        $roleName = [];

        foreach ($this->userModel->getUserRoles($userId) as $role) {
            $roleName[] = $role->name;
        }

        return $roleName;
    }

    /**
     * Edit the information of a user, delegating the process to the method
     * 'editUser' of model class 'User'.
     *
     * @param int $userId User ID.
     * @param array $options Options for editing.
     *
     * @return bool
     *
     * @see src\models\User::editUser
     */
    public function editUser(int $userId, array $options)
    {
        return $this->userModel->editUser($userId, $options);
    }

    /**
     * Delete a user.
     * Delegate to 'deleteUser' of the 'User' model.
     *
     * @param int $userId User ID.
     *
     * @return bool
     *
     * @see src\models\User::deleteUser
     */
    public function deleteUser(int $userId)
    {
        return $this->userModel->deleteUser($userId);
    }

    /**
     * Create a new user "from admin".
     * Delegates creation via the 'createUser' method of the 'User' model.
     *
     * @param string $name Name of the user.
     * @param string $email Email of the user.
     * @param string $password User password.
     * @param array $options Additional options.
     *
     * @return bool
     *
     * @see src\models\User::createUser
     */
    public function createUser(string $name, string $email, string $password, array $options = [])
    {
        return $this->userModel->createUser($name, $email, $password, $options);
    }

    /**
     * Change a user's password, through the 'changePassword' method
     * from the 'User' model.
     *
     * @param int $userId User ID.
     * @param string $currentPassword User's current password.
     * @param string $newPassword New password for the user.
     *
     * @return bool
     *
     * @see src\models\User::changePassword
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword)
    {
        return $this->userModel->changePassword($userId, $currentPassword, $newPassword);
    }

    /**
     * Returns the last password changes for a user.
     * Delegates to the 'latestChange' method of the 'PasswordChange' model.
     *
     * @param int $userId User ID.
     *
     * @return \src\models\PasswordChange|null
     *
     * @see \src\models\PasswordChange::latestChange
     */
    public function latestChange(int $userId)
    {
        return $this->passwordChangeModel->latestChange($userId);
    }

    /**
     * Starts the password reset process for a user.
     *
     * @param string $email Email of the user.
     * @param bool $send Send reset email? (default true).
     *
     * @return array|null
     *
     * @see \src\models\PasswordReset::forgotPassword
     */
    public function forgotPassword(string $email, bool $send = true)
    {
        return $this->passwordResetModel->forgotPassword($email, $send);
    }

    /**
     * Validates the password reset token.
     * Delegates to 'validateTokenPassword' of the 'PasswordReset' model class.
     *
     * @param string $token Reset token.
     * @param int $userId User ID.
     *
     * @return bool
     *
     * @see \src\models\PasswordReset::validateTokenPassword
     */
    public function validateTokenPassword(string $token, int $userId)
    {
        return $this->passwordResetModel->validateTokenPassword($token, $userId);
    }

    /**
     * Enter a new password for a user.
     *
     * @param string $password New password for the user.
     * @param int $userId User ID.
     *
     * @return void
     *
     * @see \src\models\PasswordReset::insertNewPassword
     */
    public function insertNewPassword(string $password, int $userId)
    {
        return $this->passwordResetModel->insertNewPassword($password, $userId);
    }

    /**
     * Create a new role delegating its creation to the 'createRole' method
     * from class 'RoleManagement'.
     *
     * @param string $name Role name.
     *
     * @return bool
     *
     * @see \src\RoleManagement::createRole
     */
    public function createRole(string $name)
    {
        return $this->roleManagement->createRole($name);
    }

    /**
     * Edit an existing role.
     *
     * @param int $roleId ID of the role.
     * @param string $name New name of the role.
     *
     * @return bool
     *
     * @see \src\RoleManagement::editRole
     */
    public function editRole(int $roleId, string $name)
    {
        return $this->roleManagement->editRole($roleId, $name);
    }

    /**
     * Delete a role.
     *
     * @param int $roleId ID of the role.
     *
     * @return bool
     *
     * @see \src\RoleManagement::deleteRole
     */
    public function deleteRole(int $roleId)
    {
        return $this->roleManagement->deleteRole($roleId);
    }

    /**
     * Assign roles to a user.
     *
     * @param int $userId User ID.
     * @param array $rolesId IDs of the roles.
     *
     * @return bool
     *
     * @see \src\RoleManagement::assignRoleToUser
     */
    public function assignRoleToUser(int $userId, array $rolesId)
    {
        return $this->roleManagement->assignRoleToUser($userId, $rolesId);
    }

    /**
     * Remove roles from a user.
     *
     * @param int $userId User ID.
     * @param array $rolesId IDs of the roles.
     *
     * @return bool
     *
     * @see \src\RoleManagement::removeRoleToUser
     */
    public function removeRoleToUser(int $userId, array $rolesId)
    {
        return $this->roleManagement->removeRoleToUser($userId, $rolesId);
    }

    /**
     * Create a new permission. Delegate the creation to the 'createPermission' method
     * from class 'PermissionManagement'.
     *
     * @param string $name Name of the permission.
     * @param string $description Description of the permission.
     *
     * @return bool
     *
     * @see \src\PermissionManagement::createPermission
     */
    public function createPermission(string $name, string $description)
    {
        return $this->permissionManagement->createPermission($name, $description);
    }

    /**
     * Edit an existing permission.
     *
     * @param int $permissionId ID of the permission.
     * @param string $newName New name of the permission.
     *
     * @return bool
     *
     * @see \src\PermissionManagement::editPermission
     */
    public function editPermission(int $permissionId, string $newName)
    {
        return $this->permissionManagement->editPermission($permissionId, $newName);
    }

    /**
     * Delete a permission.
     *
     * @param int $permissionId ID of the permission.
     *
     * @return bool
     *
     * @see \src\PermissionManagement::deletePermission
     */
    public function deletePermission(int $permissionId)
    {
        return $this->permissionManagement->deletePermission($permissionId);
    }

    /**
     * Assigns a permission to a role.
     * Delegates to 'assignPermissionToRole' of class 'RolePermissionManagement'.
     *
     * @param int $roleId ID of the role.
     * @param int $permissionId ID of the permission.
     *
     * @return bool
     *
     * @see \src\RolePermissionManagement::assignPermissionToRole
     */
    public function assignPermissionToRole(int $roleId, int $permissionId)
    {
        return $this->rolePermissionManagement->assignPermissionToRole($roleId, $permissionId);
    }

    /**
     * Removes a permission from a role.
     *
     * @param int $roleId ID of the role.
     * @param int $permissionId ID of the permission.
     *
     * @return bool
     *
     * @see \src\RolePermissionManagement::removePermissionFromRole
     */
    public function removePermissionFromRole(int $roleId, int $permissionId)
    {
        return $this->rolePermissionManagement->removePermissionFromRole($roleId, $permissionId);
    }

    /**
     * Returns a list of all roles.
     * Delegates to the 'showRoles' method of the 'Role' model.
     *
     * @return array
     *
     * @see \src\models\Role::showRoles
     */
    public function showRoles()
    {
        return $this->roleModel->showRoles();
    }

    /**
     * Returns a list of IDs of all roles.
     *
     * @return array
     */
    public function showRolesById()
    {
        $roleId = [];

        foreach ($this->roleModel->showRoles() as $role) {
            $roleId[] = $role->id;
        }

        return $roleId;
    }

    /**
     * Returns a list of names of all roles.
     *
     * @return array
     */
    public function showRolesByName()
    {
        $roleName = [];

        foreach ($this->roleModel->showRoles() as $role) {
            $roleName[] = $role->name;
        }

        return $roleName;
    }

    /**
     * Returns a list of all permissions.
     * Delegates to the 'showPermissions' method of the 'Permission' model.
     *
     * @return array
     *
     * @see \src\models\Permission::showPermissions
     */
    public function showPermissions()
    {
        return $this->permissionModel->showPermissions();
    }

    /**
     * Returns a list of IDs of all permissions.
     *
     * @return array
     */
    public function showPermissionsById()
    {
        $permissionId = [];

        foreach ($this->permissionModel->showPermissions() as $permission) {
            $permissionId[] = $permission->id;
        }

        return $permissionId;
    }

    /**
     * Returns a list of names of all permissions.
     *
     * @return array
     */
    public function showPermissionsByName()
    {
        $permissionName = [];

        foreach ($this->permissionModel->showPermissions() as $permission) {
            $permissionName[] = $permission->name;
        }

        return $permissionName;
    }
}
