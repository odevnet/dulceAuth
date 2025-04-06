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

use src\exceptions\roles\RoleNotFoundException;
use src\exceptions\roles\PermissionNotFoundException;
use src\exceptions\roles\MissingRoleOrPermissionException;
use src\exceptions\roles\RolePermissionAlreadyExistsException;

use src\models\Role;
use src\models\Permission;

/**
 * Class RolePermissionManagement
 *
 * Class to manage the assignment and removal of permissions to roles.
 *
 * @package src
 *
 * @since 1.0
 */
class RolePermissionManagement
{
    /**
     * Represents the role model.
     *
     *  @var \src\models\Role
     */
    private Role $roleModel;

    /**
     * Refers to the "Permission" model
     *
     * @var \src\models\Permission
     */
    private Permission $permissionModel;

    /**
     * In the constructor initialize the "Role" and "Permission" models.
     *
     * @param \src\models\Role $roleModel Role model to use.
     * @param \src\models\Permission $permissionModel Permission model to use.
     */
    public function __construct(Role $roleModel, Permission $permissionModel)
    {
        $this->roleModel = $roleModel;
        $this->permissionModel = $permissionModel;
    }

    /**
     * It is used to assign a permission to a role.
     *
     * @param int $roleId       Role ID.
     * @param int $permissionId Permission ID.
     *
     * @return bool True if the permission was assigned successfully, false otherwise.
     *
     * @throws \src\exceptions\roles\MissingRoleOrPermissionException
     * When the role or permission does not exist (haven't been selected).
     * @throws \src\exceptions\roles\RoleNotFoundException
     * If the role has not been found.
     * @throws \src\exceptions\roles\PermissionNotFoundException
     * If the permission has not been found.
     * @throws \src\exceptions\roles\RolePermissionAlreadyExistsException
     * If the role already has that permission assigned.
     */
    public function assignPermissionToRole(int $roleId, int $permissionId): bool
    {
        // Verify that a role and permission have been selected (not empty)
        if (empty($roleId) || empty($permissionId)) {
            throw new MissingRoleOrPermissionException();
        }
        // Search for the role and permission
        $role = $this->roleModel::find($roleId);
        $permission = $this->permissionModel::find($permissionId);

        // Check if the role and permission exist
        if (!$role) {
            throw new RoleNotFoundException();
        }

        if (!$permission) {
            throw new PermissionNotFoundException();
        }
        // Assign the permission to the role but first...
        // Check if the relationship already exists to avoid duplicates
        if ($role->permissions->contains($permissionId)) {
            throw new RolePermissionAlreadyExistsException();
        }

        $role->permissions()->attach($permission); // Assign the permission
        return true;
    }

    /**
     * Removes a permission from a role.
     *
     * @param int $roleId       Role ID.
     * @param int $permissionId Permission ID.
     *
     * @return bool True if the permission was removed from the role successfully,
     * false otherwise.
     *
     * @throws \src\exceptions\roles\MissingRoleOrPermissionException
     * When the role or permission does not exist (haven't been selected).
     * @throws \src\exceptions\roles\RoleNotFoundException
     * If the role has not been found.
     * @throws \src\exceptions\roles\PermissionNotFoundException
     * When the role does not have the specified permission.
     */
    public function removePermissionFromRole(int $roleId, int $permissionId): bool
    {
        if (empty($roleId) || empty($permissionId)) {
            throw new MissingRoleOrPermissionException();
        }

        $role = $this->roleModel::find($roleId);

        if (!$role) {
            throw new RoleNotFoundException();
        }

        if ($role->permissions()->where('permission_id', $permissionId)->exists()) {
            $role->permissions()->detach($permissionId);
            return true;
        } else {
            throw new PermissionNotFoundException('The role does not have the specified permission.');
        }
    }
}
