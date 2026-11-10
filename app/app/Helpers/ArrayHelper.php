<?php

namespace App\Helpers;

class ArrayHelper
{
    /**
     * Explode the last occurrence of a string in a string
     */
    public static function explodeLast(string $separator, ?string $string): array
    {
        $newArray = explode($separator, strrev($string), 2);
        $newArray = array_map('strrev', $newArray);
        $newArray = array_reverse($newArray);

        return $newArray;
    }
}
