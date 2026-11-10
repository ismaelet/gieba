<!-- Table head -->
<div class="admin-list__head">

	<?php foreach ($fields as $field) {
	    $type = $field['type'] ?? 'show';

	    if (in_array($type, ['show', 'find', 'btn', 'image', 'custom', 'boolean-icon'])) {
	        $sort = $field['name'] ?? null;
	        $sort = ! empty($field['not-sortable']) || in_array($type, ['image', 'custom']) ? null : $sort;
	        $active = $sort && $sort == $queryConfig['order-index'];
	        ?>

			<label class="admin-list__field <?= $type == 'boolean-icon' ? 'admin-list__field--centered' : '' ?> <?= $sort ? 'interactive js-list-sort js-list-filter' : '' ?>">

				<?php if ($sort) { ?>

					<input type="radio" class="hidden js-list-field" name="index" value="<?= $sort ?>">
					<input type="radio" class="hidden js-list-field" name="order" value="<?= $queryConfig['order-direction'] == 'DESC' && $active ? 'ASC' : 'DESC' ?>">

				<?php } ?>

				<p class="list-field-name txt-line">
					<?= $field['title'] ?? '' ?>
					<?php if ($active) {
					    echo $queryConfig['order-direction'] == 'DESC' ? '&#x25BE;' : '&#x25B4;';
					} ?>
				</p>
			</label>

	<?php }
	    } ?>

</div>