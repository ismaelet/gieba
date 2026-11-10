<?php

use App\View\Components\Form\SearchInput;
use Legacy\System\Classes\DB;

/**
 * Execute a MySQL query from inputs
 *
 * @param  string  $table  name of the table
 * @param  array  $formBlocks  name of the table
 * @param  int  $id  (null), the ID of the item, if set the action is UPDATE, if not is INSERT INTO
 * @return int|null
 */
function query_from_inputs(string $table, array $formBlocks, ?int $id = null)
{
    $action = $id === null ? 'add' : 'update';
    $inputs = [];
    $values = [];
    $fields = [];

    // unpack the inputs
    foreach ($formBlocks as $formBlock) {
        if (isset($formBlock[0])) {
            foreach ($formBlock as $input) {
                $inputs[] = $input;
            }
        } else {
            $inputs[] = $formBlock;
        }
    }

    // prepare the parameters
    foreach ($inputs as $input) {
        $type = $input['type'] ?? 'text';

        if (! in_array($type, ['file', 'image']) && isset($input['name']) && empty($input['ignore'])) {
            $name = $input['name'];
            $value = isset($_POST[$name]) ? trim($_POST[$name]) : ($input['value'] ?? 0);

            // parse
            if (isset($input['parse'])) {
                switch ($input['parse']) {
                    case 'int':
                        $value = (int) $value;
                        break;
                    case 'float':
                        $value = (float) str_replace(',', '.', $value);
                        break;
                }
            }

            // store values
            $fields[] = $action == 'add' ? $name : $name.' = ?';
            $values[] = $value;
        }
    }

    $fields = implode(', ', $fields);

    // exectue the query
    if ($action == 'add') {
        $query = $table.' ('.$fields.')';

        return DB::add($query, $values);
    } elseif ($action == 'update') {
        $values[] = $id;
        $indexField = DB::execute('SHOW columns FROM '.$table.' WHERE `Key` = "PRI"')->res[0]['Field'];

        DB::update($table.' SET '.$fields.' WHERE '.$indexField.' = ?', $values);
    }
}

// Generate form
function generate_form($fields, $data = false, $error = false, $done = false)
{

    foreach ($fields as $field) {

        echo '<div class="form-field">';

        if (isset($field[0])) {
            foreach ($field as $input) {
                generate_input($input, $data, $error, $done);
            }
        } else {
            generate_input($field, $data, $error, $done);
        }

        echo '</div>';
    }
}

