<?php

// avoid cache if the chached file is not up to date
function file_time($file)
{

    if (! strpos($file, '?=') && file_exists($url = SERVER_ROOT.$file)) {
        $file .= '?='.filemtime($url);
    }

    return $file;
}

// put the image as background image
function img_css($img, $view = false)
{

    return 'style="background-image: url('.client_url($img, $view).');"';
}

// put a svg as background image
function img_svg($img, $view = false)
{

    return 'style="mask: url('.client_url($img, $view).'); -webkit-mask: url('.client_url($img, $view).') center;"';
}

// get the url of an icon
function icon_url($icon)
{

    $file = __ICON__.$icon.'.svg';

    if (! file_exists(base_path().'/public/legacy/'.$file)) {
        $file = __ICON__.$icon.'.png';
    }

    return __HROOT__.'/legacy/'.file_time($file);
}

// put an icon as background image
function icon_css($icon)
{

    return 'style="background-image: url('.icon_url($icon).');"';
}

// put a svg icon as background image
function icon_svg($icon)
{

    return 'style="mask: url('.icon_url($icon).'); -webkit-mask: url('.icon_url($icon).') center;"';
}

// html url
function client_link($url = '')
{

    return __HROOT__.'/'.$url;
}

// get the url of a page's image folder
function path_img_dir($page_url)
{

    $url = substr($page_url, strpos($page_url, 'pages') - 1);

    $url = str_replace(['views', '.php'], ['images', '/'], $url);

    return $url;
}

// parse a url from php to html
function parse_html($url)
{

    $url = str_replace('\\', '/', $url);

    if (strpos($url, SERVER_ROOT) !== false) {
        $url = __HROOT__.'/'.substr($url, strlen(SERVER_ROOT) + 1);
    }

    return $url;
}

// get all files in a folder
function scan($path, $ext = '*')
{

    $files = [];

    if (strpos($path, SERVER_ROOT) === false) {
        $path = SERVER_ROOT.$path;
    }

    foreach (glob($path.'/*.'.$ext) as $file) {
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
