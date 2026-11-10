<?php

start_functions();

function start_functions()
{

    // load system functions
    $modules = glob(SERVER_ROOT.'/system/functions/*.php', GLOB_BRACE);

    foreach ($modules as $module) {
        include_once $module;
    }

    // load app functions
    $modules = glob(SERVER_ROOT.'/app/functions/*.php', GLOB_BRACE);

    foreach ($modules as $module) {
        include_once $module;
    }
}
