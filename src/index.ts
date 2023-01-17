/* global wp */

// 3D MODEL VIEWER
import { ModelViewerElement } from '@google/model-viewer';
import 'focus-visible';

import { init3dControls } from './switcher';

import './style/style.scss';
import {
	arInitialize,
	deactivateQR,
	getQRCode,
	onInteraction,
	onProgress,
	setRecenter,
	enableAutoRotate,
	setAutoRotation,
	throwErrorOnLoad,
	toggleHotspot,
	stopRotate,
} from './utils';

export const getModelViewer = (
	container: HTMLElement | null
): ModelViewerElement | null => {
	if ( container ) {
		return container.querySelector( 'model-viewer' ) as ModelViewerElement;
	}
	return null;
};

export const getQrURL = ( productID: string | null ) => {
	const modelVR = document.getElementById(
		'vsge-vr-model'
	) as HTMLElement | null;

	if ( modelVR && productID ) return getQRCode( modelVR, productID );
};

export const getModelID = ( container: HTMLElement ): string | null =>
	container?.dataset.model || null;

export const initModelViewer = ( mvContainer: ModelViewerElement ) => {
	// execute loading
	mvContainer.addEventListener( 'progress', onProgress );

	// throw an error if the model fails to load
	mvContainer.addEventListener( 'ar-status', throwErrorOnLoad );

	document.body.addEventListener( 'mouseover', onInteraction, {
		once: true,
	} );
	document.body.addEventListener( 'touchmove', onInteraction, {
		once: true,
	} );

	document.body.addEventListener( 'scroll', onInteraction, { once: true } );
	document.body.addEventListener( 'keydown', onInteraction, { once: true } );

	mvContainer.onmouseleave = () => stopRotate( mvContainer );

	mvContainer.oncontextmenu = ( ev: Event ) => ev.preventDefault();
};

/**
 * Product page only
 *
 * @param  mvContainer
 */
export const onProductInit = ( mvContainer: ModelViewerElement ) => {
	const buttonArInit =
		( document.getElementById( 'ar-init' ) as HTMLElement ) || null;
	const modalGenerateQR =
		( document.getElementById( 'vsge-modal-qrcode' ) as HTMLElement ) ||
		null;
	const button3dRecenter =
		( document.getElementById( 'ar-center' ) as HTMLElement ) || null;
	const modalToggleHotspot =
		( document.getElementById( 'ar-toggle-hotspots' ) as HTMLElement ) ||
		null;
	const button3dRotation =
		( document.getElementById( 'ar-rotation' ) as HTMLElement ) || null;

	buttonArInit.onclick = ( e ) => arInitialize( e, mvContainer );

	modalGenerateQR.onclick = () => deactivateQR;

	modalToggleHotspot.onclick = ( e ) => toggleHotspot( e, mvContainer );

	button3dRecenter.onclick = ( e ) => setRecenter( e, mvContainer );

	button3dRotation.onclick = ( e ) => setAutoRotation( e, mvContainer );

	mvContainer.onmouseenter = () =>
		enableAutoRotate( button3dRotation, mvContainer );
};

/**
 * On page Load
 */
window.addEventListener( 'load', () => {
	const container3d: HTMLElement | null = document.getElementById(
		'woocommerce-product-gallery__3d'
	);
	const mvContainer: ModelViewerElement | null =
		getModelViewer( container3d );

	/** if model viewer isn't available in this page exit immediately */
	if ( mvContainer ) {
		/**
		 * Adds the switcher between gallery and 3d model
		 */
		initModelViewer( mvContainer );

		/**
		 * Then start the basic 3d controls
		 */
		init3dControls();

		/**
		 * If the model id was found we are in the product page, and we need some extra controls
		 * ADDITIONALLY, we will add:
		 * - qr url of the related attachment page product page only
		 * - enable extra controls on the left
		 */
		if ( getQrURL( getModelID( mvContainer ) ) ) {
			onProductInit( mvContainer );
		}
	}
} );
