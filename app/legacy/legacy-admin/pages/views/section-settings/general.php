<div id="sett-pan" class="gallery">
	<div class="sett-box">
		<h1 class="sett-title bgr-c1 txt-wht pado2 padv1">Pubblica</h1>
		<div class="sett-body pad2">
			<p class="sett-txt">Visibilità del sito:</p>

			<form class="sett-slid" method="post">
				<label class="sett-slid-dot <?php if (! $public) {
				    echo 'active';
				}?>">
					<input type="checkbox" class="hide" name="cbx_public[]" value="0" <?php if (! $public) {
					    echo 'checked';
					}?>>
					<p class="sett-slid-txt">Nascosto</p>
				</label>
				
				<div class="sett-slid-line"></div>

				<label class="sett-slid-dot <?php if ($public) {
				    echo 'active';
				}?>">
					<input type="checkbox" class="hide" name="cbx_public[]" value="1" <?php if ($public) {
					    echo 'checked';
					}?>>
					<p class="sett-slid-txt">Visibile</p>
				</label>
				<div class="sett-slid-focus no-trans bgr-c1"></div>

				<button type="submit" name="public" class="hide"></button>
			</form>
		</div>
	</div>
</div>