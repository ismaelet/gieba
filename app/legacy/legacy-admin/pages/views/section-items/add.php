{% block page-body %}

<!-- Main content -->
<div class="main-content grid gap2">

	<!-- Item data -->
	<section class="form">
		<h3 class="form-title">Dati del prodotto</h3>
		<div class="form-content">
			<div class="form-inputs">

				<?php generate_form($itemInputs) ?>

				<?php if ($item->id) { ?>
					<!-- Slug -->
					<div class="form-field mar-t-05">
						<p class="form-lbl">URL del prodotto</p>
						<div class="slug">
							<p class="slug-url"><?= str_replace('/', '<wbr>/', __PUBLIC_CLIENT_URL__.'/'.legacy__('item').'/'.$item->id.'-') ?></p>
							<input type="text" class="slug-inp inp" name="slug" value="<?= $item->slug ?>" placeholder="slug" required>
						</div>
					</div>

				<?php } ?>

			</div>
		</div>
	</section>

	<!-- Item avanced data -->
	<section class="item-texts form">
		<h3 class="form-title">Dati del prodotto</h3>

		<div class="form-content">
			<div class="form-inputs">
				<?php generate_form($itemData) ?>
			</div>
		</div>
	</section>

	<!-- Images -->
	<section class="form">
		<h3 class="form-title">Galleria</h3>
		<div class="form-content">

			<?php if ($item->id && $item->images && ! empty($item->images)) { ?>

				<p class="mar-b-2 flex flex-wrap gap05 ha-cen va-cen">
					<span class="icon text-icon" <?= icon_svg('move') ?>></span>
					Puoi trascinare le immagini per riordinarle
				</p>

				<div class="item-gallery" id="gallery">
					<div class="item-imgs" id="item-imgs">

						<?php foreach ($images->res as $key => $image) { ?>

							<div class="item-img stack-content center-items">
								<input type="hidden" name="images[]" value="<?= $image['id'] ?>">

								<img src="<?= file_public_url($item->image(400, $key + 1)) ?>" alt="" loading="lazy" class="img-fill">

								<div class="item-img-overlay fill-all center-items pos-rel">
									<div class="flex gap2">

										<!-- Replace the image -->
										<input type="file" class="img-file hidden" name="img-file-<?= $image['id'] ?>" accept=".jpg, .png, .jpeg">
										<button type="submit" class="upload-img hidden" name="upload-img[]" value="<?= $image['id'] ?>"></button>
										<div class="item-img-upload item-img-icon icon-box fill-f js-img-trigger">
											<?= icon_embed('upload') ?>
										</div>

										<!-- Delete the image -->
										<button type="submit" class="delete-img hidden" name="delete-img[]" value="<?= $image['id'] ?>" onclick="return confirm('Confermi di voler eliminare questa immagine?')"></button>
										<div class="item-img-delete item-img-icon icon-box fill-f">
											<?= icon_embed('delete') ?>
										</div>

									</div>
								</div>
							</div>

						<?php } ?>

					</div>

					<!-- Upload a new image -->
					<div class="item-img item-img_empty stack-content center-items">
						<div class="fill-all js-img-trigger"></div>
						<input type="file" class="img-file hidden" name="new-img-files[]" accept=".jpg, .png, .jpeg">
						<button type="submit" class="upload-img hidden" name="upload-img"></button>
						<div class="item-img-add item-img-icon icon-box fill-text-color">
							<?= icon_embed('add') ?>
						</div>
					</div>

				</div>

			<?php } else { ?>

				<div class="form-field">
					<p class="">Seleziona le immagini del prodotto</p>

					<?php if ($item->id) { ?>
						<button type="submit" class="upload-img hidden" name="upload-img"></button>
						<input type="file" class="inp" name="new-img-files[]" multiple accept=".jpg, .png, .jpeg" onchange="$(this).prev().click()">
					<?php } else { ?>
						<input type="file" class="inp" name="new-img-files[]" multiple accept=".jpg, .png, .jpeg">
					<?php } ?>

				</div>

			<?php } ?>

		</div>
	</section>

	<!-- FAQs -->
	<!-- <section class="faqs form" data-count="<?= empty($faqs) ? 1 : count($faqs) ?>">
		<h3 class="form-title">Dúvidas sobre o produto</h3>

		<div class="form-content">
			<div class="form-inputs">

				<?php for ($i = 1; $i <= 10; $i++) { ?>

					<div class="form-field <?= $i == 1 || isset($faqs[$i - 1]) ? '' : 'hidden' ?>">
						<p class="lbl">Dúvida</p>
						<input type="text" class="inp" name="faq[]" value="<?= $faqs[$i - 1]['question'] ?? '' ?>" placeholder="Dúvida (opcional)">
						<textarea class="textarea" name="faq-answer[]" placeholder="Resposta (opcional)" rows="5"><?= $faqs[$i - 1]['answer'] ?? '' ?></textarea>
					</div>

				<?php } ?>

			</div>

			<div class="faq-add icon-box box-15rem mar-h-auto mar-t-1 clickable icon-box">
				<?= icon_embed('add') ?>
			</div>
		</div>
	</section> -->
