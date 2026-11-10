<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// store the request
$request = explode('/api/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))[1];
$requestEndpoint = explode('?', $request)[0];

if (empty($requestEndpoint)) {
    header('HTTP/1.1 400 Bad Request');
} else {
    // store the method
    $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

    // check the request method
    if (! in_array($requestMethod, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
        header('HTTP/1.1 405 Method Not Allowed');
    }

    // decode request content
    if ($requestMethod == 'GET') {
        $requestContent = (array) $_GET;
    } else {
        $requestContent = (array) json_decode(file_get_contents('php://input'), true);
    }

    // process the request
    $controller = new APIController;
    $controller->processRequest($requestEndpoint, $requestMethod, $requestContent);
}
