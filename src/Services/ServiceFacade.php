<?php

namespace Snap\Services;

use RuntimeException;
use Snap\Core\Snap;

/**
 * Base Service Facade class.
 */
abstract class ServiceFacade
{
    /**
     * Holds all resolved facade instances.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Provide static access to the underlying instance methods.
     *
     * @param string $method The method being called.
     * @param array  $args   The method arguments.
     * @return mixed
     *
     * @throws \Hodl\Exceptions\ContainerException If the service name is malformed.
     * @throws \Hodl\Exceptions\NotFoundException When the service does not exist within the container.
     */
    public static function __callStatic(string $method, array $args)
    {
        if (!isset(static::$instances[static::getServiceName()])) {
            static::resolveService();
        }

        static::whenResolving(static::$instances[static::getServiceName()]);

        return static::$instances[static::getServiceName()]->{$method}(...$args);
    }

    /**
     * Get the underlying instance from  the container.
     *
     * @return object The resolved instance.
     *
     * @throws \Hodl\Exceptions\ContainerException If the service name is malformed.
     * @throws \Hodl\Exceptions\NotFoundException When the service does not exist within the container.
     */
    public static function getRootInstance()
    {
        if (!isset(static::$instances[static::getServiceName()])) {
            static::resolveService();
        }

        return static::$instances[static::getServiceName()];
    }

    /**
     * Resolve the underlying instance from the service container.
     *
     * @throws \Hodl\Exceptions\ContainerException If the service name is malformed.
     * @throws \Hodl\Exceptions\NotFoundException When the service does not exist within the container.
     */
    protected static function resolveService()
    {
        static::$instances[static::getServiceName()] = Snap::getContainer()->get(static::getServiceName());
    }

    /**
     * Allows additional actions to be performed whenever the root instance if resolved.
     *
     * @param object $instance The resolved root instance from the container.
     */
    protected static function whenResolving($instance)
    {
    }

    /**
     * Get the name of the service to provide static access to.
     *
     * Must exist within the container.
     *
     * @return string
     *
     * @throws RuntimeException If this method is not overwritten.
     */
    abstract protected static function getServiceName(): string;
}