<?php
/**
 * Plugin Name: Custom Woo Test Plugin
 * Description: Includes all the functionalities related to the test
 * Version: 1.0
 * Author: Usman
 * Text Domain: my-plugin
 */

defined( 'ABSPATH' ) || exit;

class Custom_Product_Addons {

	public function __construct() {
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_custom_product_addons_fields' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_product_addons' ) );
	}

	// Add custom product addons fields
	public function add_custom_product_addons_fields() {
		global $post;

		echo '<div class="options_group">';

		$addons = array(
			'back_cover'       => __( 'Back Cover', 'woocommerce' ),
			'screen_protector' => __( 'Screen Protector', 'woocommerce' ),
			'phone_pouch'      => __( 'Phone Pouch', 'woocommerce' ),
		);

		foreach ( $addons as $addon_key => $addon_label ) {
			woocommerce_wp_checkbox(
				array(
					'id'          => '_addon_' . $addon_key,
					'label'       => $addon_label,
					'description' => sprintf( __( 'Add a %s', 'woocommerce' ), strtolower( $addon_label ) ),
				)
			);

			woocommerce_wp_text_input(
				array(
					'id'                => '_addon_' . $addon_key . '_price',
					'label'             => $addon_label . __( ' Price', 'woocommerce' ),
					'description'       => sprintf( __( 'Enter the price for the %s', 'woocommerce' ), strtolower( $addon_label ) ),
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => '0.01',
						'min'  => '0',
					),
				)
			);
		}

		echo '</div>';
	}

	// Save custom product addons
	public function save_custom_product_addons( $product_id ) {
		$addons = array(
			'back_cover'       => '_addon_back_cover_price',
			'screen_protector' => '_addon_screen_protector_price',
			'phone_pouch'      => '_addon_phone_pouch_price',
		);

		// Loop through addons and save data
		foreach ( $addons as $addon_key => $addon_price_key ) {
			$addon_meta_key       = '_addon_' . $addon_key;
			$addon_price_meta_key = $addon_price_key;
			$addon_value          = isset( $_POST[ $addon_meta_key ] ) ? 'yes' : 'no';
			update_post_meta( $product_id, $addon_meta_key, $addon_value );
			if ( $addon_value === 'yes' && isset( $_POST[ $addon_price_meta_key ] ) ) {
				update_post_meta( $product_id, $addon_price_meta_key, $_POST[ $addon_price_meta_key ] );
			}
		}
	}
}

new Custom_Product_Addons();


// Register admin menu page
add_action( 'admin_menu', 'custom_admin_menu_page' );

function custom_admin_menu_page() {
	add_menu_page(
		__( 'Customer Details', 'my-plugin' ),
		__( 'Customer Details', 'my-plugin' ),
		'manage_woocommerce',
		'customer-details',
		'display_customer_details_page'
	);
}

