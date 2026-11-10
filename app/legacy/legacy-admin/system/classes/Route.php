<?php

namespace Legacy\System\Classes;

class Route
{
    private static $routes = [];

    public static function add($paths, $function)
    {

        $paths = (array) $paths;

        foreach ($paths as $path) {
            array_push(self::$routes, [
                'path' => '/legacy-admin'.$path,
                'function' => $function,
            ]);
        }
    }

    public static function run()
    {
        $path_found = false;
        $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        $path = trim($path, '/');

        foreach (self::$routes as $route) {
            $routePath = trim($route['path'], '/');

            if ($path == $routePath || (strpos($routePath, '*') && strpos($path, rtrim($routePath, '/*')) !== false)) {
                $path_found = true;
                call_user_func_array($route['function'], [$path]);
                break;
            }
        }

        // error 404
        if (! $path_found) {
            View::setPage('errors/404')
                ->setTitle('Error 404')
                ->setTemplate('void')
                ->setMiddlewares([])
                ->show();
        }
    }
}
