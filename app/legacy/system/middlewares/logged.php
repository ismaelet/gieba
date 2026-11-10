<?php

if (! $user->id) {
    legacy_redirect('/login');
}
