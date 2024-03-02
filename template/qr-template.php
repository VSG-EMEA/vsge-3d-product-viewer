<?php
/**
 * The template for displaying the QR code.
 *
 * @package  vsge-3d-product-viewer
 */

$translations = array(
	'instructions' => __('Instructions', 'vsge-3d-product-viewer' ),
	'text' => __('Scan this code to open the model on your device, then, tap on the AR icon.', 'vsge-3d-product-viewer' ),
)
?>

<div class="modal vsge-modal-qr" title="ar-init">
	<canvas id="vsge-vr-model"></canvas>
	<h3><?php echo esc_html( $translations['instructions'] ); ?></h3>
	<p><?php echo esc_html( $translations['text'] ); ?></p>
	<p class="mv-close-message">
		<?php esc_html_e( 'Click to close this message', 'vsge-3d-product-viewer' ); ?>
	</p>
</div>