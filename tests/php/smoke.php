<?php
// Lightweight characterization of the read-only Importer contract; no WordPress database is touched.
$posts = array( 42 => 'product', 99 => 'attachment', 100 => 'attachment' );
$meta = array( 42 => array( 'brb_media_3d_model' => 99 ), 99 => array( 'brb_media_3d_model_preview' => 100, 'brb_media_3d_model_data' => '{"camera-orbit":"65deg 90deg 25m","hotspots":[{"slot":"one","position":"0 1m 0","title":"Safe"}]}' ) );
function absint( $value ) { return abs( (int) $value ); }
function get_post_type( $id ) { global $posts; return $posts[ $id ] ?? null; }
function get_post_meta( $id, $key ) { global $meta; return $meta[ $id ][ $key ] ?? ''; }
function wp_get_attachment_url( $id ) { return 99 === $id ? 'https://example.test/model.glb' : false; }
function wp_get_attachment_image_url( $id ) { return 100 === $id ? 'https://example.test/preview.jpg' : false; }
function get_the_title() { return 'Product'; }
function __( $string ) { return $string; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function wp_http_validate_url( $value ) { return filter_var( $value, FILTER_VALIDATE_URL ) ? $value : false; }
function esc_url_raw( $value ) { return $value; }
function wp_enqueue_script() { $GLOBALS['enqueued_scripts'] = ( $GLOBALS['enqueued_scripts'] ?? 0 ) + 1; }
function wp_enqueue_style() { $GLOBALS['enqueued_styles'] = ( $GLOBALS['enqueued_styles'] ?? 0 ) + 1; }
require dirname( __DIR__, 2 ) . '/inc/ModelData.php';
require dirname( __DIR__, 2 ) . '/inc/Renderer.php';
$GLOBALS['post'] = (object) array( 'ID' => 7 );
$result = \Vsge3DProductViewer\ModelData::for_product( 42 );
if ( ! $result || 99 !== $result['model_id'] || 100 !== $result['preview_id'] || 'attachment' !== $result['data_source'] || 7 !== $GLOBALS['post']->ID ) {
	fwrite( STDERR, "Model-data smoke test failed.\n" );
	exit( 1 );
}
$GLOBALS['enqueued_scripts'] = 0;
$GLOBALS['enqueued_styles']  = 0;
$no_model_output = \Vsge3DProductViewer\Renderer::render( 1 );
if ( '' !== $no_model_output || 0 !== $GLOBALS['enqueued_scripts'] || 0 !== $GLOBALS['enqueued_styles'] ) {
	fwrite( STDERR, "No-model rendering must return no markup and enqueue no assets.\n" );
	exit( 1 );
}
echo "Model-data smoke test passed.\n";
