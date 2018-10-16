<?php

class Controller
{
    public function handle()
    {
        Request::runMiddlewares();

        $action = Request::getAction();
        if (method_exists($this, $action)) {
            return $this->$action() ?: new Response();
        } else {
            return $this->notFound();
        }
    }

    public function auth()
    {
        return new Response('auth', ['active_tab' => 'login']);
    }

    public function login()
    {
        $params = Request::getParams('post', 'login');
        if (app()->user()->login($params)) {
            Request::redirect('profile');
        }

        return new Response('auth', [
            'active_tab' => 'login',
            'errors' => Validator::getErrors()
        ]);
    }

    public function register()
    {
        $params = Request::getParams('post', 'register');
        if (app()->user()->register($params)) {
            Attachment::create(Request::getParams('files', 'register'), 'photo');
            Request::redirect('profile');
        }

        return new Response('auth', [
            'active_tab' => 'register',
            'errors'     => Validator::getErrors()
        ]);
    }

    public function profile()
    {
        if (app()->user()->isGuest()) {
            Request::redirect('auth');
        }

        return new Response('profile', [
            'errors' => Validator::getErrors()
        ]);
    }

    public function logout()
    {
        if (Request::isPost()) {
            app()->user()->logout();

            return new Response('auth', ['active_tab' => 'login']);
        }

        return new Response('404');
    }

    public function notFound()
    {
        return new Response('404');
    }

    public function locale()
    {
        if (in_array($locale = Request::getParams('get', 'lang'), app()::LOCALES)) {
            app()->setLocale($locale);
        }

        Request::redirect();
    }
}