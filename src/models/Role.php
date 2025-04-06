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

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Model that inherits from the 'Eloquent Model' (\Illuminate\Database\Eloquent\Model).
 * This Model, will be used with the 'RoleManagement' class.
 *
 * @package src\models
 *
 * @since 1.0
 */
class Role extends Model
{
    /**
     * Name of the table where the roles will be stored.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Primary key of the 'roles' table.
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

    //protected $fillable = ['name', 'description'];

    /**
     * Defines the many-to-many relationship between the current 'Role' model and the 'Permission' model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_roles', 'role_id', 'permission_id');
    }

    /**
     * Gets all roles stored in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\src\models\Role[]
     */
    public function showRoles()
    {
        return self::all();
    }
}
