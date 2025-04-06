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

namespace src\exceptions\tokens;

/**
 * Class TokenSaveException
 *
 * If an error occurs while saving the token to the database.
 *
 * @package src\exceptions\tokens
 *
 * @since 1.0
 */
class TokenSaveException extends TokenException
{
    /**
     * Exception constructor.
     *
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param \Exception|null $previous The previous exception for exception chaining.
     */
    public function __construct(
        $message = 'An error occurred while saving the token.',
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
