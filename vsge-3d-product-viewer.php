<?php
/**
 * Plugin Name: VSGE 3d product viewer
 * Plugin URI: https://github.com/erikyo/vsge-3d-product-viewer
 * Description: WordPress plugin vsge-3d-product-viewer
 * Version: 0.2.0
 * Author: codekraft
 * Text Domain: vsge-3d-product-viewer
 * Domain Path: languages/
 */

define( 'VSGE_MV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VSGE_MV_PLUGIN_DIR', __DIR__ );
define( 'VSGE_MV_VERSION', '0.2.0' );

/**
 * Enables the upload of glb files to WordPress media library.
 */
require_once VSGE_MV_PLUGIN_DIR . '/inc/ModelData.php';
require_once VSGE_MV_PLUGIN_DIR . '/inc/Assets.php';
require_once VSGE_MV_PLUGIN_DIR . '/inc/StandaloneViewerEndpoint.php';
require_once VSGE_MV_PLUGIN_DIR . '/inc/Renderer.php';
require_once VSGE_MV_PLUGIN_DIR . '/inc/Block.php';
require_once VSGE_MV_PLUGIN_DIR . '/inc/Plugin.php';

\Vsge3DProductViewer\Plugin::register();
register_activation_hook( __FILE__, array( '\\Vsge3DProductViewer\\StandaloneViewerEndpoint', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\\Vsge3DProductViewer\\StandaloneViewerEndpoint', 'deactivate' ) );

/** Compatibility helpers retained for external VSGE callers. */
function has_3d_model( $product_id = null ) { return null !== \Vsge3DProductViewer\ModelData::for_product( $product_id ?: get_the_ID() ); }
function vsge_3d_model_container() { echo \Vsge3DProductViewer\Renderer::render(); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
