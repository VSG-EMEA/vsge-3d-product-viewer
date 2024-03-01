<?php
global $post;

global $post;
$model_3d         = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model', true );
$model_url        = wp_get_attachment_url( $model_3d );
$model_preview_id = get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_preview', true );

$model_data = (array) json_decode( get_post_meta( $post->ID, VSGE_MV_PLUGIN_NAMESPACE . '_media_3d_model_data', true ) );

$model_options = implode( ' ', array(
	isset( $model_data['camera-orbit'] ) && $model_data['camera-orbit'] !== '' ? '' : null,
	isset( $model_data['camera-target'] ) && $model_data['camera-target'] !== '' ? '' : null,
) );
include VSGE_MV_PLUGIN_DIR . '/template/header.php';

?>
<div id="woocommerce-product-gallery__3d" class="attachment-page-3d-product alignfull">
	<model-viewer id="vr-model"
				  style="min-height: 100vh; width: 100%"
				  src="<?php echo $model_url; ?>"
				  data-model="<?php echo $model_3d; ?>"
				  bounds="tight" camera-controls <?php echo $model_options; ?> min-field-of-view="10deg"
				  ar-modes="<?php
				  if ( vsge_3d_model_is_safari( $_SERVER['HTTP_USER_AGENT'] ) ) {
					  echo 'quick-look scene-viewer';
				  } else {
					  echo 'scene-viewer quick-look webxr';
				  }
				  ?>"
				  ar camera-controls>
		<button id="ar-failure">
			<div class="material-icons large">
				error
			</div>
			<?php esc_html_e( 'AR is not tracking!', 'vsge-3d-product-viewer' ); ?>
			<p id="error"></p>
		</button>
		<div class="vsge-modal-notice progress-bar-container hide" slot="progress-bar">
			<div class="inner-modal">
				<h3><?php esc_html_e( 'Ladies and Gentlemen, please start your (VR) Engines', 'vsge-3d-product-viewer' ); ?></h3>
				<p><?php printf( esc_html__( '3D model is loading, please wait. After the download augmented reality mode will be enabled, keep your phone facing the part of your workshop where you want to place your new %s product', 'vsge-3d-product-viewer' ), COMPANY ); ?></p>
				<progress class="progress-bar" value="0" max="100"></progress>
				<button id="ar-initialize" class="ar-button hide">Start AR</button>
			</div>
		</div>
	</model-viewer>
</div>
<?php
include VSGE_MV_PLUGIN_DIR . '/template/footer.php';
