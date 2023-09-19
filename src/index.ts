/* global wp */

// 3D MODEL VIEWER
import { ModelViewerElement } from '@google/model-viewer';
import 'focus-visible';

import { init3dControls } from './switcher';

import './style/style.scss';
import {
	getQRCode,
	onInteraction,
	onProgress,
	throwErrorOnFail,
	attachmentPageInit,
} from './utils';

import {
	arInitialize,
	centerView,
	enableAutoRotate,
	setAutoRotation,
	setRecenter,
	stopRotate,
	toggleHotspot,
} from './model-viewer';

/**
 * "If the container exists, return the model viewer element inside it, otherwise return null."
 *
 * The first line of the function is a type annotation. It tells TypeScript that the function will
 * return a ModelViewerElement or null
 *
 * @param {HTMLElement | null} container - HTMLElement | null
 * @return A model viewer element.
 */
export const getModelViewer = (
	container: HTMLElement | null
): ModelViewerElement | null => {
	if ( container ) {
		return container.querySelector( 'model-viewer' ) as ModelViewerElement;
	}
	return null;
};

/**
 * It returns a QR code URL for the current VR model
 *
 * @param {string | null} productID - The product ID of the product you want to generate a QR code for.
 * @return A QR code that is generated from the modelVR element and the productID.
 */
export const getQrURL = ( productID: string | null ) => {
	const modelVR = document.getElementById(
		'vsge-vr-model'
	) as HTMLElement | null;

	if ( modelVR && productID ) return getQRCode( modelVR, productID );
};

/**
 * "Get the model ID from the container element."
 *
 * The function is a pure function, meaning it doesn't have any side effects. It doesn't modify the
 * container element, and it doesn't modify any other part of the application. It just returns a value
 *
 * @param {HTMLElement} container - HTMLElement - The container element that contains the model.
 */
export const getModelID = ( container: HTMLElement ): string | null =>
	container?.dataset.model || null;

/**
 * It adds event listeners to the model viewer element, and to the document body
 *
 * @param {ModelViewerElement} mvContainer - The model viewer element.
 */
export const initModelViewer = ( mvContainer: ModelViewerElement ) => {
	// execute loading
	if ( document.body.classList.contains( 'attachment' ) ) {
		mvContainer.addEventListener( 'progress', ( e ) =>
			attachmentPageInit( e, mvContainer )
		);
	} else {
		mvContainer.addEventListener( 'progress', onProgress );
	}

  mvContainer.addEventListener( 'load', () => centerView( mvContainer ) );

	// throw an error if the model fails to load
	mvContainer.addEventListener( 'ar-status', throwErrorOnFail );

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
 * @param {ModelViewerElement} mvContainer - The model-viewer element.
 */
export const onProductInit = ( mvContainer: ModelViewerElement ) => {
	const buttonArInit = document.getElementById( 'ar-init' ) as HTMLElement;

	const button3dRecenter = document.getElementById(
		'ar-center'
	) as HTMLElement;

	const modalToggleHotspot = document.getElementById(
		'ar-toggle-hotspots'
	) as HTMLElement;

	const button3dRotation = document.getElementById(
		'ar-rotation'
	) as HTMLElement;

	if ( buttonArInit )
		buttonArInit.onclick = ( e ) => arInitialize( e, mvContainer );

	if ( modalToggleHotspot )
		modalToggleHotspot.onclick = ( e ) => toggleHotspot( e, mvContainer );

	if ( button3dRecenter )
		button3dRecenter.onclick = ( e ) => setRecenter( e, mvContainer );

	if ( button3dRotation )
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
