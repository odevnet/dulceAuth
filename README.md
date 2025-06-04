![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/odevnet/dulceAuth)
![GitHub](https://img.shields.io/github/license/odevnet/dulceAuth)
![Static Badge](https://img.shields.io/badge/Powered_by_PHP-%236375F2?style=flat&logo=PHP&logoColor=%236375F2&logoSize=27px&labelColor=%23fff)
[![Static Badge](https://img.shields.io/badge/LEEME-ESPA%C3%91OL-RED?style=flat&labelColor=%23F5190B%20&color=%23F4DA36&link=https://github.com/odevnet/dulceAuth/blob/main/LEEME.md)](https://github.com/odevnet/dulceAuth/blob/main/LEEME.md)


# What is "dulceAuth"?
**DulceAuth** is a PHP user management library that simplifies user registration and authentication, as well as the management of roles and permissions.
It is designed for small to medium-sized applications that need a robust, efficient, and extensible solution.

Some features include:
- You can register one or multiple users at once.
- Controls access to different parts of the application by assigning roles and permissions.
- Integrates Laravel's "Eloquent ORM" to facilitate working with the database.
- Verifies user accounts by generating and validating tokens.
- Resets passwords.
- Supports easy email sending.
- Utilizes sessions and allows verification of user sessions.
- Captures and logs any errors that may occur.
- Easily extensible and customizable thanks to its modular architecture.
- Manages and facilitates dependency injection through the use of a "service container."


# Table of Contents
1. [Installation and Usage](#installation-and-usage)
    1. [Through GitHub](#github)
    2. [Through Composer](#composer)
    3. [Library Usage](#usage)
2. [Configuration](#configuration)
    1. [Database](#database)
    2. [Config.php](#config-file)
    3. [Database Configuration File](#database-configuration-file)
    4. [JSON Files](#json-files)
3. [Exceptions](#exception-handling)
4. [Logger Class](#logger-class)
5. [User Registration](#register-user)
    1. [Account Verification](#account-verification)
    2. [Account Verification. Part Two.](#account-verification-part-two)
    3. [An Exceptional Case](#an-exceptional-case-or-not)
6. [Constantes personalizadas](#custom_verification_email_url-y-custom_forgot_password_email_url)
7. [Login](#login)
8. [Users](#users)
    1. [Does it Exist?](#does-the-user-exist)
    2. [Edit User](#editing-users)
    3. [Delete User](#deleting-a-user)
    4. [Create Users](#creating-a-new-user)
    5. [Change Password](#changing-a-users-password)
    6. [Recover Password/Forgot Password](#password-recovery)
9. [Roles and Permissions](#roles-and-permissions)
    1. [Create a Role](#create-a-new-role)
    2. [Edit a Role](#edit-a-role)
    3. [Delete a Role](#delete-a-role)
    4. [Assign Roles to Users](#assign-roles-to-users)
    5. [Permissions](#permissions)
        1. [Create a Permission](#create-permission)
        2. [Edit a Permission](#edit-permission)
        3. [Delete a Permission](#delete-a-permission)
        4. [Assign a Permission to a Role](#assign-permissions-to-roles)
        5. [Remove a Permission from a Role](#remove-permission-from-a-role)
10. [Roles and Permissions. Part 2](#roles-and-permissions-v2)
11. [Authorization](#authorization)
12. [Sessions](#sessions)
    1. [Session Duration](#session-duration)
13. [Email Class](#dulcemail)
14. [Creating services](#create-a-service)

Claro, aquí está la traducción respetando el formato de markdown:

# Installation and Usage
The only requirements are:
- MySQL database as a minimum (not tested on others)
- PHP version >= 8.2.0

## Github
Open the command prompt (cmd) on Windows or the terminal of your operating system and navigate to the folder where you want to clone the repository:

For example, in the console, type:

`cd path/to/your/directory`

And then clone the repository by running in the console:

`git clone https://github.com/odevnet/dulceAuth.git`

Once cloned, with the console open and in the project directory, run:

`composer install` to automatically install the necessary dependencies.

## Composer
In a terminal and with composer installed, run the following command:

`composer require odevnet/dulceauth`

This will install the library and all necessary dependencies.

You then **have two options** to complete the installation.
1. Open the *composer.json* file for your project, i.e., where you downloaded the library, and you'll see something like this:
```json
{
    "require": {
        "odevnet/dulceauth": "dev-main"
    }
}
```
Well, you should leave it this way:
```json
{
    "require": {
        "odevnet/dulceauth": "dev-main"
    },
    "scripts": {
        "post-install-cmd": [
            "Install\\Install::createDirectoryStructure"
        ]
    }
}
```
Next, in the terminal or console, type: `composer dump-autoload` and press Enter.

Finally, run `composer install` and the installation file (Install.php) will automatically run, creating the files *config/config.php*, *config/config-db.php*, *config/verification_email.json*, *config/forgot_password_email.json*, and *logs/log_file.log*.

2. Skip step one and, once the library has downloaded, run the command:

`php vendor/odevnet/dulceauth/installer.php`

This command will create the folders and copy the necessary configuration files.
Just like step one, the structure should look like this:

your-project/

    -config/

        -config.php

        -config-db.php

        -verification_email.json

        -forgot_password_email.json

    -logs/

        -log_file.log

    -vendor/

        -odevnet/

        -dulceauth/

> **Important:** This second method is preferred, as it's faster and easier.

Don't worry, you can modify the routes later if you don't like them... ;-)

## Usage
Once you have *dulceAuth* downloaded and [configured](#configuration), simply include and instantiate the library to use it like this:
```php
require __DIR__ . '/vendor/autoload.php';

$config = __DIR__ . '/config/config.php'; // path where your configuration file is located
$databaseConfig = __DIR__ . '/config/config-db.php'; // path where your database configuration file is located

$dulceAuth = new src\DulceAuth([$config, $databaseConfig]);
```
And from here, you will be able to use each of the available methods provided by the library ;)

# Configuration

## Database
Use the following [table structure](db_tables.sql "Required Tables") for a MySQL database.
These are the minimum required tables and fields. If you already have a **users** table,
you will need to add the following fields: **name, email, password, created_at, updated_at, verified, and visibility**.
For example, if your table is named *"usuarios"* and you already have a field called *"username"* for
the user's name, you will need to rename that field to *name* and the table name to *users*. You should do the same for any other fields that differ.

## Config File
During installation, a **config.php** file has been created which in itself is quite descriptive about what each configuration option does and which ones we can modify and which ones we cannot.

For now, it's basic but functional.

Mainly, you only need to modify the constants *WEB_PAGE* and *EMAIL_FROM*.
We can leave the rest as is, however, if we want more customization, we can configure the paths
of the JSON or .log files if we consider it necessary.
```php
// Define the project base route
define('DULCE_AUTH_BASE_DIR', dirname(__DIR__)); // Return to the root of the project from src/config/

// Define common constants here
define('DULCE_AUTH_WEB_PAGE', 'yourwebsite.com'); // without http(s), without www and without ending in /
// some examples: define('WEB_PAGE', 'yourwebsite.com'); or define('WEB_PAGE', 'yourwebsite.com/myFolder');
define('DULCE_AUTH_EMAIL_FROM', 'admin@yourwebsite.com');

// Error log
define('DULCE_AUTH_LOG_FILE', BASE_DIR . '/logs/log_file.log');

// A little configuration about emails...
define('DULCE_AUTH_VERIFICATION_EMAIL_JSON_FILE', BASE_DIR . '/config/verification_email.json'); // json template for verification email. Edit the text as you like
define('DULCE_AUTH_VERIFICATION_PAGE_URL', 'verification.php'); // default file where the verification email data is captured

define('DULCE_AUTH_FORGOT_PASSWORD_EMAIL_JSON_FILE', BASE_DIR . '/config/forgot_password_email.json'); // json template for forgotten password email. Edit the text as you like
define('DULCE_AUTH_FORGOT_PASSWORD_PAGE_URL', 'forgot.php'); // default file where the email data (token and user id) is captured

// Roles. At the moment do not modify anything!!
define('DULCE_AUTH_DEFAULT_ROLE', 'User'); // default role
define('DULCE_AUTH_DEFAULT_VISIBILITY', 'public'); // default profile visibility

// Accounts
define('DULCE_AUTH_VERIFIED', '0'); // 0 = unverified account, requires email validation. 1 = verified
define('DULCE_AUTH_MAX_PASSWORD_CHANGES', 3); // password changes allowed per year

// Sessions
define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60); // session lifetime.
//For 1 day: define(DULCE_AUTH_SESSION_EXPIRATION', 60 * 60 * 24);
//For 2 days: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60 * 24 * 2);
//For 7 days: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60 * 24 * 7);
//For 1 hour: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60);
```
## Database Configuration File
During installation, a file called **config-db.php** was created which is used to configure the database data.
```php
<?php
# Database configuration
define('DULCE_AUTH_DRIVER', 'mysql');
define('DULCE_AUTH_HOST', 'localhost');
define('DULCE_AUTH_DATABASE', '');
define('DULCE_AUTH_USERNAME', '');
define('DULCE_AUTH_PASSWORD', '');
define('DULCE_AUTH_CHARSET', 'utf8mb4');
define('DULCE_AUTH_COLLATION', 'utf8mb4_unicode_ci');
define('DULCE_AUTH_PREFIX', '');
```

## JSON Files
The files *verification_email.json* and *forgot_password_email.json* are automatically created when installing the library.
By convention, they are created inside the */config* folder but we can modify their path in the **config.php** file if we want.
**verification_email.json** contains the following content:
```json
{
	"verification": {
		"type": "verification",
		"subject": "Validate your account",
		"message": "You have just registered at... Click the following link: {{verification_link}} to verify your account and log in.",
		"screen_message": "We have just sent you an email to confirm your account. Please check your inbox."
	}
}
```
And the **forgot_password_email.json** file contains:
```json
{
	"forgot": {
		"type": "forgot",
		"subject": "Password Reset",
		"message": "You are receiving this email because you have forgotten your password, and a token has been generated to reset it. \nClick the following link: {{verification_link}} to reset your password. \n\n Note: If you did not request this, please contact administration immediately as your account may be at risk.",
		"screen_message": "We have just sent you an email to reset your password. Please check your inbox."
	}
}
```
# Exception Handling
Exceptions are organized by type, meaning whether they are related to roles, tokens, or users.
For example, when registering a user, it might happen that a user with that email already exists. In this case,
the exception that would be thrown is *DuplicateEmailException*, located in *src\exceptions\users*.

Below is a list of all possible exceptions:

**Users:**

+ src\exceptions\users\AccountValidationException -> See [AccountValidationException](src/exceptions/users/AccountValidationException.php "AccountValidationException")

+ src\exceptions\users\ArrayOptionsUserException -> See [ArrayOptionsUserException](src/exceptions/users/ArrayOptionsUserException.php "ArrayOptionsUserException")

+ src\exceptions\users\CreateUserException -> See [CreateUserException](src/exceptions/users/CreateUserException.php "CreateUserException")

+ src\exceptions\users\DuplicateEmailException -> See [DuplicateEmailException](src/exceptions/users/DuplicateEmailException.php "DuplicateEmailException")

+ src\exceptions\users\EditUserException -> See [EditUserException](src/exceptions/users/EditUserException.php "EditUserException")

+ src\exceptions\users\InvalidPasswordException -> See [InvalidPasswordException](src/exceptions/users/InvalidPasswordException.php "InvalidPasswordException")

+ src\exceptions\users\LimitChangesPasswordException -> See [LimitChangesPasswordException](src/exceptions/users/LimitChangesPasswordException.php "LimitChangesPasswordException")

+ src\exceptions\users\RegisterException -> See [RegisterException](src/exceptions/users/RegisterException.php "RegisterException")

+ src\exceptions\users\UserException -> See [UserException](src/exceptions/users/UserException.php "UserException")

+ src\exceptions\users\UserNotFoundException -> See [UserNotFoundException](src/exceptions/users/UserNotFoundException.php "UserNotFoundException")

**Tokens:**
+ src\exceptions\tokens\RelationShipTokenException -> See [RelationShipTokenException](src/exceptions/tokens/RelationShipTokenException.php "RelationShipTokenException")

+ src\exceptions\tokens\TokenException -> See [TokenException](src/exceptions/tokens/TokenException.php "TokenException")

+ src\exceptions\tokens\TokenExpiredException -> See [TokenExpiredException](src/exceptions/tokens/TokenExpiredException.php "TokenExpiredException")

+ src\exceptions\tokens\TokenNotFoundException -> See [TokenNotFoundException](src/exceptions/tokens/TokenNotFoundException.php "TokenNotFoundException")

+ src\exceptions\tokens\TokenSaveException -> See [TokenSaveException](src/exceptions/tokens/TokenSaveException.php "TokenSaveException")

**Roles and Permissions:**
+ src\exceptions\roles\EmptyPermissionNameException -> See [EmptyPermissionNameException](src/exceptions/roles/EmptyPermissionNameException.php "EmptyPermissionNameException")

+ src\exceptions\roles\EmptyRoleNameException -> See [EmptyRoleNameException](src/exceptions/roles/EmptyRoleNameException.php "EmptyRoleNameException")

+ src\exceptions\roles\MissingRoleOrPermissionException -> See [MissingRoleOrPermissionException](src/exceptions/roles/MissingRoleOrPermissionException.php "MissingRoleOrPermissionException")

+ src\exceptions\roles\PermissionNotFoundException -> See [PermissionNotFoundException](src/exceptions/roles/PermissionNotFoundException.php "PermissionNotFoundException")

+ src\exceptions\roles\PermissionSaveException -> See [PermissionSaveException](src/exceptions/roles/PermissionSaveException.php "PermissionSaveException")

+ src\exceptions\roles\RoleAssignmentException -> See [RoleAssignmentException](src/exceptions/roles/RoleAssignmentException.php "RoleAssignmentException")

+ src\exceptions\roles\RoleNotAssignedException -> See [RoleNotAssignedException](src/exceptions/roles/RoleNotAssignedException.php "RoleNotAssignedException")

+ src\exceptions\roles\RoleNotFoundException -> See [RoleNotFoundException](src/exceptions/roles/RoleNotFoundException.php "RoleNotFoundException")

+ src\exceptions\roles\RoleNotSelectedException -> See [RoleNotSelectedException](src/exceptions/roles/RoleNotSelectedException.php "RoleNotSelectedException")

+ src\exceptions\roles\RolePermissionAlreadyExistsException -> See [RolePermissionAlreadyExistsException](src/exceptions/roles/RolePermissionAlreadyExistsException.php "RolePermissionAlreadyExistsException")

+ src\exceptions\roles\RolePermissionException -> See [RolePermissionException](src/exceptions/roles/RolePermissionException.php "RolePermissionException")

+ src\exceptions\roles\RoleSaveException -> See [RoleSaveException](src/exceptions/roles/RoleSaveException.php "RoleSaveException")

+ src\exceptions\roles\RolesException -> See [RolesException](src/exceptions/roles/RolesException.php "RolesException")

+ src\exceptions\roles\UsedPermissionNameException -> See [UsedPermissionNameException](src/exceptions/roles/UsedPermissionNameException.php "UsedPermissionNameException")

+ src\exceptions\roles\UsedRoleNameException -> See [UsedRoleNameException](src/exceptions/roles/UsedRoleNameException.php "UsedRoleNameException")


As we explain the code, we will see when and how exceptions are used.
In some cases, capturing exceptions will be necessary, while in others it will be optional.
When registering a user, you should capture any exceptions that might occur; however,
when a user "logs in," it is not necessary. In this latter case, it may be more advisable to display a more
personalized message since the login method will return "true" or "false."

Keep in mind that each method can throw its own exceptions, but each method also has a general exception. For example, when creating a new role, you have the option to capture various exceptions that might occur, such as the role already being in use (UsedRoleNameException) or being empty (EmptyRoleNameException), etc. In this case, you can catch each of these specific exceptions individually, or you can "ignore" them and catch the general exception, which in this case would be *RolesException*.

For tokens and users, there is also a general exception for each case.

# Logger Class
The Logger class is a simple class that allows you to log errors to a file to keep track of all errors or exceptions that have occurred.
Every time we include a try-catch block, along with the exception, we should also include the Logger class:

```php
try {
    // ... code
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
}
```
This causes the exception to be recorded in the file. By default, this file is located at **logs/log_file.log** and can be modified through the *LOG_FILE* constant in the config file.

# Register User
```register(string $name, string $email, string $password, array $options = [])```

By default, and as a minimum, this method requires three fields: **user, email, and password**.

For example:
```php
try {
    $dulceAuth->register('Test', 'test@demo.com', '1234');
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}

```
However, it can also accept a fourth parameter in the form of an associative array.
The fourth parameter can be useful if you want to store additional data when registering a new user, such as country, address, phone number, etc. This is usually quite common.

You can do something like this:
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['country' => 'España']);
```
This assumes that you have a field in the **users** table called **country**.

You might also want to modify the value of *"verified"*. For example, if you want the newly created account to be created as verified, you should do it like this:
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['verified' => 1]);
```
Or make the account visibility private:
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['verified' => 1, 'visibility' => 'private', 'country' => 'España']);
```

> **Note:** Each field you pass as the fourth additional parameter must exist in the 'users' table.

As you can see, the fourth parameter is quite useful if you want to pass or register several additional data points.
Finally, regarding "registration," we have the option to register multiple users at once.

To do this, you could use a for loop like this:
```php
require __DIR__ . '/vendor/autoload.php';

use src\Logger;

$config = __DIR__ . '/config/config.php';
$databaseConfig = __DIR__ . '/config/config-db.php';

$dulceAuth = new src\DulceAuth([$config, $databaseConfig]);

$count = 3;

for ($i = 1; $i <= $count; $i++) {
    try {
        $register = $dulceAuth->register("Test$i", "test$i@demo.com", "1234", ["verified" => 1]);
        echo "User test$i registered successfully.\n";
    } catch (Exception $ex) {
        echo "Error registering the user {$i}: {$ex->getMessage()}\n";
        Logger::error($ex->getMessage(), $ex->getTraceAsString());
    }
}
```
## Account Verification
We have seen that we have the option to create accounts as verified or unverified.
Using the *VERIFIED* constant found in the config.php file, we can set whether all accounts should be created as verified or not upon registration.
```php
define('VERIFIED', '0'); // 0 = unverified account, requires email validation. 1 = verified
```
But we can also do it at the time of registering an account, as mentioned above, by passing a fourth parameter to the *register* method.
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['verified' => 1]);
```
> **Reminder:**
>
> **1** means **verified account**.
>
> **0** means account pending verification. Email validation is required.

In the case where you want all accounts to require email validation, you will need to generate a random token for each new registration and send it to the newly registered user's email.
Don’t worry, the method will handle this for us. However, the `register()` method will only send an email if the account requires verification; otherwise, it will not send anything. What I mean is, it could be useful to send another type of email to the user immediately after registration to thank them or inform them of their details, etc. But that’s better left for future versions.

> **Note:**
>
> If you pass the 'verified' option through the `register()` method, the 'VERIFIED' constant in the config will not be considered.

## Account Verification. Part Two.
How do we verify an account?
It’s simple. Once the user has registered, the `register` method sends an email to the user with a previously generated token. We need to validate it, and if everything is correct, verify the account.

The email the user will receive will contain a link similar to this:
```yourwebsite.com/verification.php?token=TOKENGENERADO&userId=IDUSUARIO``` as long as the constant **CUSTOM_VERIFICATION_EMAIL_URL**
is not defined.

So, in the part of your application, or in other words, on the page (verification.php) where you want to capture the data, i.e., the token and the user ID, you can use GET like this:
```php
$token = $_GET['token'];
$userId = $_GET['userId'];
```
And validate them using two methods:

**validateTokenAccount:** ```validateTokenAccount(string $token, int $userId)```

**verified:** ```verified(int $userId)```

For example:
```php
if ($dulceAuth->validateTokenAccount($token, $userId)) {
    // If the token is validated successfully, we verify the user's account by setting
    // the "verified" field value to 1
    $dulceAuth->verified($userId);
}
```
A more detailed example:
```php
try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenAccount($token, $userId)) {
            echo 'Account verified';
            $dulceAuth->verified($userId);
        }
    } else {
        echo 'The token or userID is empty';
    }
} catch (RelationShipTokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenExpiredException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Or with a general exception:
```php
try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenAccount($token, $userId)) {
            echo 'Account verified';
            $dulceAuth->verified($userId);
        }
    } else {
        echo 'The token or userID is empty';
    }
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```

> **Note:**
> Both the email template sent to the user (i.e., the text sent to them) and the page where the token and user ID are received can be modified in the *config* file through the **VERIFICATION_EMAIL_JSON_FILE** and **VERIFICATION_PAGE_URL** constants, respectively.

## An Exceptional Case. Or Not!
It might happen that we require all users to verify their account upon registration, but what if an account remains pending verification for a long time? At the time of registration, a token would have been generated, but after X time, it would likely have expired. In this case, you should follow these steps:

Imagine a user has registered, an email was sent to verify the account, but for some reason, the user does not click the link until several days later. As expected, the token will have expired, and the link sent to their email will no longer work. In other words, a new link or, more precisely, a new token needs to be generated.
To do this, use the following method: ``generateVerificationToken(string $email, bool $send = true)``

As seen, the second parameter *$send* is optional, and we can decide whether to pass it or not.
If we execute the method without passing the second parameter, the method itself will send an email to the user so they can verify/validate their account.
```php
try {
    $verification = $dulceAuth->generateVerificationToken('test@demo.com');
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RuntimeException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
If we execute the code above, we don’t need to do anything else. The user will receive an email with a link like:

```yourwebsite.com/verification.php?token=GENERATEDTOKEN&userId=USERID``` (or other if **CUSTOM_VERIFICATION_EMAIL_URL** is defined) and the procedure to follow will be exactly the same as described earlier in the section ["Account Verification. Part Two."](#account-verification-part-two), using the *verification.php* file and capturing the necessary data, i.e., the token and user ID.


Now, if we call the `generateVerificationToken` method with **$send** set to *false*, it will return an array containing the token and user ID.
```php
$verification = $dulceAuth->generateVerificationToken('test@demo.com', false); // return ['userId' => $userId, 'token' => $token];
```
By doing it this way, we can generate the email ourselves using the **dulceMail** class [Read about this class](#dulcemail "dulceMail Class").

For example:
```php
try {
    $verification = $dulceAuth->generateVerificationToken('test@demo.com', false);

    if ($verification) {

        $mail = $dulceAuth->dulceMail();
        $mail->from('admin@yourwebsite.com')
            ->to('test@demo.com')
            ->subject('Validate your account')
            ->message("Click on the following link:
            yourwebsite.com/verification.php?token=" . $verification['token'] . "&userId=" . $verification['userId'] . "
            to validate your account and be able to login.");
        $mail->send();

        if ($mail->send()) {
            echo "We have just sent you an email to confirm your account.
        Please check your email.";
        }
    }
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RuntimeException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
After sending the email ourselves, we carry out the entire process: send the email, then capture the data (token and user ID), and use the `validateTokenAccount` and `verified` functions as before...

# CUSTOM_VERIFICATION_EMAIL_URL y CUSTOM_FORGOT_PASSWORD_EMAIL_URL
By default, when a user registers and the verification email or password recovery email is sent,
the DulceMail class is used with a URL format of the defined type:```tuweb.com/verification.php?token=GENERATEDTOKEN&userId=IDUSER```

Well, if we want to modify this, and we want the URL to have another format, whether for the reason of using a framework or any other, we must create a constant with an anonymous function as follows:
```php
define('CUSTOM_VERIFICATION_EMAIL_URL', function (string $token, int $userId): string {
    return "https://yourwebsite.com/validate/{$token}/user/{$userId}";
});
```
Obviously we customize the URL to our liking.
Now, when a verification email is sent, the URL that the user will receive in their email will be:
```https://yourwebsite.com/validate/{$token}/user/{$userId}```

Obviously, that URL should point to a page where we capture the token and user ID.

The same goes for the forgotten password. So if we want to customize the "forgotten password" email, we will need to
create a constant like the following:
```php
define('CUSTOM_FORGOT_PASSWORD_EMAIL_URL', function (string $token, int $userId): string {
    return "https://yourwebsite.com/forgot-password/{$token}/user/{$userId}";
});
```

# Login
`$dulceAuth->login($email, $password);`

This method connects a user to the system and creates their corresponding session.

Therefore, to log in a user, you would simply do:
```php
$dulceAuth->login('test@demo.com', '1234');
```
If we want to check if the user is connected or if there is an active connection:
```php
$dulceAuth->isLoggedIn();
```
Clearer:
```php
if ($dulceAuth->isLoggedIn()) {
        echo "You are logged in!";
    }
```
To logout, close, or end the session:
```php
$dulceAuth->logout();
```
Once logged in, it may be useful to retrieve user data. To do this, we use the `currentUser()` function followed by the fields we want to display.

For example:
```php
$dulceAuth->currentUser()->name;
```
If we have a field for the country called "country," we can do:
```php
$dulceAuth->currentUser()->country;
```
And similarly for each field we want to display for the currently logged-in user.

# Users
There are several options for displaying a list of all users in the database.
For example, suppose we want to display a list of existing users but are only interested in showing their ID, name, email, and country. To do this, we can use the `$dulceAuth->showUsers()` method and iterate through it with a foreach loop:
```php
foreach ($dulceAuth->showUsers() as $user) {
        echo 'ID: ' . $user->id . '<br>';
        echo 'Name: ' . $user->name . '<br>';
        echo 'Email: ' . $user->email . '<br>';
        echo 'Country: ' . $user->country . '<br>';
    }
```
In addition to the powerful `$dulceAuth->showUsers()` method, which we can use to iterate through any user-related fields we want to display, there are also the following three methods:
```php
$dulceAuth->showUsersById();
```
```php
$dulceAuth->showUsersByName();
```
```php
$dulceAuth->showUsersByEmail();
```
I think their names are quite descriptive for an explanation of what each one does ;-).
We might think they are not useful, but who knows, if we ever need them, they are there!

## Does the user exist? ##
If we want to check if a user exists, we have two ways to do it.

The first is through the **userIdExists** method:

```userIdExists(int $userId)```
```php
$dulceAuth->userIdExists(5); // check if a user with ID 5 exists
```
This method will return **true** or **false** depending on whether the user ID we provided exists or not.

Another way to search for or determine if a user exists is by their email:
```php
$dulceAuth->userEmailExists('test@demo.com');
```
It will return **true** if the email exists, and **false** otherwise.

## Editing Users ##
To edit a user, we use the `*editUser*` method, which accepts two parameters: one is the user ID to be edited, and the other is an array of options with the new values.

```editUser(int $userId, array $options)```

Example:
```php
try {
    $dulceAuth->editUser(1, [
        'name' => 'Test',
        'email' => 'test@demo.com',
        'country' => 'Spain'
    ]);
} catch (EditUserException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (ArrayOptionsUserException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
In the previous example, we edited the name, email, and country fields for the user with ID 1.
The return value will be **true** or **false**.

By the way, going back to the topic of exceptions, the previous code could be simplified to:
```php
try {
    $dulceAuth->editUser(1, [
        'name' => 'Test',
        'email' => 'test@demo.com',
        'country' => 'Spain'
    ]);
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
But I recommend specifying each exception ;-)


## Deleting a User ##
To delete a user, simply execute the `"deleteUser"` method, passing the user ID to be deleted.

```deleteUser(int $userId)```
```php
try {
    $dulceAuth->deleteUser(1);
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
It will return **true** or **false** upon success.

## Creating a New User ##
It may be useful to create a new user without needing to use the `"register"` method.
For example, if you have an admin panel and want the option to create users, you can use the `"createUser"` method.
This method requires at least three parameters: name, email, and password. There is also an optional fourth parameter, which is an array that allows you to pass additional data to be registered, such as their phone number, country, etc.

```createUser(string $name, string $email, string $password, array $options = [])```

Here’s an example using the optional fourth parameter. If we want the new account to be created as verified and with a phone number, we would do it like this:
```php
try {
    $dulceAuth->createUser('Test', 'test@demo.com', '1234', [
        'verified' => 1,
        'phone' => '6XXXXXXXX'
    ]);
} catch (DuplicateEmailException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (CreateUserException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```

## Changing a User's Password ##
To change a user's password, you need to execute the `"changePassword"` method with three parameters:

`changePassword(int $userId, string $currentPassword, string $newPassword)`

For example, to change the password of the user with ID '1':
```php
try {
    $dulceAuth->changePassword(1, '1234', '1234da#');
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
**dulceAuth** allows you to set a limit on password changes, which by default is **3 changes per year**. You can change this through the **MAX_PASSWORD_CHANGES** constant in the **config.php** file.
I think this can be useful to prevent abusive and unnecessary use of the `changePassword` method.

It might also be helpful to know the total number of password changes made (if any) by a user.
To do this:
```php
try {
    echo $dulceAuth->latestChange(1)->changes_count;
} catch (LimitChangesPasswordException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
The `"latestChange"` method accepts one parameter, which is the user ID to query, and returns an instance of `PasswordChange` if a password change is found. You can then use the **changes_count** property on it to find out the total number of changes.

## Password Recovery ##
If a user has forgotten their password, since the password uses secure encryption, it is impossible to recover it, so a new one must be created. For this, **dulceAuth** will generate a *temporary token* so the user can create a new password.

Using the **forgotPassword** method:

``forgotPassword($email, $send = true)``

A token is generated for the user whose email has been provided as a parameter.
The `$send` parameter is optional, and you can decide whether to include it or not. Whether you include it or not affects how the method behaves.
Basically, by default, this method **sends an email to the user** with a link containing the token and their user ID, which will later be used to create a new password. [VIEW CUSTOM CONSTANTS](#custom_verification_email_url-y-custom_forgot_password_email_url)

This should sound familiar, as it works similarly to the `generateVerificationToken` method.

The common way to use it is:
```php
try {
    $forgotPassword = $dulceAuth->forgotPassword('test@demo.com');
} catch (TokenSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RuntimeException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
If we call the method with **$send** set to *false*, it will return an array containing the token and the user ID.
```php
$forgotPassword = $dulceAuth->forgotPassword('test@demo.com', false); // return ['userId' => $userId, 'token' => $token];
```
By doing it this way, we can generate the email sent to the user ourselves using the **dulceMail** class.

Let’s look at some examples.

Suppose the user named "Test" with the email "test@demo.com" has forgotten their password.
In this case, we will call the `forgotPassword` method **only** by passing the user's email, so that **the method itself handles sending the email** with the recovery link containing the token and user ID:
```php
$forgotPassword = $dulceAuth->forgotPassword('test@demo.com');
```
If we check the email and click on the link, it will take us to the page set as the "password recovery" page.
The default page for password recovery is called *forgot.php* and is set with the constant *FORGOT_PASSWORD_PAGE_URL* in the **config.php** file.

On this page, we retrieve the *token* and *userId* values using **$_GET** and pass them to the **validateTokenPassword** method:

`validateTokenPassword(string $token, int $userId): bool`

For example:
```php
$token = $_GET['token'];
$userId = $_GET['userId'];

if ($dulceAuth->validateTokenPassword($token, $userId)) {
    // If it has been validated successfully, here you can display a form to enter the new password...
}
```
Finally, if the validation is correct, we create the new password using the **insertNewPassword** method:

`insertNewPassword(string $password, int $userId): void`

This method takes the new password and the user ID as parameters.

A more complete example might be the **forgot.php** page:
```php
try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenPassword($token, $userId)) {
            $dulceAuth->insertNewPassword('new password', $userId);
            echo 'Password changed successfully';
        }
    } else {
        echo 'The token or userID is empty';
    }
} catch (RelationShipTokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenExpiredException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
We have seen the recommended and straightforward way to do this. Now, let’s move on to the second method, which essentially involves sending/generating the recovery email ourselves using the **dulceMail** class.
To do this, call the **forgotPassword** method, passing the user's email and setting the second parameter to *false*.
In this way, it will only return the user ID and the temporary token that has been generated. The next step will be to generate the email ourselves to send to the user:
```php
try {
    $forgotPassword = $dulceAuth->forgotPassword('test@demo.com', false); // return ['userId' => $userId, 'token' => $token];
    // We check that `$forgotPassword` returns the userId and the token.
    if ($forgotPassword) {
        $mail = $dulceAuth->dulceMail();
        $mail->from('admin@yourwebsite.com')
            ->to('test@demo.com')
            ->subject('Password Regeneration')
            ->message("You received this email because you forgot your password and a token has been generated to reset it.
        Click on the following link: yourwebsite.com/forgot.php?token=" . $forgotPassword['token'] . "&userId=" . $forgotPassword['userId'] . " \n
        If it wasn't you, please contact administration urgently as your account may be at risk.");
        $mail->send();
        // If the sending is successful, we can display a message in the browser.
        if ($mail->send()) {
            echo "We have just sent you an email.
        Please check your email to create a new password that you will remember.";
        }
    }
} catch (TokenSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RuntimeException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
That’s it! From here on, the process is the same as before. After clicking on the link from the password recovery page, we retrieve the *token* and *userId* values and pass them to the **validateTokenPassword** method, and then call the **insertNewPassword** method.

>**Very Important:**
>
> The **insertNewPassword** method must be called after generating a token; otherwise, it will throw an exception.
>
> In other words, calling the method directly without generating a token first will not change the password.

The recommendation is to follow the example order: first, generate a token for the email (using **forgotPassword**), then validate it with **validateTokenPassword**, and finally insert/register the new password using **insertNewPassword**.

# Roles and Permissions
## Create a New Role ##
If we want to create a new role, simply call the **createRole** function, passing a valid name as a parameter.
For example:
```php
try {
    $dulceAuth->createRole('new_role_name', 'description');
} catch (EmptyRoleNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RoleSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UsedRoleNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Or:
```php
try {
    $dulceAuth->createRole('new_role_name', 'description');
} catch (RolesException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
> **Note:** The **description** is optional.

## Edit a Role ##
To edit a role, use the method: `editRole(int $roleId, string $name, ?string $description = null)`:
where the first parameter is the role ID and the second is the new name we want to assign.
Again, the description is optional.
```php
try {
    $dulceAuth->editRole(10, 'new_name', 'description');
} catch (RoleNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (EmptyRoleNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UsedRoleNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RoleSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Or also:
```php
try {
    $dulceAuth->editRole(10, 'new_name', 'description');
} catch (RolesException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
But actually, it's better not to ;P

## Delete a Role ##
To delete a role, as expected, call the **deleteRole** method, passing the ID of the role to be deleted:
```php
try {
    $eliminarRol = $dulceAuth->deleteRole(8);
    if ($eliminarRol) {
        echo 'Role successfully deleted...';
    }
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
## Assigning Roles to Users ##
Now it may be useful to assign one or more roles to a user if we already have our list of roles created.
To do this, we call the **assignRoleToUser** method with two parameters: `assignRoleToUser(int $userId, array $roles): bool`

One parameter is the user ID, and the other is the ID(s) of the roles we want to assign.

Suppose we want to assign three roles to the user *Jhon*, whose ID is *27*, and we want to assign the roles: 'editor', 'user', and 'moderator', corresponding to IDs 4, 5, and 7 respectively.

To do this, we should do the following:
```php
try {
    $asignarRol = $dulceAuth->assignRoleToUser(27, [4, 5, 7]);
    if ($asignarRol) {
        echo 'Role(s) successfully assigned.';
    }
} catch (RoleNotSelectedException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RoleAssignmentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
It's worth noting here that if you assign a role that the user already has, nothing will happen; the rest of the roles will be assigned as usual.

Now that we've assigned roles to a user, we can also do the opposite **remove** roles from a user. For this, we have the method **removeRoleFromUser**: `removeRoleFromUser(int $userId, array $roles): bool`

This method accepts the same parameters as the previous one: the first is the user ID, and the second is an array containing the role IDs. It must receive at least one role to remove.

For example:
```php
try {
    $eliminarRol = $dulceAuth->removeRoleToUser(27, [2, 7]);
    if ($eliminarRol) {
        echo 'Role(s) successfully removed from the user.';
    }
} catch (RoleNotSelectedException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RoleNotAssignedException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```

## Permissions ##
Permissions are related to roles and vice versa. A user will have certain permissions depending on the role they have, so it is advisable to create a list of permissions. The number of roles and permissions we want to create will depend on the type of application and authorization we want to implement.

## Create Permission ##
To start, if we want to create a new permission, we need to use the method **createPermission**: `createPermission(string $name, ?string $description = null): bool`

This method takes two parameters (name and description). The description is optional but useful.

```php
try {
    $newPermission = $dulceAuth->createPermission('Post news', 'Allows you to publish news on the site');
    if ($newPermission) {
        echo 'Permission created successfully!';
    }
} catch (EmptyPermissionNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UsedPermissionNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (PermissionSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
## Edit Permission ##
If we want to edit the name of an already created permission, we need to call the method **editPermission**: `editPermission(int $permissionId, string $newName, ?string $description = null): bool`
This method takes two parameters: an integer and a string. The integer represents the ID of the permission, and the string represents the new name we want to assign to it.
Optionally and once again, the description is optional but advisable.

For example:
```php
try {
    $editPermission = $dulceAuth->editPermission(13, 'Post article');
    if ($editPermission) {
        echo 'Permission edited successfully!';
    }
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (EmptyPermissionNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (UsedPermissionNameException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (PermissionSaveException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (PermissionNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}

```
Remember, you can "ignore" the specific exceptions and catch the general one.
```php
try {
    $editPermission = $dulceAuth->editPermission(13, 'Post article');
    if ($editPermission) {
        echo 'Permission edited successfully!';
    }
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
But once again and personally, I do not recommend it ;-)

## Delete a permission ##
To delete a permission, simply run the following method: `deletePermission(int $permissionId): bool`
```php
try {
    $removePermission = $dulceAuth->deletePermission(13);
    if ($removePermission) {
        echo 'Permission removed successfully!';
    }
} catch (PermissionNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
## Assigning Permissions to Roles ##
As I mentioned earlier, a permission by itself does nothing; it is not useful unless it has an associated role. Therefore, it needs to be linked to a role. To do this, you should call the method **assignPermissionToRole** with two parameters: the role's ID and the permission's ID: `assignPermissionToRole(int $roleId, int $permissionId)`

```php
try {
    $assignPermissionToRole = $dulceAuth->assignPermissionToRole(4, 14);
    if ($assignPermissionToRole) {
        echo 'Permission successfully assigned to role';
    }
} catch (MissingRoleOrPermissionException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RoleNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (PermissionNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RolePermissionAlreadyExistsException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
## Remove Permission from a Role ##
If we want to remove a permission from a role, we need to execute the method **removePermissionFromRole**: `removePermissionFromRole(int $roleId, int $permissionId)`

Where, once again, the first parameter is the role ID and the second parameter is the permission ID.

For example:
```php
try {
    $removeRolePermission = $dulceAuth->removePermissionFromRole(4, 14);
    if ($removeRolePermission) {
        echo 'Permission successfully REMOVED from the role';
    }
} catch (MissingRoleOrPermissionException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (RoleNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (PermissionNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
# Roles and Permissions V.2 #
Earlier, we saw how to create roles and permissions and how to assign them to users. Now, there are several methods that allow us to view all available roles and permissions, as well as list all the roles a user has.

It’s important to mention that listing all the permissions of a user is not possible because permissions are **tied** to roles, not to the user. In other words, **a user will have a permission or not depending on the role they have.**

With this clarified, let's look at the methods mentioned earlier.

To find out all available roles, we call the method **showRoles()** and loop through it with a foreach:
```php
$roles = $dulceAuth->showRoles();

foreach ($roles as $role) {
    echo "ROLE ID: $role->id | NAME: $role->name <br>";
}
```
There are also two additional methods: **showRolesById()** and **showRolesByName()**, which allow you to list only the IDs or names of the roles "more quickly." However, the main method (**showRoles()**) should generally suffice.

The same applies to permissions. We have the **showPermissions()** method to list all available permissions:
```php
$permissions = $duleAuth->showPermissions();

foreach ($permissions as $permission) {
    echo "PERMISSION ID: $permission->id | PERMISSION NAME: $permission->name <br>";
}
```
And just like before, we have the methods **showPermissionsById()** and **showPermissionsByName()** to display permissions by their ID or name, respectively.

All these methods are actually useful for scenarios like an admin panel, where it's necessary to see a list of all created roles and permissions.

There’s another interesting method if you want to know what roles a specific user has. For that, we have the following method:

``userRoles($userId)``

This method accepts a single parameter, which is the ID of the user you want to check.

For example, if you want to check what roles the user with ID "2" has, you would do the following:

```php
try {
    $roles = $dulceAuth->userRoles(2);
    echo 'The user with ID 2 has the following roles: <br>';
    foreach ($roles as $role) {
        echo "ROLE: $role->name <br>";
    }
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
And, as with the previous examples, there are also two methods available to display roles by their ID or name:

`userRolesById($userId)` y `userRolesByName($userId)`

We may never use them, but for now, there they are!

# Authorization #
We have created users, roles, and permissions... Now, we are interested in implementing authorization in our application. For example, we might want to restrict access to the administration area only to administrators, or verify that a user has the necessary role before performing a specific action.

To achieve this, we have two methods to validate if a user has the required role or permission. The first one is:

 `hasRole(string $roleName, ?int $userId = null): bool`


This method is used to check if the current user, meaning the one who is logged in, has a specific role. You just need to pass the role name like this:
```php
if ($dulceAuth->hasRole('Admin')) {
    echo 'You are an administrator.';
    // Here you can add an administration area or anything else...
} else {
    echo 'You do not have the required role to view this page.';
}
// or something more complete and detailed:
$dulceAuth->login('test@demo.com', '12345');

if ($dulceAuth->isLoggedIn()) {
    echo "You are connected!";
    if ($dulceAuth->hasRole('Admin')) {
        echo 'You are an administrator.';
        // Here you can add an administration area or anything else...
    } else {
        echo 'You do not have the required role to view this page';
    }
}
```
As we have seen, the **hasRole** method can also accept a second optional parameter, which is the user’s identity. This is useful if we want to check if a specific user has a particular role.

For example:
```php
if ($dulceAuth->hasRole('SuperAdmin', 1)) {
    echo 'The user has the role!';
} else {
    echo 'The user does not have the role! :-(';
}
```
In the previous example, instead of checking if the current user is "*SuperAdmin*", we checked if the user with identity 1 is.

For permissions, there is also a similar function, although, for now, it only checks the permission of the current user, that is, the one logged into the application.

For example, assuming we have a permission to create users, we can check if the current user has that permission.

To do this, we execute the **hasPermission** method, passing the name of the permission as a parameter.
```php
if ($dulceAuth->hasPermission('Create user')) {
    echo 'You have the necessary permission to create users.';
} else {
    echo 'You do not have permission to perform this action.';
}
```

# Sessions #
There's not much to say about sessions. There is simply a straightforward class for creating and retrieving session variables. Its usage is quite simple, with two main methods:

`set(string $key, $value)` to create a session.

`get(string $key)` to get the session.
```php
$dulceAuth->session()->set('color', 'red');
```
```php
echo $dulceAuth->session()->get('color');
```

We have some more methods which are:

``has($key)``: To check if a key exists in the session.
```php
$dulceAuth->session()->has('color'); // returns true or false

if ($dulceAuth->session()->has('color')) {
    echo 'It exists!';
} else {
    echo 'It does not exist!';
}
```

``remove($key)``: To remove a key-value pair from the session.
```php
$dulceAuth->session()->remove('name');
```

``destroy()``: Completely destroys the session.
```php
$dulceAuth->session()->destroy(); // destroys the entire session.
```

It is important to mention that when a user is logged in, two sessions are automatically created. One is for the user's identity, called *"userId"*, and the other is for the duration of the active session, named *"expire_time"*.

To retrieve these sessions, for example, the ID of the logged-in user, we need to use the `get()` method of the **Session** class like this:
```php
echo $dulceAuth->session()->get('userId');
```
## Session Duration ##
As mentioned earlier, when a user logs in, two sessions are created: one that contains the user ID, and another that holds the session's duration/expiry time.

Currently, the default session duration is set to one hour, but this can be changed using the constant **SESSION_EXPIRATION** found in the **config.php** file.

To determine the exact duration of the session, you can check the value of *"expire_time"*, which can be done like this:
```php
$dulceAuth->session()->get('expire_time');
```
However, if you do the above, you'll get the date in *timestamp* format, which is somewhat difficult to read. So, there's a better method called **expirationTime()** that allows you to check this data in a more readable format.

```php
echo $dulceAuth->session()->expirationTime(); // displays time in format: Y-m-d H:i:s
```
It can also be useful, before accessing a certain part of our application, to check not only if the user is logged in (remember, the **isLoggedIn()** method is for that) but also if there is an **active and valid session.** To do this, you can use the **isValid()** method of the Session class like this:
```php
if ($dulceAuth->session()->isValid()) {
        echo 'The session is active';
        // We can do anything here...
    } else {
        echo 'The session has expired';
    }
```

# dulceMail
This is a super simple class that essentially uses PHP's native `mail()` function. It is mainly for sending and receiving emails in the simplest way possible. Please keep that in mind. If you need something more secure, consider exploring alternatives like "PHPMailer" and integrating it with dulceAuth.

To use this class its use is simple:
```php
$mail = $dulceAuth->dulceMail();
// We prepare the email: sender, recipient, subject and message
$mail->from('admin@yourwebsite.com')->to('test@demo.com')->subject('Subject/topic')->message('Any message...');
// and we send it
$mail->send();
```
But as always, a more complete example including exceptions would be as follows:
```php
try {
    $mail = $dulceAuth->dulceMail();

    $mail->from('admin@yourwebsite.com')->to('test@demo.com')->subject('Subject/topic')->message('Any message...');

    $send = $mail->send();
    // If the sending is successful, we can display a message in the browser.
    if ($send) {
        echo 'We just sent you an email.';
    }
} catch (RuntimeException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
# Create a service
Through bootstrap, all the services that dulceAuth needs to work are created/started, however,
sometimes, we may need other additional services (classes). Well, this is possible and easy to do.

We just need to *run* the dulceAuth service container like this:
```php
$dulceAuth->dulce->addService('Name of the service', function ($dulce) {
    return new espacioDeNombres\Servicio();
});
```
For example:
```php
$dulceAuth->dulce->addService('Forms', function ($dulce) {
    return new helpers\Form();
});

echo $dulceAuth->dulce->get('Forms');
```
The above example would add a new class to dulceAuth which would not come by default.
I think it's pretty easy to understand ;-)
