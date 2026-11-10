<?php

// setup Google Analytics
function google_analytics($user)
{

    if (sysData('google_analytics') && ! $user->admin) {
        return file_get_contents(SERVER_ROOT.'/vendor/google_analytics/analytics.php');
    }
}
