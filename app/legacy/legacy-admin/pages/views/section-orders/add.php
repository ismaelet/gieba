<?php

use function Ecommerce\ecommerce_config;

?>

{% block content %}

<form method="POST">

	<!-- Page body -->
	<div class="sections">

		<!-- Customer data -->
		<section class="section section--customer-data">
			<h2 class="panel__title panel__title--outside">Dati del cliente</h2>
			<div class="panel">
				<div class="form_body">
					<div class="form-field">
						<p class="form-lbl">Nome completo</p>
						<?= render_old_search_input($searchCustomer) ?>
					</div>
					<div class="form-field">
						<p class="form-lbl">Numero di telefono</p>
						<input type="text" class="inp" name="customerPhone" value="<?= $customer->phone ?? '' ?>" placeholder="Numero di telefono">
					</div>
					<div class="form-field">
						<p class="form-lbl">Info del cliente</p>
						<textarea class="textarea" name="customerInfo" placeholder="Info del cliente"><?= $customer->info ?? '' ?></textarea>
					</div>
				</div>
			</div>
		</section>

		<!-- Cart data -->
		<section class="section section--order-data">
			<h2 class="panel__title panel__title--outside">Dati dell'ordine</h2>
			<div class="panel">
				<div class="form_body">

					<?php if (! $newCart) { ?>
						<div class="form-field">
							<p class="form-lbl">ID</p>
							<input type="number" class="inp" readonly value="<?= $cart->id ?>">
						</div>
					<?php } ?>

					<!-- Status -->
					<div class="form-field">
						<p class="form-lbl">Stato</p>
						<select class="sel js-cart-status" name="cartStatus" data-status-id="<?= $oldCart->status ?? 1 ?>">

							<?php foreach (ecommerce_config('cart-statuses') as $oldCartStatusId => $oldCartStatus) { ?>

								<option value="<?= $oldCartStatusId ?>" <?= ($newCart && $oldCartStatusId == 1) || (! $newCart && $oldCart->status == $oldCartStatusId) ? 'selected' : '' ?>><?= $oldCartStatus[1] ?></option>

							<?php } ?>

						</select>
					</div>

					<!-- Data -->
					<div class="form-field">
						<p class="form-lbl">Data</p>
						<input type="date" class="inp" name="cartDate" value="<?= $newCart ? date('Y-m-d') : $oldCart->date ?>">
					</div>

					<div class="grid lg:grid-cols-2 gap-2 grid-cols-1">
						<div class="form-field">
							<label class="checkbox">
								<input type="checkbox" name="billed" <?= ! $newCart && $oldCart->billed ? 'checked' : '' ?>>
								<span></span>
								<p>Fatturato</p>
							</label>
						</div>

						<div class="form-field">
							<label class="checkbox">
								<input type="checkbox" name="printDescriptions" <?= $newCart || $oldCart->printDescriptions ? 'checked' : '' ?>>
								<span></span>
								<p>Stampa dettagli articolo</p>
							</label>
						</div>

						<div class="form-field">
							<label class="checkbox">
								<input type="checkbox" name="print_notes" <?= ! $newCart && $oldCart->print_notes ? 'checked' : '' ?>>
								<span></span>
								<p>Stampa le note</p>
							</label>
						</div>

						<div class="form-field">
							<label class="checkbox">
								<input type="checkbox" name="printStatus" <?= ! $newCart && $oldCart->printStatus ? 'checked' : '' ?>>
								<span></span>
								<p>Stampa lo stato</p>
							</label>
						</div>

						<div class="form-field">
							<label class="checkbox">
								<input type="checkbox" name="always_print_prices" <?= ! $newCart && $cart->always_print_prices ? 'checked' : '' ?>>
								<span></span>
								<p>Stampa i prezzi nella bolla</p>
							</label>
						</div>

						<div class="form-field">
							<label class="checkbox">
								<input type="checkbox" name="print_total" <?= $newCart || $cart->print_total ? 'checked' : '' ?>>
								<span></span>
								<p>Stampa il totale</p>
							</label>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Discount data -->
		<section class="section">
			<h2 class="panel__title panel__title--outside">Acconti e sconti</h2>
			<div class="panel">
				<div class="form_body">

					<!-- Down payment -->
					<div class="form-field">
						<p class="form-lbl">Acconto</p>
						<div class="discount-field">
							<div class="price-input">
								<input type="number" class="js-down-payment" name="downPayment" value="<?= ! $newCart ? $oldCart->formattedDownPayment : '0.00' ?>" step=".01">
							</div>

							<label class="checkbox">
								<input type="checkbox" class="js-down-payment-with-vat" name="downPaymentWithVat" <?= ! $newCart && $oldCart->downPaymentWithVat ? 'checked' : '' ?>>
								<span></span>
								<p>IVA</p>
							</label>
						</div>
					</div>

					<!-- Fixed discount -->
					<div class="form-field">
						<p class="form-lbl">Sconto fisso</p>
						<div class="discount-field">
							<div class="price-input">
								<input type="number" class="js-fixed-discount" name="fixedDiscount" value="<?= ! $newCart ? $oldCart->formattedFixedDiscount : '0.00' ?>" min="0" step=".01">
							</div>

							<label class="checkbox">
								<input type="checkbox" class="js-discount-with-vat" name="discountWithVat" <?= ! $newCart && $oldCart->discountWithVat ? 'checked' : '' ?>>
								<span></span>
								<p>IVA</p>
							</label>
						</div>
					</div>

					<!-- Percentage discount -->
					<div class="form-field">
						<p class="form-lbl">Sconto percentuale</p>
						<input type="number" class="js-percentage-discount" name="percentageDiscount" value="<?= ! $newCart ? $oldCart->percentageDiscount : 0 ?>" min="0" max="100">
					</div>
				</div>
			</div>
		</section>

		<!-- Orders panel -->
		<section class="section section--products">
			<h2 class="panel__title panel__title--outside">Lista dei prodotti</h2>
			<div class="panel">

				<!-- Orders -->
				<div class="orders">
					<div class="order-row order-row--head">
						<div class="_empty"></div>
						<p class="order-row_title">Nome</p>
						<p class="order-row_title">Dettagli</p>
						<p class="order-row_title">Stato</p>
						<p class="order-row_title">Quantità</p>
						<p class="order-row_title">Prezzo netto</p>
						<p class="order-row_title">Prezzo lordo</p>
						<p class="order-row_title order-row_item--center">IVA</p>
						<p class="order-row_title">Totale</p>
					</div>

					<?php

                    for ($i = 1; $i <= 50; $i++) {
                        $order = $oldCart->orders[$i - 1] ?? false;
                        $searchProduct['index-value'] = $order ? $order->product->id : false;
                        $searchProduct['visible-value'] = $order ? ($order->product->name ?: '[Articolo Rimosso]') : false;

                        $orderStatusStr = $order ? $order->statusString : '';
                        $orderQuantity = $order ? $order->quantity : 1;
                        $orderNetPrice = $order ? $order->formattedNetProductPrice : '';
                        $orderGrossPrice = $order ? $order->formattedProductPrice : '';
                        $orderTotalPrice = $order ? $order->formattedPrice : '';
                        ?>

						<div class="order-row <?= ($newCart && $i == 1) || $order ? 'active' : '' ?> js-order">
							<input type="hidden" class="inp" name="orderId[]" value="<?= $order ? $order->id : 0 ?>">
							<input type="hidden" class="inp js-order-real-price" name="orderNetPrice[]" value="<?= $order ? $order->netProductPrice : 0 ?>">

							<!-- Delete -->
							<div class="order-delete icon-box js-delete-order">
								<?= icon_embed('close') ?>
							</div>

							<!-- Product Name -->
							<?= render_old_search_input($searchProduct) ?>

							<!-- Infos -->
							<input type="text" class="js-order-info" name="orderInfo[]" value="<?= $order ? $order->info : '' ?>" title="<?= $order ? $order->info : '' ?>" placeholder="Dettagli del prodotto">

							<!-- Status -->
							<select class="js-order-status" name="orderStatus[]" title="<?= $orderStatusStr ?>" data-status-id="<?= $order->status ?? 1 ?>">

								<?php foreach (ecommerce_config('cart-statuses') as $statusId => $status) { ?>
									<option value="<?= $statusId ?>" <?= $order ? ($order->status == $statusId ? 'selected' : '') : ($statusId == 1 ? 'selected' : '') ?> title="<?= $status[1] ?>"><?= $status[1] ?></option>
								<?php } ?>

							</select>

							<!-- Quantity -->
							<input type="number" class="inp js-order-quantity" name="orderQuantity[]" value="<?= $orderQuantity ?>" title="<?= $orderQuantity ?>" placeholder="Quantità" min="0">

							<!-- Prices -->
							<div class="inp-price">
								<input type="number" class="inp js-order-net-price" name="orderPrice[]" value="<?= $orderNetPrice ?>" title="<?= $orderNetPrice ?>" placeholder="Prezzo unità" step="0.01">
							</div>
							<div class="inp-price">
								<input type="number" class="inp js-order-gross-price" value="<?= $orderGrossPrice ?>" title="<?= $orderGrossPrice ?>" placeholder="Prezzo unità" step="0.01">
							</div>
							<label class="checkbox order-row_item--center">
								<input type="checkbox" class="js-order-vat" name="orderVat<?= $i ?>" <?= $order && ! $order->vat ? '' : 'checked' ?>>
								<span></span>
							</label>
							<div class="inp-price">
								<input type="number" class="inp js-order-total-price" name="orderTotalPrice[]" value="<?= $orderTotalPrice ?>" title="<?= $orderTotalPrice ?>" placeholder="Prezzo totale" step="0.01">
							</div>
						</div>

					<?php } ?>

				</div>

				<!-- Add an order -->
				<div class="add-order <?= $order && count($oldCart->orders) >= 50 ? '' : 'active' ?> js-add-order-block">
					<div class="add-order_btn icon-box js-add-order">
						<?= icon_embed('add') ?>
					</div>
				</div>

				<!-- Recap -->
				<div class="orders-recap">
					<div class="orders-recap_row">
						<p class="orders-recap_text">Imponibile</p>
						<p class="orders-recap_text"><span class="js-recap-net"><?= $newCart ? '0.00' : $oldCart->formattedNetPrice ?></span> €</p>
					</div>
					<div class="orders-recap_row">
						<p class="orders-recap_text">IVA</p>
						<p class="orders-recap_text"><span class="js-recap-vat"><?= $newCart ? '0.00' : $oldCart->formattedVat ?></span> €</p>
					</div>
					<div class="orders-recap_row">
						<p class="orders-recap_text">Acconto</p>
						<p class="orders-recap_text"><span class="js-recap-down-payment"><?= $newCart ? '0.00' : $oldCart->formattedFullDownPayment ?></span> €</p>
					</div>
					<div class="orders-recap_row">
						<p class="orders-recap_text">Sconto</p>
						<p class="orders-recap_text"><span class="js-recap-discount"><?= $newCart ? '0.00' : $oldCart->formattedFullDiscount ?></span> €</p>
					</div>
					<div class="orders-recap_row orders-recap_row--total">
						<p class="orders-recap_text">Totale</p>
						<p class="orders-recap_text"><span class="js-recap-total"><?= $newCart ? '0.00' : $oldCart->formattedPrice ?></span> €</p>
					</div>
				</div>
			</div>
		</section>
	</div>

	<!-- Page footer -->
	<section class="page_footer">
		<button class="button button--auto-width" name="submit" autofocus><?= $newCart ? 'Aggiungi l\'ordine' : 'Modifica l\'ordine' ?></button>

		<?php if (! $newCart) { ?>
			<div class="flex-group" data-spacing="400">
				<button class="button button--secondary" name="duplicate">Duplica l'ordine</button>
				<button class="button button--secondary button--bad button--auto-width" name="delete" onclick="return confirm('Sei sicuro di voler rimuovere l\'ordine? Non sarà possibile ripristinarlo.')">Rimuovi l'ordine</button>
			</div>
		<?php } ?>
	</section>
</form>

{% end content %}