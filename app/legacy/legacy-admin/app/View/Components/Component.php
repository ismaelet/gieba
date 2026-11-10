<?php

namespace App\View\Components;

use App\View\View;
use Exception;

class Component
{
    protected static $arguments = [];

    /**
     * Initialize the component
     */
    private static function init(): void {}

    /**
     * Add custom styles
     */
    protected static function addStyle(string $style): void
    {
        \Legacy\System\Classes\View::addStyle($style);
    }

    /**
     * Add custom scripts
     */
    protected static function addScript(string $script): void
    {
        \Legacy\System\Classes\View::addScript($script);
    }

    /**
     * Render the component
     */
    public static function render(array $arguments = [], ?string $componentName = null): string
    {
        // set the component name
        if (static::class == self::class) {
            $componentView = '/src/views/'.$componentName.'.php';
        } else {
            $componentName = str_replace('\\', '/', ucfirst(explode('App\View\Components\\', static::class)[1]));
            $componentView = '/src/components/'.$componentName.'.php';
        }

        // store the arguments
        static::$arguments = array_merge(static::$arguments, $arguments);

        // initalize the component logic
        static::init();

        // pass the arguments to the component view
        foreach (static::$arguments as $_argumentName => $_argumentValue) {
            ${$_argumentName} = $_argumentValue;
        }

        unset($_argumentName);
        unset($_argumentValue);

        // render the component
        ob_start();

        require public_server_path($componentView);

        // store the render
        $content = ob_get_clean();

        // extract the html
        $html = extract_markup('template', $content);

        // store the component styles and scripts
        $style = extract_markup('style', $content);
        $script = extract_markup('script', $content);

        if ($style) {
            static::addStyle($style);
        }

        if ($script) {
            static::addScript($script);
        }

        // check if the component has a parent
        if (strpos($content, '<x-template.')) {
            $templateName = explode('>', explode('<x-template.', $content, 2)[1], 2)[0];
            $innerHtml = extract_markup('x-template.'.$templateName, $html);
            $templateClass = 'App\View\Components\Templates\\'.ucfirst($templateName);
            $render = $templateClass::render(['slot' => $innerHtml]);
        } else {
            $render = $html;
        }

        // return the render
        if ($render) {
            return $render;
        } else {
            throw new Exception('No HTML content for component '.$componentName);
        }
    }

    /**
     * Render a component without the relative class
     */
    public static function anonymousRender(string $componentName, array $arguments = [])
    {
        return self::render($arguments, $componentName);
    }
}
