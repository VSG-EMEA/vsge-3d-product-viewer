<?php
/**
 * Template for the 3D model viewer.
 */
global $post;

$model_3d = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true );

if ( ! $model_3d ) {
	include VSGE_MV_PLUGIN_DIR . '/template/missing-3d-model.php';
	return;
}

$model_url     = wp_get_attachment_url( $model_3d );
$model_preview = get_post_meta( $model_3d, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_preview', true );

$model_data = (array) json_decode( get_post_meta( $model_3d, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_data', true ) );

$model_options = implode( ' ', array(
		isset( $model_data['camera-orbit'] ) && $model_data['camera-orbit'] !== '65deg 90deg 25m' ? ' camera-orbit="' . $model_data['camera-orbit'] . '"' : null,
		isset( $model_data['camera-target'] ) && $model_data['camera-target'] !== '0m 4.5m 0m' ? ' camera-target="' . $model_data['camera-target'] . '"' : null,
	) );

// if the hotspots have set generate the hotspot HTML pointers
$hotspots_html = '';
if ( ! empty( $model_data['hotspots'] ) ) {
	foreach ( $model_data['hotspots'] as $k => $hotspot ) {
		if ( isset( $hotspot->position, $hotspot->title ) ) {
			$slot = $hotspot->slot ? $hotspot->slot : 'hotspot-' . $k;
			if ( isset( $hotspot->href ) ) {
				$hotspots_html .= sprintf( '<div class="hotspot" slot="hotspot-%s" data-position="%s" data-visibility-attribute="visible">
          <div class="dot"></div>
          <a class="annotation" href="%s">%s</a>
        </div>', $slot, $hotspot->position, $hotspot->href, $hotspot->title );
			} else {
				$hotspots_html .= sprintf( '<div class="hotspot" slot="hotspot-%s" data-position="%s" data-visibility-attribute="visible">
          <div class="dot"></div>
          <span class="annotation">%s</span>
        </div>
', $slot, $hotspot->position, $hotspot->title );
			}
		}
	}
}
?>

<!-- The 3D model container -->
<div id="woocommerce-product-gallery__3d" style="display:none">
	<!-- The 3D model buttons and helpers -->
	<div class="model-viewer-helpers">
		<button slot="ar-init" id="ar-init" class="modal-button">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M450-199 256-312q-14-8-22-22t-8-30v-226q0-16 8-30t22-22l194-113q14-8 30-8t30 8l194 113q14 8 22 22t8 30v226q0 16-8 30t-22 22L510-199q-14 8-30 8t-30-8Zm16-23v-248L254-590v226q0 8 4 15t12 12l196 115Zm28 0 196-115q8-5 12-12t4-15v-226L494-470v248ZM132-680v-88q0-25 17.5-42.5T192-828h88v28h-88q-14 0-23 9t-9 23v88h-28Zm148 548h-88q-25 0-42.5-17.5T132-192v-88h28v88q0 14 9 23t23 9h88v28Zm400 0v-28h88q14 0 23-9t9-23v-88h28v88q0 25-17.5 42.5T768-132h-88Zm120-548v-88q0-14-9-23t-23-9h-88v-28h88q25 0 42.5 17.5T828-768v88h-28ZM480-494l212-122-196-113q-8-5-16-5t-16 5L268-616l212 122Zm0 14Zm0-14Zm14 24Zm-28 0Z"/></svg>
				</span>
		</button>
		<button slot="ar-center" id="ar-center">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m182-162-20-20 150-150H172v-28h188v188h-28v-140L182-162Zm596 0L628-312v140h-28v-188h188v28H648l150 150-20 20ZM172-600v-28h140L162-778l20-20 150 150v-140h28v188H172Zm428 0v-188h28v140l150-150 20 20-150 150h140v28H600Z"/></svg>
				</span>
		</button>
		<button slot="ar-rotation" id="ar-rotation" class="active">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m336-226-20-20 90-90q-113-15-193.5-54.5T132-480q0-59 101.5-103.5T480-628q145 0 246.5 44.5T828-480q0 38-51 74t-137 56v-28q77-20 118.5-49.5T800-480q0-35-85.5-77.5T480-600q-149 0-234.5 42.5T160-480q0 30 71 66t175 50l-90-90 20-20 124 124-124 124Z"/></svg>
				</span>
		</button>
		<button slot="ar-info" id="ar-info" class="modal-button">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" clip-rule="evenodd" viewBox="0 0 24 24"><path fill-rule="nonzero" d="M12 20.45A8.47 8.47 0 0 1 3.55 12c0-4.66 3.8-8.45 8.45-8.45 4.66 0 8.45 3.8 8.45 8.45 0 4.66-3.8 8.45-8.45 8.45Zm0-16.28a7.84 7.84 0 0 0 0 15.66 7.84 7.84 0 0 0 0-15.66Z"/><path fill-rule="nonzero" d="M12 9.28a.94.94 0 0 1-.94-.94c0-.52.41-.94.93-.94H12a.94.94 0 1 1 0 1.88Zm1.63 7.01h-3.26a.63.63 0 0 1-.62-.63c0-.34.28-.62.62-.62h1v-2.82h-1a.63.63 0 0 1-.62-.63c0-.34.28-.62.62-.62H12c.34 0 .63.28.63.62v3.45h1a.63.63 0 0 1 0 1.25Z"/></svg>
				</span>
		</button>
		<?php if ( ! empty( $hotspots_html ) ) { ?>
			<button slot="ar-toggle-hotspots" id="ar-toggle-hotspots">
					<span class="button-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-120 300-300l44-44 136 136 136-136 44 44-180 180ZM344-612l-44-44 180-180 180 180-44 44-136-136-136 136Z"/></svg>
					</span>
			</button>
		<?php } ?>
	</div>

	<!-- The 3D model -->
	<model-viewer
		 			id="vr-model"
				  class="mv-3d-model-viewer"
				  data-src="<?php echo $model_url; ?>"
				  data-model="<?php echo $model_3d; ?>"
				  poster="<?php echo wp_get_attachment_image_url( $model_preview, 'square-crop-medium' ); ?>"
				  camera-controls auto-rotate min-field-of-view="10deg"
				  bounds="tight"
				  environment-image="neutral"
				  shadow-intensity="1"
		<?php echo $model_options; ?>
				  ar ar-modes="<?php
	if ( vsge_3d_model_is_safari( $_SERVER['HTTP_USER_AGENT'] ) ) {
		echo 'quick-look scene-viewer';
	} else {
		echo 'scene-viewer quick-look webxr';
	} ?>">
		<?php echo $hotspots_html; ?>
		<button slot="ar-button" class="hide"></button>
		<div class="progress-bar-container hide" slot="progress-bar">
			<progress class="progress-bar" value="0" max="100"></progress>
		</div>
	</model-viewer>
</div>

<!-- Switch between gallery and 3D -->
<div id="woo-switch-gallery" class="woocommerce-product-gallery__select">
	<button id="woo-select-3d" data-type="3d-model"><?php esc_html_e( '3D/VR', 'vsge-3d-product-viewer' ); ?></button>
	<button id="woo-select-gallery" class="active" data-type="gallery"><?php esc_html_e( 'Gallery', 'vsge-3d-product-viewer' ); ?></button>
</div>

<!-- 3D model modal -->
<div id="vsge-modal-3d" class="vsge-modal-notice outer-modal">
	<div class="inner-modal">
		
		<div class="modal vsge-modal-qr" title="ar-init">
			<canvas id="vsge-vr-model"></canvas>
			<h3><?php esc_html_e( 'Instructions:', 'vsge-3d-product-viewer' ); ?></h3>
			<p><?php esc_html_e( 'Scan this code to open the model on your device, then, tap on the AR icon.', 'vsge-3d-product-viewer' ); ?></p>
			<p><b><?php esc_html_e( 'Click to close this message', 'vsge-3d-product-viewer' ); ?></b></p>
		</div>

		<div class="modal vsge-modal-info" title="ar-info">
			<h3><?php esc_html_e( 'info:', 'vsge-3d-product-viewer' ); ?></h3>
			<div class="mv-info-illustration">
				<svg xmlns="http://www.w3.org/2000/svg" width="200" viewBox="0 0 143.12 157.5"><path fill="#ddd" d="M69.37 97.63a59.59 59.59 0 0 1-36.54-12.47v35.8a36.55 36.55 0 0 0 73.1 0v-35.8a59.6 59.6 0 0 1-36.54 12.47"/><path fill="#ddd" d="M78.28 45.92h-6.29v9.49a6.37 6.37 0 0 1 4.5 6.09v15.87c0 2.8-1.83 5.27-4.5 6.09v8.81a54.33 54.33 0 0 0 33.92-13.97v-4.76a27.67 27.67 0 0 0-27.63-27.63M32.82 78.3a54.38 54.38 0 0 0 33.92 13.97v-8.81a6.37 6.37 0 0 1-4.5-6.09V61.5c0-2.8 1.83-5.27 4.5-6.09v-9.49h-6.29a27.67 27.67 0 0 0-27.63 27.63v4.76Zm36.16-50.33h1v48h-1z"/><path fill="#ddd" d="M0 69.71h36.08v1H0zm102.08 0h41.03v1h-41.03z"/><path d="m128.12 66-5.52-5.52.82-.82 4.12 4.12v-8.41h1.15v8.41l4.12-4.12.82.82-5.52 5.52Zm-9.48-9.48L113.12 51l5.52-5.52.82.82-4.12 4.12h8.41v1.15h-8.41l4.12 4.12-.82.82Zm18.96 0-.82-.82 4.12-4.12h-8.41v-1.15h8.41l-4.12-4.12.82-.82 5.52 5.52-5.52 5.52Zm-10.05-9.89v-8.41l-4.12 4.12-.82-.82 5.52-5.52 5.52 5.52-.82.82-4.12-4.12v8.41h-1.15ZM.93 55.38A10.32 10.32 0 0 1 0 51.07a10.62 10.62 0 0 1 3.19-7.78 10.45 10.45 0 0 1 7.72-3.21h2.6l-3.34-3.34.74-.74 4.6 4.6-4.6 4.6-.74-.74 3.34-3.34h-2.6a9.47 9.47 0 0 0-7 2.9 9.64 9.64 0 0 0-2.71 8.81c.11.6.28 1.19.5 1.76l-.78.79ZM10.92 66l-4.6-4.6 4.6-4.6.74.74-3.34 3.34h2.6c2.63.05 5.17-1 7-2.9a9.64 9.64 0 0 0 2.71-8.81 9.8 9.8 0 0 0-.5-1.76l.78-.78a10.32 10.32 0 0 1 .93 4.31c.05 2.92-1.1 5.74-3.19 7.78a10.45 10.45 0 0 1-7.72 3.21h-2.6l3.34 3.34-.74.74Zm72.76-38L72.67 16.99a10.07 10.07 0 0 1-6.43 2.38 9.37 9.37 0 0 1-6.87-2.81 9.33 9.33 0 0 1-2.81-6.87 9.4 9.4 0 0 1 2.81-6.88A9.37 9.37 0 0 1 66.24 0a9.4 9.4 0 0 1 6.88 2.81 9.34 9.34 0 0 1 2.82 6.87c0 1.2-.22 2.4-.66 3.52a9.33 9.33 0 0 1-1.72 2.91l11.01 11.01-.88.88Zm-17.43-9.86a8.17 8.17 0 0 0 6.01-2.44 8.15 8.15 0 0 0 2.44-6.01 8.17 8.17 0 0 0-2.44-6.01 8.15 8.15 0 0 0-6.01-2.44 8.17 8.17 0 0 0-6.01 2.44 8.15 8.15 0 0 0-2.44 6.01 8.17 8.17 0 0 0 2.44 6.01 8.17 8.17 0 0 0 6.01 2.44Z"/><path d="M65.6 5.12h1v8.91h-1z"/><path d="M61.65 9.07h8.91v1h-8.91z"/></svg>
			</div>
		</div>
	</div>
</div>
