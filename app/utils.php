<?php

function app()
{
    global $app;
    return $app;
}

function random_string($length = 16)
{
    return bin2hex(random_bytes($length));
}

function encrypt($data)
{
    $ivlen = openssl_cipher_iv_length(Config::get('app.encrypt'));
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt(serialize($data), Config::get('app.encrypt'), Config::get('app.key'), OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, Config::get('app.key'), true);
    return base64_encode($iv . $hmac . $ciphertext_raw);
}

function decrypt($enc_data)
{
    $c = base64_decode($enc_data);
    $ivlen = openssl_cipher_iv_length(Config::get('app.encrypt'));
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, 32);
    $ciphertext_raw = substr($c, $ivlen + 32);
    $data = openssl_decrypt($ciphertext_raw, Config::get('app.encrypt'), Config::get('app.key'), OPENSSL_RAW_DATA, $iv);

    if (!$data) {
        Logger::error('hasher error');
//        app()->terminate();
    }

    $calcmac = hash_hmac('sha256', $ciphertext_raw, Config::get('app.key'), true);
    if (hash_equals($hmac, $calcmac))
    {
        return unserialize($data);
    }

    return null;
}

/**
 * Password hash generator.
 * Generates password hash
 * @param string $errorMsg error message
 * @return string generated markup
 */
function get_password_hash($string)
{
    return password_hash($string, PASSWORD_BCRYPT);
}

/**
 * User password check
 * @param string $hash user password hash from db
 * @param string $password password from login form
 * @return bool whether the passwords are same
 */
function check_password_hash($password, $hash)
{
    return password_verify($password, $hash);
}
