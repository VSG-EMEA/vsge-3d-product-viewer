// global vsgeData
import QRCode from 'qrcode';
import { __ } from '@wordpress/i18n';
import { ModelViewerElement } from '@google/model-viewer';

/**
 * It clicks the AR button
 */
export const onInteraction = () => {
	const arButton =
		( document.getElementById( 'ar-button' ) as HTMLElement ) || null;
	if ( arButton ) arButton.click();
};

/**
 * "If the canvas and modelID exist, then create a QR code and return the URL."
 *
 * @param {HTMLElement} canvas - The canvas element to render the QR code to.
 * @param {string}      id     - The attachment ID of the model.
 * @return {string|false} A function that takes two arguments, canvas and id.
 */
export function getQRCode( canvas: HTMLElement, id: string ): string | false {
	if ( canvas && id ) {
		// @ts-ignore
		const url = vsgeData.siteurl + '/model3d=' + id;
		QRCode.toCanvas( canvas, url, ( error ) => {
			// console.log( '3D Model-Viewer on mobiles at ' + url );
			if ( error ) throw new Error( error.message );
		} );
		return url;
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

export function attachmentPageInit(
	event: any,
	container: ModelViewerElement
) {
	const progress = event.currentTarget as HTMLProgressElement;
	if ( progress ) {
		/** Modal */
		const modal = progress.querySelector( '.progress-bar-container' );
		if ( modal ) {
			if ( event.detail.totalProgress !== 1 ) {
				modal.classList.remove( 'hide' );
			}

			/** Progress bar */
			const updatingBar = progress.querySelector(
				'.progress-bar'
			) as HTMLProgressElement;
			if ( updatingBar )
				updatingBar.value = event.detail.totalProgress * 100;

			// if the progressbar has reached the total.
			if ( event.detail.totalProgress === 1 ) {
				/** AR Button */
				const arButton = container.querySelector(
					'.ar-button'
				) as HTMLProgressElement;
				arButton.classList.remove( 'hide' );

				arButton.addEventListener( 'click', () => {
					// enable ar
					container.activateAR();
					modal?.classList.add( 'hide' );
				} );
				// hide the progress bar
				updatingBar.classList.add( 'hide' );
			}
		}
	}
}

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
 * If the model fails to load, display an error message
 *
 * @param {Event} event               - any - the event that is triggered when the model is loaded.
 * @param         event.detail
 * @param         event.detail.status
 */
export const throwErrorOnFail = ( event: Event ) => {
	if ( ( event as CustomEvent ).detail.status === 'failed' ) {
		const errorContainer =
			( document.getElementById( 'error' ) as HTMLElement ) || null;

		if ( errorContainer )
			logModelError(
				errorContainer,
				__(
					'An error occurred activating VR, please check your browser permissions then clean cookies!<br/>To verify that the device is correctly configured to run WebXR, browse to <a href="https://immersive-web.github.io/webxr-samples/immersive-ar-session.html">a sample WebXR page</a> in <a href="https://developer.mozilla.org/en-US/docs/Web/API/WebXR_Device_API#browser_compatibility">a compatible browser</a>.'
				)
			);
	}
};
