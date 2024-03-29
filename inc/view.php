<?php
/**
 * Redirects to custom template file for 3D model if query parameters match criteria.
 *
 * @return string URL for custom template file
 */
function vsge_3d_model_redirect( $template ) {
	// get the requested page and the query string
	$page = explode( '/', rtrim($_SERVER['REQUEST_URI'], '?') );
	$model = explode( "=", sanitize_text_field( $page[ count( $page ) - 1 ] ) );

	// check if the requested page is a 3D model
	if ( ! empty( $model[0] )  ) {
		$string_slug = $model[0];
		if ($string_slug === 'model3d') {
			$currentModel = $model[1];
			if ( ! is_numeric( $currentModel ) ) {
				$product = get_page_by_path( sanitize_text_field($currentModel), OBJECT, 'product' );
				$product_id = !empty($product) ? $product->ID : false;
			} else {
				$product_id = intval($currentModel);
			}

			// check if the post has a 3D model
			if ( $product_id && has_3d_model($product_id ) ) {
				// check if the post has a 3D model
				$template = VSGE_MV_PLUGIN_DIR . '/template/single-3d-model.php';
			} else {
				// otherwise redirect to single
				$template = VSGE_MV_PLUGIN_DIR . '/template/missing-3d-model.php';
			}
		}

	}

	return $template;
}

add_filter( 'template_include', 'vsge_3d_model_redirect' );



