<div class="field">

	<div class="buttons">

		<?php if (isset($entry['buttons'])) {
		    foreach ($entry['buttons'] as $button) {

		        if ($button['type'] == 'link') { ?>

				<a
					href="<?= $button['url'] ?>"
					class="btn <?= $button['color'] ?> <?= $button['custom-class'] ?>"
					<?= $button['download'] ? 'download' : '' ?>
				>

					<?= $button['title'] ?>
						
				</a>

			<?php } elseif ($button['type'] == 'button') { ?>

				<button
					type="<?= $button['name'] ? 'submit' : 'button' ?>"
					<?= $button['name'] ? 'name="'.$button['name'].'"' : '' ?>
					class="btn <?= $button['color'] ?> <?= $button['custom-class'] ?>"

					<?php if ($button['warning']) {?>
						onclick="return confirm('<?= $button['warning'] ?>')"
					<?php } ?>

				>

					<?= $button['title'] ?>
					
				</button>

			<?php } ?>

		<?php }
		    } ?>

	</div>
	
</div>