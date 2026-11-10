<?php

use Legacy\System\Classes\DB;

function login($userId, $url = false, $remember = true)
{

    // remove old accesses
    DB::delete('auth_tokens WHERE expiration < NOW() AND userID=?', [$userId]);

    // remember me
    if ($remember) {
        $selector = bin2hex(random_bytes(6));
        $validator = bin2hex(random_bytes(12));
        $validatorHash = hash('sha256', $validator);

        DB::add('auth_tokens (selector, validator, userID, expiration) VALUES (?, ?, ?, (NOW() + INTERVAL 1 MONTH))', [$selector, $validatorHash, $userId]);

        setcookie('auth_token', $selector.'_'.$validator, time() + 60 * 60 * 24 * 30);
    }

    if (function_exists('associate_cart')) {
        $customerId = DB::find('id FROM ecommerce_customers WHERE userId=?', [$userId]);
        associate_cart($customerId);
    }

    // login
    $_SESSION['userId'] = $userId;

    if (isset($_SESSION['login_attempts'])) {
        unset($_SESSION['login_attempts']);
    }

    // redirect
    if ($url) {
        legacy_redirect($url);
    }
}

// normal login
function standard_login($username, $password, $url = false, $remember = true)
{

    // set redirect location
    if (! $url) {
        $url = sysConfig('logged-redirect') ?: '/';
    }

    // fetch user data
    $userData = DB::row('id, password FROM users WHERE (username=? OR email=?)', [$username, $username]);

    // if it's a valid username
    if ($userData) {
        $userId = $userData['id'];
        $dbPassword = $userData['password'];

        // update old password
        if ($dbPassword[0] != '$' && ($password == $dbPassword || md5($password) == $dbPassword)) {
            $dbPassword = password_hash($password, PASSWORD_DEFAULT);
            DB::update('users SET password=? WHERE id=?', [$dbPassword, $userId]);
        }

        // successful login
        if (password_verify($password, $dbPassword)) {
            login($userId, $url, $remember);
        }
    }

    // unsuccessful login
    if (isset($_SESSION['login-attempts'])) {
        $_SESSION['login-attempts']++;
    } else {
        $_SESSION['login-attempts'] = 1;
    }

    return 'error-wrong';
}

// direct login
function login_direct($token)
{
    $validToken = false;
    $token = explode('_', $token);
    $selector = $token[0];
    $userData = DB::row('userId, validator FROM auth_tokens WHERE selector=? AND expiration>NOW()', [$selector]);

    if (isset($token[1]) && $userData) {
        $validator = $token[1];
        $validatorHash = hash('sha256', $validator);

        if (hash_equals($validatorHash, $userData['validator'])) {
            $userId = $userData['userId'];
            login($userId);
        }
    }

    if (! $validToken) {
        setcookie('auth_token', '', -1, '/');
    }
}

function logout($url = '')
{

    global $user;

    setcookie('auth_token', '', -1);

    DB::delete('auth_tokens WHERE userID=?', $user->id);

    session_destroy();

    legacy_redirect($url);
}
