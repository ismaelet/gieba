<?php

use Legacy\System\Classes\DB;

function place_text($slug)
{
    $textRow = DB::row('* FROM texts WHERE slug=?', [$slug]);

    $content = $textRow['text'];

    if (admin()) {
        $content .= ' <a class="txt-bold" href="'.client_url('/admin/content/texts/add?id='.$textRow['id']).'">Modifica</a>';
    }

    return $content;
}

function place_image($slug)
{
    $content = '';

    if (admin()) {
        $imageRow = DB::row('id FROM images WHERE path=?', [$slug]);
        $content =
            '<a class="admin-replace-img" href="'.client_url('/admin/content/images/add?id='.$imageRow['id']).'">
				<div class="admin-replace-img_icon icon-box" href="'.client_url('/admin/content/images/add?id='.$imageRow['id']).'">
					'.icon_embed('upload').'
				</div>
			</a>';
    }

    return $content;
}
