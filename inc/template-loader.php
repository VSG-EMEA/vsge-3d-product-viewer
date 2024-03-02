<?php

/**
 * Shortcode to display 3D model viewer.
 * Used in the Woocommerce product page.
 */
function vsge_3d_model_container() {
	if ( has_3d_model() ) {
		add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_scripts' );
		add_action( 'wp_footer', 'vsge_mv_frontend_style' );
		include VSGE_MV_PLUGIN_DIR . '/template/model-callback.php';
	}
}
add_shortcode( 'vsge_3d_model', 'vsge_3d_model_container' );
