import { __ } from '@wordpress/i18n';

export const strings = {
	modelLoadError: __(
		'The 3D model could not be loaded.',
		'vsge-3d-product-viewer'
	),
	viewerLoadError: __(
		'The 3D viewer could not be loaded. Please try again.',
		'vsge-3d-product-viewer'
	),
	instructionsTitle: __( '3D viewer instructions', 'vsge-3d-product-viewer' ),
	instructionsText: __(
		'Drag to rotate the model, use the mouse wheel or pinch gesture to zoom, and use the controls to recenter or open augmented reality.',
		'vsge-3d-product-viewer'
	),
	instructionsOverview: __(
		'Use the right mouse button to move the model.',
		'vsge-3d-product-viewer'
	),
	instructionsRotation: __(
		'Drag to change the viewing angle.',
		'vsge-3d-product-viewer'
	),
	instructionsZoom: __(
		'Use the mouse wheel or pinch gesture to zoom.',
		'vsge-3d-product-viewer'
	),
	qrTitle: __( 'View in augmented reality', 'vsge-3d-product-viewer' ),
	qrText: __(
		'Scan this QR code on a compatible mobile device.',
		'vsge-3d-product-viewer'
	),
	qrFallback: __(
		'Open this link on a compatible mobile device to view the model in augmented reality.',
		'vsge-3d-product-viewer'
	),
};
