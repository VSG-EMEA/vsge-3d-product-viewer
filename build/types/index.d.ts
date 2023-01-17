import { ModelViewerElement } from '@google/model-viewer';
import 'focus-visible';
import './style/style.scss';
export declare const getModelViewer: (container: HTMLElement | null) => ModelViewerElement | null;
export declare const getQrURL: (productID: string | null) => boolean | undefined;
export declare const getModelID: (container: HTMLElement) => string | null;
export declare const initModelViewer: (mvContainer: ModelViewerElement) => void;
/**
 * Product page only
 *
 * @param  mvContainer
 */
export declare const onProductInit: (mvContainer: ModelViewerElement) => void;
//# sourceMappingURL=index.d.ts.map