<?php
/**
 * Rewrite lifecycle characterization for the plugin bootstrap.
 *
 * Run from the plugin root with: php tests/php/lifecycle-smoke.php
 * This does not load WordPress or a database.
 */

declare( strict_types = 1 );

$hooks         = array();
$rewrite_calls = 0;
$flush_calls   = 0;

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {
	global $hooks;
	$hooks[] = array( $hook, $callback, $priority, $accepted_args );
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {}
function add_shortcode( $tag, $callback ): void {}
function add_rewrite_rule( $regex, $query, $position = 'bottom' ): void {
	global $rewrite_calls;
	++$rewrite_calls;
}
function flush_rewrite_rules(): void {
	global $flush_calls;
	++$flush_calls;
}

require_once dirname( __DIR__, 2 ) . '/inc/StandaloneViewerEndpoint.php';
require_once dirname( __DIR__, 2 ) . '/inc/Plugin.php';

\Vsge3DProductViewer\Plugin::register();

if ( 0 !== $rewrite_calls ) {
	fwrite( STDERR, "Plugin bootstrap registered a rewrite rule before init.\n" );
	exit( 1 );
}

$endpoint_hooked = false;
foreach ( $hooks as $hook ) {
	if ( 'init' === $hook[0] && array( 'Vsge3DProductViewer\\StandaloneViewerEndpoint', 'register' ) === $hook[1] ) {
		$endpoint_hooked = true;
	}
}

if ( ! $endpoint_hooked ) {
	fwrite( STDERR, "Standalone viewer endpoint is not registered on init.\n" );
	exit( 1 );
}

\Vsge3DProductViewer\StandaloneViewerEndpoint::activate();
if ( 1 !== $rewrite_calls || 1 !== $flush_calls ) {
	fwrite( STDERR, "Activation must register the route once and flush once.\n" );
	exit( 1 );
}

echo "3D viewer rewrite lifecycle smoke test passed.\n";
