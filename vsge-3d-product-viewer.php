<?php
/**
 * Plugin Name: VSGE 3d product viewer
 * Plugin URI: https://github.com/erikyo/vsge-3d-product-viewer
 * Description: WordPress plugin vsge-3d-product-viewer
 * Version: 0.0.2
 * Author: codekraft
 * Text Domain: vsge-3d-product-viewer
 * Domain Path: /languages/
 */

define( 'VSGE_MV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VSGE_MV_PLUGIN_DIR', __DIR__ );
define( 'VSGE_MV_PLUGIN_NAMESPACE', 'brb' );

/**
 * Enables the upload of glb files to WordPress media library.
 */
function vsge_mv_mime_types($mime_types){
	$mime_types['glb'] = 'application/octet-stream';
	return $mime_types;
}
add_filter('upload_mimes', 'vsge_mv_mime_types', 1, 1);

include_once VSGE_MV_PLUGIN_DIR . '/inc/enqueue.php';
include_once VSGE_MV_PLUGIN_DIR . '/inc/view.php';

/**
 * Adding actions to the init hook.
 */
add_action(
	'plugins_loaded',
	function() {
		load_plugin_textdomain( 'vsge-3d-product-viewer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
);

/**
 * Enqueue scripts and styles
 */
add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_scripts' );
add_action( 'wp_footer', 'vsge_mv_frontend_style' );

/**
 * Custom Template Redirect
 */
add_action( 'template_redirect', 'vsge_mv_template_redirect' );

/**
 * Single product page
 */
add_filter( 'woocommerce_single_product_image_gallery_classes', 'vsge_3d_model_container_class' );
add_action( 'woocommerce_after_product-gallery__wrapper', 'vsge_3d_model_viewer', 20 );

/**
 * Attachment page
 */
add_filter( 'the_content', 'replace_attachment_content', 2 );
