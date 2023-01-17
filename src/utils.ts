import { modelID, mvContainer } from './index';
import * as QRCode from 'qrcode';

export const tapDistance: number = 2;
export let panning: boolean = false;
export let panX: number[], panY: number[];
export let startX: number, startY: number;
export let metersPerPixel: number;

export const errorContainer =
	( document.getElementById( 'error' ) as HTMLElement ) || null;
export const buttonArInit =
	( document.getElementById( 'ar-init' ) as HTMLElement ) || null;
export const button3dRecenter =
	( document.getElementById( 'ar-center' ) as HTMLElement ) || null;
export const button3dRotation =
	( document.getElementById( 'ar-rotation' ) as HTMLElement ) || null;
export const modalToggleHotspot =
	( document.getElementById( 'ar-toggle-hotspots' ) as HTMLElement ) || null;
export const modalGenerateQR =
	( document.getElementById( 'vsge-modal-qrcode' ) as HTMLElement ) || null;
export const arButton =
	( document.getElementById( 'ar-button' ) as HTMLElement ) || null;

export const onInteraction = () => {
	if ( arButton ) arButton.click();
};

// Generate the qr code
/**
 * "If the canvas and modelID exist, then create a QR code and return the URL."
 *
 * @param {HTMLElement} canvas - The canvas element to render the QR code to.
 * @param {string}      id     - The attachment ID of the model.
 * @return A function that takes two arguments, canvas and id.
 */
export const getQRCode = ( canvas: HTMLElement, id: string ) => {
	if ( canvas && modelID ) {
		// @ts-ignore
		const url = wp.siteurl + '/?attachment_id=' + id;
		QRCode.toCanvas( canvas, url, ( error ) => {
			if ( error ) throw new Error( error.message );
		} );
		return url;
	}
	return false;
};

/**
 * It takes an event, finds the progress bar, and updates the progress bar's value
 *
 * @param {any} event - any
 */
export const onProgress = ( event: any ) => {
	const progress = event.target as HTMLProgressElement;
	if ( progress ) {
		const progressBar = progress.querySelector( '.progress-bar-container' );
		const updatingBar = progress.querySelector(
			'.progress-bar'
		) as HTMLProgressElement;

		if ( updatingBar ) updatingBar.value = event.detail.totalProgress * 100;

		if ( progressBar ) {
			if ( event.detail.totalProgress === 1 ) {
				progressBar.classList.add( 'hide' );
			} else {
				progressBar.classList.remove( 'hide' );
			}
		}
	}
};

/**
 * It takes a string as an argument, and then it displays that string in the error container
 *
 * @param {string} errorMessage - The error message to display.
 */
export const logModelError = ( errorMessage: string ) => {
	const errorWrapper = errorContainer.querySelector( '#error-message' );
	if ( errorWrapper ) {
		errorContainer.classList.remove( 'hide' );
		errorWrapper.innerHTML = errorMessage;
	}
};

/**
 * It starts the rotation of the model.
 */
export const startRotate = () => {
	if ( mvContainer ) {
		mvContainer.autoRotate = true;
		startPan();

		mvContainer.interactionPrompt = 'none';
	}
};

/**
 * It stops the rotation of the 3d model viewer
 */
export const stopRotate = () => {
	if ( mvContainer ) {
		mvContainer.autoRotate = false;
		mvContainer.interactionPrompt = 'none';
	}
};

/**
 * If the 3D rotation button is active, start or stop the rotation
 *
 * @param {boolean} [start=true] - boolean = true
 */
export const setRotate = ( start: boolean = true ) => {
	if ( button3dRotation && button3dRotation.classList.contains( 'active' ) )
		if ( start ) {
			startRotate();
		} else {
			stopRotate();
		}
};

/**
 * If the button is active, stop the rotation, otherwise start the rotation
 *
 * @param {Event} event - Event - The event object.
 */
export const setRotation = ( event: Event ) => {
	if ( button3dRotation.classList.contains( 'active' ) === true ) {
		stopRotate();
		button3dRotation.classList.remove( 'active' );
	} else {
		startRotate();
		button3dRotation.classList.add( 'active' );
	}
	event.preventDefault();
};

/**
 * It sets up the panning
 * vectors and the meters per pixel scale factor
 */
export const startPan = () => {
	if ( mvContainer ) {
		const orbit = mvContainer.getCameraOrbit();
		const { theta, phi, radius } = orbit;
		const psi = theta - mvContainer.turntableRotation;
		metersPerPixel =
			( 0.75 * radius ) / mvContainer.getBoundingClientRect().height;
		panX = [ -Math.cos( psi ), 0, Math.sin( psi ) ];
		panY = [
			-Math.cos( phi ) * Math.sin( psi ),
			Math.sin( phi ),
			-Math.cos( phi ) * Math.cos( psi ),
		];
		mvContainer.interactionPrompt = 'none';
	}
};

