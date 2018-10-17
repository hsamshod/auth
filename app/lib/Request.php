<?php

/**
 * Class Request.
 */
class Request extends Singleton
{
    protected static $_instance;

    private static $_cookies = [];

    private static $_form_token = null;

    protected function __construct()
    {
        static::$_cookies = isset($_COOKIE['app']) ? decrypt($_COOKIE['app']) : [];
    }

    /**
     * Get user specified with key cookie.
     *
     * @param string $key   Cookie key should be returned.
     * @return mixed        Cookie value. Null returned if not found.
     */
    public static function getCookie($key)
    {
        $cookies = (static::inst())::$_cookies;
        return isset($cookies[$key]) ? $cookies[$key] : (isset($_COOKIE[$key]) ? $_COOKIE[$key] : null);
    }

    /**
     * Add cookie to cookie bag which is send to user with application response.
     *
     * @param string $key     Cookie key.
     * @param mixed $value    Cookie value.
     */
    public static function setQueuedCookie($key, $value)
    {
        (static::inst())::$_cookies[$key] = $value;
        $_COOKIE[$key] = $value;
    }

    /**
     * Sends cookie immediately to user.
     *
     * @param string $key     Cookie key.
     * @param mixed $value    Cookie value.
     */
    public static function sendCookie($key, $value)
    {
        setcookie($key, encrypt($value), time() + 7 * 24 * 3600, '/', '', false,true);
    }

    /**
     * Sends collected cookies in bag to user.
     */
    public static function sendQueuedCookies()
    {
        static::sendCookie('app', (static::inst())::$_cookies);
    }

    /**
     * Removes cookies.
     */
    public static function unsetCookies()
    {
        (static::inst())::$_cookies = [];
    }

    /**
     * Generates form token for preventing csrf attacks.
     *
     * @return string   Generated token.
     */
    public static function getFormToken()
    {
        if (!static::$_form_token) {
            static::$_form_token = random_string();
            $_SESSION['form_token'] = static::$_form_token;
        }

        return static::$_form_token;
    }

    /**
     * Run application request middlewares.
     *
     * @return bool     Whether middlewares are passed.
     */
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

    /**
     * Resolve current request action.
     *
     * @return string       Action name.
     */
    public static function getAction()
    {
        if (isset($_GET['action'])) {
            return $_GET['action'];
        } else {
            return app()->user()->isGuest() ? 'auth' : 'profile';
        }
    }

    /**
     * Redirects to specified action url.
     */
    public static function redirect($action = null)
    {
        Request::sendQueuedCookies();
        header('Location: ' . ($action ? "/index.php?action={$action}" : '/index.php'));
        app()->terminate();
    }

    /**
     * Get current request url.
     * @return string   Current request url.
     */
    public static function getUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Determine if current request method is POST.
     *
     * @return bool
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get post data.
     *
     * @param string $field     Data field
     * @param string $key       Data key.
     *
     * @return mixed            Post data with specified key and value.
     */
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
     * @param string $type      Type of request params to be returned
     *
     * return mixed
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