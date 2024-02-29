<?php
function vsge_mv_template_redirect() {
	if ( is_page_template( 'custom-3d-model-template.php' ) ) {
		// Enqueue scripts and styles only when using the custom template.
		add_action( 'wp_enqueue_scripts', 'vsge_mv_frontend_scripts' );
		add_action( 'wp_footer', 'vsge_mv_frontend_style' );
	}
}

/**
 * If the current post is an attachment, and the attachment is a 3D model, then replace the content
 * with the 3D model
 *
 * @param string $content The content of the post.
 *
 * @return string The content of the attachment page.
 */
function replace_attachment_content( ) {
	if ( is_attachment() ) {
		return get_template_part('3d-model-template.php');
	}
}

/**
 * It checks if the post has a 3D model attached to it, if it does, it generates the HTML for the model
 * viewer.
 *
 * @return void - the HTML for the 3D model viewer.
 */
function vsge_3d_model_viewer() {
	global $post;

	$model_3d = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true );

	if ( ! $model_3d ) {
		return;
	}

	$model_url     = wp_get_attachment_url( $model_3d );
	$model_preview = get_post_meta( $model_3d, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_preview', true );

	$model_data = (array) json_decode( get_post_meta( $model_3d, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_data', true ) );

	$model_options = implode(
		' ',
		array(
			isset( $model_data['camera-orbit'] ) && $model_data['camera-orbit'] !== '65deg 90deg 25m' ? ' camera-orbit="' . $model_data['camera-orbit'] . '"' : null,
			isset( $model_data['camera-target'] ) && $model_data['camera-target'] !== '0m 4.5m 0m'  ? ' camera-target="' . $model_data['camera-target'] . '"' : null,
		)
	);

	// if the hotspots have set generate the hotspot HTML pointers
	$hotspots_html = '';
	if ( ! empty( $model_data['hotspots'] ) ) {
		foreach ( $model_data['hotspots'] as $k => $hotspot ) {
			if ( isset( $hotspot->position, $hotspot->title ) ) {
				$slot = $hotspot->slot ? $hotspot->slot : 'hotspot-' . $k;
				if ( isset( $hotspot->href ) ) {
					$hotspots_html .= sprintf(
						'<div class="hotspot" slot="hotspot-%s" data-position="%s" data-visibility-attribute="visible">
          <div class="dot"></div>
          <a class="annotation" href="%s">%s</a>
        </div>',
						$slot,
						$hotspot->position,
						$hotspot->href,
						$hotspot->title
					);
				} else {
					$hotspots_html .= sprintf(
						'<div class="hotspot" slot="hotspot-%s" data-position="%s" data-visibility-attribute="visible">
          <div class="dot"></div>
          <span class="annotation">%s</span>
        </div>
',
						$slot,
						$hotspot->position,
						$hotspot->title
					);
				}
			}
		}
	}

	?>
	<?php
}

/**
 * Shortcode to display 3D model viewer.
 */
function vsge_3d_model_shortcode( $atts ) {
	ob_start();
	vsge_3d_model_viewer();
	return ob_get_clean();
}
add_shortcode( 'vsge_3d_model', 'vsge_3d_model_shortcode' );
