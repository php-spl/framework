<?php

namespace Classes\Proxies;

use Closure;
use Classes\Container;
use RuntimeException;

abstract class Proxy
{
    /**
     * The application instance being Proxied.
     *
     * @var Container
     */
    protected static $app;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;

    /**
     * Run a Closure when the Proxy has been resolved.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function resolved(Closure $callback)
    {
        $accessor = static::getProxyAccessor();

        if (static::$app->resolved($accessor) === true) {
            $callback(static::getProxyRoot());
        }

        static::$app->afterResolving($accessor, function ($service) use ($callback) {
            $callback($service);
        });
    }

    /**
     * Initiate a partial mock on the Proxy.
     *
     * @return \Mockery\MockInterface
     */
    public static function partialMock()
    {
        $name = static::getProxyAccessor();

        $mock = static::isMock()
            ? static::$resolvedInstance[$name]
            : static::createFreshMockInstance();

        return $mock->makePartial();
    }

    /**
     * Initiate a mock expectation on the Proxy.
     *
     * @return \Mockery\Expectation
     */
    public static function shouldReceive()
    {
        $name = static::getProxyAccessor();

        $mock = static::isMock()
                    ? static::$resolvedInstance[$name]
                    : static::createFreshMockInstance();

        return $mock->shouldReceive(...func_get_args());
    }

    /**
     * Initiate a mock expectation on the Proxy.
     *
     * @return \Mockery\Expectation
     */
    public static function expects()
    {
        $name = static::getProxyAccessor();

        $mock = static::isMock()
            ? static::$resolvedInstance[$name]
            : static::createFreshMockInstance();

        return $mock->expects(...func_get_args());
    }

    /**
     * Get the mockable class for the bound instance.
     *
     * @return string|null
     */
    protected static function getMockableClass()
    {
        if ($root = static::getProxyRoot()) {
            return get_class($root);
        }
    }

    /**
     * Hotswap the underlying instance behind the Proxy.
     *
     * @param  mixed  $instance
     * @return void
     */
    public static function swap($instance)
    {
        static::$resolvedInstance[static::getProxyAccessor()] = $instance;

        if (isset(static::$app)) {
            static::$app->instance(static::getProxyAccessor(), $instance);
        }
    }

    /**
     * Get the root object behind the Proxy.
     *
     * @return mixed
     */
    public static function getProxyRoot()
    {
        return static::resolveProxyInstance(static::getProxyAccessor());
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getProxyAccessor()
    {
        throw new RuntimeException('Proxy does not implement getProxyAccessor method.');
    }

    /**
     * Resolve the Proxy root instance from the container.
     *
     * @param  object|string  $name
     * @return mixed
     */
    protected static function resolveProxyInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        if (static::$app) {
            return static::$resolvedInstance[$name] = static::$app->get($name);
        }
    }

    /**
     * Clear a resolved Proxy instance.
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = [];
    }

    /**
     * Get the application instance behind the Proxy.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public static function getProxyApplication()
    {
        return static::$app;
    }

    /**
     * Set the application instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public static function setProxyApplication($app)
    {
        static::$app = $app;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getProxyRoot();

        if (! $instance) {
            throw new RuntimeException('A Proxy root has not been set.');
        }

        return $instance->$method(...$args);
    }
}