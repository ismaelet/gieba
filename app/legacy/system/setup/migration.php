<?php

if ($migration) {
    migrate($con);
}

function migrate($con)
{

    // migrate system tables
    $tables = glob(SERVER_ROOT.'/system/migration/*.php');
    foreach ($tables as $table) {
        include_once $table;
    }

    // migrate app tables
    $tables = glob(SERVER_ROOT.'/app/migration/*.php');
    foreach ($tables as $table) {
        include_once $table;
    }
}
