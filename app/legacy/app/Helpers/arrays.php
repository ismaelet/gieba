<?php

/**
 * Extract the content of a markup from an html string
 */
function extract_markup(string $markup, string $content): ?string
{
    if (strpos($content, '<'.$markup.'>') !== false) {
        return explode('</'.$markup.'>', explode('<'.$markup.'>', $content, 2)[1], 2)[0];
    } else {
        return null;
    }
}
