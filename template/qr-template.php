<?php
/**
 * The template for displaying the QR code.
 *
 * @package  vsge-3d-product-viewer
 */

global $post;

$translations = array(
	'instructions' => __('Instructions', 'vsge-3d-product-viewer' ),
	'text' => __('Scan this code to open the model on your device, then, tap on the AR icon.', 'vsge-3d-product-viewer' ),
)
?>

<div class="modal vsge-modal-qr" title="ar-init">
	<a href="<?php bloginfo( 'url' ) ?>/model3d=<?php echo esc_attr( $post->post_name ); ?>" class="link mv-link-qr">
		<canvas id="vsge-vr-model"></canvas>
	</a>
	<h3><?php echo esc_html( $translations['instructions'] ); ?></h3>
	<p class="mv-instructions"><?php echo esc_html( $translations['text'] ); ?></p>
</div>