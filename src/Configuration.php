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
    private static $config = [];

    /**
     * Loads configuration from a file.
     *
     * @param string $file Path to the configuration file.
     * @throws \Exception if the file is not found.
     *
     * @since 3.0.0
     */
    public static function load($file)
    {
        if (file_exists($file)) {
            // Captura las constantes y define los valores
            $constantsBefore = get_defined_constants(true)['user'] ?? [];
            $variablesBefore = get_defined_vars();

            // Ejecutar el archivo de configuración
            $configFromFile = include $file;

            // Capturar las nuevas constantes definidas
            $constantsAfter = get_defined_constants(true)['user'] ?? [];
            $newConstants = array_diff_key($constantsAfter, $constantsBefore);

            // Capturar las nuevas variables definidas
            $variablesAfter = get_defined_vars();
            $newVariables = array_diff_key($variablesAfter, $variablesBefore);

            // Procesar las variables y combinarlas con la configuración actual
            $newConfig = [];
            foreach ($newVariables as $key => $value) {
                if ($key !== 'file' && $key !== 'constantsBefore' && $key !== 'variablesBefore') {
                    $newConfig[$key] = $value;
                }
            }

            // Guardar las constantes como parte de la configuración
            foreach ($newConstants as $key => $value) {
                $newConfig[$key] = $value;
            }

            // Si el archivo devolvió un array, lo fusionamos también
            if (is_array($configFromFile)) {
                $newConfig = array_merge($newConfig, $configFromFile);
            }

            // Combinar la configuración existente con la nueva
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
     *
     * @since 3.0.0
     */
    public static function all()
    {
        return self::$config;
    }

    /**
     * Gets a list of loaded configuration files.
     *
     * @return array List of loaded files.
     *
     * @since 3.0.0
     */
    public static function loadedFiles()
    {
        return self::$loadedFiles ?? [];
    }
}
