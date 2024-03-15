<?php
/**
 * Template Name: Single Product Tailwind
 * Template Post Type: product
 */

get_header(); ?>
<div class="product-card">
	<div class="container mx-auto py-8">
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
			<div class="lg:col-span-2">
				<?php woocommerce_content(); ?>
			</div>
			<div class="lg:col-span-1">
				<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
					<h2 class="text-gray-800 text-2xl font-bold mb-4"><?php esc_html_e( 'Product Details', 'your-text-domain' ); ?></h2>
					<?php
					// Display custom product details
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							the_content();
						endwhile;
					endif;
					?>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const checkboxes = document.querySelectorAll('.addon-checkbox');
		const priceElement = document.querySelector('.woocommerce-Price-amount.amount');
		const quantityInput = document.querySelector('.quantity input');

		let basePrice = parseFloat(priceElement.innerText.replace(/[^0-9.-]+/g, "")); // Store the initial price

		checkboxes.forEach(function(checkbox) {
			checkbox.addEventListener('change', function() {
				updateCart(); // Update cart when checkbox status changes
			});
		});

		quantityInput.addEventListener('change', updateCart);

		function updateCart() {
			const selectedAddons = [];
			checkboxes.forEach(function(checkbox) {
				if (checkbox.checked) {
					const addonName = checkbox.dataset.addon;
					const addonPrice = parseFloat(checkbox.dataset.addonPrice);
					selectedAddons.push({
						name: addonName,
						price: addonPrice
					});
				}
			});

			const quantity = quantityInput.value;
			const total = calculateTotal(basePrice, selectedAddons, quantity);

			// Update total price on the page
			priceElement.innerText = 'Total: ' + total;
		}

		function calculateTotal(basePrice, selectedAddons, quantity) {
			let total = basePrice * quantity; // Calculate base price times quantity
			selectedAddons.forEach(function(addon) {
				total += addon.price * quantity; // Add addon price times quantity
			});
			return total.toFixed(2);
		}
	});
</script>

<?php get_footer(); ?>
