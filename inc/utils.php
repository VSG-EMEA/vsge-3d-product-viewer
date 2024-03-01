<?php

/**
 * Check if the post has a 3D model attached.
 *
 * @return bool true if the post has a 3D model, false otherwise.
 */
function has_3d_model($postID = null) {
	global $post;
	// Check if the post has a 3D model already attached
	if (!$postID) {
		if ( isset( $post->ID ) ) {
			// Check if the post has a 3D model
			$postID = $post->ID;
		} else {
			$postID = intval( $_GET['id'] );
		}
	}

	if ( $post === null ) {
		$post = new \stdClass();
		$post = get_post( $postID );
	}

	if ($postID) {
		// Check if the attachment has a 3D model
		$attachment_id = get_post_meta( $postID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true );
	}

	// if the post has a 3D model return true and set the $post->has_3d_model
	if ( !empty($attachment_id) ) {
		$post->model3d = $attachment_id;
		return true;
	}

	return false;
}

/**
 * If the product has a 3D model, add a class to the product gallery container
 *
 * @param array $classes classes Array of CSS classes to add to the container.
 *
 * @return array the new product classes array.
 */
function vsge_3d_model_container_class( $classes ) {
	global $post;
	if ($post && is_product()) {
		$classes[] = has_3d_model()
			? 'woocommerce-product-gallery--with-vr'
			: 'woocommerce-product-gallery--without-vr ';
	}

	return $classes;
}

/**
 * Returns true if the browser is safari.
 *
 * @param string $user_agent The user agent.
 *
 * @return bool true if the browser is safari, false otherwise.
 */
function vsge_3d_model_is_safari( $user_agent ) {
	if ( strlen( strstr( $user_agent, 'iPhone' ) ) > 0 || strlen( strstr( $user_agent, 'iPad' ) ) > 0 ) {
		return true;
	}
}

function get_plugin_block_template_types() {
	$val = array(
		'single-product'                      => array(
			'title'       => _x( 'Single Product', 'Template name', 'woocommerce' ),
			'description' => __( 'Displays a single product.', 'woocommerce' ),
		)
	);
	return $val;
}

/**
 * Returns template titles.
 *
 * @param string $template_slug The templates slug (e.g. single-product).
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
