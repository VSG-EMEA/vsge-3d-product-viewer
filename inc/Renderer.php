<?php
namespace Vsge3DProductViewer;

final class Renderer {
	/** @return string */
	public static function render( $product_id = 0, $standalone = false, $wrapper_attributes = '' ) {
		$product_id = absint( $product_id ?: get_the_ID() );
		$model = ModelData::for_product( $product_id );
		if ( ! $model ) {
			return '';
		}
		Assets::enqueue();
		$id = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'vsge-3d-' ) : 'vsge-3d-' . $product_id;
		$viewer_url = StandaloneViewerEndpoint::url( $product_id );
		$wrapper_attributes = $wrapper_attributes ?: 'class="wp-block-vsge-3d-model vsge-3d-product-viewer"';
		ob_start();
		?>
		<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- supplied by get_block_wrapper_attributes(). ?> data-vsge-viewer data-viewer-url="<?php echo esc_url( $viewer_url ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<?php if ( ! $standalone ) : ?>
				<div class="vsge-3d-launcher">
					<button type="button" class="vsge-launch-3d" aria-controls="<?php echo esc_attr( $id ); ?>-stage" aria-expanded="false"><?php esc_html_e( 'View in 3D', 'vsge-3d-product-viewer' ); ?></button>
				</div>
			<?php endif; ?>
			<div class="vsge-3d-stage" id="<?php echo esc_attr( $id ); ?>-stage"<?php echo $standalone ? ' data-eager-stage="true" tabindex="-1"' : ' hidden tabindex="-1"'; ?>>
				<?php if ( ! $standalone ) : ?>
					<div class="vsge-3d-stage-header">
						<button type="button" class="vsge-return-to-images"><?php esc_html_e( 'Back to product images', 'vsge-3d-product-viewer' ); ?></button>
					</div>
				<?php endif; ?>
				<div class="vsge-3d-canvas">
				<model-viewer class="vsge-3d-model" data-src="<?php echo esc_url( $model['model_url'] ); ?>"<?php echo $standalone ? ' src="' . esc_url( $model['model_url'] ) . '" data-eager="true"' : ''; ?><?php echo $model['preview_url'] ? ' poster="' . esc_url( $model['preview_url'] ) . '"' : ''; ?><?php echo $model['camera_orbit'] ? ' camera-orbit="' . esc_attr( $model['camera_orbit'] ) . '"' : ''; ?><?php echo $model['camera_target'] ? ' camera-target="' . esc_attr( $model['camera_target'] ) . '"' : ''; ?> camera-controls min-field-of-view="10deg" tone-mapping="commerce" environment-image="neutral" shadow-intensity="1" ar ar-modes="webxr scene-viewer quick-look" aria-label="<?php echo esc_attr( $model['accessible_label'] ); ?>">
					<button slot="ar-button" type="button" class="vsge-ar-button" aria-label="<?php esc_attr_e( 'View this product in augmented reality', 'vsge-3d-product-viewer' ); ?>"><?php esc_html_e( 'View in AR', 'vsge-3d-product-viewer' ); ?></button>
					<div class="vsge-loading" slot="progress-bar"><span><?php esc_html_e( 'Loading 3D model…', 'vsge-3d-product-viewer' ); ?></span><progress value="0" max="100"></progress></div>
					<?php self::hotspots( $model['hotspots'] ); ?>
				</model-viewer>
				<p class="vsge-model-error" role="status" hidden></p>
				</div>
				<div class="vsge-viewer-controls">
				<button type="button" data-vsge-action="recenter" aria-label="<?php esc_attr_e( 'Recenter 3D model', 'vsge-3d-product-viewer' ); ?>"><?php esc_html_e( 'Recenter', 'vsge-3d-product-viewer' ); ?></button>
				<button type="button" data-vsge-action="rotate" aria-pressed="false"><?php esc_html_e( 'Auto-rotate', 'vsge-3d-product-viewer' ); ?></button>
				<?php if ( $model['hotspots'] ) : ?><button type="button" data-vsge-action="hotspots" aria-pressed="true"><?php esc_html_e( 'Hotspots', 'vsge-3d-product-viewer' ); ?></button><?php endif; ?>
				<button type="button" data-vsge-action="ar"><?php esc_html_e( 'View in AR', 'vsge-3d-product-viewer' ); ?></button>
				<button type="button" data-vsge-action="info"><?php esc_html_e( 'Instructions', 'vsge-3d-product-viewer' ); ?></button>
				</div>
			</div>
			<dialog class="vsge-viewer-dialog" data-vsge-dialog>
				<button type="button" data-vsge-action="close-dialog" aria-label="<?php esc_attr_e( 'Close dialog', 'vsge-3d-product-viewer' ); ?>">×</button>
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
}
