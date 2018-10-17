<?php

class Config extends Singleton
{
    const CONFIG_FILE_PATH = 'config.ini';

    protected static $_instance;

    private static $_settings = [];

    protected function __construct()
    {
        try {
            $file_info = new SplFileInfo(static::CONFIG_FILE_PATH);
            if ($file_info->isReadable()) {
                $_file = $file_info->openFile('r');
                $content = $_file->fread($_file->getSize());
                static::$_settings = parse_ini_string($content, true);
            }
        } catch (Exception $e) {
            app()->msg("Can't instantiate " . static::class);
            app()->terminate();
        }
    }

    /**
     * Get key value from loaded config.
     *
     * @param string $key      Key which value should be returned.
     *
     * @return mixed    Value of key.
     */
    private static function key($key)
    {
        $path = explode('.', $key);
        $value = static::$_settings;

        while (count($path)) {
            $curr_key = array_shift($path);

            if (array_key_exists($curr_key, $value)) {
                $value = $value[$curr_key];
            } else {
                $value = null;
                break;
            }
        }

        return $value;
    }

    /**
     * Get key value from loaded config.
     *
     * @param string $key   Key, which value is returned.
     *
     * @return mixed        Value of key.
     */
    public static function get($key)
    {
        return static::inst()->key($key);
    }

    /**
     * Get values of multiple keys.
     *
     * @param array $keys   Keys, which values should be retrieved.
     *
     * @return array        Values of keys.
     */
    public static function getList($keys)
    {
        if (! is_array($keys)) {
            $keys = [$keys];
        }

        $values = [];
        foreach ($keys as $key) {
            $values[$key] = static::get($key);
        }

        return array_values($values);
    }
}

