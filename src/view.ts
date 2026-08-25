import type { ModelViewerElement } from '@google/model-viewer';
import './style/style.scss';
import { strings } from './strings';
import {
	hasOverlayGallery,
	setOverlayState,
	showViewerError,
} from './viewer-ui';

type ViewerElement = ModelViewerElement & HTMLElement;
let modelViewerPromise: Promise< unknown > | undefined;

const loadModelViewer = () =>
	( modelViewerPromise ||= import( '@google/model-viewer' ) );

const motionIsReduced = () =>
	window.matchMedia?.( '(prefers-reduced-motion: reduce)' ).matches ?? false;

const prepareViewer = async (
	root: HTMLElement
): Promise< ViewerElement | null > => {
	const viewer = root.querySelector< ViewerElement >( 'model-viewer' );
	if ( ! viewer ) {
		return null;
	}
	try {
		await loadModelViewer();
		if ( ! viewer.getAttribute( 'src' ) ) {
			viewer.setAttribute( 'src', viewer.dataset.src || '' );
		}
		if ( viewer.dataset.vsgeControlsReady !== 'true' ) {
			viewer.autoRotate = ! motionIsReduced();
			root
				.querySelector< HTMLButtonElement >(
					'[data-vsge-action="rotate"]'
				)
				?.setAttribute( 'aria-pressed', String( viewer.autoRotate ) );
			viewer.dataset.vsgeControlsReady = 'true';
		}
		return viewer;
	} catch {
		showViewerError( root, strings.viewerLoadError );
		return null;
	}
};

const showDialog = ( root: HTMLElement, content: Node ) => {
	const dialog =
		root.querySelector< HTMLDialogElement >( '[data-vsge-dialog]' );
	const target = root.querySelector< HTMLElement >(
		'[data-vsge-dialog-content]'
	);
	if ( ! dialog || ! target ) {
		return;
	}
	target.replaceChildren( content );
	if ( ! dialog.open ) {
		dialog.showModal();
	}
};

const infoContent = () => {
	const wrapper = document.createElement( 'div' );
	const heading = document.createElement( 'h2' );
	heading.textContent = strings.instructionsTitle;
	const text = document.createElement( 'p' );
	text.textContent = strings.instructionsText;
	wrapper.append( heading, text );
	return wrapper;
};

const showQr = async ( root: HTMLElement ) => {
	const url = root.dataset.viewerUrl;
	if ( ! url ) {
		return;
	}
	const wrapper = document.createElement( 'div' );
	const heading = document.createElement( 'h2' );
	heading.textContent = strings.qrTitle;
	const text = document.createElement( 'p' );
	text.textContent = strings.qrText;
	const link = document.createElement( 'a' );
	link.href = url;
	link.textContent = url;
	const canvas = document.createElement( 'canvas' );
	wrapper.append( heading, text, canvas, link );
	showDialog( root, wrapper );
	try {
		const { default: QRCode } = await import( 'qrcode' );
		await QRCode.toCanvas( canvas, url );
	} catch {
		canvas.remove();
		text.textContent = strings.qrFallback;
	}
};

const initialiseRoot = ( root: HTMLElement ) => {
	root.classList.toggle( 'vsge-3d-overlay-mode', hasOverlayGallery( root ) );
	const viewer = root.querySelector< ViewerElement >( 'model-viewer' );
	if ( ! viewer ) {
		return;
	}
	viewer.addEventListener( 'progress', ( event: Event ) => {
		const progress =
			viewer.querySelector< HTMLProgressElement >( 'progress' );
		const detail = ( event as CustomEvent< { totalProgress?: number } > )
			.detail;
		if ( progress && typeof detail.totalProgress === 'number' ) {
			progress.value = detail.totalProgress * 100;
		}
	} );
	viewer.addEventListener( 'error', () =>
		showViewerError( root, strings.modelLoadError )
	);
	const launcher =
		root.querySelector< HTMLButtonElement >( '.vsge-launch-3d' );
	const stage = root.querySelector< HTMLElement >( '.vsge-3d-stage' );
	launcher?.addEventListener( 'click', async () => {
		if ( ! ( await prepareViewer( root ) ) ) {
			return;
		}
		setOverlayState( root, true );
		launcher.setAttribute( 'aria-expanded', 'true' );
		stage?.focus( { preventScroll: true } );
	} );
	root
		.querySelector< HTMLButtonElement >( '.vsge-return-to-images' )
		?.addEventListener( 'click', () => {
			setOverlayState( root, false );
			launcher?.setAttribute( 'aria-expanded', 'false' );
			launcher?.focus( { preventScroll: true } );
		} );
	root.querySelectorAll< HTMLButtonElement >( '[data-vsge-action]' ).forEach(
		( button ) =>
			button.addEventListener( 'click', async () => {
				const action = button.dataset.vsgeAction;
				if ( 'close-dialog' === action ) {
					root
						.querySelector< HTMLDialogElement >(
							'[data-vsge-dialog]'
						)
						?.close();
					return;
				}
				if ( 'info' === action ) {
					showDialog( root, infoContent() );
					return;
				}
				if ( 'ar' === action ) {
					const readyViewer = await prepareViewer( root );
					if ( readyViewer?.canActivateAR ) {
						readyViewer.activateAR();
					} else {
						await showQr( root );
					}
					return;
				}
				const readyViewer = await prepareViewer( root );
				if ( ! readyViewer ) {
					return;
				}
				if ( 'recenter' === action ) {
					readyViewer.cameraOrbit = 'auto auto auto';
					readyViewer.cameraTarget = 'auto auto auto';
					readyViewer.updateFraming();
				}
				if ( 'rotate' === action ) {
					readyViewer.autoRotate = ! readyViewer.autoRotate;
					button.setAttribute(
						'aria-pressed',
						String( readyViewer.autoRotate )
					);
				}
				if ( 'hotspots' === action ) {
					const visible =
						button.getAttribute( 'aria-pressed' ) !== 'false';
					readyViewer
						.querySelectorAll( '.vsge-hotspot' )
						.forEach( ( hotspot ) =>
							hotspot.toggleAttribute( 'hidden', visible )
						);
					button.setAttribute( 'aria-pressed', String( ! visible ) );
				}
			} )
	);
	if ( viewer.dataset.eager === 'true' ) {
		void prepareViewer( root );
	}
};

document.addEventListener( 'DOMContentLoaded', () =>
	document
		.querySelectorAll< HTMLElement >( '[data-vsge-viewer]' )
		.forEach( initialiseRoot )
);
