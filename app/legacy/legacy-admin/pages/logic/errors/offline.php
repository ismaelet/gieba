<?php

load_module('logout');

if (isset($_POST['submit'])) {

    if (isset($_SESSION['userID'])) {
        logout();
    }

    legacy_redirect('/login');
}
