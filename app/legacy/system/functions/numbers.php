<?php

// format numbers
function format($n)
{
    return number_format($n, 2, '.', '');
}
function formatc($n)
{
    return number_format($n, 2, ',', '');
}

function random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}
