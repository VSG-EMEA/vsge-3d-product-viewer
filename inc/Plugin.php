<?php
namespace Vsge3DProductViewer;

final class Plugin {
	public static function register() {
		// Rewrite APIs are initialized by WordPress on `init`. Keep bootstrap side
		// effects to hook registration so plugin loading is safe in every context.
		add_action( 'init', array( StandaloneViewerEndpoint::class, 'register' ) );
		add_action( 'init', array( Block::class, 'register' ) );
		add_shortcode( 'vsge_3d_model', array( __CLASS__, 'shortcode' ) );
		add_filter( 'upload_mimes', array( __CLASS__, 'mime_types' ) );
		add_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'glb_filetype' ), 10, 5 );
	}

	/** @return string */
	public static function shortcode( $attributes = array() ) {
		$attributes = shortcode_atts( array( 'product_id' => get_the_ID() ), $attributes, 'vsge_3d_model' );
		return Renderer::render( absint( $attributes['product_id'] ) );
	}

	public static function mime_types( $types ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			return $types;
		}
		$types['glb'] = 'model/gltf-binary';
		return $types;
	}

	public static function glb_filetype( $data, $file, $filename, $mimes, $real_mime ) {
		if ( 'glb' !== strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
			return $data;
		}
		$data['ext'] = 'glb';
		$data['type'] = 'model/gltf-binary';
		return $data;
	}
}
