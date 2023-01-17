/**
 * WOOCOMMERCE MEDIA SECTION GALLERY
 */
export const switcherContainer =
	document.getElementById( 'woo-switch-gallery' );
export const switcherContainerButtons = switcherContainer
	? switcherContainer.getElementsByTagName( 'button' )
	: null;

export function initControls() {
	if ( switcherContainerButtons ) {
		const modelViewerWrapper =
			( document.querySelector(
				'.woocommerce-product-gallery__3d'
			) as HTMLElement ) || null;

		Object.values( switcherContainerButtons ).forEach( ( button ) => {
			button.addEventListener( 'click', function () {
				Object.values( switcherContainerButtons ).forEach( ( item ) => {
					if ( button.dataset.type !== item.dataset.type ) {
						item.classList.remove( 'active' );
					} else {
						item.classList.add( 'active' );
					}
				} );

				if ( modelViewerWrapper && this.dataset.type === '3d-model' ) {
					modelViewerWrapper.style.display = 'block';
					modelViewerWrapper.classList.remove( 'hidden' );
				} else {
					modelViewerWrapper.classList.add( 'hidden' );
					setTimeout( function () {
						modelViewerWrapper.style.display = 'none';
					}, 350 );
				}
			} );
		} );
	}
}
