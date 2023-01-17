/* global wp */

// 3D MODEL VIEWER
import '@google/model-viewer';
import { ModelViewerElement } from '@google/model-viewer';
import 'focus-visible';
import * as QRCode from 'qrcode';

import { initControls } from './switcher';

import './style/style.scss';
import {
	arInitialize,
	button3dRecenter,
	button3dRotation,
	buttonArInit,
	deactivateQR,
	getQRCode,
	modalGenerateQR,
	modalToggleHotspot,
	onInteraction,
	onProgress,
	setRecenter,
	setRotate,
	setRotation,
	throwErrorOnLoad,
	toggleHotspot,
} from './utils';

export const getContainer = (): ModelViewerElement | null =>
	document.querySelector( 'model-viewer' ) as ModelViewerElement;
export let mvContainer: ModelViewerElement | null;

// mvContainer.autoRotate.speed = initialCameraOrbit

export const getModelID = ( container: HTMLElement ): string | false =>
	container?.dataset.model || false;
export let modelID: string | false;

export const modelVR = document.getElementById(
	'vsge-vr-model'
) as HTMLElement | null;

export const getQrURL = ( productID: string ) =>
	modelVR ? getQRCode( modelVR, productID ) : false;
let qrURL: string | false;

export const on3dInit = ( container: HTMLElement ) => {
	// execute loading
	container.addEventListener( 'progress', onProgress );

	// throw an error if the model fails to load
	container.addEventListener( 'ar-status', throwErrorOnLoad );

	document.body.addEventListener( 'mouseover', onInteraction, {
		once: true,
	} );
	document.body.addEventListener( 'touchmove', onInteraction, {
		once: true,
	} );

	document.body.addEventListener( 'scroll', onInteraction, { once: true } );
	document.body.addEventListener( 'keydown', onInteraction, { once: true } );

	container.onmouseenter = () => setRotate( false );

	container.onmouseleave = () => setRotate;

	container.oncontextmenu = ( ev: Event ) => ev.preventDefault();
};

/**
 * Product page only
 */
export const onProductInit = () => {
	buttonArInit.onclick = () => arInitialize;

	modalGenerateQR.onclick = () => deactivateQR;

	modalToggleHotspot.onclick = () => toggleHotspot;

	button3dRecenter.onclick = () => setRecenter;

	button3dRotation.onclick = () => setRotation;
};

/**
 * On page Load
 */
window.addEventListener( 'load', () => {
	mvContainer = getContainer();

	/* if model viewer isn't available in this page exit immediately */
	if ( mvContainer === null ) return;
	modelID = getModelID( mvContainer );

	/* if the model id was found the 3d model is available */
	if ( modelID ) {
		/* build the qr url of the related attachment page */
		qrURL = getQrURL( modelID );

		/* fire the basic controls shared for both views */
		on3dInit( mvContainer );

		/* 4 product page only enable extra controls on the left */
		if ( qrURL ) {
			onProductInit();
		}

		/* then start the 3d controls */
		initControls();
	}
} );
