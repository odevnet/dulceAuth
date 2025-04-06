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
 * Class DulceContainer
 *
 * This is a simple class that provides a simple service container.
 *
 * @package src
 *
 * @version 1.0
 */
class DulceContainer
{
    /**
     * The array that stores services.
     *
     * @var array
     */
    private $services = [];

    /**
     * Adds a service to the container.
     *
     * @param string $name The name of the service.
     * @param callable $resolver The resolver callable to create the service instance.
     * @param bool $shared Whether the service should be shared (singleton).
     */
    public function addService(string $name, callable $resolver, bool $shared = false): void
    {
        $this->services[$name] = [
            'resolver' => $resolver,
            'shared' => $shared,
            'instance' => null,
        ];
    }

    /**
     * Gets a service from the container.
     *
     * @param string $name The name of the service.
     * @return mixed The service instance.
     * @throws \Exception If the service is not found.
     */
    public function get(string $name): mixed
    {
        if (!isset($this->services[$name])) {
            throw new \Exception("Service '$name' not found");
        }

        $service = $this->services[$name];

        if ($service['shared'] && $service['instance'] !== null) {
            return $service['instance'];
        }

        $instance = call_user_func($service['resolver'], $this);

        if ($service['shared']) {
            $service['instance'] = $instance;
        }

        return $instance;
    }
}