/**
 * It resets the camera to its initial position
 */
export const centerView = () => {
	if ( mvContainer ) {
		const initialCameraOrbit = mvContainer?.cameraOrbit ?? '';
		const initialCameraTarget = mvContainer?.cameraTarget ?? '';
		panning = false;

		mvContainer.interpolationDecay = 100;
		mvContainer.cameraTarget = initialCameraTarget;
		mvContainer.cameraOrbit = initialCameraOrbit;
		mvContainer.updateFraming();
	}
};

/**
 * If the user has not moved the mouse more than a certain distance from the starting point, then
 * recenter the camera on the point where the user clicked
 *
 * @param {Object} pointer         - The pointer object from the event.
 * @param          pointer.clientX
 * @param          pointer.clientY
 * @return {void}
 */
export const recenter = ( pointer: { clientX: number; clientY: number } ) => {
	panning = false;
	if (
		! mvContainer ||
		Math.abs( pointer.clientX - startX ) > tapDistance ||
		Math.abs( pointer.clientY - startY ) > tapDistance
	)
		return;
	const hit = mvContainer.positionAndNormalFromPoint(
		pointer.clientX,
		pointer.clientY
	);
	mvContainer.cameraTarget =
		hit === null ? 'auto auto auto' : hit.position.toString();
};

/**
 * If the model fails to load, display an error message
 *
 * @param {any} event - any - the event that is triggered when the model is loaded.
 */
export const throwErrorOnLoad = ( event: any ) => {
	if ( event.detail.status === 'failed' ) {
		if ( errorContainer )
			logModelError(
				'An error occurred activating VR, please check your browser permissions then clean cookies!<br/>To verify that the device is correctly configured to run WebXR, browse to <a href="https://immersive-web.github.io/webxr-samples/immersive-ar-session.html">a sample WebXR page</a> in <a href="https://developer.mozilla.org/en-US/docs/Web/API/WebXR_Device_API#browser_compatibility">a compatible browser</a>.'
			);
	}
};

/**
 * It takes a NodeList of elements and a boolean, and then adds or removes a class from each element in
 * the NodeList depending on the boolean
 *
 * @param           hotspots - NodeListOf< Element > - this is the list of hotspots that we want to toggle the
 *                           visibility of.
 * @param {boolean} isActive - boolean - This is a boolean value that determines whether the hotspots
 *                           should be visible or not.
 */
export const toggleHotspotVisibility = (
	hotspots: NodeListOf< Element >,
	isActive: boolean
) => {
	hotspots.forEach( ( hotspot ) => {
		if ( isActive ) {
			hotspot.classList.add( 'hide' );
		} else {
			hotspot.classList.remove( 'hide' );
		}
	} );
};

/**
 * It toggles the visibility of all hotspots in the current panorama
 */
export const toggleHotspot = () => {
	if ( mvContainer ) {
		const isActive = modalToggleHotspot.classList.contains( 'active' );
		const hotspots = mvContainer.querySelectorAll( 'div.hotspot' );
		toggleHotspotVisibility( hotspots, isActive );
		modalToggleHotspot.classList.toggle( 'active' );
	}
};

/**
 * It sets the center of the view to the center of the canvas, and then recenters the view to the
 * center of the canvas
 *
 * @param {Event} event - The event that triggered the action.
 */
export const setRecenter = ( event: Event ) => {
	centerView();
	recenter( { clientX: 0, clientY: 0 } );
	event.preventDefault();
};

/**
 * If the mvContainer can activate AR, then activate AR and show the hotspots. Otherwise, show the QR
 * code modal
 *
 * @param {Event} event - Event - The event object that was triggered by the user.
 */
export const arInitialize = ( event: Event ) => {
	if ( mvContainer?.canActivateAR ) {
		mvContainer.activateAR();
		const hotspots = mvContainer.querySelectorAll( 'div.hotspot' );
		toggleHotspotVisibility( hotspots, true );
	} else {
		modalGenerateQR.classList.add( 'active' );
	}
	event.preventDefault();
};

/**
 * It removes the class 'active' from the modalGenerateQR element
 *
 * @param {Event} event - Event - This is the event that is being passed to the function.
 */
export const deactivateQR = ( event: Event ) => {
	modalGenerateQR.classList.remove( 'active' );
	event.preventDefault();
};
