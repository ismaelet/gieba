<?php if ($pagesCount > 1) { ?>

	<div class="change-page mar-t-2">

		<!-- First page -->
		<?php if ($pageConfig['first-page'] > 2) { ?>

			<p class="change-page__ellipsis">...</p>

		<?php } elseif ($pageConfig['first-page'] == 2) { ?>

			<label>
				<input type="radio" class="hidden js-list-filter" name="page" value="1">
				<p class="change-page__page js-change-page js-list-filter">1</p>
			</label>

		<?php } ?>

		<!-- Middle pages -->
		<?php for ($i = $pageConfig['first-page']; $i <= $pageConfig['last-page']; $i++) { ?>

			<label>
				<input type="radio" class="hidden js-list-filter" name="page" value="<?= $i ?>" <?= $currentPage == $i ? 'checked' : '' ?>>
				<p class="change-page__page <?= $currentPage == $i ? 'active' : '' ?>">
					<?= $i ?>
				</p>
			</label>

		<?php } ?>

		<!-- Last page -->
		<?php if ($pagesCount > $pageConfig['last-page'] + 1) { ?>

			<p class="change-page__ellipsis">...</p>

		<?php } elseif ($currentPage == $pageConfig['last-page'] + 1) { ?>

			<label>
				<input type="radio" class="hidden js-list-filter" name="page" value="<?= $currentPage ?>">
				<p class="change-page__page js-change-page"><?= $currentPage ?></p>
			</label>

		<?php } ?>

	</div>

<?php } ?>