import {
	initialiseGalleryOverlay,
	setOverlayState,
	showViewerError,
	syncOverlayGeometry,
} from '../../src/viewer-ui';

const rect = ( x: number, y: number, width: number, height: number ) => ( {
	x,
	y,
	width,
	height,
	top: y,
	right: x + width,
	bottom: y + height,
	left: x,
	toJSON: () => ( {} ),
} );

const setRect = (
	element: Element | null,
	x: number,
	y: number,
	width: number,
	height: number
) =>
	jest
		.spyOn( element as HTMLElement, 'getBoundingClientRect' )
		.mockReturnValue( rect( x, y, width, height ) );

describe( 'viewer controls', () => {
	beforeEach( () => {
		document.body.innerHTML =
			'<div class="vsge-product-hero__media"><div class="vsge-product-media"><div class="wp-block-woocommerce-product-gallery wc-block-product-gallery"><div class="wc-block-product-gallery-thumbnails"></div><div class="wc-block-product-gallery-large-image"><ul class="wc-block-product-gallery-large-image__container"></ul><div class="wc-block-next-previous-buttons"><button class="wc-block-next-previous-buttons__button">Previous</button><button class="wc-block-next-previous-buttons__button">Next</button></div></div></div></div><section class="wp-block-vsge-gallery-3d-model vsge-3d-product-viewer" data-vsge-viewer><div class="vsge-3d-launcher"><button class="vsge-launch-3d vsge-gallery-control" aria-pressed="false" data-model-label="View in 3D" data-gallery-label="Return to gallery"><span data-vsge-switch-model>3D/VR</span><span data-vsge-switch-gallery hidden>Gallery</span></button></div><div class="vsge-3d-stage" hidden></div><div class="vsge-viewer-controls"><button class="vsge-gallery-control" data-vsge-action="recenter"></button></div><p class="vsge-model-error" hidden></p></section></div>';
	} );

	it( 'uses current Woo geometry without inserting plugin controls into Woo DOM', () => {
		const root = document.querySelector(
			'[data-vsge-viewer]'
		) as HTMLElement;
		const media = document.querySelector(
			'.vsge-product-media'
		) as HTMLElement;
		const gallery = document.querySelector(
			'.wc-block-product-gallery'
		) as HTMLElement;
		const thumbnails = gallery.querySelector(
			'.wc-block-product-gallery-thumbnails'
		) as HTMLElement;
		const imageContainer = gallery.querySelector(
			'.wc-block-product-gallery-large-image__container'
		) as HTMLElement;
		const viewport = gallery.querySelector(
			'.wc-block-product-gallery-large-image'
		) as HTMLElement;
		const navigation = gallery.querySelector(
			'.wc-block-next-previous-buttons'
		) as HTMLElement;
		const navigationButtons = navigation.querySelectorAll(
			'.wc-block-next-previous-buttons__button'
		);
		const launcher = root.querySelector( '.vsge-launch-3d' ) as HTMLElement;

		setRect( media, 10, 20, 600, 500 );
		setRect( gallery, 10, 20, 600, 500 );
		setRect( thumbnails, 10, 30, 80, 480 );
		setRect( viewport, 110, 20, 500, 500 );
		setRect( navigation, 115, 25, 490, 490 );
		setRect( navigationButtons[ 0 ], 441, 465, 40, 40 );
		setRect( navigationButtons[ 1 ], 485, 465, 40, 40 );
		setRect( launcher, 0, 0, 70, 40 );

		expect( root.parentElement ).not.toBe( media );
		expect( initialiseGalleryOverlay( root ) ).toBe( true );
		expect( root.parentElement ).toBe( media );
		expect( gallery.querySelector( '.vsge-launch-3d' ) ).toBeNull();
		expect( navigation.children ).toHaveLength( 2 );
		expect( root.classList.contains( 'vsge-3d-overlay-ready' ) ).toBe(
			true
		);
		expect( root.style.getPropertyValue( '--vsge-3d-viewport-left' ) ).toBe(
			'100px'
		);
		expect(
			root.style.getPropertyValue( '--vsge-3d-viewport-width' )
		).toBe( '500px' );
		expect( root.style.getPropertyValue( '--vsge-3d-rail-left' ) ).toBe(
			'0px'
		);
		expect( root.style.getPropertyValue( '--vsge-3d-rail-width' ) ).toBe(
			'80px'
		);
		expect( root.style.getPropertyValue( '--vsge-3d-switch-left' ) ).toBe(
			'525px'
		);
		expect( root.style.getPropertyValue( '--vsge-3d-switch-top' ) ).toBe(
			'445px'
		);

		setOverlayState( root, true );
		expect( root.classList.contains( 'vsge-3d-active' ) ).toBe( true );
		expect(
			media.classList.contains( 'vsge-product-media--3d-active' )
		).toBe( true );
		expect(
			root.querySelector< HTMLElement >( '.vsge-3d-stage' )?.hidden
		).toBe( false );
		expect( launcher.getAttribute( 'aria-pressed' ) ).toBe( 'true' );
		expect( launcher.getAttribute( 'aria-label' ) ).toBe(
			'Return to gallery'
		);
		expect(
			launcher.querySelector< HTMLElement >( '[data-vsge-switch-model]' )
				?.hidden
		).toBe( true );
		expect(
			launcher.querySelector< HTMLElement >(
				'[data-vsge-switch-gallery]'
			)?.hidden
		).toBe( false );
		expect( thumbnails.inert ).toBe( true );
		expect( imageContainer.inert ).toBe( true );
		expect( gallery.inert ).toBeUndefined();
		expect( navigation.getAttribute( 'aria-hidden' ) ).toBeNull();

		setOverlayState( root, false );
		setOverlayState( root, true );
		setOverlayState( root, false );
		expect( root.classList.contains( 'vsge-3d-active' ) ).toBe( false );
		expect( thumbnails.getAttribute( 'aria-hidden' ) ).toBeNull();
		expect( imageContainer.getAttribute( 'aria-hidden' ) ).toBeNull();
		expect( launcher.getAttribute( 'aria-label' ) ).toBe( 'View in 3D' );
		expect(
			launcher.querySelector< HTMLElement >( '[data-vsge-switch-model]' )
				?.hidden
		).toBe( false );
		expect( navigation.children ).toHaveLength( 2 );

		const replacement = navigation.cloneNode( true ) as HTMLElement;
		navigation.replaceWith( replacement );
		setRect( replacement, 115, 25, 490, 490 );
		const replacementButtons = replacement.querySelectorAll(
			'.wc-block-next-previous-buttons__button'
		);
		setRect( replacementButtons[ 0 ], 421, 465, 40, 40 );
		setRect( replacementButtons[ 1 ], 465, 465, 40, 40 );
		expect( syncOverlayGeometry( root ) ).toBe( true );
		expect( replacement.children ).toHaveLength( 2 );
		expect( replacement.querySelector( '.vsge-launch-3d' ) ).toBeNull();
	} );

	it( 'falls back to an in-flow stage without a product-media anchor', () => {
		const root = document.querySelector(
			'[data-vsge-viewer]'
		) as HTMLElement;
		root.parentElement?.replaceWith( root );
		expect( initialiseGalleryOverlay( root ) ).toBe( false );
		setOverlayState( root, true );
		expect(
			root.querySelector< HTMLElement >( '.vsge-3d-stage' )?.hidden
		).toBe( false );
	} );

	it( 'waits for Woo to hydrate the gallery controls before exposing the switch', () => {
		const root = document.querySelector(
			'[data-vsge-viewer]'
		) as HTMLElement;
		const media = document.querySelector(
			'.vsge-product-media'
		) as HTMLElement;
		const gallery = document.querySelector(
			'.wc-block-product-gallery'
		) as HTMLElement;
		gallery.remove();

		expect( initialiseGalleryOverlay( root ) ).toBe( true );
		expect( root.classList.contains( 'vsge-3d-overlay-ready' ) ).toBe(
			false
		);

		media.prepend( gallery );
		const thumbnails = gallery.querySelector(
			'.wc-block-product-gallery-thumbnails'
		);
		const viewport = gallery.querySelector(
			'.wc-block-product-gallery-large-image'
		);
		const navigation = gallery.querySelector(
			'.wc-block-next-previous-buttons'
		);
		const buttons = navigation?.querySelectorAll(
			'.wc-block-next-previous-buttons__button'
		);
		setRect( media, 0, 0, 600, 500 );
		setRect( gallery, 0, 0, 600, 500 );
		setRect( thumbnails, 0, 0, 80, 500 );
		setRect( viewport, 100, 0, 500, 500 );
		setRect( navigation, 105, 5, 490, 490 );
		setRect( buttons?.[ 0 ] || null, 431, 455, 40, 40 );
		setRect( buttons?.[ 1 ] || null, 475, 455, 40, 40 );
		setRect( root.querySelector( '.vsge-launch-3d' ), 0, 0, 70, 40 );

		expect( syncOverlayGeometry( root ) ).toBe( true );
		expect( root.classList.contains( 'vsge-3d-overlay-ready' ) ).toBe(
			true
		);
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
