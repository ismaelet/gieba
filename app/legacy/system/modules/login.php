<?php

use Legacy\System\Classes\DB;

function login($username, $password, $url = false, $remember = false)
{

    global $user;

    // fetch user data
    $row = DB::row('id, password FROM users WHERE (username=? || mail=?)', [$username, $username]);

    if ($row) {

        $id = $row['id'];
        $db_password = $row['password'];

        // update old MD5 passwords
        if ($db_password[0] != '$' && md5($password) == $db_password) {

            $db_password = password_hash($password, PASSWORD_DEFAULT);
            DB::update('users SET password=? WHERE id=?', [$db_password, $id]);
        }

        // login if the password is correct
        if (password_verify($password, $db_password)) {

            // set redirect location
            if (! $url) {
                $url = sysVar('logged_redirect');
            }

            // login
            $_SESSION['userID'] = $id;

            if (isset($_SESSION['login_attempts'])) {
                unset($_SESSION['login_attempts']);
            }

            // remember me
            if ($remember) {

                $selector = bin2hex(random_bytes(6));
                $validator = bin2hex(random_bytes(12));
                $validatorHash = hash('sha256', $validator);

                setcookie('auth_token', $selector.'_'.$validator, time() + 60 * 60 * 24 * 30);

                if (DB::exists('auth_tokens WHERE userID=?', [$id])) {
                    DB::update('auth_tokens SET selector=?, validator=?, expiration=(NOW() + INTERVAL 1 MONTH) WHERE userID=?', [$selector, $validatorHash, $id]);
                } else {
                    DB::add('auth_tokens (selector, validator, user, expiration) VALUES (?, ?, ?, (NOW() + INTERVAL 1 MONTH))', [$selector, $validatorHash, $id]);
                }
            }

            // transfer session cart data to user if set
            if (isset($_SESSION['buyer'])) {

                $temp_cartID = DB::find('id FROM ecommerce_carts WHERE buyerID=? AND status=0', [$_SESSION['buyer']]);

                if ($temp_cartID) {

                    $user_cartID = DB::find('id FROM ecommerce_carts WHERE buyerID=? AND status=0', [$id]);

                    // if the user has a cart transfer the session orders to it
                    if ($user_cartID) {

                        DB::update('ecommerce_orders SET cartID=? WHERE cartID=?', [$user_cartID, $temp_cartID]);
                        DB::delete('ecommerce_carts WHERE id=?', [$temp_cartID]);

                        // otherwise convert the session cart to the user cart
                    } else {

                        DB::update('ecommerce_carts SET buyerID=? WHERE id=?', [$id, $temp_cartID]);
                        DB::update('ecommerce_orders SET buyerID=? WHERE cartID=?', [$id, $temp_cartID]);
                    }
                }

                unset($_SESSION['buyer']);
            }

            // redirect
            legacy_redirect($url);
        }
    }

    if (isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts']++;
    } else {
        $_SESSION['login_attempts'] = 1;
    }

    return 'error_wrong';
}
