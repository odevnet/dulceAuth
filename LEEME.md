![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/odevnet/dulceAuth)
![GitHub](https://img.shields.io/github/license/odevnet/dulceAuth)
![Static Badge](https://img.shields.io/badge/Powered_by_PHP-%236375F2?style=flat&logo=PHP&logoColor=%236375F2&logoSize=27px&labelColor=%23fff)

# ¿Qué es "dulceAuth"?
**DulceAuth** es una biblioteca PHP de gestión de usuarios que facilita el registro y autenticación de usuarios, así como la gestión de sus roles y permisos.
Está pensada para pequeñas y medianas aplicaciones que necesiten una solución robusta, eficaz y extensible.

Algunas caracteristicas:
- Puedes registrar uno o varios usuarios a la vez.
- Controla el acceso a diferentes partes de la aplicación mediante la asignación de roles y permisos.
- Integra el "ORM Eloquent" de Laravel para facilitar el trabajo/interacción con la base de datos.
- Verifica cuentas de usuario generando tokens y validandolos.
- Resablece contraseñas.
- Soporta envio de correos electrónicos de forma sencilla.
- Hace uso de sesiones y permite verificar las sesiones de usuario.
- Permite capturar y registrar cualquier error que pueda ocurrir.
- Fácilmente extensible y personalizable gracias a la arquitectura modular.
- Maneja y facilita la inyección de dependencias gracias al uso de un "contenedor de servicios".

# Índice de contenidos
1. [Instalación y uso](#instalación-y-uso)
    1. [Clonar desde github](#github)
    2. [A través de composer](#composer)
    3. [Uso de la libreria](#uso)
2. [Configuración](#configuración)
    1. [Base de datos (Tablas)](#base-de-datos)
    2. [Config.php](#archivo-config)
    3. [Archivo base de datos](#archivo-configuración-de-la-base-de-datos)
    4. [Archivos JSON](#archivos-json)
3. [Excepciones](#uso-de-excepciones)
4. [Clase Logger](#clase-logger)
5. [Registrar usuario](#registrar-usuario)
    1. [Verificación de cuentas](#verificación-de-cuentas)
    2. [Verificación de cuentas. Segunda parte.](#verificación-de-cuentas-segunda-parte)
    3. [Un caso excepcional](#un-caso-excepcional-o-no)
6. [Constantes personalizadas](#custom_verification_email_url-y-custom_forgot_password_email_url)
7. [Login](#login)
8. [Usuarios](#users)
    1. [¿Existe?](#el-usuario-existe)
    2. [Editar usuario](#edición-de-usuarios)
    3. [Eliminar usuario](#eliminar-un-usuario)
    4. [Crear usuarios](#crear-un-nuevo-usuario)
    5. [Cambiar contraseña](#cambiar-contraseña-de-usuario)
    6. [Recuperar contraseña/Contraseña olvidada](#recuperación-de-contraseña)
9. [Roles y permisos](#roles-y-permisos)
    1. [Crear un rol](#crear-un-nuevo-rol)
    2. [Editar un rol](#editar-un-rol)
    3. [Eliminar un rol](#eliminar-un-rol)
    4. [Aginar roles a usuarios](#asignación-de-roles-a-usuarios)
    5. [Permisos](#permisos)
        1. [Crear un permiso](#crear-permiso)
        2. [Editar un permiso](#editar-un-permiso)
        3. [Eliminar un permiso](#eliminar-un-permiso)
        4. [Asignar un permiso a un rol](#asignación-de-permisos-a-roles)
        5. [Quitar un permiso de un rol](#quitar-permiso-a-un-rol)
10. [Roles y permisos. Parte 2](#roles-y-permisos-v2)
11. [Autorización](#autorización)
12. [Sesiones](#sesiones)
    1. [Tiempo de la sesión](#tiempo-de-la-sesion)
13. [Clase Email](#dulcemail)
14. [Creando servicios](#crear-un-servicio)

# Instalación y uso
Los únicos requisitos son:
- Base de datos MySQL como mínimo (en otras no ha sido probado)
- Versión PHP >= 8.2.0

## Github
Abre la consola (cmd) de windows o la terminal del sistema operativo que estes usando y navega hasta la carpeta en la que deseas clonar el repositorio:

Por ejemplo, en la consola escribe:

`cd ruta/a/tu/directorio`

Y a continuación clona el repositorio ejecutando en consola:

`git clone https://github.com/odevnet/dulceAuth.git`

Una vez clonado, con la consola abierta y en el directorio del proyecto, ejecuta:

`composer install` para que automáticamente se instalen las dependencias necesarias.

## Composer
En una terminal y con composer instalado ejecuta el siguiente comando:

`composer require odevnet/dulceauth`

Esto instalara la libreria y todas las dependencias necesarias.

A continuación **tienes 2 opciones** para acabar de realizar la instalación.
1. Abrir el archivo *composer.json* de tu proyecto, es decir, donde hayas descargado la libreria y veras algo así:
```json
{
    "require": {
        "odevnet/dulceauth": "dev-main"
    }
}
```
Pues bien, deberas de dejarlo de esta otra manera:
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
A continuación en la terminal o consola escribes: `composer dump-autoload` y presionas enter.

Finalmente ejecutas `composer install` y automaticamente se ejecutará el archivo de instalación (Install.php) para crear los archivos *config/config.php*, *config/config-db.php*, *config/verification_email.json*, *config/forgot_password_email.json* y *logs/log_file.log*.

2. Ahorrate el paso uno y, una vez descargada la biblioteca, ejecuta el comando:
`php vendor/odevnet/dulceauth/installer.php`

Y este comando creará las carpetas y copiará los archivos necesarios de configuración.
Al igual que el paso uno, deberá quedar una estructura como la siguiente:

tu-proyecto/

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

> **Importante:** Se prefiere este segundo método, pues es más rapido y sencillo.

Tranquil@, más adelante podrás modificar sus rutas sino te gustan... ;-)

## Uso
Una vez tengas *dulceAuth* descargado y [configurado](#configuración), simplemente para poder usar la libreria deberas de incluirla e instanciarla asi:
```php
require __DIR__ . '/vendor/autoload.php';

$config = __DIR__ . '/config/config.php'; // ruta donde se encuentra tu archivo de configuración
$databaseConfig = __DIR__ . '/config/config-db.php'; // ruta donde se encuentra tu archivo de configuración de base de datos

$dulceAuth = new src\DulceAuth([$config, $databaseConfig]);
```

Y a partir de aqui, ya podrás usar cada uno de los métodos disponibles que tiene la libreria ;)

# Configuración
## Base de datos
Usa la siguiente [estructura de tablas](db_tables.sql "Tablas necesarias") para una base de datos en MySQL.
Estas son las tablas y campos minimos necesarios, es decir, si ya tienes una tabla **users**,
deberas de añadirle los campos: **name, email, password, created_at, updated_at, verified y visibility**.
Si por ejemplo el nombre de tu tabla cambia, es decir, se llama *"usuarios"* y ya tienes un campo llamado *"username"* para
el nombre del usuario, deberas de editar dicho campo a *name* y el nombre de la tabla a *users*. Para el resto de campos que cambien, deberas hacer lo mismo.


## Archivo config
Durante la instalación, se ha creado un archivo **config.php** que en si es bastante descriptivo con lo que hace cada opción de configuración y cual podemos modificar y cual no.
De momento es básico pero funcional.

Principalmente solo es necesario modificar las constantes *WEB_PAGE* y *EMAIL_FROM*.
Lo demás lo podemos dejar como esta, sin embargo, si queremos algo más de personalización, podemos configurar las rutas
de los archivos de JSON o del .log si lo consideramos necesario.
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
//For 1 day: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60 * 24);
//For 2 days: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60 * 24 * 2);
//For 7 days: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60 * 24 * 7);
//For 1 hour: define('DULCE_AUTH_SESSION_EXPIRATION', 60 * 60);
```
## Archivo configuración de la base de datos
También durante la instalación, se ha creado un archivo llamado **config-db.php** que sirve para configurar los datos de
la base de datos.
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

## Archivos JSON
Los archivos *verification_email.json* y *forgot_password_email.json* se crean automaticamente a la hora de instalar la biblioteca.
Por convención, se crean dentro de la carpeta */config* pero podemos modificar su ruta en el archivo **config.php** si queremos.
**verification_email.json** contiene el siguiente contenido:
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
Y el archivo **forgot_password_email.json** contiene:
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

# Uso de excepciones
Las excepciones se encuentran organizadas según el tipo, es decir, si estan relacionadas con los roles, tokens o usuarios.
Por ejemplo, a la hora de registrar un usuario podria pasar que ya exista un usuario registrado con ese email, por tanto,
la excepción que se produciría sería *DuplicateEmailException* ubicada en *src\exceptions\users*.

A continuación muestro todas las excepciones que se pueden producir:

**Usuarios:**

+ src\exceptions\users\AccountValidationException -> Ver [AccountValidationException](src/exceptions/users/AccountValidationException.php "AccountValidationException")

+ src\exceptions\users\ArrayOptionsUserException -> Ver [ArrayOptionsUserException](src/exceptions/users/ArrayOptionsUserException.php "ArrayOptionsUserException")

+ src\exceptions\users\CreateUserException -> Ver [CreateUserException](src/exceptions/users/CreateUserException.php "CreateUserException")

+ src\exceptions\users\DuplicateEmailException -> Ver [DuplicateEmailException](src/exceptions/users/DuplicateEmailException.php "DuplicateEmailException")

+ src\exceptions\users\EditUserException -> Ver [EditUserException](src/exceptions/users/EditUserException.php "EditUserException")

+ src\exceptions\users\InvalidPasswordException -> Ver [InvalidPasswordException](src/exceptions/users/InvalidPasswordException.php "InvalidPasswordException")

+ src\exceptions\users\LimitChangesPasswordException -> Ver [LimitChangesPasswordException](src/exceptions/users/LimitChangesPasswordException.php "LimitChangesPasswordException")

+ src\exceptions\users\RegisterException -> Ver [RegisterException](src/exceptions/users/RegisterException.php "RegisterException")

+ src\exceptions\users\UserException -> Ver [UserException](src/exceptions/users/UserException.php "UserException")

+ src\exceptions\users\UserNotFoundException -> Ver [UserNotFoundException](src/exceptions/users/UserNotFoundException.php "UserNotFoundException")

**Tokens:**
+ src\exceptions\tokens\RelationShipTokenException -> Ver [RelationShipTokenException](src/exceptions/tokens/RelationShipTokenException.php "RelationShipTokenException")

+ src\exceptions\tokens\TokenException -> Excepción general Ver [TokenException](src/exceptions/tokens/TokenException.php "TokenException")

+ src\exceptions\tokens\TokenExpiredException -> Ver [TokenExpiredException](src/exceptions/tokens/TokenExpiredException.php "TokenExpiredException")

+ src\exceptions\tokens\TokenNotFoundException -> Ver [TokenNotFoundException](src/exceptions/tokens/TokenNotFoundException.php "TokenNotFoundException")

+ src\exceptions\tokens\TokenSaveException -> Ver [TokenSaveException](src/exceptions/tokens/TokenSaveException.php "TokenSaveException")

**Roles y permisos:**
+ src\exceptions\roles\EmptyPermissionNameException -> Ver [EmptyPermissionNameException](src/exceptions/roles/EmptyPermissionNameException.php "EmptyPermissionNameException")

+ src\exceptions\roles\EmptyRoleNameException -> Ver [EmptyRoleNameException](src/exceptions/roles/EmptyRoleNameException.php "EmptyRoleNameException")

+ src\exceptions\roles\MissingRoleOrPermissionException -> Ver [MissingRoleOrPermissionException](src/exceptions/roles/MissingRoleOrPermissionException.php "MissingRoleOrPermissionException")

+ src\exceptions\roles\PermissionNotFoundException -> Ver [PermissionNotFoundException](src/exceptions/roles/PermissionNotFoundException.php "PermissionNotFoundException")

+ src\exceptions\roles\PermissionSaveException -> Ver [PermissionSaveException](src/exceptions/roles/PermissionSaveException.php "PermissionSaveException")

+ src\exceptions\roles\RoleAssignmentException -> Ver [RoleAssignmentException](src/exceptions/roles/RoleAssignmentException.php "RoleAssignmentException")

+ src\exceptions\roles\RoleNotAssignedException -> Ver [RoleNotAssignedException](src/exceptions/roles/RoleNotAssignedException.php "RoleNotAssignedException")

+ src\exceptions\roles\RoleNotFoundException -> Ver [RoleNotFoundException](src/exceptions/roles/RoleNotFoundException.php "RoleNotFoundException")

+ src\exceptions\roles\RoleNotSelectedException -> Ver [RoleNotSelectedException](src/exceptions/roles/RoleNotSelectedException.php "RoleNotSelectedException")

+ src\exceptions\roles\RolePermissionAlreadyExistsException -> Ver [RolePermissionAlreadyExistsException](src/exceptions/roles/RolePermissionAlreadyExistsException.php "RolePermissionAlreadyExistsException")

+ src\exceptions\roles\RolePermissionException -> Ver [RolePermissionException](src/exceptions/roles/RolePermissionException.php "RolePermissionException")

+ src\exceptions\roles\RoleSaveException -> Ver [RoleSaveException](src/exceptions/roles/RoleSaveException.php "RoleSaveException")

+ src\exceptions\roles\RolesException -> Ver [RolesException](src/exceptions/roles/RolesException.php "RolesException")

+ src\exceptions\roles\UsedPermissionNameException -> Ver [UsedPermissionNameException](src/exceptions/roles/UsedPermissionNameException.php "UsedPermissionNameException")

+ src\exceptions\roles\UsedRoleNameException -> Ver [UsedRoleNameException](src/exceptions/roles/UsedRoleNameException.php "UsedRoleNameException")


A medida que vayamos explicando el código veremos cuándo y cómo se usan las excepciones.
Hay casos que sera necesario y otros que serán opcionales.
Cuando se registra un usuario sí que hay que capturar cualquier excepción que pueda ocurrir, sin embargo,
cuando un usuario "se logea" o "hace login" no. En este último caso quizá sea más recomendable mostrar un mensaje
más personalizado, ya que el método login devolvera "true" o "false".

Ten en cuenta que cada método puede lanzar sus propias excepciones pero también cada método tiene a su vez una excepcion general, es decir, a la hora de crear un nuevo rol por ejemplo, tenemos la opcion de capturar las varias excepciones que se pueden producir, como son que el rol ya este en uso (UsedRoleNameException), que se encuentre vacio (EmptyRoleNameException), etc. En ese caso podemos capturar todas esas excepciones relacionadas una a una, pero también podemos "obviarlas" y capturar la excepcion general que seria en este caso *RolesException*.

Para los tokens y usuarios, también existe una excepcion general para cada caso.

# Clase Logger
La clase Logger es una sencilla clase que permite guardar un registro de errores en un archivo para mantener un control de todos los errores o excepciones que se han producido.
Cada vez que incluyamos un bloque try-catch, junto con la excepción, debemos de incluir también la clase Logger:
```php
try {
    // ... codigo
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
}
```
Esto hace que la excepción quede registrada en el archivo. Por defecto este archivo se encuentra en **logs/log_file.log** y puede ser modificado a través de la constante *LOG_FILE* del archivo config.

# Registrar usuario
```register(string $name, string $email, string $password, array $options = [])```

Por defecto y como mínimo éste método requiere tres campos: **user, email y password**.

Por ejemplo:
```php
try {
    $dulceAuth->register('Test', 'test@demo.com', '1234');
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}

```
Sin embargo tambien puede aceptar un cuarto parametro en forma de array asociativo.
El cuatro parametro, puede ser interesante en caso de que al registrar un nuevo usuario queramos almacenar más datos, por ejemplo, pais, dirección, teléfono, etc. Normalmente suele ser lo habitual.

Podemos hacer algo asi:
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['country' => 'España']);
```
Esto supone que tienes un campo en la tabla **users** llamado **country**.

Tambien puedes querer modificar el valor de *"verified"*, es decir, quizás te interese hacer que la nueva cuenta creada, se cree ya verificada, entonces, tienes que hacer asi:
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['verified' => 1]);
```
O que la visibilidad de la cuenta sea privada:
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['verified' => 1, 'visibility' => 'private', 'country' => 'España']);
```

> **Nota:** Cada campo que pases como cuarto parametro adicional debe de existir en la tabla 'users'.

Como ves, el cuarto parametro es bastante útil en caso de que queramos pasar o registrar varios datos adicionales.
Por último, y respecto al "tema de registros", tenemos la opción de registrar varios usuarios a la vez.

Para ello podriamos usar un bucle for así:
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
        echo "Usuario test$i registrado correctamente.\n";
        var_dump($register);
    } catch (Exception $ex) {
        echo "Error al registrar el usuario {$i}: {$ex->getMessage()}\n";
        Logger::error($ex->getMessage(), $ex->getTraceAsString());
    }
}
```
## Verificación de cuentas
Hemos visto que tenemos la opción de crear cuentas verificadas o no.
A través de la constante *VERIFIED* que se encuentra en el archivo config.php, podemos hacer que todas las cuentas cuando se registren se creen verificadas o no.
```php
define('VERIFIED', '0'); // 0 = unverified account, requires email validation. 1 = verified
```
Pero también podemos hacerlo en el momento de registrar una cuenta como hemos visto arriba, es decir, pasandole un cuarto parametro al método *register*.
```php
$dulceAuth->register('Test', 'test@demo.com', '1234', ['verified' => 1]);
```
>**Recuerda:**
>
> **1** quiere decir **cuenta verificada**.
>
> **0** cuenta pendiente de verificar. Se requiere validación por email.

En ese caso de que quieras que todas las cuentas requieran validación por email, deberas de generar un token aleatorio cada nuevo registro y enviarlo al email del usuario recien registrado.
Tranquil@, ya se encarga el propio método de hacerlo por nosotros. Eso si, el método register() solo enviará un email si la cuenta
requiere verificación, de lo contrario no envia nada. ¿Qué quiero decir? Qué podria ser útil enviar otro tipo de email al usuario nada más registrarse para darle las gracias por el registro o informandole de sus datos, etc. Pero eso mejor... lo dejamos para futuras versiones.
>**Aviso:**
>
> Si pasas la opción 'verified' a través del método register() no se tendrá en cuenta la constante 'VERIFIED' del config.

## Verificación de cuentas. Segunda parte.
¿Como verificamos una cuenta?
Sencillo. Una vez el usuario se ha registrado, el método *register* se encarga de enviar un email al usuario con un token generado previamente. Nosotros, debemos de validarlo y si todo es correcto verificar la cuenta.

El email que recibirá el usuario contendrá un enlace similar a este:
```tuweb.com/verification.php?token=TOKENGENERADO&userId=IDUSUARIO``` siempre y cuando la constante **CUSTOM_VERIFICATION_EMAIL_URL**
no este definida.

Entonces, en la parte de tu aplicación o, lo que es lo mismo, en la página (verification.php) dónde queramos capturar los datos, o sea, el token y la id de usuario, podemos hacer uso de GET asi:
```php
$token = $_GET['token'];
$userId = $_GET['userId'];
```
Y válidar éstos mediante dos métodos:

**validateTokenAccount:** ```validateTokenAccount(string $token, int $userId)```

**verified:** ```verified(int $userId)```

Por ejemplo:
```php
if ($dulceAuth->validateTokenAccount($token, $userId)) {
    // si el token es validado correctamente, verificamos la cuenta de usuario cambiando
    // el valor del campo "verified" a 1
    $dulceAuth->verified($userId);
}
```
Un ejemplo algo más detallado:
```php
try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenAccount($token, $userId)) {
            echo 'Cuenta validada';
            $dulceAuth->verified($userId);
        }
    } else {
        echo 'El token o el userID estan vacios';
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
O con excepción general:
```php
try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenAccount($token, $userId)) {
            echo 'Cuenta validada';
            $dulceAuth->verified($userId);
        }
    } else {
        echo 'El token o el userID estan vacios';
    }
} catch (TokenException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```

> **Nota:**
> Tanto la plantilla que se envia por email al usuario, es decir, el texto que se le envia como la página donde recibes el token y la id de usuario, se pueden modificar en el archivo *config* a través de las constantes **VERIFICATION_EMAIL_JSON_FILE** y **VERIFICATION_PAGE_URL** respectivamente.

## Un caso excepcional. O no!
Puede pasar que obligemos a todos los usuarios a verificar su cuenta cuando se registren, pero... y si se registra una cuenta
y queda pendiente de validación durante mucho tiempo? En el momento de registro se habria generado un token pero, despues de X tiempo,
éste seguramente que ya habria caducado, entonces, en ese caso debemos de seguir los siguientes pasos:

Imagina que se ha registrado un usuario, se le ha enviado un enlace a su correo para verificar la cuenta pero, por algun motivo,
el usuario no hace *"click"* en el enlace hasta pasados varios dias. Como es normal, el token habra caducado y con el enlace que se
le envio a su cuenta no se podra verificar. En otras palabras, se tiene que generar un nuevo enlace o, más exactos, un nuevo token.
Para ello, usamos el método: ``generateVerificationToken(string $email, bool $send = true)``

Como se observa, el segundo parámetro *$send* es opcional y podemos decidir si pasarlo o no.
Si ejecutamos el método sin pasar el segundo parámetro, el propio método enviará un email al usuario para que pueda verificar/validar su cuenta.
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
Si ejecutamos el código anterior no debemos hacer nada más. El usuario recibirá un email con un enlace tipo:
```tuweb.com/verification.php?token=TOKENGENERADO&userId=IDUSUARIO``` (u otro si **CUSTOM_VERIFICATION_EMAIL_URL** esta definido) y el procedimiento a seguir será exactamente el mismo que el descrito mucho más arriba, en el apartado [Verificación de cuentas. Segunda parte.](#verificación-de-cuentas-segunda-parte) haciendo uso del archivo *verification.php* y capturando los datos necesarios, o sea, el token y la id de usuario.


Ahora bien, si llamamos al método generateVerificationToken estableciendo **$send** a *false*, nos devolverá en forma de array su token y su id de usuario.
```php
$verification = $dulceAuth->generateVerificationToken('test@demo.com', false); // return ['userId' => $userId, 'token' => $token];
```
Al hacerlo de esta ultima forma, podemos generar nosotros mismos el email que se le enviará al usuario usando la clase **dulceMail** [Lee sobre ésta clase](#dulcemail "Clase dulceMail")

Por ejemplo:
```php
try {
    $verification = $dulceAuth->generateVerificationToken('test@demo.com', false);

    if ($verification) {

        $mail = $dulceAuth->dulceMail();
        $mail->from('admin@tusitioweb.com')
            ->to('test@demo.com')
            ->subject('Valida tu cuenta')
            ->message("Haz click en el siguiente enlace:
            tusitioweb.com/verification.php?token=" . $verification['token'] . "&userId=" . $verification['userId'] . "
            para validar tu cuenta y poder logearte.");
        $mail->send();

        if ($mail->send()) {
            echo "Te acabamos de enviar un email para confirmar tu cuenta.
        Por favor, revisa tu correo.";
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
Después de enviar el email nosotros mismos, hacemos todo el proceso, es decir, enviamos el email, luego capturamos esos datos (token e id de usuario) y hacemos uso de las funciones validateTokenAccount y verified como antes...

# CUSTOM_VERIFICATION_EMAIL_URL y CUSTOM_FORGOT_PASSWORD_EMAIL_URL
Por defecto cuando se registra un usuario y se envia el email de verificación o se envia el email de recuperar la contraseña,
se usa la clase DulceMail con un formato de url por definido tipo: ```tuweb.com/verification.php?token=TOKENGENERADO&userId=IDUSUARIO```

Pues bien, si queremos modificar esto, y queremos que la url tenga otro formato ya sea por el motivo de estar usando un framework o
cualquier otro, debemos de crear una constante con una función anónima de la siguiente manera:
```php
define('CUSTOM_VERIFICATION_EMAIL_URL', function (string $token, int $userId): string {
    return "https://tuweb.com/validar/{$token}/usuario/{$userId}";
});
```
Evidentemente la url la personalizamos a nuestro gusto.
Ahora, cuando se envie un email de verificación, la url que recibirá el usuario en su email será:
```https://tuweb.com/validar/{$token}/usuario/{$userId}```

Es obvio que esa url deberá apuntar a una página donde capturemos el token y la id de usuario.

Sucede lo mismo para la contraseña olvidada. Así que si queremos personalizar el email de "contraseña olvidada", deberemos
de crear una constante como la siguiente:
```php
define('CUSTOM_FORGOT_PASSWORD_EMAIL_URL', function (string $token, int $userId): string {
    return "https://tuweb.com/forgot-password/{$token}/usuario/{$userId}";
});
```

# Login
`$dulceAuth->login($email, $password);`

Éste método conecta un usuario al sistema y crea su correspondiente sesion.

Por tanto, si queremos loguear un usuario bastaria hacer:
```php
$dulceAuth->login('test@demo.com', '1234');
```
Si queremos saber si esta conectado o existe una conexion activa:
```php
$dulceAuth->isLoggedIn();
```
Más claro:
```php
if ($dulceAuth->isLoggedIn()) {
        echo "Estas conectado!";
    }
```
Para desconectar, cerrar o eliminar conexion:
```php
$dulceAuth->logout();
```
Una vez conectado, puede ser interesante obtener datos del usuario. Para ello usaremos la funcion *currentUser()* seguido de los campos
que queramos mostrar.

Por ejemplo:
```php
$dulceAuth->currentUser()->name;
```
Si tenemos un campo para el pais llamado "country" podemos hacer:
```php
$dulceAuth->currentUser()->country;
```
Y así para cada campo que queramos mostrar del usuario actualmente conectado.

# Users
Existen varias opciones para mostrar una lista con todos los usuarios de la base de datos.
Por ejemplo, imagina que queremos mostrar una lista de usuarios existentes pero solo nos interesa mostrar su id,
nombre, email y pais. Para eso podemos hacer uso del método `$dulceAuth->showUsers()` y recorrerlo en un foreach:
```php
foreach ($dulceAuth->showUsers() as $user) {
        echo 'ID: ' . $user->id . '<br>';
        echo 'Nombre: ' . $user->name . '<br>';
        echo 'Email: ' . $user->email . '<br>';
        echo 'Country: ' . $user->country . '<br>';
    }
```
A parte del poderoso método `$dulceAuth->showUsers()` que podemos usar para recorrer cualquier campo que queramos mostrar relacionado con el usuario, también existen los siguientes tres métodos:
```php
$dulceAuth->showUsersById();
```
```php
$dulceAuth->showUsersByName();
```
```php
$dulceAuth->showUsersByEmail();
```
Creo que sus nombres son ya bastante descriptivos como para una explicación de lo que hace cada uno ;-).
Podemos pensar que no son útiles, pero quien sabe, si alguna vez los necesitamos ¡ahí están!

## El usuario existe? ##
Si queremos comprobar si un usuario existe, tenemos dos formas de hacerlo.

La primera es a través del metodo **userIdExists**:

```userIdExists(int $userId)```
```php
$dulceAuth->userIdExists(5); // buscamos si existe el usuario con id 5
```
Este método devolvera **true** o **false** en caso de que exista o no la id de usuario que le hayamos pasado.

Otra forma de buscar o saber si un usuario existe, es a través de su email:
```php
$dulceAuth->userEmailExists('test@demo.com');
```
Devolverá **true** si el correo electrónico existe, **false** en caso contrario.

## Edición de usuarios ##
Para editar un usuario usamos el método "*editUser*" que acepta dos parametros, uno es la id de usuario a editar y el otro
un array de opciones con los nuevos valores.
```editUser(int $userId, array $options)```

Ejemplo:
```php
try {
    $dulceAuth->editUser(1, [
        'name' => 'Test',
        'email' => 'test@demo.com',
        'country' => 'España'
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
En el ejemplo anterior hemos editado los campos nombre, email y country para el usuario con id 1.
El valor devuelto sera **true** o **false**.

Por cierto, volviendo al tema de las excepciones, el código anterior se podria reducir a:
```php
try {
    $dulceAuth->editUser(1, [
        'name' => 'Test',
        'email' => 'test@demo.com',
        'country' => 'España'
    ]);
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Pero recomiendo especificar cada excepción ;-)


## Eliminar un usuario ##
Para eliminar un usuario basta ejecutar el metodo *"deleteUser"* pasandole la id de usuario a eliminar.
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
Devolverá **true** o **false** en caso de éxito.

## Crear un nuevo usuario ##
Puede ser interesante querer crear un nuevo usuario sin necesidad de recurrir al método *"register"*.
Por ejemplo, podemos tener un panel de administración y querer tener la opción de crear usuarios, para ello tenemos el metodo *"createUser"*.
Este método recibe como minimo tres parametros: nombre, email y password. Existe también un cuarto parametro opcional que es un array que nos permite pasar mas datos a registrar. Por ejemplo su telefono, pais, etc.

```createUser(string $name, string $email, string $password, array $options = [])```

Este es un ejemplo con el cuarto parametro opcional, es decir, si queremos que la nueva cuenta se cree ya verificada y con un número de teléfono hariamos así:
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

## Cambiar contraseña de usuario ##
Para cambiar la contraseña de un usuario es necesario ejecutar el método *"changePassword"* con tres parametros:

`changePassword(int $userId, string $currentPassword, string $newPassword)`

Por ejemplo, para cambiar la contraseña del usuario con id '1':
```php
try {
    $dulceAuth->changePassword(1, '1234', '1234da#');
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
**dulceAuth** permite establecer un limite de cambios de contraseña que, por defecto se encuentra en **3 cambios por año**. Puedes cambiar esto a través de la constante **MAX_PASSWORD_CHANGES** en el archivo **config.php**.
Creo que puede ser útil para evitar un uso abusivo e innecesario del método changePassword.

También puede ser útil conocer el número total de cambios de contraseña realizados (si los hubiera) por un usuario.
Para ello:
```php
try {
    echo $dulceAuth->latestChange(1)->changes_count;
} catch (LimitChangesPasswordException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
El método "*latestChange*" recibe un parámetro que es la id de usuario a consultar y retorna una instancia de PasswordChange si se encuentra un cambio de contraseña. Por eso, sobre ella podemos ejecutar la propiedad **changes_count** para conocer el número total de cambios.

## Recuperación de contraseña ##
Si un usuario ha olvidado la contraseña, dado que la contraseña usa un cifrado seguro es imposible recuperarla y por tanto se ha de crear una nueva. Para ello, **dulceAuth** generará un *token temporal* para que el usuario pueda crear una nueva contraseña.

Con el método **forgotPassword**:

``forgotPassword($email, $send = true)``

Se genera un token para el usuario cuyo email hayamos pasado como parámetro.
El parametro *$send* es opcional y podemos decidir si pasarlo o no. El hecho de pasarlo o no implica que el metodo se comporte de una manera u otra.
Básicamente y de forma predeterminada, éste método **envia un email al usuario** con un enlace que contendrá el token y su id de usuario que posteriormente nos servirá para poder crear una nueva contraseña. [VER CONSTANTES PERSONALIZADAS](#custom_verification_email_url-y-custom_forgot_password_email_url)

Seguro que todo esto te suena, ya que su funcionamiento es similar al método *generateVerificationToken*.

La forma común de usarlo es:
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
Si llamamos al método estableciendo **$send** a *false*, nos devolverá en forma de array su token y su id de usuario.
```php
$forgotPassword = $dulceAuth->forgotPassword('test@demo.com', false); // return ['userId' => $userId, 'token' => $token];
```
Al hacerlo de esta ultima forma, podemos generar nosotros mismos el email que se le enviará al usuario usando la clase **dulceMail**.

Veamos algunos ejemplos.

Supongamos que el usuario llamado "Test" y cuyo correo es "test@demo.com", ha olvidado la contraseña.
Pues bien, llamaremos al método *forgotPassword* **solo** pasandole el email del usuario, para que sea **el mismo método quien se encarge de enviar el correo** con el enlace de recuperación con el token y el id de usuario:
```php
$forgotPassword = $dulceAuth->forgotPassword('test@demo.com');
```
Si comprobamos el correo y hacemos click en el enlace, nos lleva a la pagina que hemos establecido como página de "recuperacion de contraseña".
La página por defecto para la recuperación de contraseña se llama *forgot.php* y se establece con la constante *FORGOT_PASSWORD_PAGE_URL* del archivo **config.php.**

Es en ésta página donde recuperamos los valores de *token* y *userId* mediante **$_GET** y se los pasamos al método **validateTokenPassword**:

`validateTokenPassword(string $token, int $userId): bool`

Por ejemplo:
```php
$token = $_GET['token'];
$userId = $_GET['userId'];

if ($dulceAuth->validateTokenPassword($token, $userId)) {
    // se ha validado correctamente, aqui puedes mostrar un form para escribir la nueva contraseña...
}
```
Por último, si la validación es correcta, creamos la nueva contraseña usando el método **insertNewPassword**:

`insertNewPassword(string $password, int $userId): void`

Éste método recibe como parametros la nueva contraseña y la id del usuario.

Un ejemplo algo más completo puede ser el de la página **forgot.php**:
```php
try {
    $token = $_GET['token'];
    $userId = $_GET['userId'];

    if ((!empty($token) && isset($token)) && (!empty($userId) && isset($userId))) {
        if ($dulceAuth->validateTokenPassword($token, $userId)) {
            $dulceAuth->insertNewPassword('tu nueva contraseña', $userId);
            echo 'Contraseña cambiada con éxito';
        }
    } else {
        echo 'El token o el userID estan vacios';
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
Hemos visto la forma recomendada y sencilla de hacerlo. Ahora vamos con la segunda forma que básicamente se trata de enviar/generar nosotros mismos el correo de recuperación a través de la clase **dulceMail**.
Para ello llamamos al método **forgotPassword** pasandole el email del usuario y el segundo parámetro como *false*.
De esta manera, sólo nos devolvera la id de usuario y el token temporal que se ha generado. El siguiente paso será generar nosotros mismos el correo que le enviaremos:
```php
try {
    $forgotPassword = $dulceAuth->forgotPassword('test@demo.com', false); // return ['userId' => $userId, 'token' => $token];
    // comprobamos que $forogPassword devuelve el userId y el token
    if ($forgotPassword) {
        $mail = $dulceAuth->dulceMail();
        $mail->from('admin@tusitioweb.com')
            ->to('test@demo.com')
            ->subject('Regeneracion de contraseña')
            ->message("Recibes este correo porque has olvidado tu contraseña y se ha generado un token para reestablecerla.
        Haz click en el siguiente enlace: tusitioweb.com/forgot.php?token=" . $forgotPassword['token'] . "&userId=" . $forgotPassword['userId'] . " \n
        Si no has sido tu ponte en contacto urgente con administración ya que tu cuenta esta en peligro.");
        $mail->send();
        // si el envio es satisfactorio podemos mostrar un mensaje en el navegador
        if ($mail->send()) {
            echo "Te acabamos de enviar un email.
        Por favor, revisa tu correo para crear una nueva contraseña que recuerdes.";
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
¡Ya esta! A partir de aqui el proceso es el mismo que antes. Después de hacer "click" en el enlace de la pagina establecida como "recuperación de contraseña", recuperamos los valores de *token* y *userId* y se los pasamos al método **validateTokenPassword** y después llamamos al método **insertNewPassword**.

>**Muy importante:**
>
> El método ***insertNewPassword*** ha de ser llamado después de haber generado un token, de lo contrario lanzará una excepción.
>
> En otras palabras, llamar directamente al método sin antes generar un token no hará que se cambie la contraseña.

La recomendación es seguir el orden mostrado de ejemplo, es decir, primero generamos un token para el email (usamos **forgotPassword**), después validamos mediante **validateTokenPassword** y por último insertamos/registramos la nueva contraseña mediante **insertNewPassword**.

# Roles y permisos
##  Crear un nuevo rol ##
Si queremos crear un nuevo rol, basta con llamar a la funcion **createRole** pasandole como parametro un nombre válido.
Por ejemplo:
```php
try {
    $dulceAuth->createRole('nombre_del_nuevo_rol', 'descripcion');
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
O bien:
```php
try {
    $dulceAuth->createRole('nombre_del_nuevo_rol', 'descripción');
} catch (RolesException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
> **Nota:** La **descripción** es opcional.

## Editar un rol ##
Para editar un rol existe el método: `editRole(int $roleId, string $name, ?string $description = null)`:
Donde el primero es la identidad del rol y el segundo, el nuevo nombre que le queremos poner.
Una vez más, la descripción es opcional.
```php
try {
    $dulceAuth->editRole(10, 'nuevo_nombre', 'descripción');
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
O también:
```php
try {
    $dulceAuth->editRole(10, 'nuevo_nombre', 'descripción');
} catch (RolesException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Pero no, mejor que no ;P

## Eliminar un rol ##
Para eliminar un rol, como no podia ser de otra manera, llamamos al método **deleteRole** pasandole la id del rol a eliminar:
```php
try {
    $eliminarRol = $dulceAuth->deleteRole(8);
    if ($eliminarRol) {
        echo 'Rol eliminado con éxito bla bla bla...';
    }
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
## Asignación de roles a usuarios ##
Ahora puede ser interesante que si ya tenemos nuestra lista de roles creada, podamos asignar uno o varios roles a un usuario.
Para ello llamaremos al método **assignRoleToUser** con dos parametros: `assignRoleToUser(int $userId, array $roles): bool`

Uno es la identidad del usuario y, la identidad o identidades de los roles que queramos asignar.

Supongamos que queremos asignar tres roles al usuario *Jhon* cuya id es *27* y, queremos asignarle los roles: 'editor', 'user' y 'moderador' que se corresponden con las identidades 4, 5 y 7 respectivamente.

Para eso debemos hacer lo siguiente:
```php
try {
    $asignarRol = $dulceAuth->assignRoleToUser(27, [4, 5, 7]);
    if ($asignarRol) {
        echo 'Rol(es) asignado(s) con éxito.';
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
Es interesante pararnos aqui y mencionar que, si le asignamos un rol que ya tiene, sencillamente no pasará nada, es decir, asignará el resto igual.

Bien, ahora que hemos asignado roles a un usuario, también podemos hacer lo contrario, *"desasignarlos"*, es decir, quitar roles a un usuario.
Para ello tenemos el método **removeRoleToUser**: `removeRoleToUser(int $userId, array $roles): bool`

Este método acepta los mismos parametros que el anterior, es decir, el primero es la identidad del usuario y el segundo, un array que debe contener las identidades de los roles.
Como mínimo ha de recibir un rol a eliminar.

Por ejemplo:
```php
try {
    $eliminarRol = $dulceAuth->removeRoleToUser(27, [2, 7]);
    if ($eliminarRol) {
        echo 'Rol(es) quitado(s) con éxito del usuario.';
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

## Permisos ##
Los permisos estan relacionados con los roles y viceversa. Un usuario tendrá unos determinados permisos dependiendo del rol que tenga, asi pues, es conveniente crear una lista de permisos.
La cantidad de roles como de permisos que queramos crear, dependerá del tipo de aplicación y autorización que queramos implementar.

## Crear permiso ##
Para empezar, si queremos crear un nuevo permiso hay que ejecutar el método **createPermission**: `createPermission(string $name, ?string $description = null): bool`

Este método recibe dos parametros (nombre y descripcion). La descripción es opcional pero útil.

```php
try {
    $nuevoPermiso = $dulceAuth->createPermission('Publicar noticia', 'Permite publicar noticias en el sitio');
    if ($nuevoPermiso) {
        echo 'Permiso creado con éxito!';
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
## Editar un permiso ##
Si queremos editar el nombre de un permiso ya creado, debemos de llamar al método **editPermission**: `editPermission(int $permissionId, string $newName, ?string $description = null): bool`
Este método recibe principalmente dos parámetros, un entero y un string. La identidad del permiso y el nuevo nombre que le queramos dar respectivamente.
Opcionalmente y una vez más, la descripción es opcional pero aconsejable.

Por ejemplo:
```php
try {
    $editarPermiso = $dulceAuth->editPermission(13, 'Publicar articulo');
    if ($editarPermiso) {
        echo 'Permiso editado con éxito!';
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
Recuerda, puedes "obviar" las excepciones y capturar "la general":
```php
try {
    $editarPermiso = $dulceAuth->editPermission(13, 'Publicar articulo');
    if ($editarPermiso) {
        echo 'Permiso editado con éxito!';
    }
} catch (Exception $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Pero una vez más y personalmente, no lo recomiendo ;-)

## Eliminar un permiso ##
Para eliminar un permiso basta con ejecutar el método siguiente: `deletePermission(int $permissionId): bool`
```php
try {
    $eliminarPermiso = $dulceAuth->deletePermission(13);
    if ($eliminarPermiso) {
        echo 'Permiso eliminado con éxito!';
    }
} catch (PermissionNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
## Asignación de permisos a roles ##
Como comenté al principio, un permiso por si solo no hace nada, no es útil sino tiene un rol asociado, por tanto hay que relacionarlo con algún rol.
Para eso debemos de llamar al método **assignPermissionToRole** con dos parámetros que son la identidad del rol y la identidad del permiso: `assignPermissionToRole(int $roleId, int $permissionId)`

```php
try {
    $asignarPermisoARol = $dulceAuth->assignPermissionToRole(4, 14);
    if ($asignarPermisoARol) {
        echo 'Permiso asignado con éxito al rol';
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
## Quitar permiso a un rol ##
Si lo que queremos es quitar/remover un permiso de un rol, debemos de ejecutar el metodo **removePermissionFromRole**: `removePermissionFromRole(int $roleId, int $permissionId)`

Donde una vez más, como primer parámetro es la id del rol y el segundo la id del permiso.

Por ejemplo:
```php
try {
    $quitarPermisoDeRol = $dulceAuth->removePermissionFromRole(4, 14);
    if ($quitarPermisoDeRol) {
        echo 'Permiso QUITADO con éxito al rol';
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
# Roles y permisos V.2 #
Anteriormente vimos como crear roles y permisos y también, como asignarlos a los usuarios, pues bien, hay unos cuantos métodos que nos permiten ver todos los roles y permisos disponibles y listar todos los roles que tiene un usuario.
Es importante mencionar que, listar todos los permisos de un usuario no es posible, ya que los permisos estan "ligados" a los roles, no al usuario. En otras palabras, un usuario tendrá un permiso o no, dependiendo del rol que tenga.
Dicho y recalcado ésto, veamos los métodos de los que hablé al principio.

Para conocer todos los roles disponibles, llamamos al método **showRoles** y lo recorremos a través de un foreach:
```php
$roles = $dulceAuth->showRoles();

foreach ($roles as $role) {
    echo "ROL ID: $role->id | NOMBRE: $role->name <br>";
}
```
También existen dos métodos más que son: **showRolesById()** y **showRolesByName()** para listar "de forma más rápida" solo las identidades o nombres de los roles. En principio con el método principal (showRoles()) debemos tener suficiente.

Respecto a los permisos sucede igual, disponemos del método **showPermissions()** para listar todos los permisos disponibles:
```php
$permissions = $duleAuth->showPermissions();

foreach ($permissions as $permission) {
    echo "PERMISSION ID: $permission->id | PERMISSION NAME: $permission->name <br>";
}
```
Y al igual que antes, tenemos los métodos **showPermissionsById()** y **showPermissionsByName()** para mostrar los permisos por su identidad o nombre respectivamente.

Todos éstos métodos, en realidad son útiles para por ejemplo una zona de administración, donde sea necesario ver una lista de cuantos roles y permisos hay creados.

Existe otro método más, que es interesante si queremos saber qué roles tiene un determinado usuario. Para ello tenemos el método siguiente:

``userRoles($userId)``

Éste método, acepta un único parámetro que es la identidad del usuario a consultar.

Por ejemplo, si queremos consultar qué roles tiene el usuario con identidad "2", hariamos lo siguiente:

```php
try {
    $roles = $dulceAuth->userRoles(2);
    echo 'El usuario con ID 2 tiene los siguientes roles: <br>';
    foreach ($roles as $role) {
        echo "ROL: $role->name <br>";
    }
} catch (UserNotFoundException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
Y, para no variar, como en los ejemplos anteriores, también disponemos de dos métodos para mostrar los roles por su identidad o nombre:

`userRolesById($userId)` y `userRolesByName($userId)`

A lo mejor no los usamos nunca, pero de momento, ahí están!

# Autorización #
Hemos creado usuarios, roles, permisos... Ahora, nos interesa dar autorización a nuestra aplicación, es decir, podemos querer restringir el acceso a la zona de administración solo para administradores, verificar antes de hacer algo que el usuario tiene el rol necesario, etc.

Para ello tenemos dos métodos para validar si un usuario tiene el rol o el permiso necesario.
El primero de ellos es:

 `hasRole(string $roleName, ?int $userId = null): bool`


Este método sirve para comprobar si el usuario actual, es decir, el que esta logeado tiene un rol determinado. Bastaria con pasarle solo el nombre del rol así:
```php
if ($dulceAuth->hasRole('Admin')) {
    echo 'Eres administrador.';
    // aqui puedes añadir una zona de administración o cualquier otra cosa...
} else {
    echo 'No tienes el rol necesario para ver esta página.';
}
// o algo más completo y detallado:
$dulceAuth->login('test@demo.com', '12345');

if ($dulceAuth->isLoggedIn()) {
    echo "Estas conectado!";
    if ($dulceAuth->hasRole('Admin')) {
        echo 'Eres administrador.';
        // aqui puedes añadir una zona de administración o cualquier otra cosa...
    } else {
        echo 'No tienes el rol necesario para ver esta página.';
    }
}
```
Como hemos visto, el método **hasRole**, también puede aceptar un segundo parámetro opcional que es la identidad de un usuario. Ésto nos sirve por si queremos comprobar si un usuario determinado tiene un rol en concreto.

Por ejemplo:
```php
if ($dulceAuth->hasRole('SuperAdmin', 1)) {
    echo 'Tiene el rol!';
} else {
    echo 'No tiene el rol! :-(';
}
```
En el ejemplo anterior, en lugar de comprobar si el usuario actual es "*SuperAdmin*", comprobamos si lo es el usuario con identidad 1.

Para los permisos tambien existe una función similar aunque, por ahora, solo comprueba el permiso que tiene el usuario actual, es decir, el que esta conectado en la aplicación.

Por ejemplo, suponiendo que tenemos un permiso para crear usuarios, podemos comprobar si el usuario actual tiene dicho permiso.

Para ello ejecutamos el método **hasPermission** pasandole como parámetro el nombre del permiso.
```php
if ($dulceAuth->hasPermission('Create user')) {
    echo 'Tienes el permiso necesario para crear usuarios';
} else {
    echo 'No tienes permiso para realizar ésta acción.';
}
```

# Sesiones #
Sobre las sesiones no hay mucho que decir. Simplemente existe una sencilla clase para crear y recuperar variables de sesion.
Su uso es bastante sencillo, tenemos dos métodos principales que son:

`set(string $key, $value)` para crear una sesión.

`get(string $key)` para recuperar la sesión.
```php
$dulceAuth->session()->set('color', 'rojo');
```
```php
echo $dulceAuth->session()->get('color');
```

Tenemos algunos métodos más que son:

``has($key)``: Para verificar si una clave existe en la sesion.
```php
$dulceAuth->session()->has('color'); // devuelve true o false

if ($dulceAuth->session()->has('color')) {
    echo 'si existe';
} else {
    echo 'no existe';
}
```

``remove($key)``: Para eliminar un par clave-valor de la sesion.
```php
$dulceAuth->session()->remove('nombre');
```

``destroy()``: Destruye por completo la sesion.
```php
$dulceAuth->session()->destroy(); // destruye la sesion al completo
```

Hay que mencionar y muy importante que, cuando se "logea" un usuario, automaticamente se crean dos sesiones. Una es para la identidad del usuario que se llama *"userId"* y la otra es el tiempo que durará la sesión activa, cuyo nombre es *"expire_time"*.
Para recuperar estas sesiones, por ejemplo, la id del usuario conectado, debemos de hacer uso del método `get()` de la **clase Session** así:
```php
echo $dulceAuth->session()->get('userId');
```
## Tiempo de la sesion ##
Como dije antes, cuando un usuario se autentica, se crean dos sesiones, una que contiene la id de usuario, y otra, cuyo valor es el tiempo de vida/duración de la sesion.
Actualmente esta establecido que cada sesión dure una hora, pero podemos cambiarlo con la constante "**SESSION_EXPIRATION**" que se encuentra en el archivo **config.php**.

Para saber exactamente cuanto durará la sesión, podemos pensar en consultar el valor de *"expire_time"*, es decir, hacer algo asi:
```php
$dulceAuth->session()->get('expire_time');
```
Sin embargo, si hacemos lo anterior, nos dará la fecha en formato *timestamp* y algo dificil de leer. Asi que existe un método mejor llamado **expirationTime()** que nos permite consultar éste dato de forma mas legible.

```php
echo $dulceAuth->session()->expirationTime(); // muestra el tiempo en formato: Y-m-d H:i:s
```

También puede ser interesante antes de acceder a una determinada parte de nuestra aplicación, comprobar si el usuario no solamente esta logeado (recuerda que para eso esta el método isLoggedIn()) sino que nos interesa saber si existe una **sesion activa y válida.** Pues bien, para eso podemos hacer uso del método **isValid()** de la clase Session asi:
```php
if ($dulceAuth->session()->isValid()) {
        echo 'La sesion esta activa';
        // podemos hacer cualquier cosa aqui
    } else {
        echo 'La sesion ha caducado/expirado';
    }
```



# dulceMail
Esta es una super sencilla clase que básicamente hace uso de la función nativa mail() de PHP. Es más que nada para poder enviar y recibir correos electronicos de la forma más simple posible. Por favor, tenlo en cuenta.
Si quieres algo más seguro explora alternativas como "PHPMailer" y adaptalo e integralo a dulceAuth.

Para hacer uso de esta clase su uso es bastante sencillo:
```php
$mail = $dulceAuth->dulceMail();
// preparamos el email: remitente, destinatario, asunto y mensaje
$mail->from('admin@tusitioweb.com')->to('test@demo.com')->subject('Asunto/tema')->message('Un mensaje cualquiera');
// y lo enviamos
$mail->send();
```
Pero como siempre, un ejemplo más completo incluyendo excepciones sería el siguiente:
```php
try {
    $mail = $dulceAuth->dulceMail();

    $mail->from('admin@tusitioweb.com')->to('test@demo.com')->subject('Asunto/tema')->message('Un mensaje cualquiera');

    $send = $mail->send();
    // si el envio es satisfactorio podemos mostrar un mensaje en el navegador
    if ($send) {
        echo 'Te acabamos de enviar un email.';
    }
} catch (RuntimeException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
} catch (InvalidArgumentException $ex) {
    Logger::error($ex->getMessage(), $ex->getTraceAsString());
    echo $ex->getMessage();
}
```
# Crear un servicio
A través del bootstrap, se crean/arrancan todos los servicios que dulceAuth necesita para funcionar, sin embargo,
a veces, puede que necesitemos otros servicios (clases) adicionales. Pues bien, esto es posible y fácil de hacer.

Tan solo debemos de *ejecutar* el contenedor de servicios de dulceAuth asi:
```php
$dulceAuth->dulce->addService('Nombre del servicio', function ($dulce) {
    return new espacioDeNombres\Servicio();
});
```
Por ejemplo:
```php
$dulceAuth->dulce->addService('Formularios', function ($dulce) {
    return new helpers\Form();
});

echo $dulceAuth->dulce->get('Formularios');
```
El anterior ejemplo agregaria a dulceAuth una nueva clase la cual no vendria por defecto.
Creo que es bastante fácil de entender ;-)
