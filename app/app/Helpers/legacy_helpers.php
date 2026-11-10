<?php

function client_url($url = '')
{
    return 'legacy/'.$url;
}

// put a svg icon as background image
function icon_svg($icon)
{
    return 'style="mask: url('.icon_url($icon).'); -webkit-mask: url('.icon_url($icon).') center;"';
}

// get the client url of an icon
function icon_url($file)
{
    $urlRaw = icon_url_raw($file);
    $url = CLIENT_ROOT.file_time($urlRaw);

    return $url;
}
