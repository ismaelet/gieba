<?php

class Element
{
    public $name;

    public $path = false;

    public $setup = false;

    public $styles = [];

    public $scripts = [];

    public $dependencies = [];

    // setup element
    public function __construct($name)
    {

        $this->name = $name;

        // get path
        if (file_exists(SERVER_ROOT.$path = '/app/elements/'.$this->name)) {
            $this->path = $path;
        } elseif (file_exists(SERVER_ROOT.$path = '/system/elements/'.$this->name)) {
            $this->path = $path;
        }

        // load the element only if found
        if ($this->path) {

            // get setup file
            if (file_exists($file = SERVER_ROOT.$this->path.'/setup.php')) {
                $this->setup = $file;
            }

            // get styles
            $files = glob(SERVER_ROOT.$this->path.'/styles/*.css');
            foreach ($files as $file) {
                array_push($this->styles, $file);
            }

            // get scripts
            $files = glob(SERVER_ROOT.$this->path.'/scripts/*.js');
            foreach ($files as $file) {
                array_push($this->scripts, $file);
            }

            // load config file
            if (file_exists($config = SERVER_ROOT.$this->path.'/config.php')) {
                include_once $config;
            }

            // load dependencies
            if (isset($elementConfig['dependencies'])) {
                $this->dependencies = $elementConfig['dependencies'];
            }
        }
    }
}
