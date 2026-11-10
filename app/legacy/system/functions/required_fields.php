<?php

function prop_fields(&$fields, $fields_new)
{

    foreach ($fields_new as $new) {
        if (strpos($fields, $new) === false) {
            $fields .= ', '.$new;
        }
    }

    $fields = trim($fields, ', ');

}
