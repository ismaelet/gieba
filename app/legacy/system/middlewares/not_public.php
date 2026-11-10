<?php

use Legacy\System\Classes\DB;

$public = sysDB('static') ?: DB::find('value FROM variables WHERE name=?', ['public']);

if ($public) {
    legacy_redirect('/');
}
