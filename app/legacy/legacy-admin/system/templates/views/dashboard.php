{% extends void %}

{% block main %}

<!-- Navigation bar -->
<?php require_once '-expands-dashboard/navigation.php'?>

<!-- Body -->
<section id="body">

	<div id="wrap">

		{% place content %}

	</div>

</section>

{% end main %}