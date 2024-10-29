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
 * Class Configuration
 *
 * It is responsible for managing the application configuration
 *
 * @package src
 *
 * @since 2.0.0
 */
class Configuration
{
    /**
     * @var array Array that stores the file with the configuration data.
     */
    protected $config;

    /**
     * Constructor where the configuration is loaded from the specified file
     *
     * @param array|string $customConfig Configuration file.
     */
    public function __construct($customConfig = [])
    {
        $this->config = require $customConfig;
    }

    /**
     * Gets a value from the configuration.
     *
     * Returns the value corresponding to the specified key. If not found,
     * returns the default value provided.
     *
     * @param string $key The key for the configuration value.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The value of the configuration or the default value.
     */
    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
