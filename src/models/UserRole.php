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
 * This Model, will be used with the 'RoleManagement' class.
 *
 * @package src\models
 *
 * @since 1.0
 */
class UserRole extends Model
{
    /**
     * Name of the table that contains the relationships between
     * users and roles.
     *
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * Primary key of the 'user_roles' table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * "Eloquent timestamps".
     * If 'false' is set, we tell it not to handle them automatically, that is,
     * it will stop looking for the created_at and updated_at columns in the table.
     *
     * @var bool
     */
    public $timestamps = false;
}
