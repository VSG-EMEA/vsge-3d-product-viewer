export const showViewerError = ( root: HTMLElement, message: string ) => {
	const error = root.querySelector< HTMLElement >( '.vsge-model-error' );
	if ( error ) {
		error.textContent = message;
		error.hidden = false;
	}
};

type GalleryState = {
	ariaHidden: string | null;
	inert: boolean;
};

const galleryStates = new WeakMap< HTMLElement, GalleryState >();

const getGallery = ( root: HTMLElement ) => {
	const media = root.closest< HTMLElement >( '.vsge-product-media' );
	if ( ! media ) {
		return null;
	}
	return Array.from( media.children ).find(
		( child ): child is HTMLElement =>
			child instanceof HTMLElement &&
			child.matches(
				'.wp-block-woocommerce-product-gallery, .wc-block-product-gallery'
			)
	);
};

export const hasOverlayGallery = ( root: HTMLElement ) =>
	null !== getGallery( root );

export const setOverlayState = ( root: HTMLElement, active: boolean ) => {
	const stage = root.querySelector< HTMLElement >( '.vsge-3d-stage' );
	const launcher = root.querySelector< HTMLElement >( '.vsge-3d-launcher' );
	const gallery = getGallery( root );

	root.classList.toggle( 'vsge-3d-active', active );
	if ( stage ) {
		stage.hidden = ! active;
	}
	if ( launcher ) {
		launcher.hidden = active;
	}
	if ( ! gallery ) {
		return;
	}

	if ( active ) {
		if ( ! galleryStates.has( gallery ) ) {
			galleryStates.set( gallery, {
				ariaHidden: gallery.getAttribute( 'aria-hidden' ),
				inert: gallery.inert,
			} );
		}
		gallery.inert = true;
		gallery.setAttribute( 'aria-hidden', 'true' );
		return;
	}

	const state = galleryStates.get( gallery );
	if ( ! state ) {
		return;
	}
	gallery.inert = state.inert;
	if ( state.ariaHidden === null ) {
		gallery.removeAttribute( 'aria-hidden' );
	} else {
		gallery.setAttribute( 'aria-hidden', state.ariaHidden );
	}
	galleryStates.delete( gallery );
};
