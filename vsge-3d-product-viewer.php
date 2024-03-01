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
define( 'VSGE_MV_FSE_NAMESPACE', 'vsge' );

/**
 * Enables the upload of glb files to WordPress media library.
 */
function vsge_mv_mime_types($mime_types){
	$mime_types['glb'] = 'application/octet-stream';
	return $mime_types;
}
add_filter('upload_mimes', 'vsge_mv_mime_types', 1, 1);

/**
 * Includes the required files
 */
include_once VSGE_MV_PLUGIN_DIR . '/inc/utils.php';
include_once VSGE_MV_PLUGIN_DIR . '/inc/enqueue.php';
include_once VSGE_MV_PLUGIN_DIR . '/inc/template-classic.php';
include_once VSGE_MV_PLUGIN_DIR . '/inc/template-fse.php';
include_once VSGE_MV_PLUGIN_DIR . '/inc/template-loader.php';
include_once VSGE_MV_PLUGIN_DIR . '/inc/view.php';

/** Add a custom class to the body whenever the displayed product has a 3D model */
add_filter( 'woocommerce_single_product_image_gallery_classes', 'vsge_3d_model_container_class' );

/** Load the 3D model viewer script and style */
add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_scripts' );
add_action( 'wp_footer', 'vsge_mv_frontend_style' );

/** Adds to the product gallery container the 3D model viewer */
add_action( 'woocommerce_after_product-gallery__wrapper', 'vsge_3d_model_container', 20 );
