<?php

/**
 * If the current page is a single post, and the post has a 3D model attached, then enqueue the 3D
 * model viewer script
 *
 * @return void
 */
function vsge_mv_frontend_scripts() {
	if ( has_3d_model() ) {
		$asset = include VSGE_MV_PLUGIN_DIR . '/build/vsge-3d-product-viewer.asset.php';
		wp_enqueue_script( 'vsge-3d-product-viewer', VSGE_MV_PLUGIN_URL . 'build/vsge-3d-product-viewer.js', array_merge( $asset['dependencies'] ), $asset['version'], true );
		wp_localize_script(
			'vsge-3d-product-viewer',
			'vsgenv',
			array(
				'siteurl' => get_option( 'siteurl' ),
			)
		);
	}
}

/**
 * If the current page is a single product page, then enqueue the stylesheet.
 *
 * @return void
 */
function vsge_mv_frontend_style() {
	if ( has_3d_model() ) {
		// Register and Enqueue on a single product page
		wp_enqueue_style( 'vsge-3d-product-viewer-style', VSGE_MV_PLUGIN_URL . 'build/style-vsge-3d-product-viewer.css' );
	}
}
