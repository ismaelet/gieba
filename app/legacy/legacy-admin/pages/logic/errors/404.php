<?php

// store error
$validEntry = true;
$url = $_SERVER['REQUEST_URI'] ?? false;
$src = $_SERVER['HTTP_REFERER'] ?? '';

$systemExceptions = ['.html', '.php', '.txt', '.well-known', 'alfa', 'apple-app', 'apple-touch', 'assets/plugins', 'auth_log', 'issmall', 'my-account', 'sitemap', 'test_404_page', 'wp-admin', 'wc-api', 'wp-content', 'wp-includes', 'wp-json', 'wp-plain'];

$appExceptions = [];

$exceptions = array_merge($systemExceptions, $appExceptions);

foreach ($exceptions as $exception) {
    if (strpos($url, $exception) !== false) {
        $validEntry = false;
        break;
    }
}

if ($validEntry) {
    // error_log('Error 404: destination: ' . $url . ' source: ' . $src);
}
