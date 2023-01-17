<?php
/**
 * Plugin Name: vsge-3d-product-viewer
 * Plugin URI: https://github.com/erikyo/vsge-3d-product-viewer
 * Description: WordPress plugin vsge-3d-product-viewer
 * Version: 0.0.1
 * Author: codekraft
 */

define( 'PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_NAMESPACE', 'brb' );

include_once __DIR__ . '/inc/init.php';
include_once __DIR__ . '/inc/enqueue.php';

add_action('init', function () {

    add_action('wp_enqueue_scripts', 'vsge_frontend_scripts');
    add_action('wp_footer', 'vsge_frontend_style');

    add_filter('woocommerce_single_product_image_gallery_classes', 'vsge_3d_model_container_class');
    add_action('woocommerce_after_product-gallery__wrapper', 'vsge_3d_model_viewer', 20);

	add_filter ('the_content', 'replace_attachment_content', 1 );
});
