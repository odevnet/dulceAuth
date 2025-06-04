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

namespace src;

/**
 * Class Logger
 *
 * Simple class that allows saving error messages in a log file.
 *
 * @package src
 *
 * @since 1.0
 */
class Logger
{
    /**
     * Saves an error message to the log file.
     *
     * @param string $message The error message to log.
     * @param string $trace   The associated error trace.
     * @return void
     */
    public static function error(string $message, string $trace): void
    {
        error_log('[' . date('Y-m-d H:i:s') . '] [ERROR] ' . $message . "\n" . $trace . "\n", 3, DULCE_AUTH_LOG_FILE);
    }
}
