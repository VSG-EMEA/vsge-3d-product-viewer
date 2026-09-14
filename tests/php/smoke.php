<?php
// Lightweight characterization of the read-only Importer contract; no WordPress database is touched.
$posts = array( 42 => 'product', 99 => 'attachment', 100 => 'attachment' );
$meta = array( 42 => array( 'vsge_media_3d_model' => 99 ), 99 => array( 'vsge_media_3d_model_preview' => 100, 'vsge_media_3d_model_data' => '{"camera-orbit":"65deg 90deg 25m","hotspots":[{"slot":"one","position":"0 1m 0","title":"Safe"}]}' ) );
function absint( $value ) { return abs( (int) $value ); }
function get_post_type( $id ) { global $posts; return $posts[ $id ] ?? null; }
function get_post_meta( $id, $key ) { global $meta; return $meta[ $id ][ $key ] ?? ''; }
function wp_get_attachment_url( $id ) { return 99 === $id ? 'https://example.test/model.glb' : false; }
function wp_get_attachment_image_url( $id ) { return 100 === $id ? 'https://example.test/preview.jpg' : false; }
function get_the_title() { return 'Product'; }
function get_the_ID() { return 42; }
function get_post_field( $field, $id ) { return 'post_name' === $field && 42 === $id ? 'test-product' : ''; }
function home_url( $path = '' ) { return 'https://example.test' . $path; }
function __( $string ) { return $string; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function wp_http_validate_url( $value ) { return filter_var( $value, FILTER_VALIDATE_URL ) ? $value : false; }
function esc_url_raw( $value ) { return $value; }
function esc_url( $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html_e( $value ) { echo esc_html( $value ); }
function esc_attr_e( $value ) { echo esc_attr( $value ); }
function wp_unique_id( $prefix = '' ) { static $id = 0; ++$id; return $prefix . $id; }
function wp_enqueue_script() { $GLOBALS['enqueued_scripts'] = ( $GLOBALS['enqueued_scripts'] ?? 0 ) + 1; }
function wp_enqueue_style() { $GLOBALS['enqueued_styles'] = ( $GLOBALS['enqueued_styles'] ?? 0 ) + 1; }
define( 'VSGE_MV_PLUGIN_DIR', dirname( __DIR__, 2 ) );
define( 'VSGE_MV_PLUGIN_URL', 'https://example.test/wp-content/plugins/vsge-3d-product-viewer/' );
define( 'VSGE_MV_VERSION', '0.2.0' );
require dirname( __DIR__, 2 ) . '/inc/ModelData.php';
require dirname( __DIR__, 2 ) . '/inc/Assets.php';
require dirname( __DIR__, 2 ) . '/inc/StandaloneViewerEndpoint.php';
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
$rendered = \Vsge3DProductViewer\Renderer::render( 42 );
foreach ( array( 'wp-block-vsge-gallery-3d-model', 'data-vsge-viewer', 'vsge-3d-launcher', 'vsge-launch-3d vsge-gallery-control', 'aria-pressed="false"', 'data-vsge-switch-model', 'data-vsge-switch-gallery hidden', '>3D/VR<', '>Gallery<', 'vsge-viewer-controls', 'data-vsge-action="recenter"', 'data-vsge-action="rotate"', 'data-vsge-action="hotspots"', 'data-vsge-action="ar"', 'data-vsge-action="info"', 'vsge-viewer-control-icon--model', 'vsge-viewer-control-icon--center', 'vsge-viewer-control-icon--rotation', 'vsge-viewer-control-icon--hotspots', 'vsge-viewer-control-icon--info', 'vsge-viewer-dialog-close' ) as $contract ) {
	if ( false === strpos( $rendered, $contract ) ) {
		fwrite( STDERR, "3D overlay control contract is missing: {$contract}.\n" );
		exit( 1 );
	}
}
if ( false === strpos( $rendered, 'poster="https://example.test/preview.jpg"' ) || false === strpos( $rendered, 'class="vsge-loading" slot="progress-bar" hidden' ) ) {
	fwrite( STDERR, "The existing preview poster and initially-hidden loading card must remain in the model-viewer markup.\n" );
	exit( 1 );
}
if ( 1 !== $GLOBALS['enqueued_scripts'] || 1 !== $GLOBALS['enqueued_styles'] ) {
	fwrite( STDERR, "Direct renderer compatibility must enqueue exactly one asset pair.\n" );
	exit( 1 );
}
\Vsge3DProductViewer\Renderer::render( 42, false, '', false );
if ( 1 !== $GLOBALS['enqueued_scripts'] || 1 !== $GLOBALS['enqueued_styles'] ) {
	fwrite( STDERR, "Block rendering must leave view assets to block metadata and avoid duplicate scripts.\n" );
	exit( 1 );
}
if ( false !== strpos( $rendered, 'vsge-return-to-images' ) ) {
	fwrite( STDERR, "Gallery mode must use the persistent 3D toggle instead of a separate return control.\n" );
	exit( 1 );
}
if ( false === strpos( $rendered, 'aria-expanded="false"' ) || false === strpos( $rendered, ' hidden tabindex="-1"' ) || ! preg_match( '/<div class="vsge-3d-stage"[^>]*>.*<\/div>\s*<div class="vsge-viewer-controls"/s', $rendered ) ) {
	fwrite( STDERR, "Gallery mode must be the default and the 3D controls must be a sibling layer of the measured model viewport.\n" );
	exit( 1 );
}
$standalone_rendered = \Vsge3DProductViewer\Renderer::render( 42, true );
if ( false === strpos( $standalone_rendered, 'data-eager-stage="true"' ) || false === strpos( $standalone_rendered, 'data-eager="true"' ) || false !== strpos( $standalone_rendered, 'vsge-3d-launcher' ) || false !== strpos( $standalone_rendered, 'vsge-return-to-images' ) ) {
	fwrite( STDERR, "Standalone viewer rendering must remain eager and omit gallery-only controls.\n" );
	exit( 1 );
}
$wp_root = dirname( VSGE_MV_PLUGIN_DIR, 3 );
require_once $wp_root . '/wp-includes/class-wp-block-parser.php';
$parsed_block = ( new \WP_Block_Parser() )->parse( '<!-- wp:vsge/gallery-3d-model /-->' );
if ( 'vsge/gallery-3d-model' !== $parsed_block[0]['blockName'] ) {
	fwrite( STDERR, "The gallery 3D block must use a WordPress-parseable block name.\n" );
	exit( 1 );
}
$block_metadata = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/block.json' ), true );
if ( ! isset( $block_metadata['viewStyle'], $block_metadata['editorStyle'] ) || isset( $block_metadata['style'] ) ) {
	fwrite( STDERR, "Viewer overlay CSS must be frontend-only while the editor receives its preview style.\n" );
	exit( 1 );
}
echo "Model-data smoke test passed.\n";
