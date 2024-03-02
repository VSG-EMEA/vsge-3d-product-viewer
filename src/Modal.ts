type Modal = {
	name: string;
	el: HTMLElement | null;
	displayed: boolean;
};

/**
 * It Handles the modals
 */
export class Modals {
	wrapper: HTMLElement | null = null;
	actives: Modal[] = [];
	constructor( mainModal: HTMLElement | null ) {
		this.wrapper = mainModal;
		this.init( mainModal );
	}

	init( mainModal: HTMLElement | null ) {
		mainModal?.querySelectorAll( '.modal' ).forEach( ( el ) => {
			this.actives.push( {
				name: ( el as HTMLElement )?.title as string,
				el: el as HTMLElement,
				displayed: false,
			} );
		} );
	}

	find( name: string ) {
		return this.actives.find( ( m: Modal ) => m.name === name ) as Modal;
	}

	show( modal: Modal | string ) {
		if ( typeof modal === 'string' ) {
			modal = this.find( modal ) as Modal;
		}
		this.wrapper?.classList.add( 'active' );
		modal.el?.classList.add( 'active' );

		// @ts-ignore
		modal.el.addEventListener( 'click', () => {
			this.hide( modal );
		} );
	}

	hide( modal: Modal | string ) {
		if ( typeof modal === 'string' ) {
			modal = this.find( modal );
		}
		this.wrapper?.classList.remove( 'active' );
		modal.el?.classList.remove( 'active' );
	}

	hideAll() {
		this.actives.forEach( ( modal ) => {
			this.hide( modal );
		} );
		this.wrapper?.classList.remove( 'active' );
	}
}
