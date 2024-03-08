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
             'back_cover' => __('Back Cover', 'woocommerce'),
             'screen_protector' => __('Screen Protector', 'woocommerce'),
             'phone_pouch' => __('Phone Pouch', 'woocommerce')
         );
 
         foreach ($addons as $addon_key => $addon_label) {
             woocommerce_wp_checkbox(
                 array(
                     'id'            => '_addon_' . $addon_key,
                     'label'         => $addon_label,
                     'description'   => sprintf(__('Add a %s', 'woocommerce'), strtolower($addon_label))
                 )
             );
 
             woocommerce_wp_text_input(
                 array(
                     'id'            => '_addon_' . $addon_key . '_price',
                     'label'         => $addon_label . __(' Price', 'woocommerce'),
                     'description'   => sprintf(__('Enter the price for the %s', 'woocommerce'), strtolower($addon_label)),
                     'type'          => 'number',
                     'custom_attributes' => array(
                         'step' => '0.01',
                         'min' => '0'
                     )
                 )
             );
         }
 
         echo '</div>';
     }
 
     // Save custom product addons
     public function save_custom_product_addons($product_id) {
         $addons = array(
             'back_cover' => '_addon_back_cover_price',
             'screen_protector' => '_addon_screen_protector_price',
             'phone_pouch' => '_addon_phone_pouch_price'
         );
 
         // Loop through addons and save data
         foreach ($addons as $addon_key => $addon_price_key) {
             $addon_meta_key = '_addon_' . $addon_key;
             $addon_price_meta_key = $addon_price_key;
             $addon_value = isset($_POST[$addon_meta_key]) ? 'yes' : 'no';
             update_post_meta($product_id, $addon_meta_key, $addon_value);
             if ($addon_value === 'yes' && isset($_POST[$addon_price_meta_key])) {
                 update_post_meta($product_id, $addon_price_meta_key, $_POST[$addon_price_meta_key]);
             }
         }
     }
 }
 
 new Custom_Product_Addons();


 // Register admin menu page
add_action( 'admin_menu', 'custom_admin_menu_page' );

function custom_admin_menu_page() {
    add_menu_page(
        __('Customer Details', 'my-plugin'),
        __('Customer Details', 'my-plugin'),
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
    $customers = get_users( [ 'role' => 'customer' ] );
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
                <?php foreach ( $customers as $customer ) {
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



