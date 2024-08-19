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
 * Session class.
 *
 * Allows you to configure, start, propagate and destroy sessions.
 * It also includes other related functionalities such as checking if a session exists,
 * if valid, flash messages, etc.
 *
 * @package src
 *
 * @since 1.0
 */
class Session
{
    private static bool $sessionStarted = false;

    /**
     * Configure the session with some options.
     *
     * @param array $options Configuration options that can be:
     * - 'session_lifetime': Session duration in seconds.
     * - 'cookie_path': Path of the session cookie.
     * - 'cookie_domain': Domain of the session cookie.
     *
     * @return void
     */
    public static function configure(array $options): void
    {

        self::start();

        // Configure session options if they have been passed
        if (isset($options['session_lifetime'])) {
            session_set_cookie_params($options['session_lifetime']);
        }
        if (isset($options['cookie_path'])) {
            session_set_cookie_params(['path' => $options['cookie_path']]);
        }
        if (isset($options['cookie_domain'])) {
            session_set_cookie_params(['domain' => $options['cookie_domain']]);
        }
    }

    /**
     * Method for start of session.
     *
     * @return void
     */
    public static function start(): void
    {
        if (!self::$sessionStarted) {
            session_start();
            self::$sessionStarted = true;
        }
    }

    /**
     * Create a session with its corresponding value.
     *
     * @param string $key Session key.
     * @param mixed  $value Value to store.
     *
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Gets the value of a session.
     *
     * @param string $key Session key.
     * @param mixed  $default Default value if the key does not exist in the session.
     *
     * @return mixed Value stored in the key or the default value if it does not exist.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Method to check if a key exists.
     *
     * @param string $key Session key.
     *
     * @return bool True if the key exists, false otherwise.
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Delete a value from the session.
     * Specifically eliminates the key-value relationship.
     *
     * @param string $key Session key.
     *
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Destroys the current session and deletes everything related to the session.
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    /**
     * Checks if the current session is valid.
     *
     * @return bool True if the session is valid, false otherwise.
     */
    public static function isValid(): bool
    {
        self::start();

        if (self::get('expire_time') && time() < self::get('expire_time')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This method allows you to establish a flash type message.
     *
     * @param string $type Type message (example: 'success', 'error', 'info', etc.).
     * @param string $message Message to display.
     *
     * @return void
     */
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flash'][$type][] = $message;
    }

    /**
     * Gets and deletes a flash message.
     *
     * @param string $type Type message (example: 'success', 'error', 'info', etc.).
     *
     * @return array Array of flash messages or an empty array if they not exist.
     */
    public static function getFlash(string $type): array
    {
        self::start();
        $messages = $_SESSION['_flash'][$type] ?? [];
        unset($_SESSION['_flash'][$type]);
        return $messages;
    }

    /**
     * Delete all flash messages.
     *
     * @return void
     */
    public static function clearFlash(): void
    {
        self::start();
        unset($_SESSION['_flash']);
    }

    /**
     * Clear expired flash messages from the session.
     *
     * @param int $expirationTime Flash message expiration time (in seconds).
     *
     * @return void
     */
    public static function cleanExpiredFlash(int $expirationTime): void
    {
        self::start();
        if (isset($_SESSION['_flash'])) {
            foreach ($_SESSION['_flash'] as $type => $messages) {
                foreach ($messages as $key => $message) {
                    if (time() > $expirationTime) {
                        unset($_SESSION['_flash'][$type][$key]);
                    }
                }
            }
        }
    }

    /**
     * Gets the time in a human-readable form when the login session expires
     * (in reference to a logged in user).
     *
     * @return string|false Readable session expiration time or false if not defined.
     */
    public static function expirationTime(): string|false
    {
        if (self::has('expire_time')) {
            $timestamp = self::get('expire_time');
            $readableDate = date('Y-m-d H:i:s', $timestamp);

            return $readableDate;
        }

        return false;
    }
}
