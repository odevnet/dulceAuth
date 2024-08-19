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
class Permission extends Model
{
    /**
     * Name of the table where user permissions will be stored.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Primary key of the 'permissions' table.
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
    /**
     * All fields that can be mass assignment.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
     * Defines the many-to-many relationship between the current 'Permission' model and the 'Role' model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_roles', 'permission_id', 'role_id');
    }

    /**
     * Gets all permissions stored in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\src\models\Permission[]
     */
    public function showPermissions()
    {
        return self::all();
    }
}
