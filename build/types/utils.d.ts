import { ModelViewerElement } from '@google/model-viewer';
/**
 * It clicks the AR button
 */
export declare const onInteraction: () => void;
/**
 * "If the canvas and modelID exist, then create a QR code and return the URL."
 *
 * @param {HTMLElement} canvas - The canvas element to render the QR code to.
 * @param {string}      id     - The attachment ID of the model.
 * @return {boolean} A function that takes two arguments, canvas and id.
 */
export declare function getQRCode(canvas: HTMLElement, id: string): boolean;
/**
 * It takes an event, finds the progress bar, and updates the progress bar's value
 *
 * @param {any} event - any
 */
export declare const onProgress: (event: any) => void;
export declare function attachmentPageInit(event: any, container: ModelViewerElement): void;
/**
 * It takes a string as an argument, and then it displays that string in the error container
 *
 * @param          container
 * @param {string} errorMessage - The error message to display.
 */
export declare const logModelError: (container: HTMLElement, errorMessage: string) => void;
/**
 * If the model fails to load, display an error message
 *
 * @param {Event} event               - any - the event that is triggered when the model is loaded.
 * @param         event.detail
 * @param         event.detail.status
 */
export declare const throwErrorOnFail: (event: Event) => void;
/**
 * It removes the class 'active' from the modalGenerateQR element
 *
 * @param {Event} event - Event - This is the event that is being passed to the function.
 */
export declare const deactivateQR: (event: Event) => void;
//# sourceMappingURL=utils.d.ts.map