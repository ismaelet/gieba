<?php

namespace Legacy\System\Classes;

class Route
{
    private static $routes = [];

    public static function add($paths, $function, $arguments = false)
    {

        $paths = (array) $paths;

        foreach ($paths as $path) {

            array_push(self::$routes, [
                'path' => __HROOT__.$path,
                'function' => $function,
                'arguments' => $arguments,
            ]);
        }
    }

    public static function run()
    {

        $path_found = false;

        // parse url
        $path = parse_url($_SERVER['REQUEST_URI'])['path'] ?? null;

        if ($path !== null) {
            foreach (self::$routes as $route) {

                // Check path match
                if (in_array($path, [$route['path'], $route['path'].'/'])) {

                    $path_found = true;

                    call_user_func_array($route['function'], [$route['arguments']]);

                    break;
                }
            }
        }

        // error 404
        if (! $path_found) {

            $view = new \Legacy\System\Classes\View('errors/404');

            $view->middlewares = [];

            $view->show();
        }
    }
}
