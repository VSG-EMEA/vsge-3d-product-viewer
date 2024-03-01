<?php
/**
 * Redirects to custom template file for 3D model if query parameters match criteria.
 *
 * @return string URL for custom template file
 */
function vsge_3d_model_redirect( $template ) {
	// get the requested page and the query string
	$page = explode( '/', $_SERVER['REQUEST_URI'] );
	if ( ! empty( $page[1] ) ) {
		// get the current page query string and check if it matches with model3d
		$query_string =  explode( "=", $page[2] ) ;
		if ($query_string[0] === 'model3d?id') {
			// check if the post has a 3D model
			if ( has_3d_model( $query_string[1] ) ) {
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



