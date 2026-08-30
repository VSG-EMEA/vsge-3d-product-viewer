import { useBlockProps } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { createElement } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import './style/editor.scss';

const Edit = ( { context } ) => {
	const props = useBlockProps( { className: 'vsge-3d-editor-preview' } );
	const text = context.postId
		? __(
				'The frontend shows a 3D overlay when this product has an assigned model. Place it beside WooCommerce Product Gallery inside a vsge-product-media group.',
				'vsge-3d-product-viewer'
		  )
		: __(
				'Open this block in a product context. For overlay mode, place it beside WooCommerce Product Gallery inside a vsge-product-media group.',
				'vsge-3d-product-viewer'
		  );
	return createElement(
		'div',
		props,
		createElement(
			'strong',
			null,
			__( '3D Viewer Overlay', 'vsge-3d-product-viewer' )
		),
		createElement( 'p', null, text )
	);
};

registerBlockType( 'vsge/gallery-3d-model', {
	edit: Edit,
	save: () => null,
} );
