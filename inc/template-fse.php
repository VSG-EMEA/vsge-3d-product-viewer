<?php
/**
 * Initializes the 3D model block type.
 *
 * @return void
 */
function vsge_3d_model_init() {
	register_block_type( 'vsge/3d-model', array(
		'render_callback' => 'vsge_3d_model_block_render',
	) );
}
add_action( 'init', 'vsge_3d_model_init' );

/**
 * Render the 3D model block.
 *
 * @return string
 */
function vsge_3d_model_block_render() {
	return VSGE_MV_PLUGIN_URL . '/template/single-3d-model.php';
}

/**
 * Get the available block template types for the plugin.
 *
 * @return array The available block template types.
 */
function get_plugin_block_template_types() {
	$val = array(
		'3d-model' => array(
			'title'       => _x( 'Single 3d model', 'Template name', 'vsge-3d-product-viewer' ),
			'description' => __( 'Displays a single product.', 'vsge-3d-product-viewer' ),
		)
	);

	return $val;
}

/**
 * Returns template titles.
 *
 * @param string $template_slug The templates slug (e.g. single-product).
 *
 * @return string Human friendly title.
 */
function get_block_template_title( $template_slug ) {
	$plugin_template_types = get_plugin_block_template_types();
	if ( isset( $plugin_template_types[ $template_slug ] ) ) {
		return $plugin_template_types[ $template_slug ]['title'];
	} else {
		// Human friendly title converted from the slug.
		return ucwords( preg_replace( '/[\-_]/', ' ', $template_slug ) );
	}
}

/**
 * Returns template descriptions.
 *
 * @param string $template_slug The templates slug (e.g. single-product).
 *
 * @return string Template description.
 */
function get_block_template_description( $template_slug ) {
	$plugin_template_types = get_plugin_block_template_types();
	if ( isset( $plugin_template_types[ $template_slug ] ) ) {
		return $plugin_template_types[ $template_slug ]['description'];
	}

	return '';
}

function create_new_block_template_object( $template_file, $template_type, $template_slug, $template_is_from_theme = false ) {
	$theme_name = wp_get_theme()->get( 'TextDomain' );

	$new_template_item = array(
		'slug'        => $template_slug,
		'id'          => $template_is_from_theme ? $theme_name . '//' . $template_slug : VSGE_MV_FSE_NAMESPACE . '//' . $template_slug,
		'path'        => $template_file,
		'type'        => $template_type,
		'theme'       => $template_is_from_theme ? $theme_name : VSGE_MV_FSE_NAMESPACE,
		// Plugin was agreed as a valid source value despite existing inline docs at the time of creating: https://github.com/WordPress/gutenberg/issues/36597#issuecomment-976232909.
		'source'      => $template_is_from_theme ? 'theme' : 'plugin',
		'title'       => get_block_template_title( $template_slug ),
		'description' => get_block_template_description( $template_slug ),
		'post_types'  => array(), // Don't appear in any Edit Post template selector dropdown.
	);

	return (object) $new_template_item;
}
