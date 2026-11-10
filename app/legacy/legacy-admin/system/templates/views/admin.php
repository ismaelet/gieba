{% extends void %}

{% block main %}

<!-- Navigation bar -->
<?php require_once '-expands-admin/menu.php'?>

<!-- Body -->
<section class="page-content">
	<div class="page-wrapper">

		{% place content %}

	</div>
</section>

{% end main %}