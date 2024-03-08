<?php
/*
Template Name: My Account
*/

get_header();

// Retrieve user data
$current_user = wp_get_current_user();

?>

<div class="container mx-auto">
    <h1 class="text-3xl font-bold my-8">My Account</h1>
    <form action="<?php echo esc_url(wp_logout_url()); ?>" method="post">
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Logout</button>
    </form>
    <div class="my-4 bg-gray-100 p-4 rounded-md">
        <p class="font-semibold">Username:</p>
        <p><?php echo esc_html($current_user->user_login); ?></p>
    </div>
    <div class="my-4 bg-gray-100 p-4 rounded-md">
        <p class="font-semibold">Email:</p>
        <p><?php echo esc_html($current_user->user_email); ?></p>
    </div>
    <div class="my-4 bg-gray-100 p-4 rounded-md">
        <h2 class="text-xl font-semibold mb-2">Order History</h2>
        <?php
        $orders = wc_get_orders(array(
            'customer' => $current_user->ID,
            'limit' => -1,
        ));

        if ($orders) :
            ?>
            <ul>
                <?php foreach ($orders as $order) : ?>
                    <li class="py-2 border-b border-gray-200">
                        <p class="font-semibold">Order ID:</p>
                        <p><?php echo $order->get_id(); ?></p>
                        <p class="font-semibold">Total:</p>
                        <p><?php echo wc_price($order->get_total()); ?></p>
                        <p class="font-semibold">Date:</p>
                        <p><?php echo $order->get_date_created()->date('Y-m-d H:i:s'); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
    <div class="my-4 bg-gray-100 p-4 rounded-md">
        <h2 class="text-xl font-semibold mb-2">Account Details</h2>
        <p>You can update your account details <a href="<?php echo esc_url(get_edit_user_link()); ?>" class="text-blue-500 hover:underline">here</a>.</p>
    </div>
</div>

<?php get_footer(); ?>
