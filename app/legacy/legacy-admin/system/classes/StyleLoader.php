<?php

class StyleLoader
{
    private static $list = [];

    public static function __constructStatic()
    {
        // add framework styles
        $files = glob(base_path().'/public/legacy/admin/system/styles/*.css');
        foreach ($files as $file) {
            self::add(self::getFileName($file), 'system');
        }

        // add app styles
        $files = glob(base_path().'/public/legacy/admin/app/styles/*.css');
        foreach ($files as $file) {
            self::add(self::getFileName($file), 'app');
        }
    }

    // add a style
    public static function add($fileName, $type, $priority = null)
    {
        $file = false;

        switch ($type) {
            case 'system':
                $file = '/system/styles/'.$fileName.'.css';
                if (! $priority) {
                    $priority = 1;
                }
                break;
            case 'app':
                $file = '/app/styles/'.$fileName.'.css';
                if (! $priority) {
                    $priority = 3;
                }
                break;
            case 'template':
                if (file_exists(base_path().'/public/legacy/admin'.$fetchingFile = '/app/templates/styles/'.$fileName.'.css')) {
                    $file = $fetchingFile;
                    if (! $priority) {
                        $priority = 3;
                    }
                } elseif (file_exists(base_path().'/public/legacy/admin'.$fetchingFile = '/system/templates/styles/'.$fileName.'.css')) {
                    $file = $fetchingFile;
                    if (! $priority) {
                        $priority = 2;
                    }
                }
                break;
            case 'page':
                if (file_exists(base_path().'/public/legacy/admin'.$fetchingFile = '/pages/styles/'.$fileName.'.css')) {
                    $file = $fetchingFile;
                    if (! $priority) {
                        $priority = 4;
                    }
                }
                break;
            case 'static':
                $file = $fileName;
                if (! $priority) {
                    $priority = 6;
                }
                break;
        }

        // store the file URL in the list
        if ($file) {
            $file .= '?v='.filemtime(base_path().'/public/legacy/admin'.$file);
            self::$list[] = [$priority, $file];
        }
    }

    // add the page style
    public static function addPageStyles($pageName)
    {
        $additionalStyles = glob(SERVER_ROOT.'/pages/styles/-expands-'.$pageName.'/*.css');

        self::add($pageName, 'page');

        foreach ($additionalStyles as $additionalStyle) {
            self::add('-expands-'.$pageName.'/'.self::getFileName($additionalStyle), 'page', 5);
        }
    }

    // load all the styles
    public static function load()
    {
        $list = [];

        sort_subvalue(self::$list, 0);

        foreach (self::$list as $style) {
            $list[] = '<link href="/legacy/admin'.CLIENT_ROOT.$style[1].'" rel="stylesheet" type="text/css">';
        }

        return implode(PHP_EOL, $list);
    }

    // get the file name
    private static function getFileName($fileUrl)
    {
        return explode('.css', legacy_explode_last('/', $fileUrl))[0];
    }
}
