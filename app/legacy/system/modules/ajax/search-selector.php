<?php

use Legacy\System\Classes\DB;

// setup
$full = false;
include_once '../../setup/setup.php';

// setup variables
$tbl = $_POST['tbl'];
$filter = str_replace(',', ' AND ', $_POST['filter']);
$filter_val = $_POST['filter_val'];
$field_id = $_POST['field_id'];
$field_show = $_POST['field_show'];

// fetch data
$res = DB::select($field_id.', '.$field_show.' FROM '.$tbl.' WHERE '.$filter.' ORDER BY '.$field_id.' DESC', $filter_val);

// parse to json
$array = [];

while ($row = $res->fetch_assoc()) {
    $array[] = $row;
}

echo json_encode($array);
