<?php

class Scripts
{
    private $list = [];

    public function __construct($view)
    {

        // load framework scripts
        $files = glob(base_path().'/public/legacy/system/scripts/*.js');
        foreach ($files as $file) {
            $this->add($file, 1);
        }

        // load app scripts
        $files = glob(base_path().'/public/legacy/app/scripts/*.js');
        foreach ($files as $file) {
            $this->add($file, 2);
        }

        // load page scripts
        if (file_exists($file = base_path().'/public/legacy/pages/scripts/'.$view->name.'.js')) {
            $this->add($file, 4);
        }

        // load scripts in the page js folder, if present
        $files = glob(base_path().'/public/legacy/pages/scripts/'.$view->name.'/*.js');
        foreach ($files as $file) {
            $this->add($file, 5);
        }
    }

    // load a script
    public function add($file, $priority = 3)
    {

        $file .= '?v='.filemtime($file);

        $this->list[] = $priority.'<script src="/'.explode('public/', $file)[1].'"></script>'.PHP_EOL;
    }

    // make the string of styles
    public function make()
    {

        $list = [];

        asort($this->list);

        foreach ($this->list as $script) {
            $list[] = substr($script, 1);
        }

        return implode('', $list);
    }
}
