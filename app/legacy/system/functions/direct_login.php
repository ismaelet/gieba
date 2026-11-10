<?php

/*
function login_direct($token) {

    $valid_token = false;

    $token = explode('_', $token);

    if (isset($token[1])) {

        $selector = $token[0];

        if ($row = \Legacy\System\Classes\DB::row('userID, validator FROM auth_tokens WHERE selector=? AND expiration>NOW()', $selector)) {

            $validator = $token[1];
            $validatorHash = hash('sha256', $validator);

            if (hash_equals($validatorHash, $row['validator'])) {

                //login
                $id = $row['userID'];

                \Legacy\System\Classes\DB::update('auth_tokens SET expiration=(NOW() + INTERVAL 1 MONTH) WHERE userID=?', [$id]);

                setcookie('auth_token', $selector.'_'.$validator, time() + 60*60*24*30);

                $_SESSION['userID'] = $id;

                $valid_token = true;
            }
        }
    }

    //if (!$valid_token) setcookie('auth_token', null, -1, "/");
}
*/
