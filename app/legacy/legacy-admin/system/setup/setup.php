<?php

use Legacy\System\Classes\Route;

global $con;

// Set the error log
ini_set('error_log', __DIR__.'/../../logs/error.log');

// Load resources
require_once __DIR__.'/../../../../vendor/autoload.php';

// Load environment variables
(Dotenv\Dotenv::createImmutable(__DIR__.'/../../../../'))->load();

// fetch app config
require_once __DIR__.'/../../../app/config/config.php';

// define class autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);

    if (strpos($class, 'App/Models') === 0) {
        include_once __DIR__.'/../../../app/Models/'.explode('Models/', $class)[1].'.php';
    } elseif (strpos($class, 'App') === 0) {
        include_once __DIR__.'/../../'.lcfirst($class).'.php';
    } elseif (file_exists($file = __DIR__.'/../../../'.lcfirst($class).'.php')) {
        include_once $file;
    } elseif (file_exists($file = SERVER_ROOT.'/app/classes/'.$class.'.php')) {
        include_once $file;
    } elseif (file_exists($file = SERVER_ROOT.'/system/classes/'.$class.'.php')) {
        include_once $file;
    } elseif (defined('PUBLIC_SERVER_ROOT') && file_exists($file = PUBLIC_SERVER_ROOT.'/app/classes/'.$class.'.php')) {
        include_once $file;
    } elseif (strpos($class, 'Legacy/System/Classes') === 0) {
        include_once base_path().'/legacy/legacy-admin/system/classes/'.str_replace('Legacy/System/Classes/', '', $class).'.php';
    }

    if (method_exists($class, '__constructStatic')) {
        $class::__constructStatic();
    }
});

// render
Setup::execute();

class Setup
{
    // constants
    private static function initializeConstants()
    {
        global $sysConfig;

        define('SERVER_ROOT', explode('/system', str_replace('\\', '/', __DIR__))[0]);
        define('CLIENT_ROOT', '');
        define('CLIENT_URL', ! empty($sysConfig['domain']) ? 'https://'.$sysConfig['domain'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].CLIENT_ROOT);
        define('ICONS_DIR', '/system/assets/icons/');
        define('APP_ICONS_DIR', '/app/assets/icons/');
        define('IMG_DIR', '/app/assets/images/');
        define('LOGO_DIR', '/app/assets/logo/');
    }

    // functions
    private static function initializeFunctions()
    {
        $modules = glob(__DIR__.'/../../../app/Helpers/*.php', GLOB_BRACE);

        foreach ($modules as $module) {
            include_once $module;
        }

        $modules = glob(SERVER_ROOT.'/system/functions/*.php', GLOB_BRACE);
        foreach ($modules as $module) {
            include_once $module;
        }

        $modules = glob(SERVER_ROOT.'/app/functions/*.php', GLOB_BRACE);
        foreach ($modules as $module) {
            include_once $module;
        }
    }

    // routes
    private static function initializeRoutes()
    {
        foreach (scan('/system/routes', 'php') as $route) {
            include_once $route;
        }
        foreach (scan('/app/routes', 'php') as $route) {
            include_once $route;
        }
    }

    // connect
    private static function connect()
    {
        global $con;

        if (! sys_config('static')) {
            try {
                $con = new mysqli(sys_config('host'), sys_config('user'), sys_config('pass'), sys_config('db'));
                $con->query('SET NAMES utf8');
            } catch (Exception $e) {
                $error = $e->getMessage();

                error_log('Connection error: '.$error.PHP_EOL.'On page: '.$_SERVER['REQUEST_URI']);

                if (env('DEBUG') || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
                    echo $error;
                }
            }
        }
    }

    // migrate
    private static function migrate()
    {
        if (! sys_config('static') && sys_config('migrate')) {
            $tables = glob(SERVER_ROOT.'/system/migration/*.php');
            foreach ($tables as $table) {
                include_once $table;
            }

            $tables = glob(SERVER_ROOT.'/app/migration/*.php');
            foreach ($tables as $table) {
                include_once $table;
            }
        }
    }

    // login
    private static function sessionLogin()
    {
        global $user;

        if (! sys_config('static')) {
            if (! isset($_SESSION['userId']) && isset($_COOKIE['auth_token'])) {
                login_direct($_COOKIE['auth_token']);
            }

            $userId = $_SESSION['userId'] ?? 0;
            $user = new User($userId);
        }
    }

    // language
    private static function sessionLanguage()
    {
        $acceptedLanguages = sys_config('languages');
        $defaultLanguage = sys_config('default-language');
        $browserLanguage = ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $sessionLanguage = $_SESSION['language'] ?? null;
        $requestedLanguage = $_GET['lang'] ?? null;

        // remove unused session languages
        if ($sessionLanguage && ! isset($acceptedLanguages[$sessionLanguage])) {
            $sessionLanguage = null;
            unset($_SESSION['language']);
        }

        // set the requested language
        if ($requestedLanguage && isset($acceptedLanguages[$requestedLanguage])) {
            $sessionLanguage = $_SESSION['language'] = $_GET['lang'];
        }

        // set the session language
        if (! $sessionLanguage) {
            $sessionLanguage = $_SESSION['language'] = isset($acceptedLanguages[$browserLanguage]) ? $browserLanguage : $defaultLanguage;
        }
    }

    // theme
    private static function sessionTheme()
    {
        $_SESSION['theme-color'] = sys_config('theme-color') ?: ($_COOKIE['theme-color'] ?? 'light');
    }

    // execute the setup
    public static function execute()
    {

        // load resources
        self::initializeConstants();
        self::initializeFunctions();

        // connect
        self::connect();
        self::migrate();
        self::initializeRoutes();

        // start session
        session_start();
        date_default_timezone_set(sys_config('timezone'));

        // setup the session
        self::sessionLogin();
        self::sessionLanguage();
        self::sessionTheme();

        // other setups
        foreach (['admin', 'ecommerce'] as $setupFile) {
            if (file_exists($file = __DIR__.'/'.$setupFile.'.php')) {
                require_once $file;
            }
        }

        // render page
        Route::run();
    }
}
