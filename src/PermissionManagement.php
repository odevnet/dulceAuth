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

use src\exceptions\roles\PermissionNotFoundException;
use src\exceptions\roles\PermissionSaveException;
use src\exceptions\roles\EmptyPermissionNameException;
use src\exceptions\roles\UsedPermissionNameException;

use src\models\Permission as Permission;
use src\models\PermissionRole as PermissionRole;

/**
 * Class PermissionManagement
 *
 * Class to manage permissions. Allows you to create, edit and delete permissions.
 *
 * @package src
 *
 * @since 1.0
 */
class PermissionManagement
{
    /**
     * Refers to the "Permission" model
     *
     * @var \src\models\Permission
     */
    private Permission $permissionModel;

    /**
     * Constructor where we initialize the "Permission" model
     *
     * @param \src\models\Permission $permissionModel Permission model to use.
     */
    public function __construct(Permission $permissionModel)
    {
        $this->permissionModel = $permissionModel;
    }

    /**
     * This method allows you to create a permission.
     *
     * @param string $name Permission name.
     * @param ?string $description Optional description of the permission.
     *
     * @return bool True if the permission was created successfully, false
     * if there was an error.
     *
     * @throws \src\exceptions\roles\EmptyPermissionNameException
     * When the permission name is empty.
     * @throws \src\exceptions\roles\UsedPermissionNameException
     * If the permission already exists.
     * @throws \src\exceptions\roles\PermissionSaveException
     * If occurs an error creating the permission.
     */
    public function createPermission(string $name, ?string $description = null): bool
    {
        // Validate that the name is not empty and that there is no other permission with the same name.
        if (empty($name)) {
            throw new EmptyPermissionNameException();
        }
        if ($this->permissionModel::where('name', $name)->exists()) {
            throw new UsedPermissionNameException();
        }
        // Create the new permission.
        $this->permissionModel->name = $name;
        $this->permissionModel->description = ($description !== null) ? $description : null;
        if ($this->permissionModel->save()) {
            //echo 'Permission created successfully!';
            return true;
        } else {
            throw new PermissionSaveException('An error occurred while creating the permission.');
        }
    }

    /**
     * Method to edit an existing permission
     *
     * @param int    $permissionId Unique identifier of the permission to edit.
     * @param string $newName      New name for the permission.
     *
     * @return bool True if the permission was edited successfully,
     * false if there was an error.
     *
     * @throws \InvalidArgumentException When the second parameter is not a string.
     * @throws \src\exceptions\roles\EmptyPermissionNameException
     * When the permission name is empty.
     * @throws \src\exceptions\roles\UsedPermissionNameException
     * If the permission already exists.
     * @throws \src\exceptions\roles\PermissionSaveException
     * If occur an error editing the permission.
     * @throws \src\exceptions\roles\PermissionNotFoundException
     * If don't exist the permission.
     */
    public function editPermission(int $permissionId, string $newName, ?string $newDescription = null): bool
    {
        if (!is_string($newName)) {
            throw new \InvalidArgumentException('The second parameter must be a string.');
        }
        if (empty($newName)) {
            throw new EmptyPermissionNameException();
        }
        if (
            $this->permissionModel::where('name', $newName)
            ->where('id', '!=', $permissionId)
            ->exists()
        ) {
            throw new UsedPermissionNameException();
        }


        $permission = $this->permissionModel::find($permissionId);
        if ($permission) {
            $permission->name = $newName;
            $permission->description = ($newDescription !== null) ? $newDescription : null;
            if ($permission->save()) {
                return true;
            } else {
                throw new PermissionSaveException('An error occurred while editing the permission.');
            }
        } else {
            throw new PermissionNotFoundException('There is no permission for that ID.');
        }
    }

    /**
     * Removes a permission and its associations.
     *
     * @param int $permissionId ID of the permission to delete.
     *
     * @return bool True if the permission was removed successfully,
     * false if there was an error.
     *
     * @throws PermissionNotFoundException When the permission does not exist
     * or has already been removed.
     */
    public function deletePermission(int $permissionId): bool
    {
        // Check if the permission exists.
        $permission = $this->permissionModel::find($permissionId);

        if ($permission) {
            // Because it exists, it removes associations in the permission_roles table for the permission.
            PermissionRole::where('permission_id', $permissionId)->delete();

            // Finally, remove the permission.
            $permission->delete();

            //echo "The permission was successfully removed, and the related associations were removed.";
            return true;
        } else {
            throw new PermissionNotFoundException('The permission does not exist or has already been deleted.');
        }
    }
}
