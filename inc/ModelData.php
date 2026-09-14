<?php
/**
 * Read-only access to the Importer's 3D media contract.
 *
 * The product owns brb_media_3d_model. The model attachment owns the preview
 * and JSON configuration. Product-level preview/configuration is read only as
 * a fallback for sites written by older viewer versions.
 *
 * @package Vsge3DProductViewer
 */

namespace Vsge3DProductViewer;

final class ModelData {
	const MODEL_META   = 'vsge_media_3d_model';
	const PREVIEW_META = 'vsge_media_3d_model_preview';
	const DATA_META    = 'vsge_media_3d_model_data';

	/** @return array<string,mixed>|null */
	public static function for_product( $product_id ) {
		$product_id = absint( $product_id );
		if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
			return null;
		}

		$model_id = absint( get_post_meta( $product_id, self::MODEL_META, true ) );
		$model_url = $model_id ? wp_get_attachment_url( $model_id ) : false;
		if ( ! $model_id || ! $model_url || 'attachment' !== get_post_type( $model_id ) ) {
			return null;
		}

		$preview_id = absint( get_post_meta( $model_id, self::PREVIEW_META, true ) );
		$preview_source = 'attachment';
		if ( ! $preview_id ) {
			$preview_id = absint( get_post_meta( $product_id, self::PREVIEW_META, true ) );
			$preview_source = 'product-fallback';
		}

		$data_json = (string) get_post_meta( $model_id, self::DATA_META, true );
		$data_source = 'attachment';
		if ( '' === $data_json ) {
			$data_json = (string) get_post_meta( $product_id, self::DATA_META, true );
			$data_source = 'product-fallback';
		}
		$data = json_decode( $data_json, true );
		$data = is_array( $data ) ? $data : array();

		return array(
			'product_id'       => $product_id,
			'model_id'         => $model_id,
			'model_url'        => $model_url,
			'preview_id'       => $preview_id,
			'preview_url'      => $preview_id ? wp_get_attachment_image_url( $preview_id, 'large' ) : false,
			'preview_source'   => $preview_source,
			'data_source'      => $data_source,
			'camera_orbit'     => self::vector( $data['camera-orbit'] ?? '', true ),
			'camera_target'    => self::vector( $data['camera-target'] ?? '', false ),
			'hotspots'         => self::hotspots( $data['hotspots'] ?? array() ),
			/* translators: %s: product title. */
			'accessible_label' => sprintf( __( 'Interactive 3D model of %s', 'vsge-3d-product-viewer' ), get_the_title( $product_id ) ),
		);
	}

	/** @return string */
	private static function vector( $value, $allow_angle ) {
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			return '';
		}
		$unit = $allow_angle ? '(?:deg|rad|m|cm|mm|%)?' : '(?:m|cm|mm|%)?';
		$part = '(?:auto|[-+]?(?:\d+(?:\.\d+)?|\.\d+)' . $unit . ')';
		$pattern = '/^' . $part . '\s+' . $part . '\s+' . $part . '$/i';
		return preg_match( $pattern, trim( $value ) ) ? trim( $value ) : '';
	}

	/** @return array<int,array<string,string>> */
	private static function hotspots( $hotspots ) {
		if ( ! is_array( $hotspots ) ) {
			return array();
		}
		$valid = array();
		foreach ( $hotspots as $index => $hotspot ) {
			if ( is_object( $hotspot ) ) {
				$hotspot = (array) $hotspot;
			}
			if ( ! is_array( $hotspot ) || empty( $hotspot['position'] ) || empty( $hotspot['title'] ) ) {
				continue;
			}
			$position = self::vector( $hotspot['position'], false );
			$slot = isset( $hotspot['slot'] ) ? (string) $hotspot['slot'] : 'hotspot-' . $index;
			if ( ! $position || ! preg_match( '/^[A-Za-z0-9_-]+$/', $slot ) ) {
				continue;
			}
			$item = array(
				'slot'     => $slot,
				'position' => $position,
				'title'    => sanitize_text_field( (string) $hotspot['title'] ),
			);
			if ( isset( $hotspot['normal'] ) && self::vector( $hotspot['normal'], false ) ) {
				$item['normal'] = self::vector( $hotspot['normal'], false );
			}
			if ( ! empty( $hotspot['href'] ) && wp_http_validate_url( (string) $hotspot['href'] ) ) {
				$item['href'] = esc_url_raw( (string) $hotspot['href'] );
			}
			$valid[] = $item;
		}
		return $valid;
	}
}
