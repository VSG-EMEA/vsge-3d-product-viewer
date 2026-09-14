<?php
namespace Vsge3DProductViewer;

final class Renderer {
	/** @return string */
	public static function render( $product_id = 0, $standalone = false, $wrapper_attributes = '', $enqueue_assets = true ) {
		$product_id = absint( $product_id ?: get_the_ID() );
		$model = ModelData::for_product( $product_id );
		if ( ! $model ) {
			return '';
		}
		if ( $enqueue_assets ) {
			Assets::enqueue();
		}
		$id = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'vsge-3d-' ) : 'vsge-3d-' . $product_id;
		$viewer_url = StandaloneViewerEndpoint::url( $product_id );
		$wrapper_attributes = $wrapper_attributes ?: 'class="wp-block-vsge-gallery-3d-model vsge-3d-product-viewer"';
		ob_start();
		?>
		<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- supplied by get_block_wrapper_attributes(). ?> data-vsge-viewer data-viewer-url="<?php echo esc_url( $viewer_url ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<?php if ( ! $standalone ) : ?>
				<div class="vsge-3d-launcher">
					<button type="button" class="vsge-launch-3d vsge-gallery-control" aria-controls="<?php echo esc_attr( $id ); ?>-stage" aria-expanded="false" aria-pressed="false" aria-label="<?php esc_attr_e( 'View in 3D', 'vsge-3d-product-viewer' ); ?>" data-model-label="<?php esc_attr_e( 'View in 3D', 'vsge-3d-product-viewer' ); ?>" data-gallery-label="<?php esc_attr_e( 'Return to gallery', 'vsge-3d-product-viewer' ); ?>">
						<span data-vsge-switch-model><?php esc_html_e( '3D/VR', 'vsge-3d-product-viewer' ); ?></span>
						<span data-vsge-switch-gallery hidden><?php esc_html_e( 'Gallery', 'vsge-3d-product-viewer' ); ?></span>
					</button>
				</div>
			<?php endif; ?>
			<div class="vsge-3d-stage" id="<?php echo esc_attr( $id ); ?>-stage"<?php echo $standalone ? ' data-eager-stage="true" tabindex="-1"' : ' hidden tabindex="-1"'; ?>>
				<div class="vsge-3d-canvas">
				<model-viewer class="vsge-3d-model" data-src="<?php echo esc_url( $model['model_url'] ); ?>"<?php echo $standalone ? ' src="' . esc_url( $model['model_url'] ) . '" data-eager="true"' : ''; ?><?php echo $model['preview_url'] ? ' poster="' . esc_url( $model['preview_url'] ) . '"' : ''; ?><?php echo $model['camera_orbit'] ? ' camera-orbit="' . esc_attr( $model['camera_orbit'] ) . '"' : ''; ?><?php echo $model['camera_target'] ? ' camera-target="' . esc_attr( $model['camera_target'] ) . '"' : ''; ?> camera-controls min-field-of-view="10deg" tone-mapping="commerce" environment-image="neutral" shadow-intensity="1" ar ar-modes="webxr scene-viewer quick-look" aria-label="<?php echo esc_attr( $model['accessible_label'] ); ?>">
					<button slot="ar-button" type="button" class="vsge-ar-button" hidden aria-label="<?php esc_attr_e( 'View this product in augmented reality', 'vsge-3d-product-viewer' ); ?>"><?php esc_html_e( 'View in AR', 'vsge-3d-product-viewer' ); ?></button>
					<div class="vsge-loading" slot="progress-bar" hidden><span><?php esc_html_e( 'Loading 3D model…', 'vsge-3d-product-viewer' ); ?></span><progress value="0" max="100"></progress></div>
					<?php self::hotspots( $model['hotspots'] ); ?>
				</model-viewer>
				<p class="vsge-model-error" role="status" hidden></p>
				</div>
			</div>
			<div class="vsge-viewer-controls" aria-label="<?php esc_attr_e( '3D viewer controls', 'vsge-3d-product-viewer' ); ?>">
				<button type="button" class="vsge-gallery-control" data-vsge-action="ar" aria-label="<?php esc_attr_e( 'View in augmented reality', 'vsge-3d-product-viewer' ); ?>" title="<?php esc_attr_e( 'View in augmented reality', 'vsge-3d-product-viewer' ); ?>"><?php echo self::control_icon( 'model' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static legacy SVG markup. ?></button>
				<button type="button" class="vsge-gallery-control" data-vsge-action="recenter" aria-label="<?php esc_attr_e( 'Recenter 3D model', 'vsge-3d-product-viewer' ); ?>" title="<?php esc_attr_e( 'Recenter 3D model', 'vsge-3d-product-viewer' ); ?>"><?php echo self::control_icon( 'center' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static legacy SVG markup. ?></button>
				<button type="button" class="vsge-gallery-control" data-vsge-action="rotate" aria-pressed="false" aria-label="<?php esc_attr_e( 'Auto-rotate 3D model', 'vsge-3d-product-viewer' ); ?>" title="<?php esc_attr_e( 'Auto-rotate 3D model', 'vsge-3d-product-viewer' ); ?>"><?php echo self::control_icon( 'rotation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static legacy SVG markup. ?></button>
				<button type="button" class="vsge-gallery-control" data-vsge-action="info" aria-label="<?php esc_attr_e( '3D viewer instructions', 'vsge-3d-product-viewer' ); ?>" title="<?php esc_attr_e( '3D viewer instructions', 'vsge-3d-product-viewer' ); ?>"><?php echo self::control_icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static legacy SVG markup. ?></button>
				<?php if ( $model['hotspots'] ) : ?><button type="button" class="vsge-gallery-control" data-vsge-action="hotspots" aria-pressed="true" aria-label="<?php esc_attr_e( 'Toggle hotspots', 'vsge-3d-product-viewer' ); ?>" title="<?php esc_attr_e( 'Toggle hotspots', 'vsge-3d-product-viewer' ); ?>"><?php echo self::control_icon( 'hotspots' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static legacy SVG markup. ?></button><?php endif; ?>
			</div>
			<dialog class="vsge-viewer-dialog" data-vsge-dialog>
				<button type="button" class="vsge-viewer-dialog-close" data-vsge-action="close-dialog" aria-label="<?php esc_attr_e( 'Close dialog', 'vsge-3d-product-viewer' ); ?>"><span class="vsge-viewer-control-icon vsge-viewer-control-icon--close" aria-hidden="true"></span></button>
				<div data-vsge-dialog-content></div>
			</dialog>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/** @param array<int,array<string,string>> $hotspots */
	private static function hotspots( $hotspots ) {
		foreach ( $hotspots as $hotspot ) {
			$label = isset( $hotspot['href'] ) ? sprintf( '<a href="%s">%s</a>', esc_url( $hotspot['href'] ), esc_html( $hotspot['title'] ) ) : esc_html( $hotspot['title'] );
			printf( '<div class="vsge-hotspot" slot="hotspot-%1$s" data-position="%2$s"%3$s role="note" aria-label="%4$s"><span class="vsge-hotspot-label">%5$s</span></div>', esc_attr( $hotspot['slot'] ), esc_attr( $hotspot['position'] ), isset( $hotspot['normal'] ) ? ' data-normal="' . esc_attr( $hotspot['normal'] ) . '"' : '', esc_attr( $hotspot['title'] ), $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $label is escaped above.
		}
	}

	/** @return string */
	private static function control_icon( $icon ) {
		$icons = array(
			// These are the filled symbols used by the pre-refactor product gallery.
			'model'    => '<svg class="vsge-viewer-control-icon vsge-viewer-control-icon--model" viewBox="0 -960 960 960" aria-hidden="true"><path d="M448-167 228-296q-13.775-8.426-21.387-22.213Q199-332 199-348v-257q0-16 7.613-29.787Q214.225-648.574 228-657l221-131q14-8 31-8t31 8l221 131q13.775 8.426 21.388 22.213Q761-621 761-605v257q0 16-7.875 29.787Q745.25-304.426 731-296L508-167q-14.328 8-30.164 8Q462-159 448-167Zm2-69v-224L260-569v219l190 114Zm60 0 191-114v-219L510-460v224ZM80-691v-129q0-24.75 17.625-42.375T140-880h129v60H140v129H80ZM269-80H140q-24.75 0-42.375-17.625T80-140v-129h60v129h129v60Zm422 0v-60h129v-129h60v129q0 24.75-17.625 42.375T820-80H691Zm129-611v-129H691v-60h129q24.75 0 42.375 17.625T880-820v129h-60ZM480-514l190-110-190-109-190 109 190 110Zm0 25Zm0-25Zm30 54Zm-60 0Z"/></svg>',
			'center'   => '<svg class="vsge-viewer-control-icon vsge-viewer-control-icon--center" viewBox="0 -960 960 960" aria-hidden="true"><path d="m143-100-43-43 147-147H120v-60h230v230h-60v-127L143-100Zm674 0L670-247v127h-60v-230h230v60H713l147 147-43 43ZM120-610v-60h127L100-817l43-43 147 147v-127h60v230H120Zm490 0v-230h60v127l148-148 43 43-148 148h127v60H610Z"/></svg>',
			'rotation' => '<svg class="vsge-viewer-control-icon vsge-viewer-control-icon--rotation" viewBox="0 -960 960 960" aria-hidden="true"><path d="m357-167-43-43 80-81q-136-15-225-66T80-486q0-79 116.5-134.5T480-676q168 0 284 55.5T880-486q0 59-64 104t-170 70v-65q80-20 127-52t47-57q0-32-83.5-81T480-616q-172 0-256 49t-84 81q0 45 57.5 77.5T397-349l-83-81 43-43 153 152-153 154Z"/></svg>',
			'hotspots' => '<svg class="vsge-viewer-control-icon vsge-viewer-control-icon--hotspots" viewBox="0 -960 960 960" aria-hidden="true"><path d="M480-120 300-300l44-44 136 136 136-136 44 44-180 180ZM344-612l-44-44 180-180 180 180-44 44-136-136-136 136Z"/></svg>',
			'info'     => '<svg class="vsge-viewer-control-icon vsge-viewer-control-icon--info" viewBox="0 0 27 27" aria-hidden="true"><path d="m13.5 27C6.06 27 0 20.94 0 13.5S6.06 0 13.5 0 27 6.06 27 13.5 20.94 27 13.5 27Zm0-26C6.61 1 1 6.61 1 13.5S6.61 26 13.5 26 26 20.39 26 13.5 20.39 1 13.5 1Z"/><path d="M13.51 9.15c-.83 0-1.51-.67-1.51-1.5s.66-1.5 1.49-1.5h.01c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5Z"/><path d="M16.1 20.35h-5.2c-.55 0-1-.45-1-1s.45-1 1-1h1.6v-4.5h-1.6c-.55 0-1-.45-1-1s.45-1 1-1h2.6c.55 0 1 .45 1 1v5.5h1.6c.55 0 1 .45 1 1s-.45 1-1 1Z"/></svg>',
		);

		return $icons[ $icon ] ?? '';
	}
}
