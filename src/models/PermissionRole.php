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

/**
 * Model that inherits from the 'Eloquent Model' (\Illuminate\Database\Eloquent\Model).
 * This Model, will be used with the 'PermissionManagement' class.
 *
 * @package src\models
 *
 * @since 1.0
 */
class PermissionRole extends Model
{
    /**
     * Name of the table that contains the relationships between
     * roles and permissions
     *
     * @var string
     */
    protected $table = 'permission_roles';

    /**
     * Primary key of the 'permission_roles' table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
}
