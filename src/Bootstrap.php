<?php

namespace src;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Class Bootstrap
 *
 * It is responsible for initializing the database configuration using Eloquent
 * and registering the services in the container.
 *
 * @package src
 *
 * @since 2.0.0
 */
class Bootstrap
{

    /**
     * @var Capsule Eloquent ORM instance.
     */
    protected $capsule;

    /**
     * @var DulceContainer Dependency container.
     */
    protected $dulce;

    /**
     * Constructor.
     *
     * Constructor that initializes the database connection using configuration and registers the services
     * in the service container.
     *
     * @param \src\Configuration $config Instance of the Configuration class.
     * @param \src\DulceContainer $dulce Service container where the services will be registered.
     */
    public function __construct($config, $dulce)
    {
        // Inicializa Capsule (Eloquent)
        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver' => $config->get('DRIVER'),
            'host' => $config->get('HOST'),
            'database' => $config->get('DATABASE'),
            'username' => $config->get('USERNAME'),
            'password' => $config->get('PASSWORD'),
            'charset' => $config->get('CHARSET'),
            'collation' => $config->get('COLLATION'),
            'prefix' => $config->get('PREFIX'),
        ]);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        // Initializes the "dulce" service container
        $this->dulce = $dulce;
        $this->registerServices();
    }

    /**
     * Registers all services in the "dulce" container.
     *
     * Adds various instances of classes (users, roles, permissions, authentication...)
     * and other services to the container.
     *
     * @return void
     */
    public function registerServices()
    {

        $this->dulce->addService('User', function ($dulce) {
            return new models\User();
        });

        $this->dulce->addService('Role', function ($dulce) {
            return new models\Role();
        });

        $this->dulce->addService('Permission', function ($dulce) {
            return new models\Permission();
        });

        $this->dulce->addService('Session', function ($dulce) {
            return new Session();
        });

        $this->dulce->addService('PasswordChange', function ($dulce) {
            return new models\PasswordChange();
        });

        $this->dulce->addService('DulceMail', function ($dulce) {
            return new DulceMail();
        });

        $this->dulce->addService('AccountVerification', function ($dulce) {
            return new models\AccountVerification($dulce->get('User'));
        });

        $this->dulce->addService('Auth', function ($dulce) {
            return new Auth($dulce->get('User'), $dulce->get('Role'), $dulce->get('Session'));
        });

        $this->dulce->addService('Authorization', function ($dulce) {
            return new Authorization($dulce->get('Auth'), $dulce->get('User'));
        });

        $this->dulce->addService('RoleManagement', function ($dulce) {
            return new RoleManagement($dulce->get('User'), $dulce->get('Role'));
        });

        $this->dulce->addService('PermissionManagement', function ($dulce) {
            return new PermissionManagement($dulce->get('Permission'));
        });

        $this->dulce->addService('RolePermissionManagement', function ($dulce) {
            return new RolePermissionManagement($dulce->get('Role'), $dulce->get('Permission'));
        });

        $this->dulce->addService('PasswordReset', function ($dulce) {
            return new models\PasswordReset($dulce->get('User'));
        });
    }
}
