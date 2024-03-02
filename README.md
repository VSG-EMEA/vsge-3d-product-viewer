# VSGE 3D Product Viewer WordPress Plugin

## Overview

The VSGE 3D Product Viewer is a WordPress plugin that allows you to easily add a 3D model viewer to your WooCommerce product pages. Showcase your products in a more interactive way by enabling customers to view your products in 3D and even in Augmented Reality (AR).

## Features

- Upload and manage 3D models in the WordPress media library.
- Automatically adds a 3D viewer to WooCommerce product pages.
- Supports Augmented Reality (AR) on compatible devices.
- Customizable templates for displaying 3D models.

## Installation

1. Download the plugin ZIP file from the [GitHub repository](https://github.com/erikyo/vsge-3d-product-viewer).
2. Upload and activate the plugin through the WordPress admin interface.

## Usage

### Uploading 3D Models

1. Go to the WordPress Media Library.
2. Upload your 3D models (in GLB format).
3. Assign 3D models to your WooCommerce products.

### Displaying 3D Models

To enable the plugin for a product, follow these steps:

** Manual Add **
- add a post meta to the product named "brb_media_3d_model" with the id of the 3D model in the media library
- add a post meta to the product named "brb_media_3d_model" with the id of the 3D model preview in the media library (optional, accepted images are .png, .jpg, .jpeg, .gif, and .svg)

** BRB Importer **
- in our importer in order to add the model to the product we need to register the 3D model in this way:
- 
```json
{
	"media": {
		"3d_model": [
			"3dmodel.glb"
		],
		"3d_model_preview": [
			"preview.png"
		],
		"3d_model_data": {
			"camera-orbit": "65deg 90deg 25m",
			"camera-target": "0m 1m 0m",
			"hotspots": [
				{
					"slot": "pin3",
					"position": "0 0.75 0.75",
					"title": "Pin 1"
				},
				{
					"slot": "pin3",
					"position": "0.01 1.25 -1",
					"title": "Pin 2",
					"href": "https://url"
				},
				{
					"slot": "pin3",
					"position": "0.62 0.05 0.58",
					"title": "Pin 3",
          "href": "https://url"
				}
			]
		}
	}
}
```

#### Automatic Display (WooCommerce Product Pages)

- The plugin automatically adds a 3D viewer to WooCommerce product pages if a 3D model is assigned overlaying the product images gallery using the hook `do_action( 'woocommerce_after_product-gallery__wrapper' );`.

#### Custom Display (Shortcode)

- Use the `[vsge_3d_model]` shortcode to manually display the 3D model viewer on any page or post classic editor or Gutenberg.
- Use the block `vsge/3d_model` to manually display the 3D model viewer on any page or post using the block editor.

## Configuration

- Configure 3D model settings in the plugin settings page in the WordPress admin.

## Screenshots


## Frequently Asked Questions (FAQ)

- **Q:** Can I use this plugin with any theme?
    - **A:** The plugin is designed to work with most themes, but some custom themes may require additional adjustments.

## Contributing

Contributions are welcome! If you find a bug or have a feature request, please open an issue or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
