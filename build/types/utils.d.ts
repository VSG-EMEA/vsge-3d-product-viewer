export declare const tapDistance: number;
export declare let panning: boolean;
export declare let panX: number[], panY: number[];
export declare let startX: number, startY: number;
export declare let metersPerPixel: number;
export declare const errorContainer: HTMLElement;
export declare const buttonArInit: HTMLElement;
export declare const button3dRecenter: HTMLElement;
export declare const button3dRotation: HTMLElement;
export declare const modalToggleHotspot: HTMLElement;
export declare const modalGenerateQR: HTMLElement;
export declare const arButton: HTMLElement;
export declare const onInteraction: () => void;
/**
 * "If the canvas and modelID exist, then create a QR code and return the URL."
 *
 * @param {HTMLElement} canvas - The canvas element to render the QR code to.
 * @param {string}      id     - The attachment ID of the model.
 * @return A function that takes two arguments, canvas and id.
 */
export declare const getQRCode: (canvas: HTMLElement, id: string) => string | false;
/**
 * It takes an event, finds the progress bar, and updates the progress bar's value
 *
 * @param {any} event - any
 */
export declare const onProgress: (event: any) => void;
/**
 * It takes a string as an argument, and then it displays that string in the error container
 *
 * @param {string} errorMessage - The error message to display.
 */
export declare const logModelError: (errorMessage: string) => void;
/**
 * It starts the rotation of the model.
 */
export declare const startRotate: () => void;
/**
 * It stops the rotation of the 3d model viewer
 */
export declare const stopRotate: () => void;
/**
 * If the 3D rotation button is active, start or stop the rotation
 *
 * @param {boolean} [start=true] - boolean = true
 */
export declare const setRotate: (start?: boolean) => void;
/**
 * If the button is active, stop the rotation, otherwise start the rotation
 *
 * @param {Event} event - Event - The event object.
 */
export declare const setRotation: (event: Event) => void;
/**
 * It sets up the panning
 * vectors and the meters per pixel scale factor
 */
export declare const startPan: () => void;
/**
 * It resets the camera to its initial position
 */
export declare const centerView: () => void;
/**
 * If the user has not moved the mouse more than a certain distance from the starting point, then
 * recenter the camera on the point where the user clicked
 *
 * @param {Object} pointer         - The pointer object from the event.
 * @param          pointer.clientX
 * @param          pointer.clientY
 * @return {void}
 */
export declare const recenter: (pointer: {
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
 */
export declare const toggleHotspot: () => void;
/**
 * It sets the center of the view to the center of the canvas, and then recenters the view to the
 * center of the canvas
 *
 * @param {Event} event - The event that triggered the action.
 */
export declare const setRecenter: (event: Event) => void;
/**
 * If the mvContainer can activate AR, then activate AR and show the hotspots. Otherwise, show the QR
 * code modal
 *
 * @param {Event} event - Event - The event object that was triggered by the user.
 */
export declare const arInitialize: (event: Event) => void;
/**
 * It removes the class 'active' from the modalGenerateQR element
 *
 * @param {Event} event - Event - This is the event that is being passed to the function.
 */
export declare const deactivateQR: (event: Event) => void;
//# sourceMappingURL=utils.d.ts.map