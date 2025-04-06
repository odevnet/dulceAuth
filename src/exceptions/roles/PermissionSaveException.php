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
 * Class PermissionSaveException
 *
 * This exception is thrown when an error occurs while saving/creating
 * a new permission.
 *
 * @package src\exceptions\roles
 *
 * @since 1.0
 */
class PermissionSaveException extends RolePermissionException
{
    /**
     * Exception constructor.
     *
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param \Exception|null $previous The previous exception for exception chaining.
     */
    public function __construct(
        $message = 'Error saving the permission.',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
