<header class="header" data-mobile-threshold="<?= $header['mobile-threshold'] ?>">
	<div class="h-content">

		<!-- Header desktop -->
		<div class="h-desktop desk<?= $header['mobile-threshold'] ?>">
			<section class="h-bars h-bars--desktop">
				<!-- Top left bar -->
				<div class="h-bar h-bar--desktop">
					<?php foreach ($header['structure']['left'] as $h_component) {
					    echo $headerComponent[$h_component]();
					} ?>
				</div>
				<div class="h-bar h-bar--desktop">
					<?php foreach ($header['structure']['center'] as $h_component) {
					    echo $headerComponent[$h_component]();
					} ?>
				</div>
				<div class="h-bar h-bar--desktop">
					<?php foreach ($header['structure']['right'] as $h_component) {
					    echo $headerComponent[$h_component]();
					} ?>
				</div>
			</section>
		</div>

		<!-- Megamenus -->
		<?= $headerComponent['megamenus']() ?>

		<!-- Mobile Header -->
		<div class="h-mobile mob<?= $header['mobile-threshold'] ?>">

			<div class="h-bars h-bars--mobile">
				<div class="h-bar h-bar--mob">
					<?php foreach ($header['structure']['mobile-left'] as $h_component) {
					    echo $headerComponent[$h_component]();
					} ?>
				</div>
				<div class="h-bar h-bar--mob">
					<?php foreach ($header['structure']['mobile-center'] as $h_component) {
					    echo $headerComponent[$h_component]();
					} ?>
				</div>
				<div class="h-bar h-bar--mob">
					<?php foreach ($header['structure']['mobile-right'] as $h_component) {
					    echo $headerComponent[$h_component]();
					} ?>
				</div>
			</div>

			<!-- Mobile menu -->
			<?= $headerComponent['mobile-menu']() ?>
		</div>
	</div>
</header>