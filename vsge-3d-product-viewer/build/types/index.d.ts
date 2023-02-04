import { ModelViewerElement } from '@google/model-viewer';
import 'focus-visible';
import './style/style.scss';
/**
 * "If the container exists, return the model viewer element inside it, otherwise return null."
 *
 * The first line of the function is a type annotation. It tells TypeScript that the function will
 * return a ModelViewerElement or null
 *
 * @param {HTMLElement | null} container - HTMLElement | null
 * @return A model viewer element.
 */
export declare const getModelViewer: (container: HTMLElement | null) => ModelViewerElement | null;
/**
 * It returns a QR code URL for the current VR model
 *
 * @param {string | null} productID - The product ID of the product you want to generate a QR code for.
 * @return A QR code that is generated from the modelVR element and the productID.
 */
export declare const getQrURL: (productID: string | null) => boolean | undefined;
/**
 * "Get the model ID from the container element."
 *
 * The function is a pure function, meaning it doesn't have any side effects. It doesn't modify the
 * container element, and it doesn't modify any other part of the application. It just returns a value
 *
 * @param {HTMLElement} container - HTMLElement - The container element that contains the model.
 */
export declare const getModelID: (container: HTMLElement) => string | null;
/**
 * It adds event listeners to the model viewer element, and to the document body
 *
 * @param {ModelViewerElement} mvContainer - The model viewer element.
 */
export declare const initModelViewer: (mvContainer: ModelViewerElement) => void;
/**
 * Product page only
 *
 * @param {ModelViewerElement} mvContainer - The model-viewer element.
 */
export declare const onProductInit: (mvContainer: ModelViewerElement) => void;
//# sourceMappingURL=index.d.ts.map