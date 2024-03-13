<?php
get_header();

/*
Template Name: Cart Template
*/
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<div class="product-cart">
	<div class="container mx-auto p-4">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div class="col-span-1 bg-white rounded shadow-md p-4">
				<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

					<?php do_action( 'woocommerce_before_cart_table' ); ?>

					<table class="w-full border-collapse border border-gray-200">
						<thead>
							<tr class="bg-gray-100 border-b border-gray-200">
								<th class="p-2 text-left bg-blue-700 text-white"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
								<th class="p-2 text-left"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
								<th class="p-2 text-left"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
								<th class="p-2 text-left"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
								<th class="p-2 text-left"><?php esc_html_e( 'Addons Total', 'woocommerce' ); ?></th>
								<th class="p-2 text-left"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php do_action( 'woocommerce_before_cart_contents' ); ?>

							<?php
							foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
								$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
								$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

								if ( isset( $cart_item['addon_total'] ) ) {
									// If addon_total exists, you can access its value
									$addon_total = $cart_item['addon_total'];
								}

								if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
									?>
									<tr class="border-b border-gray-200">
										<td class="p-2">
											<div class="flex items-center">
												<div class="w-16 h-16 mr-2">
													<?php
													$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

													if ( ! $_product->is_visible() ) {
														echo wp_kses_post( $thumbnail );
													} else {
														printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), wp_kses_post( $thumbnail ) );
													}
													?>
												</div>
												<div>
													<?php
													if ( ! $_product->is_visible() ) {
														echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
													} else {
														echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_name() ), $cart_item, $cart_item_key );
													}

													// Meta data.
													echo wc_get_formatted_cart_item_data( $cart_item );

													// Backorder notification.
													if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
														echo '<p class="text-xs text-gray-600">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
													}
													?>
												</div>
											</div>
										</td>
										<td class="p-2"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></td>
										<td class="p-2"><?php echo isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : ''; ?></td>
										<td class="p-2"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></td>
										<td class="p-2">
											<?php
											echo wc_price( $addon_total * $cart_item['quantity'] );
											?>
										</td>
										<td class="p-2">
											<?php
											echo apply_filters(
												'woocommerce_cart_item_remove_link',
												sprintf(
													'<a href="%s" class="remove-button" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
													esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
													esc_html__( 'Remove this item', 'woocommerce' ),
													esc_attr( $product_id ),
													esc_attr( $_product->get_sku() )
												),
												$cart_item_key
											);
											?>
										</td>
									</tr>
									<?php
								}
							}
							?>

							<?php do_action( 'woocommerce_cart_contents' ); ?>

							<?php do_action( 'woocommerce_cart_actions' ); ?>

							<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
						</tbody>
					</table>

					<?php do_action( 'woocommerce_after_cart_table' ); ?>
				</form>
			</div>
			<div class="col-span-1">
				<?php do_action( 'woocommerce_cart_collaterals' ); ?>
			</div>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>

<?php get_footer(); ?>
