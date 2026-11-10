<?php

if ($user->admin) {
    legacy_redirect(sysVar('logged_redirect'));
}
