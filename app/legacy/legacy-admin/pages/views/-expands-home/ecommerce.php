{% block content %}

<div id="grid" class="gap2">

	<!-- Panel -->
	<section class="panel">

		<div class="body">
			<div class="panel-header">
				<div class="panel-icon icon-box">
					<?php

use Ecommerce\Cart;

?><?= icon_embed(APP_ICONS_DIR.'navigation/items') ?>
				</div>
				<h1 class="title">Prodotti</h1>
			</div>
			<p class=""><strong><?= $itemCount ?></strong> prodotti aggiunti</p>
		</div>

		<div class="footer">
			<a href="items/list" class="sbtn">Lista dei prodotti</a>
			<a href="items/add" class="pbtn">Aggiungi un prodotto</a>
		</div>

	</section>

	<!-- Order list -->
	<section class="panel orders">

		<div class="body">
			<div class="panel-header">
				<div class="panel-icon icon-box">
					<?= icon_embed(APP_ICONS_DIR.'navigation/orders') ?>
				</div>
				<h1 class="title">Ordini recenti</h1>
			</div>

			<div class="orders-wrap">
				<div class="order-list">
					<div class="order order_header">
						<p class="order-field">Ordine</p>
						<p class="order-field">Prodotti</p>
						<p class="order-field">Cliente</p>
						<p class="order-field">Totale</p>
						<p class="order-field">Regalo</p>
						<p class="order-field">Stato</p>
						<p class="order-field">Data</p>
					</div>

					<?php while ($cart = $carts->fetch_assoc()) {
					    $cart = new Cart($cart['id']);
					    ?>
						<a class="order" href="<?= client_url('/orders/edit?id='.$cart->id) ?>">
							<p class="order-field"><?= $cart->idCode ?></p>
							<p class="order-field"><?= $cart->address->fullname ?></p>
							<p class="order-field"><?= $cart->quantity ?> <?= $cart->quantity == 1 ? 'prodotto' : 'prodotti' ?></p>
							<p class="order-field"><?= $cart->priceStr ?></p>
							<p class="order-field"><?= $cart->gift ? 'SI' : 'NO' ?></p>
							<p class="order-field">
								<span class="order-status order-status_<?= $cart->statusCode ?>">
									<?= $cart->statusStr ?>
								</span>
							</p>
							<p class="order-field"><?= $cart->date ?></p>
						</a>
					<?php } ?>

				</div>
			</div>
		</div>

		<div class="footer">
			<a href="orders/list" class="sbtn">Lista degli ordini</a>
		</div>

	</section>

</div>

{% end content %}