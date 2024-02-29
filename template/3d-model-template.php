<?php
/**
 * Template for the 3D model viewer.
 */
?>
<div id="woocommerce-product-gallery__3d" style="display:none">
	<div class="model-viewer-helpers">
		<button slot="ar-init" id="ar-init">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48"><path d="M448-167 228-296q-13.775-8.426-21.387-22.213Q199-332 199-348v-257q0-16 7.613-29.787Q214.225-648.574 228-657l221-131q14-8 31-8t31 8l221 131q13.775 8.426 21.388 22.213Q761-621 761-605v257q0 16-7.875 29.787Q745.25-304.426 731-296L508-167q-14.328 8-30.164 8Q462-159 448-167Zm2-69v-224L260-569v219l190 114Zm60 0 191-114v-219L510-460v224ZM80-691v-129q0-24.75 17.625-42.375T140-880h129v60H140v129H80ZM269-80H140q-24.75 0-42.375-17.625T80-140v-129h60v129h129v60Zm422 0v-60h129v-129h60v129q0 24.75-17.625 42.375T820-80H691Zm129-611v-129H691v-60h129q24.75 0 42.375 17.625T880-820v129h-60ZM480-514l190-110-190-109-190 109 190 110Zm0 25Zm0-25Zm30 54Zm-60 0Z"/></svg>
				</span>
		</button>
		<button slot="ar-center" id="ar-center">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48"><path d="m143-100-43-43 147-147H120v-60h230v230h-60v-127L143-100Zm674 0L670-247v127h-60v-230h230v60H713l147 147-43 43ZM120-610v-60h127L100-817l43-43 147 147v-127h60v230H120Zm490 0v-230h60v127l148-148 43 43-148 148h127v60H610Z"/></svg>
				</span>
		</button>
		<button slot="ar-rotation" id="ar-rotation" class="active">
				<span class="button-icon">
					<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48"><path d="m357-167-43-43 80-81q-136-15-225-66T80-486q0-79 116.5-134.5T480-676q168 0 284 55.5T880-486q0 59-64 104t-170 70v-65q80-20 127-52t47-57q0-32-83.5-81T480-616q-172 0-256 49t-84 81q0 45 57.5 77.5T397-349l-83-81 43-43 153 152-153 154Z"/></svg>
				</span>
		</button>
		<?php if ( ! empty( $hotspots_html ) ) { ?>
			<button slot="ar-toggle-hotspots" id="ar-toggle-hotspots" class="active">
					<span class="button-icon">
						<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 -960 960 960" width="48"><path d="M480-120 300-300l44-44 136 136 136-136 44 44-180 180ZM344-612l-44-44 180-180 180 180-44 44-136-136-136 136Z"/></svg>
					</span>
			</button>
		<?php } ?>
	</div>
	<model-viewer id="vr-model"
	              src="<?php echo $model_url; ?>"
	              data-model="<?php echo $model_3d; ?>"
	              poster="<?php echo wp_get_attachment_image_url( $model_preview, 'square-crop-medium' ); ?>"
	              camera-controls auto-rotate min-field-of-view="10deg"
	              bounds="tight" environment-image="neutral" shadow-intensity="1"
		<?php echo $model_options; ?>
		          ar ar-modes="<?php
	if ( vsge_3d_model_is_safari( $_SERVER['HTTP_USER_AGENT'] ) ) {
		echo 'quick-look scene-viewer';
	} else {
		echo 'scene-viewer quick-look webxr';
	}
	?>"
	>
		<?php echo $hotspots_html; ?>
		<button slot="ar-button" class="hide"></button>
		<div class="progress-bar-container hide" slot="progress-bar">
			<progress class="progress-bar" value="0" max="100"></progress>
		</div>
	</model-viewer>
</div>
<div id="woo-switch-gallery" class="woocommerce-product-gallery__select">
	<button id="woo-select-3d" data-type="3d-model"><?php esc_html_e( '3D/VR', 'vsge-3d-product-viewer' ); ?></button>
	<button id="woo-select-gallery" class="active" data-type="gallery"><?php esc_html_e( 'Gallery', 'vsge-3d-product-viewer' ); ?></button>
</div>
<div id="vsge-modal-qrcode" class="vsge-modal-notice outer-modal">
	<div class="inner-modal">
		<canvas id="vsge-vr-model"></canvas>
		<h3><?php esc_html_e( 'Instructions:', 'vsge-3d-product-viewer' ); ?></h3>
		<p><?php esc_html_e( 'Scan this code to open the model on your device, then, tap on the AR icon.', 'vsge-3d-product-viewer' ); ?></p>
		<p><b><?php esc_html_e( 'Click to close this message', 'vsge-3d-product-viewer' ); ?></b></p>
	</div>
</div>
