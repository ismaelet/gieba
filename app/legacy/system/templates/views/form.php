{% extends main %}

{% block main %}

<div id="bgr" class="bgr <?= isset($form['bgr_color']) ? 'bgr-'.$form['bgr_color'] : '' ?>" <?= $form['bgr'] ?? '' ?>></div>

<div class="form-tbl">

	<div class="form-cell">

		<form id="form" class="form" method="POST">

			<div class="form-head">

				<h1 class="form-title"><?= $title ?? $view->title; ?></h1>

			</div>
			
			<div class="form-body">

				{% place content %}
				{% end content %}

			</div>
		</form>
	</div>
</div>

{% end main %}