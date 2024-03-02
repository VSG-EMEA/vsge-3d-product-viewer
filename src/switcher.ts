/**
 * True if you want the 3D model to be displayed immediately, if not the gallery will be displayed first
 */

/**
 * WOOCOMMERCE MEDIA SECTION GALLERY
 * It adds an event listener to each button in the gallery switcher, and when clicked, it toggles the
 * visibility of the 3D model viewer
 */
export function init3dControls(): void {
	const switchButtons = document
		.getElementById( 'woo-switch-gallery' )
		?.getElementsByTagName( 'button' );

	if ( switchButtons ) {
		const modelViewerWrapper =
			( document.getElementById(
				'woocommerce-product-gallery__3d'
			) as HTMLElement ) || null;

		Object.values( switchButtons ).forEach( ( button ) => {
			button.addEventListener( 'click', function () {
				// handle the buttons items state
				Object.values( switchButtons ).forEach( ( item ) => {
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
