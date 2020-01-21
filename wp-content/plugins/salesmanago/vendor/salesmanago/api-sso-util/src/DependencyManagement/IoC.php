<?php

namespace SALESmanago\DependencyManagement;

use SALESmanago\Entity\Settings;
use SALESmanago\Exception\SalesManagoException;


class IoC
{
    protected static $instance;
    protected static $registry = [];

    public static function register($name, \Closure $resolve)
    {
        static::$registry[$name] = $resolve;
    }

    public static function registered($name)
    {
        return array_key_exists($name, static::$registry);
    }

    /**
     * @throws SalesManagoException
     * @param string $name
     * @var $resolve
     * @return Settings object
     */
    public static function extend($name, \Closure $resolve)
    {
        if (!static::registered($name)) {
            throw new SalesManagoException('Container is not registered', 9);
        }

        static::$registry[$name] = $resolve;

        $name = static::$registry[$name];

        return $name(new self);
    }

    /**
     * @throws SalesManagoException
     * @param string $name
     * @return Settings object
     */
    public static function resolve($name)
    {
        if (!static::registered($name)) {
            throw new SalesManagoException('Container is not registered', 9);
        }

        $name = static::$registry[$name];

        return $name(new self);
    }

    public function __clone()
    {
        return false;
    }

    public function __wakeup()
    {
        return false;
    }

    public static function init()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
