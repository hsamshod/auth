<?php

/**
 * Class Validator.
 *
 * Validates input data.
 * Validating errors added in error bag.
 */
class Validator
{
    /* allowed image types */
    const IMAGE_TYPES = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];

    const MAX_IMAGE_SIZE = 1024 * 1024 * 2; // 2mb

    /**
     * @var array $error_bag    Errors holder. Validation errors are collected in error_bag.
     */
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

    /**
     * @return array    Errors list.
     */
    public static function getErrors()
    {
        return static::$error_bag;
    }

    /**
     * Adds error into error bag.
     *
     * @param string $key    Error key.
     * @param string $type   Error type.
     */
    public static function addError($key, $type)
    {
        static::$error_bag[$key][$type] = static::msg($type);
    }

    /**
     * Gets error message.
     *
     * @param string $msg   Error type.
     *
     * @return string|null  Error message.
     */
    public static function msg($msg)
    {
        return static::$messages[app()->getLocale()][$msg];
    }

    /**
     * Validating user data for register.
     * @param array $params array with user username, password, email, birth date, sex.
     *
     * @return bool is user data valid.
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
     * Validating user data for login.
     * @param array $params array with user username, password, email, birth date, gender.
     *
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

    /**
     * @param string $string    String to be validated.
     *
     * @return bool             Result if value is valid alphanum.
     */
    public static function alphanum($string)
    {
        $filtered = preg_replace('/[^a-zA-Z0-9_]/', '', $string);
        return strlen($filtered) && $string === $filtered;
    }


    /**
     * @param string $string    String to be validated.
     * @param int $length       String length.
     *
     * @return bool             Result if string has valid length.
     */
    public static function length($string, $length)
    {
        return is_string($string) && strlen($string) >= $length;
    }

    /**
     * Date validation.
     *
     * @param int $month    User birth month.
     * @param int $day      User birth day.
     * @param int $year     User birth year.
     *
     * @return bool         Whether birth date is valid.
     */
    public static function date($year, $month, $day)
    {
        return checkdate($month, $day, $year);
    }


    /**
     * Validates uploaded image format.
     *
     * @param array $uploaded_file    Uploaded file data.
     * @param string $key             File field name in form.
     *
     * @return bool     Whether image format is allowed.
     */
    public static function isPhoto($uploaded_file, $key)
    {
        if ($uploaded_file['error'][$key] === 0) {
                return in_array(exif_imagetype($uploaded_file['tmp_name'][$key]), static::IMAGE_TYPES);
        }

        return false;
    }

    /**
     * Validates if uploaded file is valid photo.
     *
     * @param array $uploaded_file    Uploaded file data.
     * @param string $key             File field name in form.
     *
     * @return bool         Whether the file is valid photo.
     */
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