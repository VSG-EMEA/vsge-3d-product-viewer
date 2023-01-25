<?php

/**
 * If the product has a 3D model, add a class to the product gallery container
 *
 * @param array $classes classes Array of CSS classes to add to the container.
 *
 * @return array the new product classes array.
 */
function vsge_3d_model_container_class( $classes ) {
	global $product;
	$classes[] = 'woocommerce-product-gallery--' . ( get_post_meta( $product->get_id(), VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true ) > 0 ? 'with-vr' : '' );

	return $classes;
}

/**
 * If the current post is an attachment, and the attachment is a 3D model, then replace the content
 * with the 3D model
 *
 * @param string $content The content of the post.
 *
 * @return string The content of the attachment page.
 */
function replace_attachment_content( $content ) {
	if ( ! is_main_query() || in_the_loop() || ! is_singular() ) {
		return $content;
	}

	global $post;
	$attachment_meta = wp_get_attachment_metadata( $post->ID );

	if ( isset( $attachment_meta['mime-type'] ) && $attachment_meta['mime-type'] == 'glb' ) {
		$model_3d         = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true );
		$model_url        = wp_get_attachment_url( $model_3d );
		$model_preview_id = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_preview', true );

		$model_data = (array) json_decode( get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_data', true ) );

		$model_options = implode(
			' ',
			array(
				isset( $model_data['camera-orbit'] ) ? ' camera-orbit="' . $model_data['camera-orbit'] . '"' : null,
				isset( $model_data['camera-target'] ) ? ' camera-target="' . $model_data['camera-target'] . '"' : null,
			)
		);

		ob_start();
		?><div id="woocommerce-product-gallery__3d">
		<model-viewer id="vr-model"
					  src="<?php echo $model_url; ?>"
					  data-model="<?php echo $model_3d; ?>"
					  poster="<?php echo wp_get_attachment_image_url( $model_preview_id, 'square-crop-570' ); ?>"
					  bounds="tight" camera-controls <?php echo $model_options; ?> min-field-of-view="10deg"
					  ar camera-controls environment-image="neutral" shadow-intensity="1">
			<button id="ar-failure">
				<div class="material-icons large">
					error
				</div>
				<?php esc_html_e( 'AR is not tracking!', 'vsge-mv' ); ?>
				<p id="error"></p>
			</button>
			<div class="vsge-modal-notice progress-bar-container hide" slot="progress-bar">
				<div class="inner-modal">
					<h3><?php esc_html_e( 'Ladies and Gentlemen, please start your (VR) Engines', 'vsge-mv' ); ?></h3>
					<p><?php printf( esc_html__( '3D model is loading, please wait. After the download augmented reality mode wil be enabled, keep your phone facing the part of your workshop where you want to place your new %s product', 'vsge-mv' ), COMPANY ); ?></p>
					<progress class="progress-bar" value="0" max="100"></progress>
				</div>
			</div>
		</model-viewer>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
	}

	return $content;
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
			isset( $model_data['camera-orbit'] ) ? ' camera-orbit="' . $model_data['camera-orbit'] . '"' : null,
			isset( $model_data['camera-target'] ) ? ' camera-target="' . $model_data['camera-target'] . '"' : null,
		)
	);
	// if the hotspots has set generate the hotspot html pointers
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
	<!-- <model-viewer> HTML element -->
	<div id="woocommerce-product-gallery__3d">
		<div class="model-viewer-helpers">
			<button slot="ar-init" id="ar-init">
		  <span class="material-icons">
		  view_in_ar
		  </span>
			</button>
			<button slot="ar-center" id="ar-center">
		  <span class="material-icons">
		  zoom_in_map
		  </span>
			</button>
			<button slot="ar-rotation" id="ar-rotation" class="active">
		  <span class="material-icons">
		  360
		  </span>
			</button>
			<?php if ( ! empty( $hotspots_html ) ) { ?>
				<button slot="ar-toggle-hotspots" id="ar-toggle-hotspots" class="active">
					<span class="material-icons">unfold_more</span>
				</button>
			<?php } ?>
		</div>
		<model-viewer id="vr-model"
					  src="<?php echo $model_url; ?>"
					  data-model="<?php echo $model_3d; ?>"
					  poster="<?php echo wp_get_attachment_image_url( $model_preview[0], 'square-crop-570' ); ?>"
					  camera-controls auto-rotate min-field-of-view="10deg" <?php echo $model_options; ?>
					  ar ar-modes="webxr scene-viewer quick-look" ar-scale="fixed"
					  bounds="tight" environment-image="neutral" shadow-intensity="1"
		>
			<?php echo $hotspots_html; ?>
			<button slot="ar-button" class="hide"></button>
			<div class="progress-bar-container hide" slot="progress-bar">
				<progress class="progress-bar" value="0" max="100"></progress>
			</div>
		</model-viewer>
	</div>
	<div id="woo-switch-gallery" class="woocommerce-product-gallery__select">
		<button id="woo-select-3d" class="active" data-type="3d-model"><?php esc_html_e( '3D/VR', 'vsge-mv' ); ?></button>
		<button id="woo-select-gallery" data-type="gallery"><?php esc_html_e( 'Gallery', 'vsge-mv' ); ?></button>
	</div>
	<div id="vsge-modal-qrcode" class="vsge-modal-notice outer-modal">
		<div class="inner-modal">
			<canvas id="vsge-vr-model"></canvas>
			<h3><?php esc_html_e( 'Instructions:', 'vsge-mv' ); ?></h3>
			<p><?php esc_html_e( 'Scan this code to open the model on your device, then, tap on the AR icon.', 'vsge-mv' ); ?></p>
			<p><b><?php esc_html_e( 'Click to close this message', 'vsge-mv' ); ?></b></p>
		</div>
	</div>
	<?php
}
