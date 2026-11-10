<?php

if ($user->id) {
    legacy_redirect(sysVar('logged_redirect'));
}
