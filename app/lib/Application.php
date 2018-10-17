<?php

class Application
{
    /**
     * Supported locales.
     */
    const LOCALES = ['ru', 'en'];

    /**
     * @var $_locale    Application locale.
     */
    private $_locale;

    /**
     * @var User $_user    Application user.
     */
    private $_user;

    private static $_booted_components = [];

    public function __construct()
    {
        session_start();
        $this->loadLocale();
        $this->loadUser();
    }

    /**
     * Get application locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Sets application locale.
     *
     * @param string $locale    Locale to be set.
     */
    public function setLocale($locale)
    {
        $_SESSION['locale'] = $locale;
        $this->loadLocale();
    }

    /**
     * Restores application locale from session.
     * If not found in session, default value from config is set.
     */
    private function loadLocale()
    {
        if (! isset($_SESSION['locale'])) {
            $_SESSION['locale'] = Config::get('app.locale');
        }

        $this->_locale = $_SESSION['locale'];
    }

    /**
     * Restores user using session, cookie.
     */
    private function loadUser()
    {
        $this->_user = new User;
        $this->_user->restore();
    }

    /**
     * Get application user.
     *
     * @return User
     */
    public function user()
    {
        return $this->_user;
    }

    /**
     * Remembers booted singleton classes.
     *
     * @param string $component_class   Booted class name.
     */
    public static function setBooted($component_class)
    {
        static::$_booted_components[] = $component_class;
    }

    /**
     * Outputs message to client browser.
     *
     * @param string $msg
     */
    public function msg($msg)
    {
        echo $msg;
    }

    /**
     * Terminates app.
     * Destructs created singleton instances.
     */
    public function terminate()
    {
        /* @var $component_class Singleton */
        foreach (static::$_booted_components as $component_class) {
            if (method_exists($component_class, 'close')) {
                $component_class::close();
            }
        }

        exit();
    }

    public function run()
    {
        $controller = new Controller();
        $response = $controller->handle();
        $response->send();
        $this->terminate();
    }
}