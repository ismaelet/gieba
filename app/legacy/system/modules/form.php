<?php

use Legacy\System\Classes\DB;

// Add to database
function form_db_add($tbl, $inputs)
{

    global $con;

    $fields = $pholders = '';
    $values = [];

    foreach ($inputs as $input) {

        $type = $input['type'] ?? 'text';

        if ($type != 'file') {

            if (isset($input['name'])) {

                $field = $input['name'];
                $val = $_POST[$field] ?? 0;

                $fields .= $field.', ';
                $pholders .= '?, ';
                $values[] = $val;
            }
        }
    }

    $fields = rtrim($fields, ', ');
    $pholders = rtrim($pholders, ', ');

    $query = $tbl.' ('.$fields.') VALUES ('.$pholders.')';

    return DB::add($query, $values);
}

// Edit database
function form_db_update($tbl, $inputs, $index, $index_val)
{

    $query = '';
    $values = [];

    foreach ($inputs as $input) {

        $type = $input['type'] ?? 'text';
        $type = $input['type_parse'] ?? $type;

        if (! in_array($type, ['file', 'error', 'done'])) {

            $name = $input['name'];

            if ($type == 'direct') {
                $value = $input['value'];
            } else {
                $value = $_POST[$name] ?? 0;
            }

            $values[] = $value;

            $query .= $name.'=?, ';
        }
    }

    $query = rtrim($query, ', ');

    $query = $tbl.' SET '.$query.' WHERE '.$index.'=?';
    $values[] = $index_val;

    DB::update($query, $values);
}

