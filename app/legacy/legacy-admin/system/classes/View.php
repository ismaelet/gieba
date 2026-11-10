<?php

namespace Legacy\System\Classes;

use Logic;
use MiddlewareLoader;
use ScriptLoader;
use StyleLoader;
use Viewer;

class View
{
    public static $name = null;

    public static $title;

    public static $tabTitle;

    public static $thumbnail = null;

    public static $description;

    public static $location = null;

    public static $middlewares = ['public'];

    public static $template = 'main';

    public static $url = null;

    public static $variables = [];

    public static function __constructStatic()
    {
        self::$description = legacy__(sys_config('description'));
        self::$title = sys_config('name');
        self::$tabTitle = sys_config('name');
        self::$thumbnail = client_url(sys_config('logo'));

        if (! empty(sys_config('short-description'))) {
            self::$tabTitle .= ' | '.sys_config('short-description');
        }
    }

    /*
    * Set properties
    */
    public static function setPage(string $name)
    {
        self::$name = $name;

        return new static(self::$name);
    }

    public static function setTemplate(string $template)
    {
        self::$template = $template;

        return new static(self::$name);
    }

    public static function setTitle(string $title)
    {
        self::$title = $title;
        self::$tabTitle = $title.' | '.sys_config('name');

        return new static(self::$name);
    }

    public static function setDescription(string $description)
    {
        self::$description = $description;

        return new static(self::$name);
    }

    public static function setThumbnail(?string $thumbnail = null)
    {
        if ($thumbnail) {
            self::$thumbnail = strpos($thumbnail, CLIENT_ROOT) === 0 ? $thumbnail : client_url($thumbnail);
        }

        return new static(self::$name);
    }

    public static function setLocation(string $location)
    {
        self::$location = $location;

        return new static(self::$name);
    }

    public static function setMiddlewares(array $middlewares = [])
    {
        $middlewares = (array) $middlewares;

        self::$middlewares = $middlewares;

        return new static(self::$name);
    }

    public static function addMiddlewares(array $middlewares)
    {
        $middlewares = (array) $middlewares;

        foreach ($middlewares as $middleware) {
            self::$middlewares[] = $middleware;
        }

        return new static(self::$name);
    }

    /*
    * Manage the variables
    */
    public static function set(string $var, $value, mixed $arrayKey = null)
    {
        if (! $arrayKey) {
            self::$variables[$var] = $value;
        } else {
            self::$variables[$var][$arrayKey] = $value;
        }

        return new static(self::$name);
    }

    public static function get($var)
    {
        return self::$variables[$var] ?? null;

        return new static(self::$name);
    }

    /*
    * Render the view
    */
    public static function show()
    {
        // set basic data
        if (! self::$location) {
            self::$location = self::$name;
        }
        if (! self::$url) {
            self::$url = SERVER_ROOT.'/pages/views/'.self::$name.'.php';
        }

        // load the middlewares
        foreach (self::$middlewares as $middleware) {
            MiddlewareLoader::run($middleware);
        }

        // load the logic
        Logic::add(self::$template, 'template');
        Logic::add(self::$name, 'page');
        Logic::load();

        // load page resources
        StyleLoader::addPageStyles(View::$name);
        ScriptLoader::addPageScripts(View::$name);

        // render page
        Viewer::render();
    }
}
