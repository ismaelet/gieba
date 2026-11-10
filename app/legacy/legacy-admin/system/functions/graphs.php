<?php

/*
* Requires:
* 	- columns (key => value)
*/
function graph_columns($graphData)
{

    $renderData = [];
    $max = $graphData['max'];

    foreach ($graphData['columns'] as $columnName => $columnData) {

        if (isset($columnData['subcolumns'])) {
            // multiple columns
            $renderColumn = [];
            $intColumnData = [];
            $renderColumn['subcolumns'] = [];
            $renderColumn['name'] = $columnName;

            // store sub-columns data
            foreach ($columnData['subcolumns'] as $subName => $subValue) {
                $value = strip_letters($subValue);
                $subData = [];
                $subData['name'] = $subName;
                $subData['valueStr'] = $subValue;
                $subData['value'] = round($value / $max * 100);
                $renderColumn['subcolumns'][] = $subData;
            }

            foreach ($columnData['subcolumns'] as $subValue) {
                $intColumnData[] = strip_letters($subValue);
            }

            $renderColumn['average'] = round(array_sum($intColumnData) / (count($intColumnData) * $max) * 100);
        } else {
            // single column
            $value = strip_letters($columnData);
            $renderColumn = [];
            $renderColumn['name'] = $columnName;
            $renderColumn['valueStr'] = $columnData;
            $renderColumn['value'] = round($value / $max * 100);
        }

        // store column data
        $renderData[] = $renderColumn;
    }
    ?>

	<section class="column-graph load" style="--_total-columns: <?= count($graphData['columns']) ?>;">

		<?php if (isset($graphData['legend'])) { ?>

			<!-- Legend -->
			<div class="flex ha-rig">
				<div class="graph-legend <?= 'graph-legend--'.($graphData['legend-style'] ?? 'line') ?>">

					<?php foreach ($graphData['legend'] as $legendEntry) { ?>

						<div class="graph-legend-row">
							<div class="graph-legend-icon"></div>
							<p class="graph-legend-name"><?= $legendEntry ?></p>
						</div>

					<?php } ?>
				</div>
			</div>

		<?php } ?>

		<!-- Graph render -->
		<div class="graph-box">

			<!-- Columns -->
			<?php foreach ($renderData as $columnKey => $columnData) {

			    if (isset($columnData['subcolumns'])) { ?>

					<!-- Multiple columns -->
					<div class="graph-column graph-column_multiple" style="--_count: <?= $columnKey + 1 ?>; --_average: <?= $columnData['average'] ?>">

						<!-- Sub-columns -->
						<?php foreach ($columnData['subcolumns'] as $subKey => $subdata) { ?>
							<div class=" graph-subcolumn" style="--_subcount: <?= $subKey + 1 ?>; --_value: <?= $subdata['value'] ?>"></div>
						<?php } ?>

						<!-- Average -->
						<div class="graph-column-average"></div>

						<!-- Info box -->
						<div class="graph-info <?= $columnKey + 1 > count($renderData) / 2 ? 'graph-info_left' : '' ?>">
							<h3 class="graph-info-title"><?= $columnData['name'] ?><span class="graph-info_average"> - <strong><?= $columnData['average'] ?> %</strong></span></h3>
							<div class="graph-info-body">

								<?php foreach ($columnData['subcolumns'] as $subKey => $subdata) { ?>

									<div class="graph-info-row" style="--_subcount: <?= $subKey + 1 ?>;">
										<div class=" graph-info-icon"></div>
										<p><?= $subdata['name'] ?></p>
										<p class="txt-bold"><?= $subdata['valueStr'] ?></p>
									</div>

								<?php } ?>

							</div>
						</div>
					</div>

				<?php } else { ?>

					<!-- Single column -->
					<div class="graph-column graph-column_single" style="--_count: <?= $columnKey + 1 ?>; --_value: <?= $columnData['value'] ?>">

						<!-- Info box -->
						<div class="graph-info <?= $columnKey + 1 > count($renderData) / 2 ? 'graph-info_left' : '' ?>">
							<h3 class="graph-info-title"><?= $columnData['name'] ?></h3>
							<div class="graph-info-body">

								<div class="graph-info-row">
									<div class="graph-info-icon"></div>
									<p>Risultato:</p>
									<p class="txt-bold">
										<?= $columnData['valueStr'] ?>
									</p>
								</div>

							</div>
						</div>
					</div>
			<?php }
				} ?>

			<!-- Average lines -->
			<?php if (isset($graphData['averages'])) {
			    foreach ($graphData['averages'] as $average) { ?>
					<div class="graph-box-average" style="--_value: <?= strip_letters($average) ?>;"></div>
			<?php }
			    } ?>

		</div>

		<!-- Bottom labels -->
		<div class="graph-labels">
			<?php foreach ($renderData as $columnKey => $columnData) { ?>
				<p class="graph-label txt-vertical" style="--_count: <?= $columnKey + 1 ?>;"><?= $columnData['name'] ?></p>
			<?php } ?>
		</div>
	</section>

<?php }