</div>

<!-- Side bar -->
<aside class="side-bar">

	<!-- Status, inventory, price -->
	<section class="form">
		<h3 class="form-title">Impostazioni del prodotto</h3>

		<div class="form-content">
			<div class="form-inputs">

				<div class="form-field">
					<p class="form-lbl">Stato del prodotto</p>
					<select class="sel" name="status">
						<option value="0" <?= $item->status ? '' : 'selected' ?>>Bozza</option>
						<option value="1" <?= ! $item->id || $item->status ? 'selected' : '' ?>>Attivo</option>
					</select>
				</div>

				<div class="inventory-field <?= ! $item->id || $item->inStock ? 'inventory-field_present' : 'inventory-field_absent' ?> form-field">
					<p class="form-lbl">Inventario</p>
					<select class="sel" name="inStock">
						<option value="0" <?= $item->inStock ? '' : 'selected' ?>>Esaurito</option>
						<option value="1" <?= ! $item->id || $item->inStock ? 'selected' : '' ?>>Disponibile</option>
					</select>
				</div>

				<div class="price-field form-field">
					<p class="form-lbl">Prezzo</p>
					<div class="price-input stack-content center-items">
						<input type="text" class="inp" name="price" value="<?= formatc($item->basePrice) ?>" placeholder="Prezzo">
					</div>
				</div>

				<?php if ($item->id) { ?>
					<div class="sku-field form-field">
						<p class="form-lbl">SKU</p>
						<input type="text" class="inp" name="sku" value="<?= $item->sku ?>" placeholder="SKU" maxlength="15">
					</div>
				<?php } ?>

			</div>

			<label class="toggle-switch toggle-switch_rg mar-t-1">
				<input type="checkbox" name="highlighted" value="1" <?= $item->highlighted ? 'checked' : '' ?>>
				<div class="toggle-switch_bgr">
					<div class="icon"></div>
				</div>
				<p>Prodotto in evidenza</p>
			</label>
		</div>
	</section>

	<!-- Item tech -->
	<!-- <section class="tech-data form">
		<h3 class="form-title">Especificações do produto</h3>

		<div class="form-content">
			<h3>Dados do produto</h3>
			<div class="form-inputs">
				<?php generate_form($itemTech) ?>
			</div>
			<h3 class="mar-t-2">Dados para frete</h3>
			<div class="form-inputs">
				<?php generate_form($itemShippingTech) ?>
			</div>
		</div>
	</section> -->

	<?php if ($item->id) { ?>

		<!-- Promotions -->
		<section class="tech-data form" id="promotions">
			<h3 class="form-title">Promozione</h3>

			<div class="form-content">

				<?php if ($item->promotion->id) { ?>

					<div class="form-inputs">
						<p class="">Nuovo prezzo:</p>
						<input type="text" class="inp read-only-inp" value="<?= $item->promotion->priceStr ?>" disabled>
						<p class="">Dal:</p>
						<input type="date" class="inp read-only-inp" value="<?= $item->promotion->start ?>" disabled>
						<p class="">Fino al:</p>
						<input type="date" class="inp read-only-inp" value="<?= $item->promotion->end ?>" disabled>
					</div>
					<button type="submit" class="pbtn wid-100 mar-t-1" name="delete-promotion" data-style='warning'>Elimina</button>

				<?php } else { ?>

					<h3 class="mar-b-1">Aggiungi una promozione:</h3>
					<div class="form-inputs">
						<p class="">Nuovo prezzo:</p>
						<input type="text" class="inp" name="promotion-price" placeholder="Prezzo in promozione">
						<p class="">Dal:</p>
						<input type="date" class="inp" name="promotion-start" value="<?= (new DateTime)->format('Y-m-d') ?>">
						<p class="">Fino al:</p>
						<input type="date" class="inp" name="promotion-end" min="<?= (new DateTime('tomorrow'))->format('Y-m-d') ?>" value="<?= (new DateTime('tomorrow'))->format('Y-m-d') ?>">
					</div>
					<button type="submit" class="pbtn wid-100 mar-t-1" name="add-promotion">Aggiungi</button>

				<?php } ?>

			</div>
		</section>

	<?php } ?>

	<!-- Variations -->
	<?php /*
    <section class="variations form" data-count="<?= $item->variations ? 1 : 0 ?>">
        <h3 class="form-title">Variações</h3>

        <div class="form-content">

            <p>Para cada variação é possível especificar um preço. Se deixar o campo do preço vazio, será utilizado o preço geral do produto para a variação.</p>

            <?php if (!$item->variations) { ?>
                <!-- Add a variation type -->
                <div class="add-variation mar-t-1">
                    <input type="text" class="add-variation-inp inp" placeholder="Nome do tipo de variação" data-function="add-variation">
                    <button type="button" class="add-variation-btn icon-box">
                        <?= icon_embed('add') ?>
                    </button>
                </div>
            <?php } ?>

            <!-- Variation list -->
            <div class="variation-list mar-t-1">

                <?php if ($item->variations) foreach ($item->variations as $variation) { ?>

                    <div class='variation'>
                        <input type='hidden' name='variation-1' value='<?= $variation['title'] ?>'>
                        <h3 class='variation-title'><?= $variation['title'] ?>
                            <button type="button" class="delete-variation icon-box">
                                <?= icon_embed('delete') ?>
                            </button>
                        </h3>

                        <!-- Variant list -->
                        <div class='variants mar-t-05'>

                            <?php foreach ($variation['variants'] as $variant) { ?>

                                <div class='variant'>
                                    <input type='text' class='inp' name='variation-1-variants[]' value="<?= $variant[0] ?>" placeholder='Nome da variação'>
                                    <input type='text' class='inp' name='variation-1-prices[]' value="<?= $variant[1] ? format_price($variant[1]) : '' ?>" placeholder='Preço (opcional)'>
                                    <button type="button" class="delete-variant icon-box">
                                        <?= icon_embed('close') ?>
                                    </button>
                                </div>

                            <?php } ?>

                        </div>

                        <!-- Add a variant -->
                        <button type='button' class='add-variant icon-box mar-t-05'>
                            <?= icon_embed('add') ?>
                        </button>
                    </div>

                <?php } ?>

            </div>
        </div>
    </section>
    */ ?>

	<?php if ($item->id) { ?>
		<!-- SEO preview -->
		<section class="form">
			<h3 class="form-title">Preview SEO</h3>

			<div class="form-content">
				<p class="seo-url"><?= $item->staticUrl ?></p>
				<h3 class="seo-title"><?= $item->name ?></h3>
				<p class="seo-text txt-line3"><?= $item->info ?></p>
			</div>
		</section>
	<?php } ?>

</aside>

{% end page-body %}