// Generate form
function gen_form($inputs, $data = false, $error = false, $done = false)
{

    foreach ($inputs as $input) {

        // get type
        $type = $input['type'] ?? 'text';

        // generate input
        if ($type != 'direct') {

            // get basic data
            $name = $input['name'] ?? false;
            $lbl = $input['lbl'] ?? false;
            $pholder = $input['pholder'] ?? $lbl;

            // get value
            $val = false;

            // if value is explicit
            if (isset($input['val'])) {
                $val = $input['val'];
            }

            // else get from POST
            elseif (isset($name) && isset($_POST[$name])) {
                $val = $_POST[$name];
            }

            // else get from result
            elseif ($data && $name && ! in_array($type, ['file', 'cbx'])) {
                $val = $data[$name];
            }

            // else if it's a checkbox
            elseif ($type == 'cbx') {
                $val = 1;
            }

            // start to generate
            // label
            if ($lbl) { ?>

				<p class="lbl"><?= $lbl; ?>:</p>

			<?php }

            // label
            if (isset($input['text'])) { ?>

				<p class="text txt-09rem"><?= $input['text'] ?></p>

			<?php }

            // selector
            if ($type == 'select') {

                $res = DB::select($input['query']) ?>

				<select name="<?= $name ?>" class="sel <?= isset($input['affect']) ? 'affect' : '' ?>" <?= isset($input['affect']) ? 'data-affect="'.$input['affect'].'"' : '' ?>>

					<?php if (isset($input['empty'])) { ?>

						<option value="0" selected>-Vuoto-</option>

					<?php }
					while ($row = $res->fetch_assoc()) { ?>

						<option value="<?= $row[$input['field_id']]; ?>" <?php if ($row[$input['field_id']] == $val) {
						    echo 'selected';
						} ?>>

							<?= $row[$input['field_show']] ?>

						</option>

					<?php } ?>

				</select>

			<?php

                // dependent selector
            } elseif ($type == 'select-dependent') { ?>

				<select class="sel" name="<?= $name ?>" data-tbl="<?= $input['query-tbl'] ?>" data-filter="<?= $input['query-filter'] ?>" data-id="<?= $input['field_id'] ?>" data-show="<?= $input['field_show'] ?>" data-root="<?= __HROOT__ ?>" disabled>
					<option>-</option>
				</select>

			<?php

                // text area
            } elseif ($type == 'textarea') { ?>

				<textarea class="textarea" placeholder="<?= $pholder; ?>" name="<?= $name; ?>" <?php if (isset($input['max'])) {
				    echo ' maxlength="'.$input['max'].'" ';
				} ?>><?php

                                                                                                                                                                                if ($val) {
                                                                                                                                                                                    echo $val;
                                                                                                                                                                                }

                ?></textarea>

			<?php

                // searcher
            } elseif ($type == 'search') {

                render_old_search_input($input['search']);
            }

            // error manager
            elseif ($type == 'error' && $error == $input['error']) { ?>

				<!-- Error manager -->
				<p class="inp err"><?= $input['txt']; ?></p>

			<?php

                // checkbox
            } elseif ($type == 'cbx') { ?>

				<label class="cbx-box">
					<input type="checkbox" class="cbx cbx-mirror" value="<?= $val ?>" <?php
                                                                                        if (isset($name)) {
                                                                                            echo ' name="'.$name.'" ';
                                                                                        }
                if (isset($input['checked']) || (isset($name) && isset($_POST[$name])) || ($data && $data[$name] == $val)) {
                    echo ' checked ';
                }
                if (isset($input['required'])) {
                    echo ' required ';
                }
                ?>>

					<div class="cbx-icon">
						<div class="cbx-icon-dot"></div>
					</div>

					<p class="cbx-txt"><?= $input['txt']; ?></p>
				</label>

			<?php

                // file visibile
            } elseif ($type == 'file') { ?>

				<input type="file" class="inp file <?= $input['size'] ?? ''; ?> <?php if (isset($input['submit'])) {
				    echo 'file-inp';
				} ?>" name="<?= $name; ?>" <?php
                                                                                                                                                                if (isset($input['required'])) {
                                                                                                                                                                    echo ' required ';
                                                                                                                                                                }
                if (isset($input['multiple'])) {
                    echo ' multiple ';
                }
                ?>>

			<?php

                // file hidden
            } elseif ($type == 'file_hidden') { ?>

				<input type="file" class="hide file <?= $input['submit'] ?? 'file-inp'; ?>" name="<?= $name; ?>" <?php
                                                                                                                    if (isset($input['required'])) {
                                                                                                                        echo ' required ';
                                                                                                                    }
                if (isset($input['multiple'])) {
                    echo ' multiple ';
                }
                ?>>

			<?php

                // button
            } elseif ($type == 'btn') { ?>

				<button type="<?= $input['submit'] ?? 'submit'; ?>" class="mart2 <?= $input['btn_type'] ?? 'mbtn'; ?> <?php if (isset($input['load'])) {
				    echo 'file-submit';
				} ?>" name="<?= $name ?? 'submit'; ?>">
					<?= $input['txt'] ?? ''; ?>

				</button>

				<?php if (isset($input['load'])) { ?>
					<div class="file-load"></div>
				<?php } ?>

			<?php

                // number
            } elseif ($type == 'number') { ?>

				<input type="number" class="inp <?= $input['size'] ?? ''; ?>" value="<?= $val; ?>" <?php
                                                                                                    if (isset($name)) {
                                                                                                        echo 'name="'.$input['name'].'" ';
                                                                                                    }

                if (isset($input['required'])) {
                    echo ' required ';
                }

                if (isset($input['max'])) {
                    echo 'max="'.$input['max'].'" ';
                }
                if (isset($input['min'])) {
                    echo 'min="'.$input['min'].'" ';
                }
                if (isset($input['step'])) {
                    echo 'step="'.$input['step'].'" ';
                }

                if ($type != 'file') {
                    echo ' placeholder="'.$pholder.'" ';
                }
                ?>>
			<?php

                // other inputs
            } elseif (in_array($type, ['text', 'mail', 'date', 'hidden'])) { ?>

				<input type="<?= $type; ?>" class="
						inp
						<?= $input['size'] ?? '' ?>
						<?php if (isset($input['classes'])) {
						    foreach ($input['classes'] as $class) {
						        echo ' '.$class;
						    }
						} ?>
					" value="<?= $val ?>" <?php
                                            if ($name) {
                                                echo ' name="'.$input['name'].'" ';
                                            }

                if (isset($input['disabled'])) {
                    echo ' disabled ';
                }

                if (isset($input['required'])) {
                    echo ' required ';
                }

                if (isset($input['max'])) {
                    echo ' maxlength="'.$input['max'].'" ';
                }

                if ($type != 'file') {
                    echo ' placeholder="'.$pholder.'" ';
                }

                if (isset($input['submit'])) {
                    echo ' onchange="$(this).parent().find(\'.form-submit\').click();" ';
                }

                ?>>
			<?php }

            // if it's needed a hidden submit button
            if (isset($input['submit'])) { ?>

				<button type="submit" class="hide form-submit" name="submit-file"></button>
<?php }
            }
    }
}
