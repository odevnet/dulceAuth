<?php

use Illuminate\Database\Capsule\Manager as Capsule;

use src\DulceContainer;

require 'vendor/autoload.php';
require(__DIR__ . '/src/config/config.php');

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => $driver,
    'host' => $host,
    'database' => $database,
    'username' => $username,
    'password' => $password,
    'charset' => $charset,
    'collation' => $collation,
    'prefix' => $prefix,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// A "dulce" service container is created
$dulce = new DulceContainer();

// Container services list:
$dulce->addService('User', function ($dulce) {
    return new src\models\User();
});

$dulce->addService('Role', function ($dulce) {
    return new src\models\Role();
});

$dulce->addService('Permission', function ($dulce) {
    return new src\models\Permission();
});

$dulce->addService('Session', function ($dulce) {
    return new src\Session();
});

$dulce->addService('PasswordChange', function ($dulce) {
    return new src\models\PasswordChange();
});

$dulce->addService('DulceMail', function ($dulce) {
    return new src\DulceMail();
});

$dulce->addService('AccountVerification', function ($dulce) {
    return new src\models\AccountVerification($dulce->get('User'));
});

$dulce->addService('Auth', function ($dulce) {
    return new src\Auth($dulce->get('User'), $dulce->get('Role'), $dulce->get('Session'));
});

$dulce->addService('Authorization', function ($dulce) {
    return new src\Authorization($dulce->get('Auth'), $dulce->get('User'));
});

$dulce->addService('RoleManagement', function ($dulce) {
    return new src\RoleManagement($dulce->get('User'), $dulce->get('Role'));
});

$dulce->addService('PermissionManagement', function ($dulce) {
    return new src\PermissionManagement($dulce->get('Permission'));
});

$dulce->addService('RolePermissionManagement', function ($dulce) {
    return new src\RolePermissionManagement($dulce->get('Role'), $dulce->get('Permission'));
});

$dulce->addService('PasswordReset', function ($dulce) {
    return new src\models\PasswordReset($dulce->get('User'));
});
