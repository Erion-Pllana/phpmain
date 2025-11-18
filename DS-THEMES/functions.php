<?php
function load_scripts() {
    wp_enqueue_style('style' ,get_stylesheet_uri());

    wp_enqueue_script('dropdown' , get_template_directory_uri() . '/js/dropdown.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts','load_scripts');
?>