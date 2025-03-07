<?php
function enqueue_glightbox_scripts() {
    wp_enqueue_script('glightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/js/glightbox.min.js', array(), null, true);
    wp_enqueue_style('glightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.min.css', array(), null, 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_glightbox_scripts');