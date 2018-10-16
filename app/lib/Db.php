<?php

class Db extends Singleton
{
    protected static $_instance;

    /**
     * Holds db connection
     * @var PDO
     */
    private static $_db;

    protected function __construct()
    {
        try {
            list($host, $db, $user, $pass) = Config::getList(['db.host', 'db.name', 'db.user', 'db.pass']);

            static::$_db = new PDO("mysql:host={$host};dbname={$db}", $user, $pass);
            static::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            Logger::error($e->getMessage() . " mysql:host={$host} dbname={$db} user={$user} pass={$pass}");
            app()->terminate();
        }
    }

    /**
     * @return PDO
     */
    public static function conn()
    {
        return (static::inst())::$_db;
    }

    public static function close()
    {
        static::$_db = null;
    }

    /**
     * Query one record from db
     * @param string $sql sql query
     * @return
     */
    public static function queryOne($params)
    {
        try {
            if (empty($params)) {
                throw new Exception('db query: too few arguments');
            }

            $condition = static::buildCondition($params);
            $sql = "select * from {$params['table']} {$condition}";

            $record = static::conn()->query($sql);
            $record->setFetchMode(PDO::FETCH_ASSOC);
            if ($record) {
                return $record->fetch();
            } else {
                return null;
            }
        } catch (Exception  $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }

    /**
     * Query one record from db
     * @param string $sql sql query
     * @return
     */
    public static function insert($params)
    {
        try {
            if (empty($params)) {
                throw new Exception('db inssert: too few arguments');
            }

            $keys = static::wrapWith(array_keys($params['values']), '`');
            $values = static::wrapWith($params['values'], "'");

            $sql = "insert into {$params['table']} ({$keys}) values ({$values})";

            if (static::conn()->exec($sql)) {
                return static::conn()->lastInsertId();
            }

            return null;
        } catch (Exception  $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }


    /**
     * Query one record from db
     * @param string $sql sql query
     * @return
     */
    public static function update($params)
    {
        try {
            if (empty($params) || !is_array($params['values'])) {
                throw new Exception('db update: too few arguments');
            }

            $values = '';
            foreach ($params['values'] as $key => $value) {
                $values .= ($values ? ',' : '') . "{$key}='{$value}'";
            }
            $condition = static::buildCondition($params);

            $sql = "update {$params['table']} set {$values} {$condition}";

            return static::conn()->exec($sql);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }

    private static function wrapWith($params, $wrapper = "'")
    {
        $values = '';
        foreach (array_values($params) as $key => $value) {
            $values .= ($values ? ',' : '') . $wrapper . $value . $wrapper;
        }

        return $values;
    }

    private static function buildCondition($params)
    {
        if (isset($params['where'])) {
            $conditions = [];
            foreach ($params['where'] as $field => $value) {
                $conditions[] = "{$field}='{$value}'";
            }

            return ' where ' . implode(' and ', $conditions);
        }

        return '';
    }
}
