<?php

use Legacy\System\Classes\DB;

$response = [];
$table = 'ecommerce_customers';
$showField = 'name';
$search = $_GET['search'] ?? '';
$search = strtolower(trim($search));

if ($search) {
    $results = DB::select('id, '.$showField.' FROM '.$table.' WHERE id=? OR LOWER('.$showField.") LIKE CONCAT('%',?,'%') ORDER BY ".$showField.' ASC LIMIT 10', [$search, $search]);

    while ($result = $results->fetch_assoc()) {
        $response[] = [
            'value-field' => $result['id'],
            'show-field' => $result[$showField],
        ];
    }
}

echo json_encode($response);
