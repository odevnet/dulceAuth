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

namespace src\exceptions\roles;

/**
 * Class RolePermissionException
 *
 * General exception for permissions.
 *
 * @package src\exceptions\roles
 *
 * @since 1.0
 */
class RolePermissionException extends \Exception
{
    public function __construct(
        $message = 'Permissions error',
        $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
