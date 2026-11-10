<?php

class ScriptLoader
{
    private static $list = [];

    private static $headInlineScripts = [];

    private static $footInlineScripts = [];

    public static function __constructStatic()
    {
        // node packages
        if (file_exists($configFile = SERVER_ROOT.'/package.json')) {
            $config = file_get_contents($configFile);
            $config = json_decode($config, true);

            // setup the packages
            foreach ($config['dependencies'] as $packageName => $_looping) {
                $packageDirectory = '/node_modules/'.strtolower($packageName).'/dist';
                $packageName = $packageName.'.min.js';
                $packagePath = $packageDirectory.'/'.$packageName;

                if (! file_exists(SERVER_ROOT.$packagePath)) {
                    $packageFiles = glob(SERVER_ROOT.$packageDirectory.'/*.min.js');
                    $packagePath = explode(SERVER_ROOT, $packageFiles[0])[1];
                }

                self::add($packagePath, 'static', 1);
            }
        }

        // add framework scripts
        $files = glob(base_path().'/public/legacy/admin/system/scripts/*.js');
        foreach ($files as $file) {
            self::add(self::getFileName($file), 'system');
        }

        // add app scripts
        $files = glob(base_path().'/public/legacy/admin/app/scripts/*.js');
        foreach ($files as $file) {
            self::add(self::getFileName($file), 'app');
        }
    }

    // load a script
    public static function add($fileName, $type, $priority = false)
    {
        $file = false;

        switch ($type) {
            case 'system':
                $file = '/system/scripts/'.$fileName.'.js';
                if (! $priority) {
                    $priority = 2;
                }
                break;
            case 'app':
                $file = '/app/scripts/'.$fileName.'.js';
                if (! $priority) {
                    $priority = 3;
                }
                break;
            case 'template':
                if (file_exists(base_path().'/public/legacy/admin'.$fetchingFile = '/app/templates/scripts/'.$fileName.'.js')) {
                    $file = $fetchingFile;
                    if (! $priority) {
                        $priority = 3;
                    }
                } elseif (file_exists(base_path().'/public/legacy/admin'.$fetchingFile = '/system/templates/scripts/'.$fileName.'.js')) {
                    $file = $fetchingFile;
                    if (! $priority) {
                        $priority = 2;
                    }
                }
                break;
            case 'page':
                if (file_exists(base_path().'/public/legacy/admin'.$fetchingFile = '/pages/scripts/'.$fileName.'.js')) {
                    $file = $fetchingFile;
                    if (! $priority) {
                        $priority = 4;
                    }
                }
                break;
            case 'static':
                $file = $fileName;
                if (! $priority) {
                    $priority = 5;
                }
                break;
        }

        // store the file URL in the list
        if ($file) {
            $file .= '?v='.filemtime(base_path().'/public/legacy/admin'.$file);
            self::$list[] = [$priority, $file];
        }
    }

    public static function addPageScripts($pageName)
    {
        $pageScripts = glob(SERVER_ROOT.'/pages/scripts/-expands-'.$pageName.'/*.js');

        self::add($pageName, 'page');

        foreach ($pageScripts as $pageScript) {
            self::add('-expands-'.$pageName.'/'.self::getFileName($pageScript), 'page', 5);
        }
    }

    // get the file name
    private static function getFileName($fileUrl)
    {
        return explode('.js', legacy_explode_last('/', $fileUrl))[0];
    }

    /*
    * Load the scripts
    */
    // load all the scripts
    public static function load()
    {
        $list = [];

        sort_subvalue(self::$list, 0);

        foreach (self::$list as $style) {
            $list[] = '<script src="/legacy/admin'.$style[1].'"></script>';
        }

        return implode(PHP_EOL, $list);
    }

    // load the inline script to be placed in the head
    public static function loadHeadInline()
    {
        return implode(PHP_EOL.PHP_EOL, self::$headInlineScripts);
    }

    // load the inline script to be placed at the end of the body
    public static function loadFootInline()
    {
        return implode(PHP_EOL.PHP_EOL, self::$footInlineScripts);
    }
}
