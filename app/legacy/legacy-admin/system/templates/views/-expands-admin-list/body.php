<?php

foreach ($listRows as $rowId => $result) {

    // if set, make the row a link
    if (isset($pageConfig['row-action'])) {
        $rowAction = str_replace('{{id}}', $rowId, $pageConfig['row-action']);
        $listFilters = explode('?', $_SERVER['REQUEST_URI'], 2)[1] ?? false;

        if ($listFilters) {
            $listFilters = urlencode($listFilters);
            $rowAction .= '&list-filters='.$listFilters;
        } ?>

		<a class="admin-list__row" href="<?= $rowAction  ?>">

		<?php } else { ?>

			<div class="admin-list__row">

				<?php }

		// generate the fields
    foreach ($result as $entry) {

        switch ($entry['type']) {

            case 'show':
            case 'find':
                /*
                    * Show & find fields
                    */
                $fullContentSnippet = $entry['overflow'] ? '<p class="text-full">'.$entry['value'].'</p>' : '';
                $classesSnippet = implode(
                    ' ',
                    array_filter(['field-'.$entry['name'], ($entry['overflow'] ? 'overflow' : ''), ...$entry['custom-classes']])
                );
                ?>

						<div class="admin-list__field text-field <?= $classesSnippet  ?>" id="row<?= $rowId ?>">
							<p class="text-short"><?= $entry['showed-value'] ?></p>
							<?= $fullContentSnippet ?>
						</div>

					<?php
                        break;

            case 'image':
                /*
                    * Image field
                    */
                $maxSizeSnippet = $entry['max-size'] ? '--_max-size: '.$entry['max-size'].'rem;' : '';
                ?>
						<div class="admin-list__field">
							<img class="admin-list__image" src="<?= $entry['src'] ?>" alt="" loading="lazy" style="<?= $maxSizeSnippet ?>">
						</div>
					<?php
                    break;

            case 'boolean-icon':
                /*
                    * Boolean icon field
                    */
                ?>
						<div class="admin-list__field">
							<?php if ($entry['value']) { ?>
								<div class="admin-list__icon active"><?= icon_embed('correct') ?></div>
							<?php } else { ?>
								<div class="admin-list__icon inactive"><?= icon_embed('wrong') ?></div>
							<?php } ?>
						</div>

					<?php
                    break;

            case 'custom':
                /*
                    * Custom field
                    */
                ?>
						<div class="admin-list__field">
							<?= $entry['content'] ?>
						</div>

					<?php
                    break;

            case 'hidden':
                /*
                    * Hidden field
                    */
                ?>
						<input type="hidden" name="<?= $entry['name'] ?>" value="<?= $entry['value'] ?>">

					<?php
                    break;

            case 'btn':
                /*
                    * Button field
                    */
                ?>

						<div class="admin-list__field">
							<div class="admin-list_buttons">

								<?php if (isset($entry['buttons'])) {
								    foreach ($entry['buttons'] as $button) {

								        if ($button['type'] == 'link') {
								            echo '
									<object>
										<a
											href="'.$button['url'].'"
											class="admin-list__button '.$button['custom-class'].'"
											data-color="'.$button['color'].'" '.
								                ($button['download'] ? 'download' : '').'>
												'.$button['title'].'
										</a>
									</object>';
								        } elseif ($button['type'] == 'button') { ?>

										<object>
											<button type="<?= $button['name'] ? 'submit' : 'button' ?>" <?= $button['name'] ? 'name="'.$button['name'].'" value="'.$rowId.'"' : '' ?> class="admin-list__button <?= $button['custom-class'] ?>" data-color="<?= $button['color'] ?>" <?php if ($button['warning']) { ?> onclick="return confirm('<?= $button['warning'] ?>')" <?php } ?>>

												<?= $button['title'] ?>

											</button>
										</object>

									<?php } ?>

								<?php }
								    } ?>

							</div>
						</div>

			<?php
                        break;
        }
    } ?>


			<?php if (isset($pageConfig['row-action'])) { ?>

		</a>

	<?php } else { ?>

		</div>

<?php }
	}
