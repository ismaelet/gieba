<?php

function snippet_binary_icon($value)
{
    if ($value) {
        $snippet = '<div class="binary-icon binary-icon_yes icon-box">';
        $snippet .= icon_embed('correct');
    } else {
        $snippet = '<div class="binary-icon binary-icon_no icon-box">';
        $snippet .= icon_embed('close');
    }

    $snippet .= '</div>';

    return $snippet;
}

function snippet_breadcrumbs($breadcrumbs)
{
    $snippetParts = [];

    foreach ($breadcrumbs as $name => $link) {
        if ($link) {
            $snippetParts[] = '<a href="'.$link.'">'.$name.'</a>';
        } else {
            $snippetParts[] = $name;
        }
    }

    $snippet = '<p class="breadcrumbs">';
    $snippet .= implode('<span class="breadcrumbs_arrow icon-box">'.icon_embed('navigation/arrow1-right').'</span>', $snippetParts);
    $snippet .= '</p>';

    return $snippet;
}

function animate_text($text, $speed = 5, $offset = 0, $style = 'bottom')
{

    $split = '';

    $text = str_split($text);
    $speed = $speed / 100;
    $offset = $offset / 100;

    if ($style == 'bottom') {
        $class = 'ld-bot';
    } elseif ($style == 'top') {
        $class = 'ld-top';
    }

    foreach ($text as $i => $char) {

        if ($style == 'mixedV') {
            $seed = rand(1, 2);
            $class = $seed == 1 ? 'ld-bot' : 'ld-top';
        }

        $split .= '<span class="load '.$class.' inlb" style="transition-delay: '.($speed * $i + $offset).'s;">'.$char.'</span>';
    }

    return $split;
}

// favicon snippet
function favicon()
{

    return '<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/legacy/admin/app/assets/favicon/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/legacy/admin/app/assets/favicon/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/legacy/admin/app/assets/favicon/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/legacy/admin/app/assets/favicon/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon-precomposed" sizes="60x60" href="/legacy/admin/app/assets/favicon/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/legacy/admin/app/assets/favicon/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/legacy/admin/app/assets/favicon/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/legacy/admin/app/assets/favicon/apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="/legacy/admin/app/assets/favicon/favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="/legacy/admin/app/assets/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="/legacy/admin/app/assets/favicon/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="/legacy/admin/app/assets/favicon/favicon-16x16.png" sizes="16x16" />
<link rel="icon" type="image/png" href="/legacy/admin/app/assets/favicon/favicon-128.png" sizes="128x128" />';
}
