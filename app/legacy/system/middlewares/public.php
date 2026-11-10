<?php

use Legacy\System\Classes\DB;

$public = sysDB('static') ?: DB::find('value FROM config WHERE name=?', ['public']);

if (! $public && ! $user->admin) {
    legacy_redirect('/offline');
}
