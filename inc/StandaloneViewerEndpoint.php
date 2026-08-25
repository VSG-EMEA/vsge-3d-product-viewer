<?php
namespace Vsge3DProductViewer;

final class StandaloneViewerEndpoint {
	const QUERY_VAR = 'vsge_model3d';

	public static function register() {
		add_rewrite_rule( '^model3d=([^/]+)/?$', 'index.php?' . self::QUERY_VAR . '=$matches[1]', 'top' );
		add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
		add_filter( 'template_include', array( __CLASS__, 'template' ) );
	}

	/** @return array */
	public static function query_vars( $vars ) {
		$vars[] = self::QUERY_VAR;
		return $vars;
	}

	/** @return string */
	public static function url( $product_id ) {
		$product_id = absint( $product_id );
		$slug = $product_id ? get_post_field( 'post_name', $product_id ) : '';
		return home_url( '/model3d=' . rawurlencode( $slug ?: (string) $product_id ) . '/' );
	}

	/** @return string */
	public static function template( $template ) {
		$value = get_query_var( self::QUERY_VAR );
		if ( '' === (string) $value ) {
			return $template;
		}
		$product_id = ctype_digit( (string) $value ) ? absint( $value ) : 0;
		if ( ! $product_id ) {
			$product = get_page_by_path( sanitize_title_for_query( (string) $value ), OBJECT, 'product' );
			$product_id = $product ? $product->ID : 0;
		}
		if ( ! ModelData::for_product( $product_id ) ) {
			status_header( 404 );
			nocache_headers();
			return VSGE_MV_PLUGIN_DIR . '/template/missing-3d-model.php';
		}
		$GLOBALS['vsge_3d_product_id'] = $product_id;
		return VSGE_MV_PLUGIN_DIR . '/template/standalone.php';
	}

	public static function activate() {
		// Activation runs after WordPress is loaded, but before the next ordinary
		// request reaches init. Register once before flushing so the persistent
		// rewrite rules include the standalone viewer route.
		self::register();
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}
}
