<?php

function legacy_explode_last($separator, $string)
{
    $array = explode($separator, $string);

    return end($array);
}

function use_natural_keys(&$array)
{
    $i = 0;
    $newArray = [];

    foreach ($array as $value) {
        $i++;
        $newArray[$i] = $value;
    }

    $array = $newArray;
}

function sort_subvalue(&$array, $key)
{
    usort($array, function ($a, $b) use ($key) {
        return $a[$key] <=> $b[$key];
    });
}

function extract_subvalue(array $array, mixed $subKey)
{
    foreach ($array as $key => $values) {
        $array[$key] = $values[$subKey];
    }

    return $array;
}

function array_to_sql($array)
{
    $sqlArray = is_array($array) ? "'".implode("','", $array)."'" : '';

    return $sqlArray;
}

function rows_to_objects(?Query_Result $rows, string $className)
{
    $objects = [];

    while ($row = $rows->fetch_assoc()) {
        $objects[] = new $className($row[$className::INDEX]);
    }

    return $objects;
}

function debug($array)
{
    echo '<pre>'.print_r($array, true).'</pre>';
}
