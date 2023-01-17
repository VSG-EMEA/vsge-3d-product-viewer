import '@google/model-viewer';
import { ModelViewerElement } from '@google/model-viewer';
import 'focus-visible';
import './style/style.scss';
export declare const getContainer: () => ModelViewerElement | null;
export declare let mvContainer: ModelViewerElement | null;
export declare const getModelID: (container: HTMLElement) => string | false;
export declare let modelID: string | false;
export declare const modelVR: HTMLElement | null;
export declare const getQrURL: (productID: string) => string | false;
export declare const on3dInit: (container: HTMLElement) => void;
/**
 * Product page only
 */
export declare const onProductInit: () => void;
//# sourceMappingURL=index.d.ts.map