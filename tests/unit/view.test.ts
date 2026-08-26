import { setOverlayState, showViewerError } from '../../src/viewer-ui';

describe( 'viewer controls', () => {
	beforeEach( () => {
		document.body.innerHTML =
			'<div class="vsge-product-media"><div class="wp-block-woocommerce-product-gallery wc-block-product-gallery" aria-hidden="false"><button type="button">Woo image</button></div><section class="wp-block-vsge-3d-model vsge-3d-product-viewer" data-vsge-viewer><div class="vsge-3d-launcher"><button class="vsge-launch-3d"></button></div><div class="vsge-3d-stage" hidden></div><p class="vsge-model-error" hidden></p></section></div>';
	} );

	it( 'overlays the gallery without changing its DOM and restores its state', () => {
		const root = document.querySelector(
			'[data-vsge-viewer]'
		) as HTMLElement;
		const gallery = document.querySelector(
			'.wp-block-woocommerce-product-gallery'
		) as HTMLElement;
		const galleryParent = gallery.parentElement;
		setOverlayState( root, true );
		expect( root.classList.contains( 'vsge-3d-active' ) ).toBe( true );
		expect(
			root.querySelector< HTMLElement >( '.vsge-3d-stage' )?.hidden
		).toBe( false );
		expect(
			root.querySelector< HTMLElement >( '.vsge-3d-launcher' )?.hidden
		).toBe( true );
		expect( gallery.parentElement ).toBe( galleryParent );
		expect( gallery.getAttribute( 'aria-hidden' ) ).toBe( 'true' );
		expect( gallery.inert ).toBe( true );

		setOverlayState( root, false );
		expect( gallery.parentElement ).toBe( galleryParent );
		expect( gallery.getAttribute( 'aria-hidden' ) ).toBe( 'false' );
		expect( gallery.inert ).toBeUndefined();
	} );

	it( 'falls back to an in-flow stage without the media wrapper', () => {
		const root = document.querySelector(
			'[data-vsge-viewer]'
		) as HTMLElement;
		root.parentElement?.replaceWith( root );
		setOverlayState( root, true );
		expect(
			root.querySelector< HTMLElement >( '.vsge-3d-stage' )?.hidden
		).toBe( false );
	} );

	it( 'uses text content for safe runtime errors', () => {
		const root = document.querySelector(
			'[data-vsge-viewer]'
		) as HTMLElement;
		showViewerError( root, '<strong>unsafe</strong>' );
		const error = root.querySelector( '.vsge-model-error' ) as HTMLElement;
		expect( error.innerHTML ).toBe( '&lt;strong&gt;unsafe&lt;/strong&gt;' );
	} );
} );
