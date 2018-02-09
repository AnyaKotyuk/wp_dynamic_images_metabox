<?php
/**
 * Init featured images metabox
 *
 */

include 'featured-images.php';

add_action('add_meta_boxes', 'init_featured_images_metabox');
add_action('save_post', 'save_featured_images_metabox', 10, 2);
//add_action('init', 'add_featured_images_scripts');
