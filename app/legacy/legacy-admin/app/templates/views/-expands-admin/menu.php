<section class="menu js-menu">
	<div class="menu__content">

		<div class="">
			<!-- Title -->
			<div class="menu__header" href="<?= PUBLIC_CLIENT_URL ?>">
				<?= $navigationLogo ?>
				<h1 class="menu__title"><?= sys_config('name') ?></h1>
			</div>

			<!-- Admin sections -->
			<div class="menu__groups grid-flow js-open-menu" data-spacing="400">
				<div class="menu__group">
					<!-- Home link -->
					<div class="menu-item">
						<a class="menu-item__header" href="<?= public_client_url('/') ?>" target="_blank">
							<?= icon_embed('link') ?>
							<p class="menu-item__title">Sito pubblico</p>
						</a>
					</div>
				</div>

				<span class="menu__divider"></span>

				<div class="menu__group">

					<!-- Home link -->
					<div class="menu-item">
						<a class="menu-item__header" href="<?= client_url('/') ?>">
							<?= icon_embed('home') ?>
							<p class="menu-item__title">Home</p>
						</a>
					</div>

					<!-- Navigation -->
					<?php foreach ($menu['categories'] as $category) { ?>

						<!-- Menu category -->
						<div class="menu-item <?= $category['checked'] ? 'active' : '' ?> js-menu-category">

							<!-- Item header -->
							<div class="menu-item__header js-open-menu-category">
								<?= icon_embed(APP_ICONS_DIR.'navigation/'.$category['slug']) ?>
								<p class="menu-item__title"><?= $category['name'] ?></p>
							</div>

							<!-- Dropdown -->
							<div class="menu__dropdown">

								<?php foreach ($category['sections'] as $section) { ?>

									<div class="menu-subitem">
										<p class="menu-subitem__title <?= $section['checked'] ? 'active' : '' ?>">
											<a href="<?= $section['url'] ?>"><?= $section['name'] ?></a>
										</p>
									</div>

								<?php } ?>

							</div>
						</div>

					<?php } ?>

					</span>
				</div>

				<span class="menu__divider"></span>

				<form class="menu__group" method="POST">
					<button class="button button--bad button--secondary button-shutdown" name="shutdown" onclick="return confirm('Sicuro di voler uscire?')">
						<?= icon_embed('exit') ?>
						<span>Esci</span>
					</button>
				</form>
			</div>

			<!-- Mobile open menu icon -->
			<div class="menu__open js-toggle-menu">
				<?= icon_embed('navigation/arrow1-right') ?>
			</div>
		</div>
	</div>
</section>