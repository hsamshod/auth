<?php

/**
 * Class Singleton
 * Abstract singleton class.
 * Instances should have protected static field $_instance
 */
abstract class Singleton
{
    protected function __construct()
    {
    }

    final protected function __clone()
    {
    }

    /**
     * Returns single instance of class
     *
     * @return static
     */
    final public static function inst()
    {
        if (!static::$_instance instanceof static) {
            static::$_instance = new static();
            Application::setBooted(static::class);

            $f = fopen("logs/test.txt", 'a');
            fwrite($f, 'sig: ' . date(' H:i:s ') . static::class . "\n");
            fclose($f);
        }

        return static::$_instance;
    }

    public static function close()
    {
    }
}