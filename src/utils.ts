import QRCode from 'qrcode';
import { ModelViewerElement } from '@google/model-viewer';

export const tapDistance: number = 2;
export let panning: boolean = false;
export let panX: number[], panY: number[];
export let startX: number, startY: number;
export let metersPerPixel: number;

export const onInteraction = () => {
	const arButton =
		( document.getElementById( 'ar-button' ) as HTMLElement ) || null;
	if ( arButton ) arButton.click();
};

// Generate the qr code
/**
 * "If the canvas and modelID exist, then create a QR code and return the URL."
 *
 * @param {HTMLElement} canvas - The canvas element to render the QR code to.
 * @param {string}      id     - The attachment ID of the model.
 * @return {boolean} A function that takes two arguments, canvas and id.
 */
export function getQRCode( canvas: HTMLElement, id: string ): boolean {
	if ( canvas && id ) {
		// @ts-ignore
		const url = wp.siteurl + '/?attachment_id=' + id;
		QRCode.toCanvas( canvas, url, ( error ) => {
			if ( error ) throw new Error( error.message );
		} );
		return true;
	}
	return false;
}

/**
 * It takes an event, finds the progress bar, and updates the progress bar's value
 *
 * @param {any} event - any
 */
export const onProgress = ( event: any ) => {
	const progress = event.currentTarget as HTMLProgressElement;
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
 * @param          container
 * @param {string} errorMessage - The error message to display.
 */
export const logModelError = (
	container: HTMLElement,
	errorMessage: string
) => {
	const errorWrapper = container.querySelector( '#error-message' );
	if ( errorWrapper ) {
		container.classList.remove( 'hide' );
		errorWrapper.innerHTML = errorMessage;
	}
};

/**
 * the rotation button is used to determine whether the model is autoRotating or not,
 * if it has the classname "active" if the model is autoRotating
 *
 * @param  button
 */
export const getAutoRotation = ( button: HTMLElement | null ): boolean => {
	return button ? button.classList.contains( 'active' ) : false;
};

/**
 * It starts the rotation of the model.
 *
 * @param  container
 */
export const startRotate = ( container: ModelViewerElement ) => {
	if ( container ) {
		container.autoRotate = true;
		startPan( container );

		container.interactionPrompt = 'none';
	}
};

/**
 * It stops the rotation of the 3d model viewer
 *
 * @param  container
 */
export const stopRotate = ( container: ModelViewerElement ) => {
	if ( container ) {
		container.autoRotate = false;
		container.interactionPrompt = 'none';
	}
};

/**
 * If the 3D rotation isn't active, start the rotation
 *
 * @param  button3dRotation
 * @param  mvContainer
 */
export const enableAutoRotate = (
	button3dRotation: HTMLElement | null,
	mvContainer: ModelViewerElement
) => {
	if ( getAutoRotation( button3dRotation ) ) {
		startRotate( mvContainer );
	}
};

/**
 * If the button is active, stop the rotation, otherwise start the rotation
 *
 * @param  event     - Event - The event object.
 * @param  container
 */
export const setAutoRotation = (
	event: Event,
	container: ModelViewerElement
) => {
	const button = event.currentTarget as HTMLInputElement;
	if ( button.classList.contains( 'active' ) === true ) {
		stopRotate( container );
		button.classList.remove( 'active' );
	} else {
		startRotate( container );
		button.classList.add( 'active' );
	}
	event.preventDefault();
};

/**
 * It sets up the panning
 * vectors and the meters per pixel scale factor
 *
 * @param  container
 */
export const startPan = ( container: ModelViewerElement ) => {
	if ( container ) {
		const orbit = container.getCameraOrbit();
		const { theta, phi, radius } = orbit;
		const psi = theta - container.turntableRotation;
		metersPerPixel =
			( 0.75 * radius ) / container.getBoundingClientRect().height;
		panX = [ -Math.cos( psi ), 0, Math.sin( psi ) ];
		panY = [
			-Math.cos( phi ) * Math.sin( psi ),
			Math.sin( phi ),
			-Math.cos( phi ) * Math.cos( psi ),
		];
		container.interactionPrompt = 'none';
	}
};

/**
 * It resets the camera to its initial position
 *
 * @param  container
 */
export const centerView = ( container: ModelViewerElement ) => {
	if ( container ) {
		const initialCameraOrbit = container?.cameraOrbit ?? '';
		const initialCameraTarget = container?.cameraTarget ?? '';
		panning = false;

		container.interpolationDecay = 100;
		container.cameraTarget = initialCameraTarget;
		container.cameraOrbit = initialCameraOrbit;
		container.updateFraming();
	}
};

/**
 * If the user has not moved the mouse more than a certain distance from the starting point, then
 * recenter the camera on the point where the user clicked
 *
 * @param {ModelViewerElement} container
 * @param {Object}             pointer         - The pointer object from the event.
 * @param                      pointer.clientX
 * @param                      pointer.clientY
 * @return {void}
 */
export const recenter = (
	container: ModelViewerElement,
	pointer: { clientX: number; clientY: number } = { clientX: 0, clientY: 0 }
) => {
	panning = false;
	if (
		! container ||
		Math.abs( pointer.clientX - startX ) > tapDistance ||
		Math.abs( pointer.clientY - startY ) > tapDistance
	)
		return;
	const hit = container.positionAndNormalFromPoint(
		pointer.clientX,
		pointer.clientY
	);
	container.cameraTarget =
		hit === null ? 'auto auto auto' : hit.position.toString();
};

/**
 * If the model fails to load, display an error message
 *
 * @param {any} event - any - the event that is triggered when the model is loaded.
 */
export const throwErrorOnLoad = ( event: any ) => {
	if ( event.detail.status === 'failed' ) {
		const errorContainer =
			( document.getElementById( 'error' ) as HTMLElement ) || null;

		if ( errorContainer )
			logModelError(
				errorContainer,
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
 *
 * @param {Event}              event     - The event that triggered the action.
 * @param {ModelViewerElement} container
 */
export const toggleHotspot = (
	event: Event,
	container: ModelViewerElement
) => {
	if ( container ) {
		const isActive = (
			event.currentTarget as HTMLInputElement
		 ).classList.contains( 'active' );
		const hotspots = container.querySelectorAll( 'div.hotspot' );
		toggleHotspotVisibility( hotspots, isActive );
		( event.currentTarget as HTMLInputElement ).classList.toggle( 'active' );
	}
};

/**
 * It sets the center of the view to the center of the canvas, and then recenters the view to the
 * center of the canvas
 *
 * @param {Event}              event     - The event that triggered the action.
 * @param {ModelViewerElement} container
 */
export const setRecenter = ( event: Event, container: ModelViewerElement ) => {
	centerView( container );
	recenter( container );
	event.preventDefault();
};

/**
 * If the mvContainer can activate AR, then activate AR and show the hotspots. Otherwise, show the QR
 * code modal
 *
 * @param {Event} event     - Event - The event object that was triggered by the user.
 * @param         container
 */
export const arInitialize = ( event: Event, container: ModelViewerElement ) => {
	if ( container?.canActivateAR ) {
		container.activateAR();
		const hotspots = container.querySelectorAll( 'div.hotspot' );
		toggleHotspotVisibility( hotspots, true );
	} else {
		( event.currentTarget as HTMLInputElement ).classList.add( 'active' );
	}
	event.preventDefault();
};

/**
 * It removes the class 'active' from the modalGenerateQR element
 *
 * @param {Event} event - Event - This is the event that is being passed to the function.
 */
export const deactivateQR = ( event: Event ) => {
	( event.currentTarget as HTMLInputElement ).classList.remove( 'active' );
	event.preventDefault();
};
