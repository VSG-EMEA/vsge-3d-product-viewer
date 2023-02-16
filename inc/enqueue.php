<?php

/**
 * If the current page is a single post, and the post has a 3D model attached, then enqueue the 3D
 * model viewer script
 *
 * @return void
 */
function vsge_mv_frontend_scripts() {
	if ( ! is_main_query() || in_the_loop() || ! is_singular() ) {
		return;
	}
	global $post;

	$model_3d = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true );

	if ( is_attachment() || $model_3d ) {
		wp_register_script( 'model-viewer', 'https://cdn.jsdelivr.net/npm/@google/model-viewer@3.0.1/dist/model-viewer.min.js' );
		wp_enqueue_script('model-viewer');

		$asset = include VSGE_MV_PLUGIN_DIR . '/build/vsge-3d-product-viewer.asset.php';
		wp_enqueue_script( 'vsge-3d-product-viewer', VSGE_MV_PLUGIN_URL . 'build/vsge-3d-product-viewer.js', array( 'model-viewer', $asset['dependencies'][0] ) );
		wp_localize_script(
			'vsge-3d-product-viewer',
			'vsgemv',
			array(
				'siteurl' => get_option( 'siteurl' ),
			)
		);
	}
}

/**
 * If the script handle is 'model-viewer', then add the type="module" attribute to the script tag
 *
 * @param string $tag The HTML tag for the script.
 * @param string $handle The handle of the script.
 * @param string $src The URL of the script.
 *
 * @return string The script tag with the type="module" attribute added.
 */
function add_type_attribute( $tag, $handle, $src ) {
	// if not your script, do nothing and return original $tag
	if ( 'model-viewer' !== $handle ) {
		return $tag;
	}
	// change the script tag by adding type="module" and return it.
	$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
	return $tag;
}
add_filter( 'script_loader_tag', 'add_type_attribute', 10, 3 );

/**
 * If the current page is a single product page, then enqueue the stylesheet.
 *
 * @return void
 */
function vsge_mv_frontend_style() {
	if ( ! is_main_query() || in_the_loop() || ! is_singular() ) {
		return;
	}

	// Register and Enqueue on a single product page
	wp_enqueue_style( 'vsge-3d-product-viewer-style', VSGE_MV_PLUGIN_URL . 'build/style-vsge-3d-product-viewer.css' );
}
