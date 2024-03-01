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

	if ( $post === null && is_numeric($postID) ) {
		global $wp_query;
		global $post;
		$post = get_post( $postID );
		$wp_query->is_single = true;
		$wp_query->is_404 = false;
		$wp_query->queried_object = $post;
		$post->ID = $postID;
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
