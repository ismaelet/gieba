<?php

use Legacy\System\Classes\DB;

$response = [];
$table = 'tableName';
$showField = 'name';
$search = $_GET['search'] ?? '';
$search = trim($search);

if ($search) {
    $results = DB::select('id, '.$showField.' FROM '.$table.' WHERE id=? OR '.$showField." LIKE CONCAT('%',?,'%') ORDER BY ".$showField.' ASC LIMIT 10', [$search, $search]);

    while ($result = $results->fetch_assoc()) {
        $response[] = [
            'value-field' => $result['id'],
            'show-field' => $result[$showField],
        ];
    }
}

echo json_encode($response);
