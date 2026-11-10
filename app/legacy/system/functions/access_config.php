<?php

function sysDB($field)
{

    global $sysConfig;

    return $sysConfig[$field] ?? false;
}

function sysData($field)
{

    global $sysConfig;

    return $sysConfig[$field] ?? false;
}

function sysVar($field)
{

    global $sysConfig;

    return $sysConfig[$field] ?? false;
}

function sysConfig($field)
{
    global $sysConfig;

    return $sysConfig[$field] ?? false;
}
