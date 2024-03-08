<?php
/**
 * Template Name: Order Details
 *
 * This template displays completed order details for logged-in customers.
 */

get_header();
?>

<div class="container mx-auto p-4">

    <?php
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Get the current user's ID
        $user_id = get_current_user_id();

        // Get the user's orders
        $customer_orders = wc_get_orders(array(
            'customer' => $user_id,
            'status' => 'completed', // Only completed orders
        ));
        // Display each order's details
        if (!empty($customer_orders)) {
            foreach ($customer_orders as $order) {
    ?>
                <div class="border border-gray-300 rounded-lg p-4 mb-4">
                    <h2 class="text-lg font-semibold mb-2">Order #<?php echo $order->get_order_number(); ?></h2>
                    <p><strong>Date:</strong> <?php echo $order->get_date_created()->format('F j, Y'); ?></p>
                    <p><strong>Total:</strong> <?php echo wc_price($order->get_total()); ?></p>

                    <?php
                    // Get order items
                    $order_items = $order->get_items();

                    if (!empty($order_items)) {
                    ?>
                        <h3 class="text-lg font-semibold mt-4 mb-2">Order Items:</h3>
                        <ul>
                            <?php
                            foreach ($order_items as $item) {
                                $product = $item->get_product();
                            ?>
                                <li><?php echo $product->get_name(); ?> &times; <?php echo $item->get_quantity(); ?> - <?php echo wc_price($item->get_total()); ?></li>
                            <?php
                                // Display meta data for the item
                                $item_meta_data = $item->get_meta_data();
                                foreach ($item_meta_data as $meta_data) {
                                    $meta_key = $meta_data->key;
                                    $meta_value = $meta_data->value;
                                    echo '<li><strong>' . $meta_key . ':</strong> ' . $meta_value . '</li>';
                                }
                            }
                            ?>
                        </ul>
                    <?php
                    }

                    // Get customer details
                    $customer = $order->get_user();
                    ?>
                    <h3 class="text-lg font-semibold mt-4 mb-2">Customer Details:</h3>
                    <p><strong>Name:</strong> <?php echo $customer->display_name; ?></p>
                    <p><strong>Email:</strong> <?php echo $customer->user_email; ?></p>
                </div>
        <?php
            }
        } else {
            echo '<p class="text-gray-600">No completed orders found.</p>';
        }
    } else {
        echo '<p class="text-gray-600">Please log in to view your order details.</p>';
    }
    ?>
</div>

<?php
get_footer();
?>
