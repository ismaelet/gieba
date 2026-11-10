<?php

class Styles
{
    private $list = [];

    public function __construct($view)
    {

        $this->root();

        // load framework styles
        $files = glob(base_path().'/public/legacy/system/styles/*.css');
        foreach ($files as $file) {
            $this->add($file, 1);
        }

        // load app styles
        $files = glob(base_path().'/public/legacy/app/styles/*.css');
        foreach ($files as $file) {
            $this->add($file, 2);
        }

        // load page styles
        if (file_exists($file = base_path().'/public/legacy/pages/styles/'.$view->name.'.css')) {
            $this->add($file, 4);
        }

        // load styles in the page css folder, if present
        $files = glob(base_path().'/public/legacy/pages/styles/'.$view->name.'/*.css');
        foreach ($files as $file) {
            $this->add($file, 5);
        }
    }

    // make the root
    private function root()
    {

        $root = '<style type="text/css">'.PHP_EOL;
        $root .= "	@import url('https://fonts.googleapis.com/css2?family=".sysVar('main_font_url')."&display=swap');".PHP_EOL;
        if (sysVar('side_font_url')) {
            $root .= "	@import url('https://fonts.googleapis.com/css2?family=".sysVar('side_font_url')."&display=swap');".PHP_EOL;
        }
        $root .= "	@import url('https://fonts.googleapis.com/css2?family=".sysVar('text_font_url')."&display=swap');

	:root {
		--c1: ".sysVar('c1').';
		--c1-h: '.sysVar('c1_hover').';

		--c2: '.sysVar('c2').';
		--c2-h: '.sysVar('c2_hover').';
		
		--c3: '.sysVar('c3').';
		--c3-h: '.sysVar('c3_hover').';
		
		--c4: '.sysVar('c4').';
		--c4-h: '.sysVar('c4_hover').';
		
		--c5: '.sysVar('c5').';
		--c5-h: '.sysVar('c5_hover').";

		--font-main: '".sysVar('main_font_name')."', sans-serif;
		--font-side: '".sysVar('side_font_name')."', sans-serif;
		--font-text: '".sysVar('text_font_name')."', sans-serif;
	}
</style>".PHP_EOL.PHP_EOL;

        $this->list[] = '0'.$root;
    }

    // load a style
    public function add($file, $priority = 3)
    {

        $file .= '?v='.filemtime($file);

        $this->list[] = $priority.'<link href="/'.explode('public/', $file)[1].'" rel="stylesheet" type="text/css">'.PHP_EOL;
    }

    // make the string of styles
    public function make()
    {

        $list = [];

        asort($this->list);

        foreach ($this->list as $style) {
            $list[] = substr($style, 1);
        }

        return implode('', $list);
    }
}