// Display customer details page content
function display_customer_details_page() {
	// Check user capabilities
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	// Retrieve customer details
	$customers = get_users( array( 'role' => 'customer' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Customer Details', 'my-plugin' ); ?></h1>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Customer ID', 'my-plugin' ); ?></th>
					<th><?php esc_html_e( 'Email', 'my-plugin' ); ?></th>
					<th><?php esc_html_e( 'Order ID', 'my-plugin' ); ?></th>
					<th><?php esc_html_e( 'Latitude', 'my-plugin' ); ?></th>
					<th><?php esc_html_e( 'Longitude', 'my-plugin' ); ?></th>
					<th><?php esc_html_e( 'Website', 'my-plugin' ); ?></th>
					<th><?php esc_html_e( 'Company Name', 'my-plugin' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $customers as $customer ) {
					$order_ids = get_user_orders( $customer->ID );
					?>
					<tr>
						<td><?php echo esc_html( $customer->ID ); ?></td>
						<td><?php echo esc_html( $customer->user_email ); ?></td>
						<td>
							<select>
								<?php foreach ( $order_ids as $order_id ) { ?>
									<option value="<?php echo esc_attr( $order_id ); ?>"><?php echo esc_html( $order_id ); ?></option>
								<?php } ?>
							</select>
						</td>

						<td><?php echo esc_html( get_user_meta( $customer->ID, 'latitude', true ) ); ?></td>
						<td><?php echo esc_html( get_user_meta( $customer->ID, 'longitude', true ) ); ?></td>
						<td><?php echo esc_html( get_user_meta( $customer->ID, 'website', true ) ); ?></td>
						<td><?php echo esc_html( get_user_meta( $customer->ID, 'company_name', true ) ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php
}

function get_user_orders( $user_id ) {
	global $wpdb;
	$order_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT id FROM {$wpdb->prefix}wc_orders WHERE customer_id = %d",
			$user_id
		)
	);
	return $order_ids;
}


// WooCommerce Custom Checkbox On Product


// Add the new field
add_action( 'woocommerce_before_add_to_cart_button', 'ecommercehints_custom_product_checkbox_field' );

function ecommercehints_custom_product_checkbox_field() {

	$addons = array(
		'back_cover'       => array(
			'label' => __( 'Back Cover', 'your-text-domain' ),
			'price' => get_post_meta( get_the_ID(), '_addon_back_cover_price', true ),
		),
		'screen_protector' => array(
			'label' => __( 'Screen Protector', 'your-text-domain' ),
			'price' => get_post_meta( get_the_ID(), '_addon_screen_protector_price', true ),
		),
		'phone_pouch'      => array(
			'label' => __( 'Phone Pouch', 'your-text-domain' ),
			'price' => get_post_meta( get_the_ID(), '_addon_phone_pouch_price', true ),
		),
	);
	// Display checkboxes for addons only if the corresponding checkbox is checked in the admin panel
	foreach ( $addons as $addon_key => $addon_data ) {
		$addon_checked = get_post_meta( get_the_ID(), '_addon_' . $addon_key, true );
		if ( $addon_checked === 'yes' ) {
			echo '<label class="block">
            <input type="checkbox" class="addon-checkbox" value=' . esc_attr( $addon_data['price'] ) . ' id=' . esc_attr( $addon_key ) . ' name=' . esc_attr( $addon_key ) . ' data-addon="' . esc_attr( $addon_key ) . '" data-addon-price="' . esc_attr( $addon_data['price'] ) . '"> ' . esc_html( $addon_data['label'] ) . ' - ' . wc_price( $addon_data['price'] ) . '</label>';

		}
	}
}

// Save the field to the cart data
add_filter( 'woocommerce_add_cart_item_data', 'ecommercehints_save_custom_checkbox_to_cart_data', 10, 3 );
function ecommercehints_save_custom_checkbox_to_cart_data( $cart_item_data, $product_id, $variation_id ) {
	$fields      = array( 'back_cover', 'screen_protector', 'phone_pouch' );
	$addon_total = 0;
	foreach ( $fields as $field ) {
		if ( ! empty( $_POST[ $field ] ) ) {
			$cart_item_data[ $field ] = sanitize_text_field( $_POST[ $field ] );
			$addon_total             += (float) $cart_item_data[ $field ];
		}
	}
	// Save the addon total as metadata
	$cart_item_data['addon_total'] = $addon_total;
	return $cart_item_data;
}

// Show custom field data on cart, checkout, and thank you page.
add_filter( 'woocommerce_get_item_data', 'ecommercehints_show_custom_field_data_under_product_name', 10, 2 );
function ecommercehints_show_custom_field_data_under_product_name( $item_data, $cart_item ) {
	$fields = array(
		'back_cover'       => 'Back Cover',
		'screen_protector' => 'Screen Protector',
		'phone_pouch'      => 'Phone Pouch',
	);

	$item_data = array();
	foreach ( $fields as $field => $label ) {
		if ( ! empty( $cart_item[ $field ] ) ) {
			$item_data[] = array(
				'key'   => $label,
				'value' => '$' . $cart_item[ $field ],
			);
		}
	}
	return $item_data;
}

// Save the custom field data as order meta
add_action( 'woocommerce_checkout_create_order_line_item', 'ecommercehints_add_custom_field_data_as_order_meta', 10, 4 );
function ecommercehints_add_custom_field_data_as_order_meta( $item, $cart_item_key, $values, $order ) {
	$fields = array(
		'back_cover'       => 'Back Cover',
		'screen_protector' => 'Screen Protector',
		'phone_pouch'      => 'Phone Pouch',
	);

	foreach ( $fields as $key => $label ) {
		if ( ! empty( $values[ $key ] ) ) {
			$item->add_meta_data( $label, $values[ $key ] );
		}
	}
}


// Set custom cart item price
add_action( 'woocommerce_before_calculate_totals', 'add_custom_price', 1000, 1 );
function add_custom_price( $cart ) {
	// This is necessary for WC 3.0+
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	// Avoiding hook repetition (when using price calculations for example | optional)
	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	// Loop through cart items
	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		$product = $cart_item['data'];

		// Get the product price
		$product_price = $product->get_price();

		// Get addon_total if exists, otherwise set it to 0
		$addon_total = isset( $cart_item['addon_total'] ) ? (float) $cart_item['addon_total'] : 0;

		// Set the new price as the sum of product price and addon total
		$new_price = $product_price + $addon_total;

		// Set the new price for the cart item
		$cart_item['data']->set_price( $new_price );
	}
}

