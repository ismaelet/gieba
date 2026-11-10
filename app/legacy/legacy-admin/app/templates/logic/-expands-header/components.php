<?php

use Legacy\System\Classes\View;

/*
* Desktop navigation bar
*/
$headerComponent['desktop-navigation'] = function () {
    global $header;
    ?>

	<nav class="h-nav">

		<?php foreach ((array) $header['links'] as $link) {
		    if (empty($link['mobile'])) { ?>

				<div class="h-link <?= ! isset($link['url']) ? 'h-link--inactive' : '' ?>">
					<a <?= isset($link['url']) ? 'href="'.$link['url'].'"' : '' ?> <?= isset($link['megamenu']) ? 'data-function="open-megamenu" data-target="'.$link['megamenu'].'"' : false ?>>
						<h3 class="h-link-title <?= View::$location == $link['loc'] ? 'active' : false ?>">
							<?= $link['title'] ?>
							<?= isset($link['drop']) ? '<span class="h-ddown-icon">&#x25BC;</span>' : false ?>
						</h3>
					</a>
					<?php if (isset($link['drop'])) { ?>

						<div class="h-ddown bgr-c1">

							<?php foreach ((array) $header['dropdowns'][$link['drop']] as $dropdownLink) { ?>
								<a href="<?= $dropdownLink[1] ?>">
									<p class="h-ddown-link"><?= $dropdownLink[0] ?></p>
								</a>
							<?php } ?>

						</div>
					<?php } ?>

				</div>
		<?php }
		    } ?>

	</nav>
<?php };

/*
* Mobile icon
*/
$headerComponent['mobile-icon'] = function () { ?>
	<div class="h-mob-icon" data-function="mobile-menu-toggle">
		<div class="h-mob-icon-part">
			<div class="h-mob-icon-line"></div>
		</div>
		<div class="h-mob-icon-part">
			<div class="h-mob-icon-line"></div>
		</div>
		<div class="h-mob-icon-part">
			<div class="h-mob-icon-line"></div>
		</div>
	</div>
<?php };

/*
* Mobile menu
*/
$headerComponent['mobile-menu'] = function () {
    global $header;
    ?>

	<div class="h-mob-menu">
		<div class="h-mob-bgr" data-function="mobile-menu-close"></div>
		<div class="h-mob-ddown">
			<div class="h-mob-close icon-box" data-function="mobile-menu-close">
				<?= icon_embed('close') ?>
			</div>

			<!-- Links -->
			<div class="h-mob-links">

				<?php foreach ((array) $header['links'] as $link) {
				    if (! isset($link['mobile']) || $link['mobile']) { ?>

						<!-- Link -->
						<label class="link">
							<input type="checkbox" class="hidden">

							<a class="hm-link_header flex-line" <?= ! isset($link['drop']) && ! isset($link['megamenu']) ? 'href="'.$link['url'].'"' : '' ?>>
								<h3 class="h-mob-link-title">
									<?= $link['title'] ?>
								</h3>

								<?php if (isset($link['drop']) || isset($link['megamenu'])) { ?>
									<div class="hm-ddown_icon icon-box">
										<?= icon_embed('navigation/arrow1-down') ?>
									</div>
								<?php } ?>
							</a>

							<?php if (isset($link['drop'])) { ?>

								<!-- Dropdown menu -->
								<div class="ddown-menu">

									<?php foreach ((array) $header['dropdowns'][$link['drop']] as $dropdownLink) { ?>

										<p class="ddown-link txt-wht-h bgr-c3-h">
											<a href="<?= $dropdownLink[1] ?>">
												<?= $dropdownLink[0] ?>
											</a>
										</p>

									<?php } ?>
								</div>

							<?php } ?>

							<?php if (isset($link['megamenu'])) {
							    $megamenuData = (array) $header['megamenus'][$link['megamenu']];
							    ?>

								<!-- Megamenu menu -->
								<div class="hm-ddown">

									<?php
							            if (isset($megamenuData['type']) && $megamenuData['type'] == 'free') {
							                foreach ($megamenuData['links'] as $ddownLink) { ?>

											<p class="hm-ddown_link">
												<a href="<?= $ddownLink[1] ?>">
													<?= $ddownLink[0] ?>
												</a>
											</p>

										<?php }
							                } else {
							                    foreach ($megamenuData as $ddownLinkGroup) { ?>

											<p class="hm-ddown_link">
												<a href="<?= $ddownLinkGroup['url'] ?>">
													<?= $ddownLinkGroup['title'] ?>
												</a>
											</p>

									<?php }
							                    } ?>
								</div>

							<?php } ?>

						</label>

				<?php }
				    } ?>

			</div>
		</div>
	</div>
<?php };

/*
* Logo
*/
$headerComponent['logo'] = function () { ?>
	<a class="h-logo" href="<?= client_url('/') ?>">
		<img class="img-fit" src="<?= client_url(LOGO_DIR.'logo.svg') ?>">
	</a>
<?php };

