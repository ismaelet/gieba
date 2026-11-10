<?php

use App\View\View as AppView;
use Legacy\System\Classes\View as GlobalView;

?>

<!doctype html>
<html data-theme="dark" data-theme-fluid="<?= empty(sys_config('theme-fluid')) ? 0 : 1 ?>" data-client-root="<?= CLIENT_ROOT ?>">

<head>
	<meta charset="utf-8">
	<meta name="GlobalViewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
	<meta name="description" content="<?= GlobalView::$description ?>">
	<meta property="og:image" content="<?= GlobalView::$thumbnail ?>">
	<meta property="og:description" content="<?= GlobalView::$description ?>">

	<title><?= GlobalView::$tabTitle ?></title>

	<!-- Styles -->
	<?= StyleLoader::load().PHP_EOL.PHP_EOL ?>
	<?= AppView::stackedStyles() ?>

	<!-- Scripts -->
	<script defer src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<?= AppView::stackedScripts() ?>
	<?= ScriptLoader::load().PHP_EOL.PHP_EOL ?>
	<?= ScriptLoader::loadHeadInline().PHP_EOL.PHP_EOL ?>

	<!-- Favicon -->
	<?= favicon().PHP_EOL ?>
</head>

<body>
	{% place body %}

	<?php if (sys_config('cookies-warning') && ! isset($_COOKIE['cookies-warning'])) { ?>

		<div class="cookies-warning">
			<h3 class="cookies-warning_title">Avviso cookies</h3>
			<div class="cookies-warning_body">
				<p class="cookies-warning_text">
					Questo sito fa uso di cookie per migliorare l’esperienza di navigazione degli utenti e per raccogliere informazioni sull’utilizzo del sito stesso. Utilizziamo sia cookie tecnici sia cookie di parti terze per inviare messaggi promozionali sulla base dei comportamenti degli utenti. Può conoscere i dettagli consultando la nostra privacy policy <a class="cookies-warning_link" href="<?= client_url('/privacy') ?>">qui</a>. Proseguendo nella navigazione si accetta l’uso dei cookie; in caso contrario è possibile abbandonare il sito.
				</p>
				<div class="cookies-warning_footer">
					<div class="cookies-warning_pbtn cookies-warning_btn pbtn js-accept-cookies-warning">Accetta</div>
					<!-- <div class="cookies-warning_sbtn cookies-warning_btn sbtn">Rifiuta</div> -->
				</div>
			</div>
		</div>

	<?php } ?>
</body>

<!-- Foot scripts -->
<?= ScriptLoader::loadFootInline() ?>

</html>