function generate_input($input, $data, $error, $done)
{

    // basic data
    $type = $input['type'] ?? 'text';
    $name = $input['name'] ?? false;
    $parse = $input['parse'] ?? false;
    $styling = $input['styling'] ?? false;

    // calculate value
    $value = false;

    // if value is explicit
    if (isset($input['value'])) {
        $value = $input['value'];
    }

    // else get from POST, if set
    elseif ($name && isset($_POST[$name])) {
        $value = $_POST[$name];
    }

    // else get from MySQL result, if set
    elseif ($name && isset($data[$name]) && $type != 'cbx') {
        $value = $data[$name];
    }

    // else if it's a checkbox
    elseif ($type == 'cbx') {
        $value = 1;
    }

    // parse value
    if ($parse) {
        switch ($parse) {
            case 'float':
                $value = format($value);
                break;
        }
    }

    // styling
    if ($styling == 'fancy') { ?>

		<div class="fancy-input <?= isset($input['size']) ? 'fancy-input_size'.$input['size'] : '' ?>">

			<?php }

    // start to generate
    // label
    switch ($type) {

        case 'lbl': ?>

				<p class="label"><?= $input['text'] ?>:</p>

			<?php break;

        case 'info': ?>

				<p class="form-text"><?= $input['text'] ?></p>

				<?php break;

        case 'done':

            if ($type == 'done' && $done) { ?>

					<p class="confirmation-message"><?= $input['text'] ?></p>

				<?php }
            break;

        case 'error':

            if ($input['error'] == $error) { ?>

					<p class="error"><?= $input['text'] ?></p>

				<?php }
            break;

        case 'auto-filled-search':

            echo render_search_input($input);

            break;

        case 'search':

            echo render_old_search_input($input);

            break;

        case 'file': ?>

				<input type="file" class="inp file" name="<?= $name ?>" <?= isset($input['multiple']) ? 'multiple' : '' ?> <?= ! empty($input['required']) ? 'required' : null ?>>

			<?php break;

        case 'select':

            if (isset($input['query'])) {
                $selectOptions = DB::select($input['query'])->res;
                $fieldId = $input['field-name'];
                $fieldShow = $input['field-show'];
            } elseif (isset($input['array'])) {
                $selectOptions = $input['array'];
                $fieldId = 0;
                $fieldShow = 1;
            }
            ?>

				<select class="sel <?= isset($input['affect']) ? 'affect' : '' ?>" <?= isset($input['affect']) ? 'data-affect="'.$input['affect'].'"' : '' ?> name="<?= $name ?>">

					<?php if (isset($input['empty'])) { ?>

						<option value="0" selected>- <?= legacy__('None') ?> -</option>

					<?php }
					foreach ($selectOptions as $option) { ?>

						<option value="<?= $option[$fieldId] ?>" <?php if ($option[$fieldId] == $value) {
						    echo 'selected';
						} ?>>
							<?= $option[$fieldShow] ?>
						</option>

					<?php } ?>

				</select>

			<?php break;

        case 'select-dependent': ?>

				<select class="sel" data-tbl="<?= $input['query-tbl'] ?>" data-filter="<?= $input['query-filter'] ?>" data-id="<?= $input['field-name'] ?>" data-show="<?= $input['field-show'] ?>" data-root="<?= CLIENT_ROOT ?>" name="<?= $name ?>" disabled>
					<option>-</option>
				</select>

			<?php break;

        case 'textarea': ?>

				<textarea class="textarea" name="<?= $name ?>" placeholder="<?= $input['text'] ?? '' ?>" <?= isset($input['maxlength']) ? 'maxlength="'.$input['maxlength'].'"' : '' ?> <?= isset($input['required']) ? 'required' : '' ?>><?= $value ?: ''
            ?></textarea>

			<?php break;

        case 'cbx': ?>

				<label class="
					checkbox
					<?= $input['custom-class'] ?? '' ?>
					<?= isset($input['size']) ? 'inp-size'.$input['size'] : '' ?>
				">

					<input type="checkbox" name="<?= $name ?>" value="<?= $value ?>" <?= ! empty($input['checked']) || ($name && isset($_POST[$name])) || ($name && isset($data[$name]) && $data[$name] == $value) ? 'checked' : '' ?> <?= isset($input['required']) ? 'required' : '' ?>>

					<span></span>

					<p><?= $input['text'] ?></p>

				</label>

			<?php break;

        case 'image':
            echo render_image_input($input);

            break;

        case 'btn': ?>

				<button type="<?= ! empty($input['submit']) ? 'submit' : 'button' ?>" class="<?= $input['btn-type'] ?? 'pbtn' ?> <?= isset($input['load']) ? 'file-submit' : '' ?> <?= $input['custom-class'] ?? '' ?>" name="<?= $name ?: 'submit' ?>" data-size="<?= $input['size'] ?? 4 ?>">
					<?= $input['text'] ?? '' ?>
				</button>

				<?php if (isset($input['load'])) { ?>

					<div class="file-load"></div>

				<?php } ?>

			<?php break;

        default: ?>
				<input type="<?= $type ?>" class="
					<?= $input['custom-class'] ?? '' ?>
				" name="<?= $name ?>" value="<?= $value ?>" placeholder="<?= $styling ? '' : ($input['text'] ?? '') ?>" <?= isset($input['minlength']) ? 'minlength="'.$input['minlength'].'"' : '' ?> <?= isset($input['maxlength']) ? 'maxlength="'.$input['maxlength'].'"' : '' ?> <?= isset($input['min']) ? 'min="'.$input['min'].'"' : '' ?> <?= isset($input['max']) ? 'max="'.$input['max'].'"' : '' ?> <?= isset($input['step']) ? 'step="'.$input['step'].'"' : '' ?> <?= isset($input['required']) ? 'required' : '' ?> <?= isset($input['readonly']) ? 'readonly' : '' ?> <?= isset($input['required']) ? 'required' : '' ?> data-size="<?= $input['size'] ?? 4 ?>" <?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (isset($input['data'])) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    foreach ($input['data'] as $dataName => $dataValue) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo 'data-'.$dataName.'="'.$dataValue.'" ';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }
            ?>>

			<?php break;
    }

    // styling closing tag
    if ($styling == 'fancy') { ?>

			<p class="fancy-input-lbl"><?= $input['text'] ?></p>

		</div>

	<?php }
    }

