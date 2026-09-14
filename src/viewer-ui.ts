export const showViewerError = ( root: HTMLElement, message: string ) => {
	const error = root.querySelector< HTMLElement >( '.vsge-model-error' );
	if ( error ) {
		error.textContent = message;
		error.hidden = false;
	}
};

type ElementState = {
	element: HTMLElement;
	ariaHidden: string | null;
	inert: boolean;
};

type OverlayConnection = {
	media: HTMLElement;
	mutationObserver: MutationObserver;
	resizeObserver?: ResizeObserver;
	frame?: number;
};

type ThumbnailRailGeometry = {
	left: number;
	top: number;
	width: number;
	height: number;
	gap: number;
};

const galleryStates = new WeakMap< HTMLElement, ElementState[] >();
const overlayConnections = new WeakMap< HTMLElement, OverlayConnection >();
const thumbnailRailGeometries = new WeakMap<
	HTMLElement,
	ThumbnailRailGeometry
>();

const GALLERY_SELECTOR =
	'.wp-block-woocommerce-product-gallery, .wc-block-product-gallery';
const VIEWPORT_SELECTOR = '.wc-block-product-gallery-large-image';
const THUMBNAILS_SELECTOR = '.wc-block-product-gallery-thumbnails';
const THUMBNAIL_ITEM_SELECTOR =
	'.wc-block-product-gallery-thumbnails__thumbnail';
const NAVIGATION_SELECTOR = '.wc-block-next-previous-buttons';
const NAVIGATION_BUTTON_SELECTOR = '.wc-block-next-previous-buttons__button';

const directChild = ( parent: HTMLElement, selector: string ) =>
	Array.from( parent.children ).find(
		( child ): child is HTMLElement =>
			child instanceof HTMLElement && child.matches( selector )
	) || null;

const getProductMedia = ( root: HTMLElement ) => {
	if ( root.parentElement?.matches( '.vsge-product-media' ) ) {
		return root.parentElement;
	}

	const parent = root.parentElement;
	return parent ? directChild( parent, '.vsge-product-media' ) : null;
};

const getGallery = ( root: HTMLElement ) => {
	const media = getProductMedia( root );
	return media ? directChild( media, GALLERY_SELECTOR ) : null;
};

const getLauncher = ( root: HTMLElement ) =>
	root.querySelector< HTMLButtonElement >( '.vsge-launch-3d' );

const numberValue = ( value: string ) => Number.parseFloat( value ) || 0;

const getNaturalSwitchWidth = (
	launcher: HTMLButtonElement,
	minimumWidth: number
) => {
	const style = getComputedStyle( launcher );
	const labelWidth = Math.max(
		0,
		...Array.from(
			launcher.querySelectorAll< HTMLElement >(
				'[data-vsge-switch-model], [data-vsge-switch-gallery]'
			)
		).map( ( label ) => label.scrollWidth )
	);
	const chromeWidth =
		numberValue( style.paddingLeft ) +
		numberValue( style.paddingRight ) +
		numberValue( style.borderLeftWidth ) +
		numberValue( style.borderRightWidth );
	return Math.max(
		minimumWidth,
		labelWidth + chromeWidth,
		launcher.scrollWidth,
		launcher.getBoundingClientRect().width
	);
};

const setPixelProperty = (
	root: HTMLElement,
	property: string,
	value: number
) => root.style.setProperty( property, `${ Math.max( 0, value ) }px` );

const getThumbnailRailGeometry = (
	gallery: HTMLElement,
	mediaRect: DOMRect
): ThumbnailRailGeometry | null => {
	const items = Array.from(
		gallery.querySelectorAll< HTMLElement >( THUMBNAIL_ITEM_SELECTOR )
	).map( ( item ) => ( { item, rect: item.getBoundingClientRect() } ) );
	const first = items.find(
		( { rect } ) => rect.width > 0 && rect.height > 0
	);
	if ( ! first ) {
		return null;
	}

	const next = items.find(
		( { rect } ) =>
			rect.top > first.rect.top && rect.width > 0 && rect.height > 0
	);
	return {
		left: first.rect.left - mediaRect.left,
		top: first.rect.top - mediaRect.top,
		width: first.rect.width,
		height: first.rect.height,
		gap: next ? Math.max( 0, next.rect.top - first.rect.bottom ) : 0,
	};
};