// Mini cart: Display custom price
add_filter( 'woocommerce_cart_item_price', 'filter_cart_item_price', 10, 3 );
function filter_cart_item_price( $price_html, $cart_item, $cart_item_key ) {

	// Check if custom_price metadata exists
	if ( isset( $cart_item['custom_price'] ) ) {
		// Get the product price
		$product_price = $cart_item['data']->get_price();

		// Get addon_total if exists, otherwise set it to 0
		$addon_total = isset( $cart_item['addon_total'] ) ? (float) $cart_item['addon_total'] : 0;

		// Calculate the new price by adding the product price and addon total
		$new_price = $product_price + $addon_total;

		// Return the new price formatted as currency
		return wc_price( $new_price );
	}
	// If custom_price metadata doesn't exist, return the original price HTML
	return $price_html;
}


// account creation

add_action( 'woocommerce_checkout_order_processed', 'create_customer_account_during_checkout' );

function create_customer_account_during_checkout( $order_id ) {
	$order = wc_get_order( $order_id );
	$email = $order->get_billing_email();

	// check if there are any users with the billing email as user or email
	$email_check = email_exists( $email );
	$user        = username_exists( $email );

	if ( $user == false && $email_check == false ) {
		// Retrieve random details from JSON Placeholder API
		$json_data = wp_remote_get( 'https://jsonplaceholder.typicode.com/users' );
		if ( ! is_wp_error( $json_data ) ) {
			$body = wp_remote_retrieve_body( $json_data );
			$data = json_decode( $body, true );

			// Assuming you want to pick a random user from the returned data
			$random_user = $data[ array_rand( $data ) ];

			// Extract required details
			$latitude     = $random_user['address']['geo']['lat'];
			$longitude    = $random_user['address']['geo']['lng'];
			$website      = $random_user['website'];
			$company_name = $random_user['company']['name'];

			// Create WordPress user as customer
			$user_id = wp_create_user( $email, wp_generate_password(), $email );
			if ( ! is_wp_error( $user_id ) ) {
				// Set user role as customer
				$customer = new WP_User( $user_id );
				$customer->set_role( 'customer' );

				// Save retrieved details as user meta
				update_user_meta( $user_id, '_last_order_id', $order_id );
				update_user_meta( $user_id, 'latitude', $latitude );
				update_user_meta( $user_id, 'longitude', $longitude );
				update_user_meta( $user_id, 'website', $website );
				update_user_meta( $user_id, 'company_name', $company_name );

				// user's billing data
				update_user_meta( $user_id, 'billing_address_1', $order->billing_address_1 );
				update_user_meta( $user_id, 'billing_address_2', $order->billing_address_2 );
				update_user_meta( $user_id, 'billing_city', $order->billing_city );
				update_user_meta( $user_id, 'billing_company', $order->billing_company );
				update_user_meta( $user_id, 'billing_country', $order->billing_country );
				update_user_meta( $user_id, 'billing_email', $order->billing_email );
				update_user_meta( $user_id, 'billing_first_name', $order->billing_first_name );
				update_user_meta( $user_id, 'billing_last_name', $order->billing_last_name );
				update_user_meta( $user_id, 'billing_phone', $order->billing_phone );
				update_user_meta( $user_id, 'billing_postcode', $order->billing_postcode );
				update_user_meta( $user_id, 'billing_state', $order->billing_state );

				// user's shipping data
				update_user_meta( $user_id, 'shipping_address_1', $order->shipping_address_1 );
				update_user_meta( $user_id, 'shipping_address_2', $order->shipping_address_2 );
				update_user_meta( $user_id, 'shipping_city', $order->shipping_city );
				update_user_meta( $user_id, 'shipping_company', $order->shipping_company );
				update_user_meta( $user_id, 'shipping_country', $order->shipping_country );
				update_user_meta( $user_id, 'shipping_first_name', $order->shipping_first_name );
				update_user_meta( $user_id, 'shipping_last_name', $order->shipping_last_name );
				update_user_meta( $user_id, 'shipping_method', $order->shipping_method );
				update_user_meta( $user_id, 'shipping_postcode', $order->shipping_postcode );
				update_user_meta( $user_id, 'shipping_state', $order->shipping_state );

				// Auto-login user
				wp_set_auth_cookie( $user_id, true );

			} else {
				// Handle error if user creation fails
				error_log( 'Failed to create user: ' . $user_id->get_error_message() );
			}
			// link past orders to this newly created customer
			wc_update_new_customer_past_orders( $user_id );
		}
	}
}


add_action( 'template_redirect', 'wc_custom_redirect_after_purchase' );

function wc_custom_redirect_after_purchase() {
	global $wp;
	if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
		$order_id      = absint( $wp->query_vars['order-received'] );
		$thank_you_url = add_query_arg( array( 'order_id' => $order_id ), 'http://wordpress-dev.local/thank-you/' );
		wp_redirect( $thank_you_url );
		exit;
	}
}



