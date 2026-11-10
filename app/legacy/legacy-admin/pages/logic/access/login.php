<?php

// setup page
$header['floating'] = true;

// setup variabiles
$error = false;
$action = $_GET['action'] ?? false;

// structure
$loginConfig = [
    'welcome-text' => true,
    'stay-connected' => true,
    'password-recovery' => true,
    'register' => true,
];

// login
if (isset($_POST['submit'])) {
    if (isset($_SESSION['login-attempts']) && $_SESSION['login-attempts'] > 4) {
        exit('Too many login attempts');
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);
        $url = $_POST['url'] ?? false;

        $error = standard_login($username, $password, $url, $remember);

        if ($error) {
            if (isset($_SESSION['login-attempts'])) {
                $_SESSION['login-attempts']++;
            } else {
                $_SESSION['login-attempts'] = 1;
            }
        }
    }
}
