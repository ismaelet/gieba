<?php

function load_module($name)
{

    $module = false;

    if (file_exists($file = SERVER_ROOT.'/app/modules/'.$name.'.php')) {
        $module = $file;
    } elseif (file_exists($file = SERVER_ROOT.'/system/modules/'.$name.'.php')) {
        $module = $file;
    }

    if ($module) {
        include_once $module;
    }
}
