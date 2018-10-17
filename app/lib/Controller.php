<?php

/**
 * Class Controller
 * Controller handles all requests and calls corresponding action.
 */
class Controller
{
    /**
     * Handles all application requests.
     *
     * @return Response     Action response.
     */
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

    /**
     * Renders auth page.
     *
     * @return Response
     */
    public function auth()
    {
        return new Response('auth', ['active_tab' => 'login']);
    }

    /**
     * Authenticates user with login, password.
     * If successful, redirects to user profile.
     * Otherwise renders form with errors.
     *
     * @return Response
     */
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

    /**
     * Registers user.
     * If successful, redirects to user profile.
     * Otherwise renders form with errors.
     *
     * @return Response
     */
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

    /**
     * Renders user profile.
     *
     * @return Response
     */
    public function profile()
    {
        if (app()->user()->isGuest()) {
            Request::redirect('auth');
        }

        return new Response('profile', [
            'errors' => Validator::getErrors()
        ]);
    }

    /**
     * Logs out user.
     *
     * @return Response
     */
    public function logout()
    {
        if (Request::isPost()) {
            app()->user()->logout();

            return new Response('auth', ['active_tab' => 'login']);
        }

        return new Response('404');
    }

    /**
     * Renders 404 page.
     *
     * @return Response
     */
    public function notFound()
    {
        return new Response('404');
    }

    /**
     * Sets application locale settings.
     */
    public function locale()
    {
        if (in_array($locale = Request::getParams('get', 'lang'), app()::LOCALES)) {
            app()->setLocale($locale);
        }

        Request::redirect();
    }
}