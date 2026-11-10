<?php

use Legacy\System\Classes\View;

/*
* File url
*/
function generate_base_url(string $file = '')
{
    if (! strpos($file, '.')) {
        $file .= '.webp';
    }

    if ($file[0] == '/') {
        $url = $file;
    } else {
        $url = assets_directory(View::$url).$file;
    }

    return $url;
}

function client_path(string $file = '')
{
    $url = '/legacy/admin'.file_time(generate_base_url($file));

    return $url;
}

function public_client_path(string $file = '')
{
    if (defined('PUBLIC_CLIENT_ROOT')) {
        $url = PUBLIC_CLIENT_ROOT.file_time(generate_base_url($file));
    } else {
        $url = client_path($file);
    }

    return $url;
}

function server_path(string $file = '')
{
    $url = SERVER_ROOT.generate_base_url($file);

    return $url;
}

function public_server_path(string $file = '')
{
    if (defined('PUBLIC_SERVER_ROOT')) {
        $url = PUBLIC_SERVER_ROOT.generate_base_url($file);
    } else {
        $url = server_path($file);
    }

    return $url;
}

function public_server_directory(string $path = '')
{
    if (defined('PUBLIC_SERVER_ROOT')) {
        $url = PUBLIC_SERVER_ROOT.$path;
    } else {
        $url = SERVER_ROOT.$path;
    }

    return $url;
}

/*
* Links
*/

function public_client_url(string $parcialUrl = '')
{
    if (defined('PUBLIC_CLIENT_ROOT')) {
        $url = PUBLIC_CLIENT_ROOT.$parcialUrl;
    } else {
        $url = client_url($parcialUrl);
    }

    return $url;
}

// avoid cache if the chached file is not up to date
function file_time($file)
{
    if (! strpos($file, '?=') && file_exists($url = SERVER_ROOT.$file)) {
        $file .= '?='.filemtime($url);
    }

    return $file;
}

// put the image as background image
function background_image($img)
{
    return 'style="background-image: url('.client_path($img).');"';
}

// put a svg as background image
function img_svg($img)
{
    return 'style="mask: url('.client_path($img).'); -webkit-mask: url('.client_path($img).') center;"';
}

// get the raw url of an icon
function icon_url_raw($file)
{
    $file = $file.'.svg';

    if ($file[0] == '/') {
        $urlRaw = $file;
    } elseif (file_exists(SERVER_ROOT.$path = assets_directory(View::$url).$file)) {
        $urlRaw = $path;
    } elseif (file_exists(SERVER_ROOT.$path = APP_ICONS_DIR.$file)) {
        $urlRaw = $path;
    } else {
        $urlRaw = ICONS_DIR.$file;
    }

    return $urlRaw;
}

// get an embedded icon
function icon_embed(string $file, array $customClasses = [])
{
    $classes = implode(' ', ['icon-embed', ...$customClasses]);

    $url = base_path().'/public/legacy/admin'.icon_url_raw($file);
    $icon = file_get_contents($url);
    $icon = str_replace('<svg', '<svg class="'.$classes.'"', $icon);

    return $icon;
}

// put an icon as background image
function icon_css($icon)
{
    return 'style="background-image: url('.icon_url($icon).');"';
}

// get the url of a page's image folder
function assets_directory(?string $pagePath = null)
{
    $path = null;

    if ($pagePath) {
        $path = substr($pagePath, strpos($pagePath, 'pages') - 1);
        $path = str_replace(['/views/', '.php'], ['/assets/', '/'], $path);
    }

    return $path;
}

// parse a url from server url to client url
function parse_html($url, $full = true)
{
    $url = str_replace('\\', '/', $url);

    if (strpos($url, SERVER_ROOT) !== false) {
        $url = $full ? CLIENT_ROOT.'/'.substr($url, strlen(SERVER_ROOT) + 1) : '/'.substr($url, strlen(SERVER_ROOT) + 1);
    }

    return $url;
}

// parse a url from client url to server url
function parse_server_path($url)
{
    if (strpos($url, CLIENT_ROOT) !== false) {
        $url = SERVER_ROOT.explode(CLIENT_ROOT, $url)[1];
    }

    return $url;
}

// get all files in a folder
function scan(string $path, string $ext = '*', string $filter = '*')
{
    $files = [];

    if (strpos($path, SERVER_ROOT) === false) {
        $path = SERVER_ROOT.$path;
    }

    foreach (glob($path.'/'.$filter.'.'.$ext) as $file) {
        $files[] = $file;
    }

    return $files;
}

// get all files in the subfolders of a folder
function scans($path, $ext = '*')
{
    $files = [];

    foreach (glob(SERVER_ROOT.$path.'/*', GLOB_ONLYDIR) as $dir) {
        array_push($files, ...scan($dir, $ext));
    }

    return $files;
}

// inlcude a file if it exits
function try_include(string $filePath)
{
    if (file_exists($filePath)) {
        include $filePath;
    }
}

// get the server root
function force_base_url(string $filePath)
{
    return strpos($filePath, SERVER_ROOT) !== false ? explode(SERVER_ROOT, $filePath)[1] : $filePath;
}

// get the server root
function server_root()
{
    return defined('PUBLIC_SERVER_ROOT') ? PUBLIC_SERVER_ROOT : SERVER_ROOT;
}

// get the public server root
function public_server_root()
{
    return server_root();
}

/*
* Redirect
*/
function legacy_redirect($path = false)
{
    if (empty($path)) {
        $url = $_SERVER['REQUEST_URI'];
    } elseif ($path[0] == '?') {
        $url = explode('?', $_SERVER['REQUEST_URI'])[0].$path;
    } elseif ($path[0] == '#') {
        $url = explode('#', $_SERVER['REQUEST_URI'])[0].$path;
    } elseif ($path[0] == '&') {
        $url = $_SERVER['REQUEST_URI'].$path;
    } elseif ($path[0] == '/') {
        $url = client_url($path);
    } else {
        $url = $path;
    }

    header('Location: '.$url);

    exit();
}
