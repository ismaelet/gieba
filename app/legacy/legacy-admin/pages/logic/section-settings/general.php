<?php

use Legacy\System\Classes\DB;

$public = DB::find('value FROM variables WHERE name=?', ['public']);

if (isset($_POST['public'])) {

    foreach ($_POST['cbx_public'] as $public);

    DB::update('variables SET value=? WHERE name=?', [$public, 'public']);
}
