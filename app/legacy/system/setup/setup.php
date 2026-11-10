<?php

use Legacy\System\Classes\Route;

// setup global variables
global $user, $con;

// Set the error log
ini_set('error_log', __DIR__.'/../../logs/error.log');

// Load resources
require_once __DIR__.'/../../../vendor/autoload.php';

// Load environment variables
(Dotenv\Dotenv::createImmutable(__DIR__.'/../../../'))->load();

// check if to use the lighter version for ajax
$full = $full ?? true;

// set migration status
$migration = false;

// retrive and start session
session_start();

// set constants
define('SERVER_ROOT', explode('/system', str_replace('\\', '/', __DIR__))[0]);
define('__HROOT__', '');
define('__ICON__', 'system/resources/icons/');
define('__IMG__', 'app/resources/images/');
define('LOGO_DIR', 'app/resources/logo/');

include_once SERVER_ROOT.'/app/config/config.php';

date_default_timezone_set($sysConfig['timezone']);

// connect
if (! $sysConfig['static']) {
    $con = new mysqli($sysConfig['host'], $sysConfig['user'], $sysConfig['pass'], $sysConfig['db']) or false;
    $con->query('SET NAMES utf8');
}

// load basic resources
include_once 'migration.php';
include_once 'classes.php';
include_once 'modules.php';
include_once 'functions.php';

if ($full) {
    include_once 'middlewares.php';
    foreach (scan('/app/routes', 'php') as $route) {
        include_once $route;
    }
}

// recover login
if (! isset($_SESSION['userID']) && isset($_COOKIE['auth_token'])) {
    login_direct($_COOKIE['auth_token']);
}

$userID = $_SESSION['userID'] ?? 0;

$user = new User($userID);

// view content
if ($full) {
    Route::run();
}
