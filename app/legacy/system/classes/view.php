<?php

namespace Legacy\System\Classes;

use Element;
use Scripts;
use Styles;
use Viewer;

class View
{
    public $name;

    public $title;

    public $page_title;

    public $loc;

    public $styles;

    public $scripts;

    public $elements = [];

    public $middlewares = ['public'];

    public $preload = [];

    public $template = 'main';

    public $path;

    public $url;

    public $variabiles = [];

    public $stored = [];

    public function __construct($name)
    {

        global $user;

        $this->name = $name;
        $this->loc = $name;

        $this->set('user', $user);
    }

    // setup page
    public function show()
    {

        // setup assets
        $this->styles = new Styles($this);
        $this->scripts = new Scripts($this);
        $this->set('favicon', favicon());

        // set page title
        if (isset($this->title)) {
            $this->title($this->title);
        } else {
            $page_title = sysData('name');

            if (! empty(sysData('desc_shr_'.sysData('lang')))) {
                $page_title .= ' | '.sysData('desc_shr_'.sysData('lang'));
            }

            $this->page_title = $page_title;
        }

        // save url
        $this->url = SERVER_ROOT.'/pages/views/'.$this->name.'.php';

        // add middleware
        foreach ($this->middlewares as $name) {
            load_middleware($name, $this->get('user'), $this);
        }

        // add logic
        $this->add_logic($this->template, 'template');
        $this->add_logic($this->name, 'page');

        // add elements
        foreach ($this->elements as $name) {
            $this->add_element($name);
        }

        // setup google analytics
        $this->start_analytics();

        // render page
        $viewer = new Viewer($this);
        $viewer->show();
    }

    // variables
    public function set($var, $value, $key = false)
    {
        if ($key) {
            $this->variables[$var][$key] = $value;
        } else {
            $this->variables[$var] = $value;
        }
    }

    public function get($var)
    {
        return $this->variables[$var] ?? null;
    }

    // add logic
    public function add_logic($file, $type)
    {

        $exists = false;

        if ($type == 'template') {
            if (file_exists($file_exists = SERVER_ROOT.'/app/templates/logic/'.$file.'.php')) {
                $exists = $file_exists;
            } elseif (file_exists($file_exists = SERVER_ROOT.'/system/templates/logic/'.$file.'.php')) {
                $exists = $file_exists;
            }
        } elseif ($type == 'page') {

            if (file_exists($file_exists = SERVER_ROOT.'/pages/logic/'.$file.'.php')) {
                $exists = $file_exists;
            }
        } elseif ($type == 'static') {

            if (file_exists($file)) {
                $exists = $file;
            }
        }

        if ($exists) {
            include_once $exists;
        }
    }

    // add an element
    public function add_element($name)
    {

        $element = new Element($name);

        // add styles
        foreach ($element->styles as $style) {
            $this->styles->add($style);
        }

        // add scripts
        foreach ($element->scripts as $script) {
            $this->scripts->add($script);
        }

        // add logic
        if ($element->setup) {
            $this->add_logic($element->setup, 'static');
        }

        // add dependencies
        foreach ($element->dependencies as $dependency) {
            $this->add_element($dependency);
        }
    }

    // page title
    public function title($title)
    {

        $this->title = $title;
        $this->page_title = $title.' | '.sysData('name');
    }

    // google analytics
    private function start_analytics()
    {

        $this->set('google_analytics', google_analytics($this->get('user')));
    }
}