const applyThumbnailRailGeometry = (
	root: HTMLElement,
	geometry: ThumbnailRailGeometry
) => {
	setPixelProperty( root, '--vsge-3d-rail-left', geometry.left );
	setPixelProperty( root, '--vsge-3d-rail-top', geometry.top );
	setPixelProperty( root, '--vsge-3d-rail-width', geometry.width );
	setPixelProperty( root, '--vsge-3d-rail-height', geometry.height );
	setPixelProperty( root, '--vsge-3d-rail-gap', geometry.gap );
	root.classList.add( 'vsge-3d-has-thumbnail-rail' );
};

const updateSwitchState = ( root: HTMLElement, active: boolean ) => {
	const launcher = getLauncher( root );
	if ( ! launcher ) {
		return;
	}
	launcher.setAttribute( 'aria-expanded', String( active ) );
	launcher.setAttribute( 'aria-pressed', String( active ) );
	launcher.setAttribute(
		'aria-label',
		active
			? launcher.dataset.galleryLabel || ''
			: launcher.dataset.modelLabel || ''
	);
	launcher
		.querySelector< HTMLElement >( '[data-vsge-switch-model]' )
		?.toggleAttribute( 'hidden', active );
	launcher
		.querySelector< HTMLElement >( '[data-vsge-switch-gallery]' )
		?.toggleAttribute( 'hidden', ! active );
};

const restoreGalleryStates = ( root: HTMLElement ) => {
	const states = galleryStates.get( root ) || [];
	states.forEach( ( state ) => {
		state.element.inert = state.inert;
		if ( state.ariaHidden === null ) {
			state.element.removeAttribute( 'aria-hidden' );
		} else {
			state.element.setAttribute( 'aria-hidden', state.ariaHidden );
		}
	} );
	galleryStates.delete( root );
};

export const hasOverlayGallery = ( root: HTMLElement ) =>
	null !== getGallery( root );

export const syncOverlayGeometry = ( root: HTMLElement ) => {
	const media = getProductMedia( root );
	const gallery = getGallery( root );
	const viewport = gallery?.querySelector< HTMLElement >( VIEWPORT_SELECTOR );
	const navigation =
		gallery?.querySelector< HTMLElement >( NAVIGATION_SELECTOR );
	const navigationButtons = navigation
		? Array.from(
				navigation.querySelectorAll< HTMLElement >(
					NAVIGATION_BUTTON_SELECTOR
				)
		  ).filter( ( button ) => button.getBoundingClientRect().width > 0 )
		: [];
	const lastNavigationButton = navigationButtons.at( -1 );
	const launcher = getLauncher( root );

	if (
		! media ||
		! gallery ||
		! viewport ||
		! navigation ||
		! lastNavigationButton ||
		! launcher
	) {
		root.classList.remove( 'vsge-3d-overlay-ready' );
		return false;
	}

	const mediaRect = media.getBoundingClientRect();
	const viewportRect = viewport.getBoundingClientRect();
	const navigationRect = navigation.getBoundingClientRect();
	const navigationButtonRect = lastNavigationButton.getBoundingClientRect();
	if (
		mediaRect.width <= 0 ||
		viewportRect.width <= 0 ||
		viewportRect.height <= 0 ||
		navigationRect.width <= 0 ||
		navigationButtonRect.height <= 0
	) {
		root.classList.remove( 'vsge-3d-overlay-ready' );
		return false;
	}
	const naturalSwitchWidth = getNaturalSwitchWidth(
		launcher,
		navigationButtonRect.width
	);

	media.style.setProperty(
		'--vsge-gallery-switch-width',
		`${ Math.ceil( naturalSwitchWidth ) }px`
	);
	setPixelProperty(
		root,
		'--vsge-3d-viewport-left',
		viewportRect.left - mediaRect.left
	);
	setPixelProperty(
		root,
		'--vsge-3d-viewport-top',
		viewportRect.top - mediaRect.top
	);
	setPixelProperty( root, '--vsge-3d-viewport-width', viewportRect.width );
	setPixelProperty( root, '--vsge-3d-viewport-height', viewportRect.height );
	setPixelProperty(
		root,
		'--vsge-3d-switch-left',
		navigationRect.right - mediaRect.left - naturalSwitchWidth
	);
	setPixelProperty(
		root,
		'--vsge-3d-switch-top',
		navigationButtonRect.top - mediaRect.top
	);
	setPixelProperty( root, '--vsge-3d-switch-width', naturalSwitchWidth );
	setPixelProperty(
		root,
		'--vsge-3d-switch-height',
		navigationButtonRect.height
	);

	const active = root.classList.contains( 'vsge-3d-active' );
	const nextThumbnailRailGeometry = active
		? null
		: getThumbnailRailGeometry( gallery, mediaRect );
	if ( nextThumbnailRailGeometry ) {
		thumbnailRailGeometries.set( root, nextThumbnailRailGeometry );
	}
	const thumbnailRailGeometry =
		nextThumbnailRailGeometry || thumbnailRailGeometries.get( root );
	if ( thumbnailRailGeometry ) {
		applyThumbnailRailGeometry( root, thumbnailRailGeometry );
	} else {
		root.classList.remove( 'vsge-3d-has-thumbnail-rail' );
	}

	root.classList.add( 'vsge-3d-overlay-ready' );
	return true;
};

