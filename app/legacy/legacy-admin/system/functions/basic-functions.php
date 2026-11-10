<?php

// access the system config
function sys_config($field)
{
    global $sysConfig;

    return $sysConfig[$field] ?? null;
}

// access the vendor config
function vendorConfig($field)
{
    global $vendorConfig;

    return $vendorConfig[$field] ?? null;
}

// enable full error report
function full_debug()
{
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL ^ E_DEPRECATED);
}

// parse colors
function parse_rgba($hex, $alpha = 1)
{
    $rgba = [];
    $hex = str_replace('#', '', $hex);

    for ($i = 0; $i < 3; $i++) {
        $rgba[] = hexdec(substr($hex, $i * 2, 2));
    }

    $rgba[] = $alpha;

    return $rgba;
}

/*
* Translation
*/
function legacy__($text)
{

    static $translations = [];

    $language = $_SESSION['language'] ?? sys_config('default-language');

    if (empty($translations)) {

        if (file_exists($file = SERVER_ROOT.'/system/translations/'.$language.'.php')) {
            include_once $file;
        }
        $additionalFiles = scan('/app/translations/'.$language, 'php');

        foreach ($additionalFiles as $additionalFile) {
            include_once $additionalFile;
            $translations = array_merge($translations, $additionalTranslations);
        }
    }

    $translated = $translations[$text] ?? $text;

    return $translated;
}

/*
* User functions
*/
function user()
{
    global $user;

    return $user;
}

function userId()
{
    return user()->id;
}

function logged()
{
    return (bool) userId();
}

function guest()
{
    return ! logged();
}

function admin()
{
    return (bool) user()->admin;
}

function manager()
{
    return user()->admin == 2;
}

/*
* CSV download
*/
function download_csv($array, $title, $separator = ',', $download = true)
{

    function generate_csv(array &$array, $separator)
    {

        if (! count($array)) {
            return null;
        }

        ob_start();

        $df = fopen('php://output', 'w');
        fputcsv($df, array_keys(reset($array)), $separator);
        foreach ($array as $row) {
            fputcsv($df, $row, $separator);
        }

        fclose($df);

        return ob_get_clean();
    }

    function send_headers($filename, $download)
    {

        // disable caching
        $now = gmdate('D, d M Y H:i:s');
        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
        header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
        header("Last-Modified: {$now} GMT");

        // force download
        if ($download) {
            header('Content-Type: application/force-download');
            header('Content-Type: application/octet-stream');
            header('Content-Type: application/download');
        }

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header('Content-Transfer-Encoding: binary');
    }

    send_headers($title.'.csv', $download);
    echo generate_csv($array, $separator);
    exit();
}
