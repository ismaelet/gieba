<!-- 
	Version 1.2.0

	search: string, the search class endpoint
	storedName: string, the name of the input to store the value
	shownName: ?string, the name of the input to show the value
	storedValue: ?mixed, the value of the input to store the value
	shownValue: ?mixed, the value of the input to show the value
	where: ?array, the where clause for the search
	exclude: ?array, the ids to exclude from the search
	placeholder: ?string, the input placeholder
	required: ?bool, if the input is required
-->

<template>
	<div class="search-input js-search-input" data-endpoint="<?= $search ?>" <?= isset($where) ? 'data-where="'.implode(',', $where).'"' : '' ?> <?= isset($exclude) ? 'data-exclude="'.implode(',', $exclude).'"' : '' ?>>

		<input type="hidden" class="js-search-store" name="<?= $storedName ?>" value="<?= $storedValue ?? '' ?>" <?= isset($required) ? 'required' : '' ?>>

		<input type="text" class="js-search-show" <?= isset($shownName) ? 'name="'.$shownName.'"' : '' ?> value="<?= $shownValue ?? '' ?>" placeholder="<?= $placeholder ?? '' ?>" <?= isset($required) ? 'required' : '' ?> autocomplete="off">

		<div class="search-input__dropdown js-search-dropdown">
			<!-- Populated after fetch -->
		</div>
	</div>
</template>

<style>
	.search-input {
		position: relative;
	}

	.search-input__dropdown {
		display: none;
		position: absolute;
		top: calc(100% + 1rem);
		width: 100%;
		z-index: 10;
		overflow: hidden;
		background-color: var(--clr-surface-400);
		border: 1px solid var(--border-clr-400);
		border-radius: var(--border-radius-400);
		box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
		text-align: left;

		&.active {
			display: block;
		}
	}

	.search-input__item {
		padding: 0.6rem 0.6rem;
		cursor: pointer;
		transition: background-color 0.1s;
	}

	.search-input__item.focused,
	.search-input__item:hover {
		background-color: var(--clr-surface-700);
	}
</style>

<script>
	function highlight_search_result(text, search) {
		let pattern = new RegExp('(' + search + ')', 'ig')

		return text.replace(pattern, '<strong>$1</strong>')
	}

	document.addEventListener('DOMContentLoaded', function() {
		/**
		 * Fetch the search results
		 */
		document.querySelectorAll('.js-search-show').forEach((element) => {
			element.addEventListener('keyup', async (event) => {
				// Prevent arrow keys from triggering the search
				if (['ArrowUp', 'ArrowDown'].includes(event.key)) {
					return false;
				}

				// Set the search parameters
				const searchBlock = element.closest('.js-search-input')
				const storedInput = searchBlock.querySelector('.js-search-store')
				const shownInput = element
				const dropDown = searchBlock.querySelector('.js-search-dropdown')
				const parcialEndpoint = searchBlock.dataset.endpoint
				const filter = shownInput.value
				const exclude = searchBlock.dataset.exclude

				// Generate the endpoint
				let fullEndpoint = `/api/${parcialEndpoint}?filter=${encodeURIComponent(filter)}`

				if (exclude) {
					fullEndpoint += `&exclude=${exclude}`
				}

				// Empty the previous results
				dropDown.innerHTML = ''

				// Fetch the results
				axios.get(fullEndpoint)
					.then(response => {
						// Get the results
						const results = response.data
						const count = results.length

						if (count) {
							dropDown.innerHTML = ''

							// Populate the dropdown
							for (const result of results) {
								const itemText = highlight_search_result(result.name, filter)

								dropDown.insertAdjacentHTML('beforeend', `<p class="search-input__item js-search-item" data-id="${result.id}" data-text="${result.name}" tabindex="-1">${itemText}</p>`)

								dropDown.classList.add('active')
							}
						} else {
							// Hide the dropdown
							dropDown.classList.remove('active')
						}
					})
					.catch(error => {
						// Log the error
						console.error(error);
					});
			})
		})

		/**
		 * Select the search result
		 */
		document.querySelectorAll('.js-search-input').forEach((element) => {
			element.addEventListener('click', (event) => {
				if (event.target.classList.contains('js-search-item')) {
					const searchBlock = event.target.closest('.js-search-input')
					const storedInput = searchBlock.querySelector('.js-search-store')
					const shownInput = searchBlock.querySelector('.js-search-show')
					const dropDown = searchBlock.querySelector('.js-search-dropdown')

					storedInput.value = event.target.dataset.id
					shownInput.value = event.target.dataset.text

					shownInput.focus()

					dropDown.classList.remove('active')
				}
			})
		})

		/**
		 * Keyboard navigation
		 */
		document.querySelectorAll('.js-search-input').forEach((element) => {
			element.addEventListener('keydown', (event) => {
				const searchBlock = event.target.closest('.js-search-input')
				const dropDown = searchBlock.querySelector('.js-search-dropdown')
				const items = dropDown.querySelectorAll('.js-search-item')
				const focused = dropDown.querySelector('.focused')

				if (event.key == 'ArrowDown') {
					if (focused) {
						focused.classList.remove('focused')

						if (focused.nextElementSibling) {
							focused.nextElementSibling.classList.add('focused')
						} else {
							items[0].classList.add('focused')
						}
					} else {
						items[0].classList.add('focused')
					}
				} else if (event.key == 'ArrowUp') {
					if (focused) {
						focused.classList.remove('focused')

						if (focused.previousElementSibling) {
							focused.previousElementSibling.classList.add('focused')
						} else {
							items[items.length - 1].classList.add('focused')
						}
					} else {
						items[items.length - 1].classList.add('focused')
					}
				} else if (event.key == 'Enter') {
					if (focused) {
						event.preventDefault()
					}

					if (focused) {
						focused.click()
					}
				}
			})
		})
	})
</script>