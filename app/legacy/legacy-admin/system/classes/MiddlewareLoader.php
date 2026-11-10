<?php

use Legacy\System\Classes\DB;

class MiddlewareLoader
{
    private static $middlewares = [];

    public static function __constructStatic()
    {
        $defaultMiddlewareLoader = [
            'public' => function () {
                global $user;
                $public = sys_config('static') ?: DB::find('value FROM config WHERE name=?', ['public']);
                if (! $public && ! $user->admin) {
                    legacy_redirect('/offline');
                }
            },

            'not-public' => function () {
                $public = sys_config('static') ?: DB::find('value FROM config WHERE name=?', ['public']);
                if ($public) {
                    legacy_redirect('/');
                }
            },

            'wip' => function () {
                legacy_redirect('/wip');
            },

            'logged' => function () {
                if (! logged()) {
                    legacy_redirect('/login');
                }
            },

            'not-logged' => function () {
                if (sys_config('static') || logged()) {
                    legacy_redirect(sys_config('logged-redirect') ?: '/');
                }
            },

            'admin' => function () {
                global $user;
                if (! $user->admin) {
                    legacy_redirect('/login');
                }
            },

            'not-admin' => function () {
                global $user;
                if ($user->admin) {
                    legacy_redirect('/');
                }
            },
        ];

        foreach ($defaultMiddlewareLoader as $middlewareName => $middlewareFunction) {
            self::add($middlewareName, $middlewareFunction);
        }
    }

    public static function add($middlewareName, $middlewareFunction)
    {
        self::$middlewares[$middlewareName] = $middlewareFunction;
    }

    public static function run($middlewareName)
    {
        $middlewareFunction = self::$middlewares[$middlewareName] ?? null;

        if ($middlewareFunction) {
            call_user_func_array($middlewareFunction, []);
        } else {
            exit('Access denied: '.$middlewareName);
        }
    }
}
