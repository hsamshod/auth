<?php

class Response
{
    private $_view;

    private $_data;

    const VIEWS_DIR = 'assets' . DIRECTORY_SEPARATOR . 'views';

    public function __construct($view = null, $data = [])
    {
        $this->_view = $view;
        $this->_data = $data;
    }

    private function isEmptyResponse()
    {
        return !$this->_view;
    }

    private function resolveView($view = null, $base = null)
    {
        $templates = [];
        $view = $view ?: $this->_view;
        $templates[] =
            static::VIEWS_DIR . DIRECTORY_SEPARATOR .
            app()->getLocale() . DIRECTORY_SEPARATOR .
            ($base ? $base . DIRECTORY_SEPARATOR : '')
            . $view . '.php';
        $templates[] = static::VIEWS_DIR . DIRECTORY_SEPARATOR . ($base ? $base . DIRECTORY_SEPARATOR : '') . $view . '.php';

        $found_view = null;

        foreach ($templates as $template) {
            if ((new SplFileInfo($template))->isFile()) {
                $found_view = $template;
                break;
            }
        }

        if (!$found_view) {
            Logger::error('views not found ' . implode($templates));
            app()->terminate();
        }

        return $found_view;
    }

    public function send()
    {
        Request::sendQueuedCookies();
        if ($this->isEmptyResponse()) {
            return;
        }

        $this->_data['content'] = $this->render($this->resolveView());
        echo $this->render($this->resolveView('layout'));
    }


    /**
     * Renders view file
     * @param string $view_file view file name
     *
     * @return string rendered
     */
    private function render($view_file)
    {
        $f = fopen("logs/test.txt", 'a');
        fwrite($f, 'view: ' . date('H:i:s ') . $view_file . "\n");
        fclose($f);

        extract($this->_data);
        ob_start();
        require $view_file;
        return ob_get_clean();
    }

    public function renderPartial($view_file, $base)
    {
        return $this->render($this->resolveView($view_file, $base));
    }

    public function e($field, $error = null)
    {
        $err = isset($this->_data['errors']) ? $this->_data['errors'] : null;
        if ($error) {
            return $err && isset($err[$field]) && isset($err[$field][$error]);
        } else {
            return $err && isset($err[$field]);
        }
    }
}