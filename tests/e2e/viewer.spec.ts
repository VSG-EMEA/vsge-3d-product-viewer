import { expect, test } from '@wordpress/e2e-test-utils-playwright';

test( 'the active plugin is visible in WordPress administration', async ( {
	admin,
	page,
} ) => {
	await admin.visitAdminPage( 'plugins.php' );
	await expect(
		page.getByText( 'VSGE 3d product viewer', { exact: false } )
	).toBeVisible();
} );

// Product/media fixtures are intentionally a separate helper concern: this test environment does not
// own Importer media writes. A fixture product can populate the three persistent contracts and then
// assert gallery switching, standalone routing, dynamic block rendering, and shortcode output.
