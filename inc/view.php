<?php

/**
 * Redirects to a custom page if the current page is '3d-model'.
 *
 * @param mixed $template The original template.
 * @return mixed
 */
function vsge_redirect_to_custom_page( $template ) {
	if ( is_page( 'model' ) ) {
		$custom_template = VSGE_MV_PLUGIN_DIR . '/template/single-3d-model.php';
		return $custom_template;
	}

	return $template;
}
add_filter( 'template_include', 'vsge_redirect_to_custom_page' );

/**
 * Redirects to custom template file for 3D model if query parameters match criteria.
 *
 * @return string URL for custom template file
 */
function vsge_3d_model_redirect( $template ) {
	if ( is_page( 'model' ) && isset( $_GET['id'] ) ) {
		$attachment_id = intval( $_GET['id'] );

		if ( has_3d_model( $attachment_id ) ) {
			$custom_template = VSGE_MV_PLUGIN_DIR . '/template/single-3d-model.php';
			return $custom_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'vsge_3d_model_redirect' );

/**
 * Function to enqueue scripts and styles for the custom template.
 */
function vsge_mv_enqueue_scripts() {
	wp_enqueue_style( 'your-style-handle', 'path-to-your-style.css' );
	wp_enqueue_script( 'your-script-handle', 'path-to-your-script.js', array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'vsge_mv_enqueue_scripts' );

/**
 * Function to redirect based on page template.
 */
function vsge_mv_template_redirect() {
	if ( is_page( 'model' ) ) {
		// Your code here
		include VSGE_MV_PLUGIN_DIR . '/template/single-3d-model.php';
		die();
	}
}
add_action( 'template_redirect', 'vsge_mv_template_redirect' );
