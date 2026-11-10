<section class="dashboard-navigation">
	<div class="floating">
		<div class="menu-wrapper">
			<div class="categories">

				<?php if ($dashboardConfig['logo']) { ?>
					<!-- logo -->
					<a class="header" href="<?= client_url('/') ?>">
						<div class="logo icon bgr-wht" <?= img_svg(LOGO_DIR.'logo.svg') ?>></div>
						<h1 class="title txt-low"><?= sys_config('name') ?></h1>
					</a>
				<?php } ?>

				<?php if ($dashboardConfig['return-link']) { ?>
					<!-- Return link -->
					<a href="<?= client_url('/teacher') ?>" class="dashboard-return">
						<h3 class="dashboard-return-title flex-line">
							<div class="text-icon" <?= icon_svg('back') ?>></div>
							Torna all'account
						</h3>
					</a>
				<?php } ?>

				<!-- Admin sections -->
				<div class="group">

					<!-- Home link -->
					<div class="category <?= ! $currentPath ? 'active current-category' : '' ?>">
						<a href="<?= client_url('/'.$dashboardConfig['path']) ?>" class="title-box grid">
							<div class="highlight"></div>
							<div class="icon-box">
								<?= icon_embed('home') ?>
							</div>
							<p class="title txt-line"><?= legacy__('Dashboard') ?></p>
						</a>
					</div>

					<!-- Navigation -->
					<?php foreach ($dashboardNavigation as $category) { ?>

						<!-- Menu category -->
						<div class="category <?= $category['checked'] ? 'active current-category' : '' ?>">

							<!-- Title box -->
							<div class="title-box grid">
								<div class="highlight"></div>
								<div class="icon-box">
									<?= icon_embed(APP_ICONS_DIR.'dashboards/'.$category['slug']) ?>
								</div>
								<p class="title txt-line"><?= $category['title'] ?></p>
							</div>

							<!-- Dropdown -->
							<div class="ddown" style="--count: <?= count($category['links']) ?>">

								<?php foreach ($category['links'] as $i => $link) { ?>

									<a href="<?= $link['url'] ?>" class="link-box <?= $link['checked'] ? 'active' : '' ?>">
										<div class="square square-<?= $i % 5 + 1 ?>"></div>
										<p class="link txt-line">
											<?= $link['title'] ?>
										</p>
									</a>

								<?php } ?>

							</div>
						</div>

					<?php } ?>

				</div>
			</div>
		</div>

		<!-- Mobile open menu icon -->
		<div id="open-menu" class="icon-box">
			<?= icon_embed('navigation/arrow1-right') ?>
		</div>
	</div>
</section>