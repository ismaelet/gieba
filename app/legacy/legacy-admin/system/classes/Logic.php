<?php

use Legacy\System\Classes\View;

class Logic
{
    private static $files = [];

    public static $priorities = [
        'blank' => 25,
        'main' => 50,
        'void' => 50,
        'form' => 60,
        'header' => 70,
        'footer' => 70,
    ];

    public static $dependencies = [
        'main' => ['blank', 'header', 'footer'],
        'void' => 'blank',
        'form' => 'main',
    ];

    // add a file
    public static function add($fileName, $type, $priority = false)
    {
        $file = false;

        switch ($type) {
            case 'template':
                if (file_exists($fetchingFile = SERVER_ROOT.'/app/templates/logic/'.$fileName.'.php')) {
                    $file = $fetchingFile;
                } elseif (file_exists($fetchingFile = SERVER_ROOT.'/system/templates/logic/'.$fileName.'.php')) {
                    $file = $fetchingFile;
                }
                if (! $priority) {
                    $priority = self::$priorities[$fileName] ?? 50;
                }
                break;
            case 'page':
                if (file_exists($fetchingFile = SERVER_ROOT.'/pages/logic/'.$fileName.'.php')) {
                    $file = $fetchingFile;
                }
                if (! $priority) {
                    $priority = 150;
                }
                break;
            case 'static':
                if (file_exists($file)) {
                    $file = $fileName;
                }
                if (! $priority) {
                    $priority = 150;
                }
                break;
        }

        if ($type == 'template' && isset(self::$dependencies[$fileName])) {
            foreach ((array) self::$dependencies[$fileName] as $dependency) {
                self::add($dependency, 'template');
            }
        }

        self::$files[$type.'.'.$fileName] = [
            'name' => $fileName,
            'type' => $type,
            'priority' => $priority,
            'url' => $file,
        ];
    }

    // load all the files
    public static function load()
    {
        sort_subvalue(self::$files, 'priority');

        // debug(self::$files);

        foreach (self::$files as $file) {
            if ($file['url']) {
                self::loadFile($file['url']);
            }

            if ($file['type'] == 'template') {
                StyleLoader::add($file['name'], 'template');
                ScriptLoader::add($file['name'], 'template');
            }
        }
    }

    private static function loadFile($__file)
    {
        include_once $__file;

        $variables = get_defined_vars();

        foreach ($variables as $key => $value) {
            if (! in_array($key, ['__file'])) {
                if (! is_array($value) || array_is_list($value)) {
                    View::set($key, $value);
                } else {
                    foreach ($value as $arrayKey => $arrayValue) {
                        View::set($key, $arrayValue, $arrayKey);
                    }
                }
            }
        }
    }
}
