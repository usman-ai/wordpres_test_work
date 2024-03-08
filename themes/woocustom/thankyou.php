<?php
/*
 * Template Name: Thank You Page
 */

get_header();

// Retrieve order data
$order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
$order = wc_get_order($order_id);

if (!$order || !$order->get_id()) {
    // Order not found, display a message or redirect to another page
    echo 'Order not found.';
    get_footer();
    exit;
}

?>

<div class="container mx-auto">
    <h1 class="text-3xl font-bold my-8">Thank You for Your Purchase!</h1>
    <div class="my-4 bg-gray-100 p-4 rounded-md">
        <h2 class="text-xl font-semibold mb-2">Order Details</h2>
        <p><strong>Order ID:</strong> <?php echo $order->get_order_number(); ?></p>
        <p><strong>Total:</strong> <?php echo wc_price($order->get_total()); ?></p>
        <p><strong>Date:</strong> <?php echo $order->get_date_created()->date('Y-m-d H:i:s'); ?></p>
        
        <!-- Shipping Address -->
        <h2 class="text-xl font-semibold mt-4 mb-2">Shipping Address</h2>
        <p><strong>Name:</strong> <?php echo $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(); ?></p>
        <p><strong>Address:</strong> <?php echo $order->get_shipping_address_1() . ', ' . $order->get_shipping_city() . ', ' . $order->get_shipping_state() . ', ' . $order->get_shipping_postcode() . ', ' . $order->get_shipping_country(); ?></p>
        <!-- End Shipping Address -->

        <!-- Order Items -->
        <h2 class="text-xl font-semibold mt-4 mb-2">Order Items</h2>
        <?php
        foreach ($order->get_items() as $item_id => $item) {
            echo '<p><strong>' . $item->get_name() . '</strong> - Quantity: ' . $item->get_quantity() . ' - Total: ' . wc_price($item->get_total()) . '</p>';
        }
        ?>
        <!-- End Order Items -->

        <!-- Payment Method -->
        <h2 class="text-xl font-semibold mt-4 mb-2">Payment Method</h2>
        <p><?php echo $order->get_payment_method_title(); ?></p>
        <!-- End Payment Method -->

        <!-- Customer Information -->
        <h2 class="text-xl font-semibold mt-4 mb-2">Customer Information</h2>
        <p><strong>Name:</strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
        <p><strong>Email:</strong> <?php echo $order->get_billing_email(); ?></p>
        <!-- End Customer Information -->
        
      
    </div>
</div>

<?php get_footer(); ?>
