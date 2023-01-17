import { ModelViewerElement } from '@google/model-viewer';
export declare const tapDistance: number;
export declare let panning: boolean;
export declare let panX: number[], panY: number[];
export declare let startX: number, startY: number;
export declare let metersPerPixel: number;
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
/**
 * It takes a string as an argument, and then it displays that string in the error container
 *
 * @param          container
 * @param {string} errorMessage - The error message to display.
 */
export declare const logModelError: (container: HTMLElement, errorMessage: string) => void;
/**
 * the rotation button is used to determine whether the model is autoRotating or not,
 * if it has the classname "active" if the model is autoRotating
 *
 * @param  button
 */
export declare const getAutoRotation: (button: HTMLElement | null) => boolean;
/**
 * It starts the rotation of the model.
 *
 * @param  container
 */
export declare const startRotate: (container: ModelViewerElement) => void;
/**
 * It stops the rotation of the 3d model viewer
 *
 * @param  container
 */
export declare const stopRotate: (container: ModelViewerElement) => void;
/**
 * If the 3D rotation isn't active, start the rotation
 *
 * @param  button3dRotation
 * @param  mvContainer
 */
export declare const enableAutoRotate: (button3dRotation: HTMLElement | null, mvContainer: ModelViewerElement) => void;
/**
 * If the button is active, stop the rotation, otherwise start the rotation
 *
 * @param  event     - Event - The event object.
 * @param  container
 */
export declare const setAutoRotation: (event: Event, container: ModelViewerElement) => void;
/**
 * It sets up the panning
 * vectors and the meters per pixel scale factor
 *
 * @param  container
 */
export declare const startPan: (container: ModelViewerElement) => void;
/**
 * It resets the camera to its initial position
 *
 * @param  container
 */
export declare const centerView: (container: ModelViewerElement) => void;
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
export declare const recenter: (container: ModelViewerElement, pointer?: {
    clientX: number;
    clientY: number;
}) => void;
/**
 * If the model fails to load, display an error message
 *
 * @param {any} event - any - the event that is triggered when the model is loaded.
 */
export declare const throwErrorOnLoad: (event: any) => void;
/**
 * It takes a NodeList of elements and a boolean, and then adds or removes a class from each element in
 * the NodeList depending on the boolean
 *
 * @param           hotspots - NodeListOf< Element > - this is the list of hotspots that we want to toggle the
 *                           visibility of.
 * @param {boolean} isActive - boolean - This is a boolean value that determines whether the hotspots
 *                           should be visible or not.
 */
export declare const toggleHotspotVisibility: (hotspots: NodeListOf<Element>, isActive: boolean) => void;
/**
 * It toggles the visibility of all hotspots in the current panorama
 *
 * @param {Event}              event     - The event that triggered the action.
 * @param {ModelViewerElement} container
 */
export declare const toggleHotspot: (event: Event, container: ModelViewerElement) => void;
/**
 * It sets the center of the view to the center of the canvas, and then recenters the view to the
 * center of the canvas
 *
 * @param {Event}              event     - The event that triggered the action.
 * @param {ModelViewerElement} container
 */
export declare const setRecenter: (event: Event, container: ModelViewerElement) => void;
/**
 * If the mvContainer can activate AR, then activate AR and show the hotspots. Otherwise, show the QR
 * code modal
 *
 * @param {Event} event     - Event - The event object that was triggered by the user.
 * @param         container
 */
export declare const arInitialize: (event: Event, container: ModelViewerElement) => void;
/**
 * It removes the class 'active' from the modalGenerateQR element
 *
 * @param {Event} event - Event - This is the event that is being passed to the function.
 */
export declare const deactivateQR: (event: Event) => void;
//# sourceMappingURL=utils.d.ts.map