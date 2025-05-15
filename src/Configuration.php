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
 * @since 1.0.0
 */
class Configuration
{
    /**
     * @var array Array that stores the file with the configuration data.
     */
    private static $config = [];

    /**
     * Loads configuration from a file.
     *
     * @param string $file Path to the configuration file.
     * @throws \Exception if the file is not found.
     */
    public static function load($file)
    {
        if (file_exists($file)) {
            // Captures constants and defines values
            $constantsBefore = get_defined_constants(true)['user'] ?? [];
            $variablesBefore = get_defined_vars();

            // Include the configuration file
            $configFromFile = include $file;

            // Capture the newly defined constants
            $constantsAfter = get_defined_constants(true)['user'] ?? [];
            $newConstants = array_diff_key($constantsAfter, $constantsBefore);

            // Capture the newly defined variables
            $variablesAfter = get_defined_vars();
            $newVariables = array_diff_key($variablesAfter, $variablesBefore);

            // Process the variables and combine them with the current configuration
            $newConfig = [];
            foreach ($newVariables as $key => $value) {
                if ($key !== 'file' && $key !== 'constantsBefore' && $key !== 'variablesBefore') {
                    $newConfig[$key] = $value;
                }
            }

            // Save constants as part of the configuration
            foreach ($newConstants as $key => $value) {
                $newConfig[$key] = $value;
            }

            // If the file returned an array, we merge it as well
            if (is_array($configFromFile)) {
                $newConfig = array_merge($newConfig, $configFromFile);
            }

            // Merge existing configuration with new one
            self::$config = array_merge(self::$config, $newConfig);
        } else {
            throw new \Exception("Config file not found: $file");
        }
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
        return self::$config[$key] ?? $default;
    }

    /**
     * Retrieves all configuration values.
     *
     * @return array The entire configuration array.
     */
    public static function all()
    {
        return self::$config;
    }

    /**
     * Gets a list of loaded configuration files.
     *
     * @return array List of loaded files.
     */
    public static function loadedFiles()
    {
        return self::$loadedFiles ?? [];
    }
}
