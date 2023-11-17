module.exports = ( api ) => {
	api.cache( true );

	return {
		presets: [
			'@babel/preset-typescript',
			'@wordpress/babel-preset-default',
		],
		plugins: [
			[
				'@wordpress/babel-plugin-makepot',
				{ output: 'languages/vsge-3d-product-viewer.pot' },
			],
		],
	};
};