/**
 * Render a image input
 *
 * @param  array  $config  [
 *                         'name' => the name of the input
 *                         'client-path' => string (null), the full client path of the image
 *                         ]
 * @return html
 */
function render_image_input(array $config)
{
    $name = $config['name'];
    $clientPath = $config['client-path'] ?? null;

    ob_start();

    if ($clientPath) {
        ?>

		<label class="replace-image form-image">
			<input type="file" name="<?= $name ?>">
			<img src="<?= $clientPath ?>" alt="">
		</label>

	<?php
    } else { ?>

		<input type="file" name="<?= $name ?>">

	<?php }

    return ob_get_clean();
}

/**
 * Render a search input
 */
function render_search_input(array $config): string
{
    return SearchInput::render($config);
}

/**
 * OLD
 * Render a search input
 *
 * @param  array  $config  [
 *                         'search' => string, the name of the table to be searched
 *                         'search-fields' => array|string, the fields to search
 *                         'index-field' => string, the name of column associanted to the hidden input
 *                         'index-name' => string (index-field), the name of the hidden input
 *                         'index-value' => mixed (null), the value of the hidden input
 *                         'visible-field' => string, the name of column associanted to the visible input
 *                         'visible-name' => string (index-field), the name of the visible input
 *                         'visible-value' => mixed (null), the value of the visible input
 *                         'filter' => string = null, the WHERE sql filter
 *                         'placeholder' => string (null), the placeholder text
 *                         'custom-classes' => array ([]), custom classes that the whole block will have
 *                         'required' => bool (false), set if the input is required
 *                         'has-title' => bool (false), if set, it will show the visible input content on hover
 *                         ]
 * @return html
 */
function render_old_search_input(array $config)
{
    $search = $config['search'];
    $searchFields = is_array($config['search-fields']) ? implode(',', $config['search-fields']) : $config['search-fields'];
    $indexField = $config['index-field'];
    $indexName = $config['index-name'] ?? $indexField;
    $indexValue = $config['index-value'] ?? null;
    $visibleField = $config['visible-field'];
    $visibleName = $config['visible-name'] ?? $visibleField;
    $visibleValue = str_replace('"', '&quot;', $config['visible-value'] ?? null);
    $placeholder = $config['placeholder'] ?? null;
    $customClasses = implode(' ', $config['custom-classes'] ?? []);
    $required = $config['required'] ?? false;
    $hasTitle = $config['has-title'] ?? false;
    $filter = $config['filter'] ?? false;

    ob_start();
    ?>

	<div class="ajax-search <?= $customClasses ?> js-ajax-search" data-search="<?= $search ?>" data-search-fields="<?= $searchFields ?>" data-index-field="<?= $indexField ?>" data-visible-field="<?= $visibleField ?>" data-filter="<?= $filter ?>">
		<input type="hidden" class="ajax-search__index-input js-ajax-search__index-input" name="<?= $indexName ?>" value="<?= $indexValue ?>" <?= $required ? 'required' : '' ?>>

		<input type="text" class="ajax-search__visible-inpu js-ajax-search__visible-input" name="<?= $visibleName ?>" value="<?= $visibleValue ?>" <?= $hasTitle ? 'title="'.$visibleValue.'"' : '' ?> placeholder="<?= $placeholder ?>" <?= $required ? 'required' : '' ?> autocomplete="off">

		<div class="ajax-search__dropdown hidden js-ajax-search__dropdown">
			<div class="ajax-search__items js-ajax-search__items"></div>
		</div>
	</div>

<?php

        return ob_get_clean();
}
