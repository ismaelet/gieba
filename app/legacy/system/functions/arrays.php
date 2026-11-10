<?php

function bump(&$array)
{

    array_unshift($array, '');

    unset($array[0]);
}
