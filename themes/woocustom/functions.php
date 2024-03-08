<?php
// Enqueue styles and scripts
function my_custom_theme_enqueue_styles() {
    // Enqueue Tailwind CSS
    wp_enqueue_style('tailwindcss_output', get_template_directory_uri() . '/src/output.css' , array(), '1.0');
   
}
add_action('wp_enqueue_scripts', 'my_custom_theme_enqueue_styles');
