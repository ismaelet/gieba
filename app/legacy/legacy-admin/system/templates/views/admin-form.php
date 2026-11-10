{% extends admin %}

{% block content %}

<form method="POST" action="#form" enctype="multipart/form-data" autocomplete="off" id="form" class="file-form <?= $pageConfig['centered'] ? 'fa-center' : '' ?>">

	<!-- Head -->
	<section class="head">
		<h1 class="admin-form__title"><?= $pageConfig['title'] ?></h1>
	</section>

	<!-- Body -->
	<section class="body">

		<?php if ($pageConfig['free']) { ?>

			{% place form-body %}

		<?php } elseif ($inputs) {
		    generate_form($inputs, $entry);
		} ?>

	</section>

	<!-- Footer -->
	<section class="footer">

		<?php if ($pageConfig['free']) { ?>

			{% place form-footer %}

		<?php } else { ?>

			<button type="submit" id="submit" class="button <?= $pageConfig['upload'] ? 'file-submit' : ''; ?>" name="<?= $pageConfig['edit'] ? 'edit' : 'add' ?>" <?php if ($pageConfig['confirm']) { ?> onclick="return confirm('<?= $pageConfig['confirm'] ?>')" <?php } ?>>

				<?= $pageConfig['edit'] ? legacy__('Save') : legacy__('Add') ?>

			</button>

			<?php if ($pageConfig['upload']) { ?>
				<div class="file-load"></div>
			<?php } ?>

			<a href="<?= $pageConfig['return-url'] ?>" class="button button--secondary"><?= legacy__('Back') ?></a>

		<?php } ?>

	</section>

</form>

{% end content %}