/*
* Name
*/
$headerComponent['name'] = '
	<div class="name-box">
		<a href="'.client_url('/').'">
			<h3 class="name desk1024">'.$header['name'].'</h3>
		</a>

		<a href="'.client_url('/').'">
			<h3 class="name mob1024">'.$header['name-mob'].'</h3>
		</a>
	</div>
';

/*
* Search bar
*/
$headerComponent['search-icon'] = function () { ?>
	<form class="h-search" action="<?= client_url('/shop#shop') ?>" method="GET">
		<input type="checkbox" class="hidden" id="h-srch-cbx">
		<label class="h-search-cta stack-content" for="h-srch-cbx">
			<div class="h-search-icon icon-box">
				<?= icon_embed('search') ?>
			</div>
		</label>

		<div class="h-search-body stack-content">
			<h3 class="h-search-title clickable"><?= legacy__('Search') ?></h3>
			<div class="h-search-inp">
				<input type="text" name="search" placeholder="<?= legacy__('Search') ?>">
			</div>
		</div>
	</form>
<?php };

$headerComponent['search-simple'] = '
	<form class="h-search h-search_simple" action="'.client_url('/shop#shop').'" method="GET" autocomplete="off">
		<div class="h-search-icon icon-box">
			'.icon_embed('search').'
		</div>
		
		<input type="text" class="h-search-inp" name="search" placeholder="'.legacy__('Search').'">
	</form>
';

/*
* Languages
*/
/*$headerComponent['languages'] = '

    <div class="h-languages load ld-bot ld-del2">

            <div class="h-language-icon h-language-icon_active bgr-fill" <?= icon_css('languages/' . $_SESSION['language']) ?>></div>

            <div class="h-languages-ddown">

                <?php foreach ($header['languages'] as $h_lang) { ?>

                    <a href="?lang=<?= $h_lang[1] ?>" class="h-language <?= $_SESSION['language'] == $h_lang[1] ? 'active' : '' ?>">

                        <div class="h-language-icon bgr-fill" <?= icon_css('languages/' . $h_lang[1]) ?>></div>

                        <p class="h-language-name"><?= $h_lang[0] ?></p>

                    </a>

                <?php } ?>

            </div>

    </div>
';*/

/*
* Theme switch
*/
/*$headerComponent['theme-switch'] = '
                    <!-- Theme switch -->
                    <div class="h-theme-switch h-nav-icon">
                        <div class="light-icon icon-box" data-theme="light">
                            <?= icon_embed('sun') ?>
                        </div>
                        <div class="dark-icon icon-box" data-theme="dark">
                            <?= icon_embed('moon') ?>
                        </div>
                    </div>

*/

/*
* Profile icon
*/
$headerComponent['profile'] = '
	<a class="h-profile h-nav-icon" href="'.client_url('/profile').'">
		<div class="icon-box">
			'.icon_embed('profile-circle').'
		</div>

		<p>'.($user->firstname ?: 'Profilo').'</p>
	</a>
';

/*
* Megamenus
*/
$headerComponent['megamenus'] = function () {
    global $header;
    ?>

	<section class="megamenus">

		<div class="megamenus_backdrop"></div>

		<?php foreach ((array) $header['megamenus'] as $megamenuName => $megamenu) { ?>

			<div class="megamenu <?= isset($megamenu['type']) ? 'megamenu--'.$megamenu['type'] : '' ?>" id="megamenu-<?= $megamenuName ?>">
				<div class="megamenu-content main-wrap pad-t-2 pad-b-2">

					<?php if (isset($megamenu['type']) && $megamenu['type'] == 'free') {
					    foreach ($megamenu['links'] as $megamenulink) {

					        if (! isset($megamenuSection['mobile']) || ! $megamenuSection['mobile']) { ?>

								<p class="megamenu_link">
									<a href="<?= $megamenulink[1] ?>"><?= $megamenulink[0] ?></a>
								</p>

							<?php }
					        }
					} else {
					    foreach ($megamenu as $megamenuSection) {

					        if (! isset($megamenuSection['mobile']) || ! $megamenuSection['mobile']) { ?>

								<div class="megamenu-group grid gap1">
									<?php if ($megamenuSection['title']) { ?>

										<h3 class="megamenu-title ">
											<a href="<?= $megamenuSection['url'] ?>"><?= $megamenuSection['title'] ?></a>
										</h3>

									<?php } ?>

									<div class="megamenu-links grid gap1">
										<?php foreach ($megamenuSection['links'] as $h_megamenuLink) { ?>
											<p class=""><a href="<?= $h_megamenuLink[1] ?>"><?= $h_megamenuLink[0] ?></a></p>
										<?php } ?>
									</div>
								</div>
					<?php }
					        }
					} ?>

				</div>
			</div>

		<?php } ?>

	</section>

<?php };
