import { initialiseRoot } from '../../src/view';
import { setOverlayState } from '../../src/viewer-ui';

describe( 'viewer loading lifecycle', () => {
	const root = () =>
		document.querySelector< HTMLElement >(
			'[data-vsge-viewer]'
		) as HTMLElement;
	const viewer = () =>
		root().querySelector< HTMLElement >( 'model-viewer' ) as HTMLElement;
	const loading = () =>
		root().querySelector< HTMLElement >( '.vsge-loading' ) as HTMLElement;

	beforeEach( () => {
		document.body.innerHTML =
			'<section data-vsge-viewer><div class="vsge-3d-stage" hidden></div><model-viewer data-src="https://example.test/model.glb"><div class="vsge-loading" hidden><progress value="0" max="100"></progress></div></model-viewer><p class="vsge-model-error" hidden></p></section>';
		initialiseRoot( root() );
	} );

	it( 'shows progress only while loading and clears it on progress completion or load', () => {
		viewer().dispatchEvent(
			new CustomEvent( 'progress', { detail: { totalProgress: 0.4 } } )
		);
		expect( loading().hidden ).toBe( false );
		expect(
			root().querySelector< HTMLProgressElement >( 'progress' )?.value
		).toBe( 40 );

		viewer().dispatchEvent(
			new CustomEvent( 'progress', { detail: { totalProgress: 1 } } )
		);
		expect( loading().hidden ).toBe( true );

		setOverlayState( root(), true );
		setOverlayState( root(), false );
		setOverlayState( root(), true );
		expect( loading().hidden ).toBe( true );

		viewer().dispatchEvent( new Event( 'load' ) );
		expect( loading().hidden ).toBe( true );
	} );

	it( 'clears the loading card before exposing the existing error state', () => {
		viewer().dispatchEvent(
			new CustomEvent( 'progress', { detail: { totalProgress: 0.2 } } )
		);
		viewer().dispatchEvent( new Event( 'error' ) );

		expect( loading().hidden ).toBe( true );
		expect(
			root().querySelector< HTMLElement >( '.vsge-model-error' )?.hidden
		).toBe( false );
	} );
} );
