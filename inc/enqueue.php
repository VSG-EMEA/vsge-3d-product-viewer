<?php

function vsge_frontend_scripts()
{

	if (!is_main_query() || in_the_loop() || !is_singular()) {
		return;
	}
    global $post;

    $model_3d = get_post_meta($post->ID, PLUGIN_NAMESPACE.'_media_3d_model', true);

    if (is_attachment() || $model_3d) {
        $asset = include PLUGIN_DIR . 'build/vsge-3d-product-viewer.asset.php';
        wp_enqueue_script('vsge-3d-product-viewer', PLUGIN_DIR.'build/vsge-3d-product-viewer.js', $asset['dependencies'], true );
        wp_localize_script('vsge-3d-product-viewer', 'wp', [
            'siteurl' => get_option('siteurl'),
        ]);
    }
}

function vsge_frontend_style()
{

	if (!is_main_query() || in_the_loop() || !is_singular()) {
		return;
	}

    // Register and Enqueue on a single product page
    wp_enqueue_style('vsge-3d-product-viewer-style', PLUGIN_DIR.'build/style-vsge-3d-product-viewer.css');
}
