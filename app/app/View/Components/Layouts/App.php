<?php

namespace App\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Component;

class App extends Component
{
    /**
     * Property: The language attribute for the HTML tag
     */
    public string $lang;

    /**
     * Create a new component instance
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $thumbnail = null,
        public ?string $colorScheme = null
    ) {
        // Set the full title
        $this->title = $title ?
            $title.' | '.config('app.name') :
            config('app.name').' | '.config('custom.short_description');

        // Set the description
        $this->description = $description ?: config('custom.description');

        // Set the thumbnail
        $this->thumbnail = $thumbnail ?: Vite::image('thumbnail.jpg');

        // Set the description
        $this->colorScheme = $colorScheme ?: $this->getColorScheme();

        // Set the language attribute
        $this->lang = str_replace('_', '-', app()->getLocale());
    }

    /**
     * Get the view that represents the component
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.app');
    }

    /**
     * Get the current color scheme
     */
    private function getColorScheme(): string
    {
        // Get the default color scheme from config, or use 'light' if not set
        $defaultColorScheme = config('custom.meta_data.default_color_scheme') ?? 'light';

        // Check if the app has dynamic color scheme enabled
        $appHasDynamicColorScheme = config('custom.meta_data.dynamic_color_scheme') == true;

        // If dynamic color scheme is not enabled, return the default color scheme
        if (! $appHasDynamicColorScheme) {
            return $defaultColorScheme;
        }

        // Check if the user has a color scheme preference in cookies
        $cookieColorScheme = isset($_COOKIE['color-scheme']) && in_array($_COOKIE['color-scheme'], ['light', 'dark'])
            ? $_COOKIE['color-scheme']
            : false;

        return $cookieColorScheme ? $cookieColorScheme : $defaultColorScheme;
    }
}
