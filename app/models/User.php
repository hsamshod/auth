<?php

class User
{
    private $_attributes = [];

    const TABLE = 'users';
    const MAX_LOGIN_ATTEMPTS = 3;

    const GENDERS = ['m', 'f'];

    function __get($field)
    {
        return isset($this->_attributes[$field]) ? $this->_attributes[$field] : null;
    }

    public function isGuest()
    {
        return !$this->id;
    }

    public function restore()
    {
        if (isset($_SESSION['auth']) && (Request::getCookie('_token') && Request::getCookie('_token') === $_SESSION['auth']['_token'])) {
            $found = $this->findBy(['id' => $_SESSION['auth']['id']]);
            if ($found !== null) {
                $this->_attributes = $found;
                $this->refreshTokens();
            }
        } elseif (Request::getCookie('_token') && Request::getCookie('id')) {
            $found = $this->findBy(['_token' => Request::getCookie('_token'), 'id' => Request::getCookie('id')]);
            if ($found !== null) {
                $this->_attributes = $found;
                $this->refreshTokens();
            }
        }
    }

    public function login($params)
    {
        $this->logout();
        if (Validator::validateLoginForm($params)) {
            $username = $params['username'];
            $password = $params['password'];

            $user = $this->findBy(['username' => $username]);
            if ($user) {
                if ($user['blocked'] > 0) {
                    Validator::addError('login-username', 'blocked');
                } elseif (password_verify($password, $user['password'])) {
                    $this->_attributes = $user;
                    if ($this->attempts > 0) {
                        Db::update([
                            'table' => static::TABLE,
                            'where' => ['id' => $this->id],
                            'values' => ['attempts' => 0, 'blocked' => 0]
                        ]);
                    }

                    $this->refreshTokens();
                    return true;
                } else {
                    $user['attempts'] += 1;

                    if ($user['attempts'] >= static::MAX_LOGIN_ATTEMPTS) {
                        $blocked = 1;
                    }

                    Db::update([
                        'table' => static::TABLE,
                        'where' => ['id' => $user['id']],
                        'values' => ['attempts' => $this->attempts, 'blocked' => $blocked]
                    ]);

                    Validator::addError('login-username', 'login');
                }
            } else {
                Validator::addError('login-username', 'login');
            }

        }

        return false;
    }

    public function register($params)
    {
        $this->logout();

        if (Validator::validateRegisterForm($params)) {
            $username = $params['username'];
            $password = $params['password'];
            $birthday = $params['year'] . '-' . $params['month'] . '-' . $params['day'];
            $sex = in_array($params['sex'], static::GENDERS) ? $params['sex'] : static::GENDERS[0];

            $insert_id = Db::insert([
                'table' => static::TABLE,
                'values' => [
                    'username' => $username,
                    'password' => get_password_hash($password),
                    'sex'      => $sex,
                    'birthday' => $birthday,
                    'photo'    => Attachment::DEFAULT_PHOTO
                ]
            ]);

            if ($insert_id) {
                $this->_attributes = $this->findBy(['id' => $insert_id]);
                $this->refreshTokens();
                return true;
            }
        }
        return false;
    }

    public function findBy($params = [])
    {
        return Db::queryOne(['table' => static::TABLE, 'where' => array_merge($params, ['blocked' => 0])]);
    }

    /**
     * Sign out user
     */
    public function logout()
    {
        Request::unsetCookies();
        unset($_SESSION['auth']);
    }

    private function refreshTokens()
    {
        $token = random_string();
        Db::update([
            'table' => static::TABLE,
            'where' => ['id' => $this->id],
            'values' => ['_token' => $token]
        ]);
        $_SESSION['auth']['_token'] = $this->_token = $token;
        Request::setQueuedCookie('_token', $token);

        $_SESSION['auth']['id'] = $this->id;
        Request::setQueuedCookie('id', $this->id);
    }

    public function setPhoto($file_name)
    {
        return Db::update([
            'table' => static::TABLE,
            'where' => ['id' => $this->id],
            'values' => ['photo' => $file_name]
        ]);
    }
}