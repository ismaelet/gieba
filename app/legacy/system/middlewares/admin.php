<?php

if (! $user->admin) {
    legacy_redirect('/login');
}
