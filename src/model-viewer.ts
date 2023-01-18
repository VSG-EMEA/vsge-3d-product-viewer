import { ModelViewerElement } from '@google/model-viewer';
import { deactivateQR } from './utils';

export const tapDistance: number = 2;
export let panning: boolean = false;
export let panX: number[], panY: number[];
export let startX: number, startY: number;
export let metersPerPixel: number;

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
		const modalGenerateQR =
			( document.getElementById( 'vsge-modal-qrcode' ) as HTMLElement ) ||
			null;
		modalGenerateQR?.addEventListener( 'click', deactivateQR );
		modalGenerateQR.classList.add( 'active' );
	}

	event.preventDefault();
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
 * If the button has the class 'active', return true, otherwise return false.
 *
 * @param {Event} event - Event - The event object that was triggered.
 * @return A boolean value.
 */
export const isSelected = ( event: Event ) => {
	const button = event.currentTarget as HTMLButtonElement;
	return button.classList.contains( 'active' );
};

/**
 * It starts the rotation of the model.
 *
 * @param  container
 */
export const startRotate = ( container: ModelViewerElement ) => {
	if ( container ) {
		container.autoplay = true;
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
 * It takes a button and a boolean, and if the boolean is true, it removes the active class from the
 * button, otherwise it adds the active class to the button
 *
 * @param {EventTarget | null} button  - The button element that we want to change the state of.
 * @param {boolean}            enabled - boolean - This is the state of the button. If it's enabled, it will be
 *                                     active. If it's disabled, it will be inactive.
 */
export const fireButtonState = (
	button: EventTarget | null,
	enabled: boolean
): void => {
	if ( enabled ) {
		( button as Element ).classList.remove( 'active' );
	} else {
		( button as Element ).classList.add( 'active' );
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
	if ( isSelected( event ) ) {
		stopRotate( container );
		fireButtonState( event.currentTarget, true );
	} else {
		startRotate( container );
		fireButtonState( event.currentTarget, false );
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
		const hotspots = container.querySelectorAll( 'div.hotspot' );
		toggleHotspotVisibility( hotspots, isSelected( event ) );
		( event.currentTarget as HTMLInputElement ).classList.toggle(
			'active'
		);
	}
};
