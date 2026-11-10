{% extends blank %}

{% block body %}

<?php include_once SERVER_ROOT.'/app/templates/views/header.php'; ?>

<main>

	{% place main %}

	{% end main %}

</main>

<?php include_once SERVER_ROOT.'/app/templates/views/footer.php'; ?>

{% end body %}