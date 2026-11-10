{% block content %}

<input type="hidden" name="url" value="<?php if (isset($_GET['url'])) {
    echo $_GET['url'];
}?>">

<div class="load ld-lef">
	<input type="text" name="username" id="username" class="inp" placeholder="Username" required>
</div>

<div class="load ld-rig">
	<input type="password" name="password" id="password" class="inp" placeholder="Password" required>
</div>

<?php if ($error) {?><p class="inp err load ld-rig">Dati di accesso errati</p><?php }?>

<label class="cbx-box mart1 blk load ld-rig">
	<input type="checkbox" class="cbx cbx-mirror" name="remember" checked>

	<div class="cbx-icon">
		<div class="cbx-icon-dot"></div>
	</div>

	<p class="cbx-txt">Resta connesso</p>
</label>

<div class="form-foot">

	<div class="load ld-rig">
		<button type="submit" name="submit" id="submit" class="mbtn">Log In</button>
	</div>

</div>

<a href="<?= __HROOT__; ?>/login/recover">
	<p class="txt load ld-lef mart1 marl5px">Hai dimenticato la password?</p>
</a>

{% end content %}