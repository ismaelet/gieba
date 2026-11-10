<?php

AdminSetup::execute();

class AdminSetup
{
    // routes
    private static function initializeRoutes()
    {
        Logic::$dependencies['admin'] = 'void';
        Logic::$dependencies['admin-list'] = 'admin';
        Logic::$dependencies['admin-form'] = 'admin';
        Logic::$dependencies['admin-advanced'] = 'admin';
        Logic::$priorities['admin'] = 60;
        Logic::$priorities['admin-list'] = 200;
        Logic::$priorities['admin-form'] = 200;
        Logic::$priorities['admin-advanced'] = 65;
    }

    private static function initializeFunctions()
    {
        if (file_exists($file = PUBLIC_SERVER_ROOT.'/system/functions/ecommerce.php')) {
            require_once $file;
        }
        if (file_exists($file = PUBLIC_SERVER_ROOT.'/system/functions/ecommerce-emails.php')) {
            require_once $file;
        }
    }

    private static function initializeConstants()
    {
        define('PUBLIC_SERVER_ROOT', explode('/admin', SERVER_ROOT)[0]);
        define('PUBLIC_CLIENT_ROOT', explode('/admin', CLIENT_ROOT)[0]);
        define('PUBLIC_CLIENT_URL', explode('/admin', CLIENT_URL)[0]);
    }

    public static function execute()
    {
        self::initializeRoutes();
        self::initializeConstants();
        self::initializeFunctions();
    }
}
