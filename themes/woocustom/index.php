<?php get_header(); ?>

<div class="container mx-auto">
    <div class="grid grid-cols-3 gap-4">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="p-4 border border-gray-300">
                <h2><?php the_title(); ?></h2>
                <p><?php the_excerpt(); ?></p>
            </div>
        <?php endwhile;
        else :
            echo '<p>No content found</p>';
        endif; ?>
    </div>
</div>

<?php get_footer(); ?>
