<?php

function vsge_redirect_to_custom_page( $template ) {
	$custom_template = VSGE_MV_PLUGIN_DIR . '/template/single-3d-model.php';
	if ( ! is_page( '3d-model' ) ) {
		return $template;
	}
	// Load the custom template file
	if ( file_exists( $custom_template ) ) {
		include($custom_template);
		exit;
	}
}
add_filter( 'template_include', 'vsge_redirect_to_custom_page' );


function vsge_3d_model_redirect() {
	// Check if the query parameters match your criteria
	if ( is_page( '3d-model' ) &&  isset( $_GET['model_id'] ) ) {
		$attachment_id = intval( $_GET['model_id'] );

		// Check if the 3D model exists
		if ( has_3d_model( $attachment_id ) ) {
			// Use your custom template file for redirection
			return VSGE_MV_PLUGIN_URL . '/template/single-3d-model.php';
		}
	}
}
add_filter( 'redirect_canonical', 'vsge_3d_model_redirect' );

function register_custom_template_part() {
	if ( is_page( '3d-model' ) ) {
		get_template_part( 'vsge-3d-product-viewer' );
	}
}
add_action( 'after_setup_theme', 'register_custom_template_part' );

function vsge_3d_model_init() {
	register_block_type( 'vsge/3d-model', array(
		'render_callback' => 'vsge_3d_model_block_render',
	) );
}
add_action( 'init', 'vsge_3d_model_init' );

function vsge_3d_model_block_render() {
	return VSGE_MV_PLUGIN_URL . '/template/single-3d-model.php';
}

function vsge_3d_model_classic_render() {
	get_header();

	echo VSGE_MV_PLUGIN_URL . '/template/single-3d-model.php';

	get_footer();
}


function vsge_mv_template_redirect() {
	if ( is_page_template( 'single-3d-model.php' ) ) {
		// Enqueue scripts and styles only when using the custom template.
		add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_style' );
		add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_scripts' );
	}
}
add_action( 'template_redirect', 'vsge_mv_template_redirect' );

/**
 * Shortcode to display 3D model viewer.
 * Used in the "3D model" in a custom user page
 */
function vsge_3d_model_container() {
	if ( has_3d_model() ) {
		add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_scripts' );
		add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_style' );
		include VSGE_MV_PLUGIN_DIR . '/template/model-callback.php';
	}
}
add_shortcode( 'vsge_3d_model', 'vsge_3d_model_container' );
