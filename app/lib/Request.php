<?php

class Request extends Singleton
{
    protected static $_instance;

    private static $_cookies = [];

    private static $_form_token = null;

    protected function __construct()
    {
        static::$_cookies = isset($_COOKIE['app']) ? decrypt($_COOKIE['app']) : [];
    }

    public static function getCookie($key)
    {
        $cookies = static::inst()::$_cookies;
        return isset($cookies[$key]) ? $cookies[$key] : (isset($_COOKIE[$key]) ? $_COOKIE[$key] : null);
    }

    public static function setQueuedCookie($key, $value)
    {
        (static::inst())::$_cookies[$key] = $value;
        $_COOKIE[$key] = $value;
    }

    public static function sendCookie($key, $value)
    {
        setcookie($key, encrypt($value), time() + 7 * 24 * 3600, '/', '', false,true);
    }

        public static function sendQueuedCookies()
    {
        static::sendCookie('app', (static::inst())::$_cookies);
    }

    public static function unsetCookies()
    {
        return (static::inst())::$_cookies = [];
    }

    public static function getFormToken()
    {
        if (!static::$_form_token) {
            static::$_form_token = random_string();
            $_SESSION['form_token'] = static::$_form_token;
        }

        return static::$_form_token;
    }

    public static function runMiddlewares()
    {
        if (static::isPost()) {
            $passed = isset($_SESSION['form_token']) && $_SESSION['form_token'] === static::postInput('form_token');
            if (!$passed) {
                static::redirect();
            }
        }

        return true;
    }

    public static function getAction()
    {
        if (isset($_GET['action'])) {
            return $_GET['action'];
        } else {
            return app()->user()->isGuest() ? 'auth' : 'profile';
        }
    }

    public static function redirect($action = null)
    {
        Request::sendQueuedCookies();
        header('Location: ' . ($action ? "/index.php?action={$action}" : '/index.php'));
        app()->terminate();
    }

    public static function getUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function postInput($field, $key = null)
    {
        $value = $_POST[$field];

        if ($value && $key) {
            return $value[$key];
        }

        return $value;
    }

    /**
     * Returns request global params (GET, POST, FILES)
     *
     * @param string $type Type of request params to be returned
     *
     * return array
     */
    public static function getParams($type = '*', $field = null)
    {
        $param = null;
        switch ($type) {
            case 'get':
                $param = $_GET;
                break;
            case 'post':
                $param = $_POST;
                break;
            case 'files':
                $param = $_FILES;
                break;
            default:
                return compact('_GET', '_POST', '_FILES') + ['method' => $_SERVER['REQUEST_METHOD']];
        }

        return isset($param[$field]) ? $param[$field] : null;
    }
}