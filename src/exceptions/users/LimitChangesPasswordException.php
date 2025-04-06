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

namespace src\exceptions\users;

/**
 * Class LimitChangesPasswordException
 *
 * This exception is thrown when the password change limit is exceeded.
 *
 * @package src\exceptions\users
 *
 * @since 1.0
 */
class LimitChangesPasswordException extends UserException
{
    /**
     * Exception constructor.
     *
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param \Exception|null $previous The previous exception for exception chaining.
     */
    public function __construct(
        $message = 'You have reached the limit for password changes per year.',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
