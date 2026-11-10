<?php

use Legacy\System\Classes\DB;
use Legacy\System\Classes\Route;

/**
 * Ajax calls
 */
Route::add('/ajax/*', function ($path) {
    $ajaxFile = null;
    $ajaxRequest = explode('ajax/', $path)[1] ?? null;
    $ajaxModule = explode('?', $ajaxRequest)[0] ?? null;

    if (file_exists($file = SERVER_ROOT.'/app/functions/ajax/'.$ajaxModule.'.php')) {
        $ajaxFile = $file;
    } elseif (file_exists($file = SERVER_ROOT.'/system/functions/ajax/'.$ajaxModule.'.php')) {
        $ajaxFile = $file;
    }

    if ($ajaxFile) {
        include_once $ajaxFile;
    } else {
        echo 'Error: invalid requested file';
    }
});

/**
 * API calls
 */
// Function call
Route::add('/api/call-function/*', function ($path) {
    $response = [];

    // request data
    $request = explode('call-function/', $path)[1] ?? null;
    $functionName = str_replace('/', '\\', explode('?', $request)[0]) ?? null;
    $arguments = isset($_GET['arguments']) ? (array) $_GET['arguments'] : [];

    $response = call_user_func($functionName, ...$arguments);

    echo json_encode($response);
});

// Search a table
Route::add('/api/search-table/*', function ($path) {
    $response = [];
    $queryFilters = [];
    $queryParameters = [];

    // request data
    $request = explode('search-table/', $path)[1] ?? null;
    $table = str_replace('/', '\\', explode('?', $request)[0]) ?? null;
    $searchFields = explode(',', $_GET['searchFields']);
    $responseFields = explode(',', $_GET['responseFields']);
    $orderField = $_GET['orderField'] ?? null;
    $orderDirection = $_GET['orderDirection'] ?? 'DESC';
    $limit = $_GET['limit'] ?? 10;
    $search = $_GET['search'] ?? null;
    $filter = $_GET['filter'] ?? null;

    if ($search) {
        $querySelect = implode(', ', $responseFields);
        $queryOrder = $orderField;
        $queryOrderDirection = strtoupper($orderDirection);

        foreach ($searchFields as $searchField) {
            $queryFilters[] = 'LOWER('.$searchField.") LIKE CONCAT('%',?,'%')";
            $queryParameters[] = strtolower($search);
        }

        $queryFilters = implode(' OR ', $queryFilters);

        $query = $querySelect.' FROM '.$table.' WHERE '.$queryFilters.($filter ? ' AND '.$filter.'' : null).'  ORDER BY '.$queryOrder.' '.$queryOrderDirection.' LIMIT '.$limit;

        $response = (DB::select($query, $queryParameters))->res;
    }

    echo json_encode($response);
});

// Class interactions
Route::add(['/api/get-class-properties/*', '/api/call-class-method/*'], function ($path) {
    $response = [];

    // request data
    $requetType = strpos($path, 'properties') ? 'properties' : 'method';
    $request = explode($requetType.'/', $path)[1] ?? null;
    $className = str_replace('/', '\\', explode('?', $request)[0]) ?? null;
    $objectId = $_GET['objectId'] ?? null;
    $object = $objectId !== null ? new $className($objectId) : null;
    $scope = $_GET['scope'] ?? null;
    $arguments = $_GET['arguments'] ?? null;
    $setProperties = isset($_GET['setProperties']) ? explode(',', $_GET['setProperties']) : [];

    // set the properties
    foreach ($setProperties as $setProperty) {
        $setProperty = explode('=', $setProperty);
        $setMethod = 'set'.ucfirst($setProperty[0]);
        $setValue = $setProperty[1];

        if ($object) {
            $object->$setMethod($setValue);
        } else {
            $className::$setMethod($setValue);
        }
    }

    // get the response
    if ($requetType == 'properties') {
        // get properties
        $scope = explode(',', $scope);

        foreach ($scope as $propertyName) {
            if ($object) {
                $propertyValue = $object->$propertyName;
            } else {
                $propertyValue = $className::$propertyName;
            }

            $response[$propertyName] = $propertyValue;
        }
    } else {
        if ($arguments && $arguments[0] == '[') {
            $arguments = explode(',', trim($arguments, '[]'));
            $response = $object ? $object->$scope($arguments) : $className::$scope($arguments);
        } elseif ($arguments) {
            $arguments = explode(',', $arguments);
            $response = $object ? $object->$scope(...$arguments) : $className::$scope(...$arguments);
        } else {
            $response = $object ? $object->$scope() : $className::$scope();
        }
    }

    echo json_encode($response);
});

/**
 * Favicon
 */
Route::add('/favicon.ico', function () {
    include SERVER_ROOT.'/app/assets/favicon/favicon.ico';
});
