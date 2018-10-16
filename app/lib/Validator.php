<?php

class Validator
{
    const IMAGE_TYPES = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];

    const MAX_IMAGE_SIZE = 1024 * 1024 * 2; // 2mb

    private static $error_bag = [];

    public static $messages = [
        'en' => [
            'alphanum'   => 'Only letters and numbers allowed',
            'blocked'    => 'This user blocked for 10 minutes',
            'date'       => 'Date is not valid',
            'empty'      => 'Fill the field',
            'image'      => 'Only jpg, png, gif formats allowed',
            'length'     => 'Min password length is 6',
            'login'      => 'Username or password is incorrect',
            'photo_size' => 'Max file size is 2mb',
            'same'       => 'Passwords are not same',
            'unique'     => 'This login is reserved',
        ],

        'ru' => [
            'alphanum'   => 'Только буквы и цифры разрешены',
            'blocked'    => 'Этот пользователь заблокирован на 10 минут',
            'date'       => 'Некорректная дата',
            'empty'      => 'Заполните поле',
            'image'      => 'Разрешены файлы формата jpg, png, gif',
            'length'     => 'Длина пароля меньше 6 символов',
            'login'      => 'Неправильная пара логин/пароль',
            'photo_size' => 'Максимальный размер файла 2мб',
            'same'       => 'Пароли не совпадают',
            'unique'     => 'Логин уже занят',
        ]
    ];

    public static function getErrors()
    {
        return static::$error_bag;
    }

    public static function addError($key, $type)
    {
        static::$error_bag[$key][$type] = static::msg($type);
    }

    public static function msg($msg)
    {
        return static::$messages[app()->getLocale()][$msg];
    }

    /**
     * Validating user data for register
     * @param array $params array with user username, password, email, birth date, sex
     * @return bool is user data valid
     */
    public static function validateRegisterForm($params)
    {
        $valid = true;
        if (!static::alphanum($params['username'])) {
            static::addError('register-username', 'alphanum');
            $valid = false;
        } else {
            if ((new User)->findBy(['username' => $params['username']])) {
                static::addError('register-username', 'unique');
                $valid = false;
            }
        }

        if (!static::length($params['password'], 6)) {
            static::addError('register-password', 'length');
            $valid = false;
        }

        if ($params['password'] !== $params['password_repeat']) {
            static::addError('register-password_repeat', 'same');
            $valid = false;
        }

        if (!static::date($params['year'], $params['month'], $params['day'])) {
            static::addError('register-date', 'date');
            $valid = false;
        }

        return $valid;
    }

    /**
     * Validating user data for login
     * @param array $params array with user username, password, email, birth date, gender
     * @return bool is user data valid
     */
    public static function validateLoginForm($params)
    {
        if (!static::alphanum($params['username'])) {
            static::addError('login-username', 'alphanum');

            return false;
        }

        return true;
    }

    public static function alphanum($string)
    {
        $filtered = preg_replace('/[^a-zA-Z0-9_]/', '', $string);
        return strlen($filtered) && $string === $filtered;
    }

    public static function length($string, $length)
    {
        return is_string($string) && strlen($string) >= $length;
    }

    /**
     * Date validation
     * @param int $month user birth month
     * @param int $day user birth day
     * @param int $year user birth year
     * @return bool whether birth date is valid
     */
    public static function date($year, $month, $day)
    {
        return checkdate($month, $day, $year);
    }

    public static function isPhoto($uploaded_file, $key)
    {
        if ($uploaded_file['error'][$key] === 0) {
                return in_array(exif_imagetype($uploaded_file['tmp_name'][$key]), static::IMAGE_TYPES);
        }

        return false;
    }

    public static function validateUploadedPhoto($uploaded_photo, $key)
    {
        $valid = true;
        if (!static::isPhoto($uploaded_photo, $key)) {
            static::addError($key, 'image');
            $valid = false;
        }

        if ($uploaded_photo['size'][$key] > static::MAX_IMAGE_SIZE) {
            static::addError($key, 'photo_size');
            $valid = false;
        }

        return $valid;
    }
}