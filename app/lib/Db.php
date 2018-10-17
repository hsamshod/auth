<?php

/**
 * Class Db.
 * Establishes database connection and runs queries.
 */
class Db extends Singleton
{
    /* @var static $_instance   Class instance */
    protected static $_instance;

    /* @var PDO $_db    PDO connection instance. */
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
     * Get active database connection.
     *
     * @return PDO
     */
    public static function conn()
    {
        return (static::inst())::$_db;
    }

    /**
     * Close active database connection.
     */
    public static function close()
    {
        static::$_db = null;
    }

    /**
     * Query one record from database.
     *
     * @param array $params     Query params.
     *
     * @return array|null       Query result if found. Null otherwise.
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
     * Insert data.
     *
     * @param array $params     Record data to be stored.
     *
     * @return null|int         Id of created row. Null if record not saved.
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
     * Update record in database.
     *
     * @param array $params     Record data to be updated.
     *
     * @return bool     Record updating result.
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

    /**
     * Prepares data for using in sql.
     *
     * @param array $params     Values to be wrapped.
     * @param string $wrapper   Wrapper value.
     *
     * @return string   Built string.
     *
     * @example wrap([1,2,3], '"') => "1","2","3"
     */
    private static function wrapWith($params, $wrapper = "'")
    {
        $values = '';
        foreach (array_values($params) as $key => $value) {
            $values .= ($values ? ',' : '') . $wrapper . $value . $wrapper;
        }

        return $values;
    }

    /**
     * Builds where condition for using in sql query.
     *
     * @param array $params   Key-value pairs used in condition.
     *
     * @return string       Build condition.
     *
     * @example ['id' => 2, 'name' => 'foo'] => where id = '2' and name = 'foo'.
     */
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
