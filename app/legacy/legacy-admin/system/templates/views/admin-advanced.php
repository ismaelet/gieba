{% extends admin %}

{% block content %}

<form class="content-wrap" id="form" method="POST" enctype="multipart/form-data">

	<!-- Title -->
	<div class="page-title flex gap05">
		<a href="list" class="title-arrow icon-box center-items">
			<?php

use Legacy\System\Classes\View;

?><?= icon_embed('navigation/arrow1-left') ?>
		</a>
		<h1 class="add_title"><?= View::$title ?></h1>
	</div>

	<?php if ($updated) { ?>
		<p class="confirmation">Modifiche salvate</p>
	<?php } elseif ($added) { ?>
		<p class="confirmation">Elemento aggiunto</p>
	<?php } ?>

	<!-- Page body -->
	<div class="page-body">

		{% place page-body %}

	</div>

	<?php if (! empty($layout['delete']) || ! empty($layout['save']) || ! empty($layout['preview'])) { ?>

		<!-- Footer -->
		<section class="page-footer <?= $item->id ? '' : 'page-footer_add-item' ?>">

			<?php if (! empty($layout['delete'])) { ?>
				<!-- Delete -->
				<button type="submit" class="sbtn btn-with-icons" name="delete" onclick="return confirm('Confermi di voler eliminare questo elemento? Non potrà essere ripristinato.')">
					Elimina l'elemento
					<span class="text-icon" <?= icon_svg('delete') ?>></span>
				</button>
			<?php } ?>

			<?php if (! empty($layout['preview'])) { ?>
				<!-- Preview -->
				<a class="sbtn preview-btn btn-with-icons" href="<?= $item->url ?>" target="_blank">
					Esamina
					<span class="text-icon text-icon_big" <?= icon_svg('view') ?>></span>
				</a>
			<?php } ?>

			<?php if (! empty($layout['save'])) { ?>
				<!-- Save -->
				<button type="submit" class="pbtn btn-with-icons" name="submit-form">
					<?= $item->id ? 'Salva' : 'Aggiungi' ?>
					<span class="text-icon" <?= icon_svg($item->id ? 'save' : 'add') ?>></span>
				</button>
			<?php } ?>
		</section>

	<?php } ?>

	<!-- Loading panel -->
	<div class="loading">
		<div class="load-icon place-cen"></div>
	</div>

</form>

{% end content %}