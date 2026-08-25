<?php
namespace Vsge3DProductViewer;

final class Assets {
	const HANDLE = 'vsge-3d-product-viewer';

	public static function enqueue() {
		$asset_file = VSGE_MV_PLUGIN_DIR . '/build/view.asset.php';
		$asset = file_exists( $asset_file ) ? include $asset_file : array( 'dependencies' => array(), 'version' => VSGE_MV_VERSION );
		wp_enqueue_script( self::HANDLE, VSGE_MV_PLUGIN_URL . 'build/view.js', $asset['dependencies'] ?? array(), $asset['version'] ?? VSGE_MV_VERSION, true );
		wp_enqueue_style( self::HANDLE, VSGE_MV_PLUGIN_URL . 'build/style-view.css', array(), $asset['version'] ?? VSGE_MV_VERSION );
	}
}
