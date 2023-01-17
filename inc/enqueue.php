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
        wp_enqueue_script('model-viewer', 'https://unpkg.com/@google/model-viewer@2.1.1/dist/model-viewer.min.js', false, true);
        wp_enqueue_script('vsge-3d-product-viewer', PLUGIN_DIR.'build/vsge-3d-product-viewer.js', 'model-viewer', true);
        wp_localize_script('vsge-3d-product-viewer', 'wp', [
            'siteurl' => get_option('siteurl'),
        ]);
    }
}

function add_type_attribute($tag, $handle, $src)
{
    // if not your script, do nothing and return original $tag
    if ('model-viewer' !== $handle) {
        return $tag;
    }
    // change the script tag by adding type="module" and return it.
    $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    return $tag;
}
add_filter('script_loader_tag', 'add_type_attribute', 10, 3);

function vsge_frontend_style()
{

    if (!is_main_query() || in_the_loop() || !is_singular()) {
        return;
    }

    // Register and Enqueue on a single product page
    wp_enqueue_style('vsge-3d-product-viewer-style', PLUGIN_DIR.'build/style-vsge-3d-product-viewer.css');
}
