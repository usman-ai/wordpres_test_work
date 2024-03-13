<?php
/*
Template Name: Woo Template
*/
defined( 'ABSPATH' ) || exit;

global $product;

get_header();
?>
<div class="bg-gray-100">
	<div class="container mx-auto py-8">
		<h1 class="text-3xl font-semibold mb-8">Our Products</h1>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<!-- Product Loop -->
			<?php
			// Query WooCommerce products
			$args           = array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
			);
			$products_query = new WP_Query( $args );

			if ( $products_query->have_posts() ) {
				while ( $products_query->have_posts() ) {
					$products_query->the_post();
					$product = wc_get_product( get_the_ID() );
					?>
					<a href="<?php the_permalink(); ?>" class="block bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition duration-300">
						<img class="w-full h-48 object-cover object-center" src="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>" alt="<?php the_title(); ?>">
						<div class="p-6">
							<h2 class="font-semibold text-lg mb-2"><?php the_title(); ?></h2>
							<p class="text-gray-700 mb-2"><?php echo $product->get_price_html(); ?></p>
							<!-- Add to Cart Form -->
							<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" class="inline-block">
								<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( get_the_ID() ); ?>">
								<button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full">Add to Cart</button>
							</form>
						</div>
					</a>
					<?php
				}
			} else {
				echo 'No products found';
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
<?php
get_footer();
?>
