/**
 * WOOCOMMERCE MEDIA SECTION GALLERY
 * It adds an event listener to each button in the gallery switcher, and when clicked, it toggles the
 * visibility of the 3D model viewer
 */
export function init3dControls(): void {
	const switcherContainer = document.getElementById( 'woo-switch-gallery' );
	const switcherContainerButtons = switcherContainer
		? switcherContainer.getElementsByTagName( 'button' )
		: null;

	if ( switcherContainerButtons ) {
		const modelViewerWrapper =
			( document.getElementById(
				'woocommerce-product-gallery__3d'
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
