<?php
namespace Vsge3DProductViewer;

final class Block {
	public static function register() {
		register_block_type( VSGE_MV_PLUGIN_DIR, array( 'render_callback' => array( __CLASS__, 'render' ) ) );
	}

	/** @return string */
	public static function render( $attributes = array(), $content = '', $block = null ) {
		$product_id = isset( $attributes['productId'] ) ? absint( $attributes['productId'] ) : 0;
		if ( ! $product_id && $block instanceof \WP_Block && isset( $block->context['postId'] ) ) {
			$product_id = absint( $block->context['postId'] );
		}
		$product_id = $product_id ?: get_the_ID();
		$wrapper_attributes = function_exists( 'get_block_wrapper_attributes' ) ? get_block_wrapper_attributes( array( 'class' => 'vsge-3d-product-viewer' ) ) : 'class="wp-block-vsge-3d-model vsge-3d-product-viewer"';
		return Renderer::render( $product_id, false, $wrapper_attributes );
	}
}
