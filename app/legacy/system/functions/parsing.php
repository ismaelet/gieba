<?php

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
