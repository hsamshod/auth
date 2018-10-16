<?php

class Application
{
    const LOCALES = ['ru', 'en'];

    private $_locale;
    private $_user;

    private static $_booted_components = [];

    public function __construct()
    {
        session_start();
        $this->loadLocale();
        $this->loadUser();
    }

    public function getLocale()
    {
        return $this->_locale;
    }

    public function setLocale($locale)
    {
        $_SESSION['locale'] = $locale;
        $this->loadLocale();
    }

    private function loadLocale()
    {
        if (! isset($_SESSION['locale'])) {
            $_SESSION['locale'] = Config::get('app.locale');
        }

        $this->_locale = $_SESSION['locale'];
    }

    private function loadUser()
    {
        $this->_user = new User;
        $this->_user->restore();
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->_user;
    }

    /**
     * @param $component_class
     * @todo
     */
    public static function setBooted($component_class)
    {
        static::$_booted_components[] = $component_class;
    }

    /**
     * Outputs message
     *
     * @param string $msg
     */
    public function msg($msg)
    {
        echo $msg;
    }

    /**
     * Terminates app
     */
    public function terminate()
    {
        /**
         * @var $component_class Singleton
         */
        foreach (static::$_booted_components as $component_class) {
            if (method_exists($component_class, 'close')) {
                $component_class::close();
            }
        }

        $f = fopen("logs/test.txt", 'a');
        fwrite($f, 'req: ' . date('H:i:s ') . $_SERVER['REQUEST_URI'] . "\n");
        fwrite($f, 'app ended ' . date('H:i:s ') . "\n\n\n");
        fclose($f);
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