export const initialiseGalleryOverlay = ( root: HTMLElement ) => {
	const media = getProductMedia( root );
	if ( ! media ) {
		return false;
	}
	if ( root.parentElement !== media ) {
		media.append( root );
	}

	root.classList.add( 'vsge-3d-overlay-mode' );
	media.classList.add( 'vsge-product-media--has-3d' );

	const connection: OverlayConnection = {
		media,
		mutationObserver: new MutationObserver( () => scheduleSync() ),
	};
	function scheduleSync() {
		if ( connection.frame ) {
			cancelAnimationFrame( connection.frame );
		}
		connection.frame = requestAnimationFrame( () => {
			connection.frame = undefined;
			syncOverlayGeometry( root );
			if ( root.classList.contains( 'vsge-3d-active' ) ) {
				setOverlayState( root, true );
			}
			if ( connection.resizeObserver ) {
				connection.resizeObserver.disconnect();
				[
					media,
					getGallery( root ),
					getGallery( root )?.querySelector< HTMLElement >(
						VIEWPORT_SELECTOR
					),
					getGallery( root )?.querySelector< HTMLElement >(
						THUMBNAILS_SELECTOR
					),
					getGallery( root )?.querySelector< HTMLElement >(
						NAVIGATION_SELECTOR
					),
				].forEach( ( element ) => {
					if ( element ) {
						connection.resizeObserver?.observe( element );
					}
				} );
			}
		} );
	}

	if ( typeof ResizeObserver !== 'undefined' ) {
		connection.resizeObserver = new ResizeObserver( scheduleSync );
	}
	connection.mutationObserver.observe( media, {
		childList: true,
		subtree: true,
	} );
	overlayConnections.set( root, connection );
	syncOverlayGeometry( root );
	scheduleSync();
	return true;
};

export const setOverlayState = ( root: HTMLElement, active: boolean ) => {
	const stage = root.querySelector< HTMLElement >( '.vsge-3d-stage' );
	const gallery = getGallery( root );
	const media = getProductMedia( root );

	if ( active ) {
		// Capture Woo's still-visible thumbnail cards before its gallery state is
		// visually replaced by this alternate viewer.
		syncOverlayGeometry( root );
	}
	root.classList.toggle( 'vsge-3d-active', active );
	media?.classList.toggle( 'vsge-product-media--3d-active', active );
	if ( stage ) {
		stage.hidden = ! active;
	}
	updateSwitchState( root, active );
	if ( ! gallery ) {
		return;
	}

	if ( active ) {
		restoreGalleryStates( root );
		const elements = [
			gallery.querySelector< HTMLElement >( THUMBNAILS_SELECTOR ),
			gallery.querySelector< HTMLElement >(
				`${ VIEWPORT_SELECTOR }__container`
			),
		].filter( ( element ): element is HTMLElement => Boolean( element ) );
		galleryStates.set(
			root,
			elements.map( ( element ) => ( {
				element,
				ariaHidden: element.getAttribute( 'aria-hidden' ),
				inert: element.inert,
			} ) )
		);
		elements.forEach( ( element ) => {
			element.inert = true;
			element.setAttribute( 'aria-hidden', 'true' );
		} );
		return;
	}

	restoreGalleryStates( root );
	// Gallery cards are visible again, so refresh the cached geometry for the
	// next activation rather than retaining a stale responsive measurement.
	syncOverlayGeometry( root );
};
