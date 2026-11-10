<?php

namespace App\View;

use App\View\Components\Component;

class View
{
    private static $name;

    private static $styles = [];

    private static $scripts = [];

    /**
     * Get the view name
     */
    public static function name()
    {
        return self::$name;
    }

    /**
     * Add an inline style
     */
    public static function addStyle(string $style): void
    {
        $htmlStyle = '<style>'.$style.'</style>';

        if (! in_array($htmlStyle, self::$styles)) {
            self::$styles[] = $htmlStyle;
        }
    }

    /**
     * Get a string of all the stacked inline styles
     */
    public static function stackedStyles(): string
    {
        return implode(PHP_EOL, self::$styles);
    }

    /**
     * Add an inline script
     */
    public static function addScript(string $script): void
    {
        $htmlScript = '<script defer>'.$script.'</script>';

        if (! in_array($htmlScript, self::$scripts)) {
            self::$scripts[] = $htmlScript;
        }
    }

    /**
     * Get a string of all the stacked inline scripts
     */
    public static function stackedScripts(): string
    {
        return implode(PHP_EOL, self::$scripts);
    }

    /**
     * Render the view
     */
    public static function render(string $viewName, array $arguments = []): void
    {
        self::$name = $viewName;

        echo Component::anonymousRender($viewName, $arguments);
    }
}
