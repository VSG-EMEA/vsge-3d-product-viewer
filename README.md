# VSGE 3D Product Viewer

Standalone WooCommerce 3D and AR viewing for VSGE products. The plugin reads media written by VSGE Products Importer; it does not acquire, migrate, or modify product media.

## Requirements

- WordPress and WooCommerce with a `product` post type.
- A GLB attachment assigned through the importer contract below.
- Node 20.19+ for development tooling.

## Data contract

The persisted contracts are intentionally unchanged:

- The **product** owns `brb_media_3d_model`, containing the GLB attachment ID.
- That **model attachment** canonically owns `brb_media_3d_model_preview`, containing an image attachment ID.
- That **model attachment** canonically owns `brb_media_3d_model_data`, containing JSON camera and hotspot configuration.

The viewer reads preview/configuration from the product only as a read-only fallback for older site data. It never migrates metadata.

Example data JSON:

```json
{
  "camera-orbit": "65deg 90deg 25m",
  "camera-target": "0m 1m 0m",
  "hotspots": [
    { "slot": "lift-arm", "position": "0 0.75 0.75", "title": "Lift arm" },
    { "slot": "control", "position": "0.01 1.25 -1", "title": "Controls", "href": "https://example.com" }
  ]
}
```

Malformed camera or hotspot values are omitted safely at render time.

## Display methods

- Dynamic overlay block: `vsge/3d-model`.
- Shortcode: `[vsge_3d_model]`, optionally `[vsge_3d_model product_id="123"]`.
- Standalone page: `/model3d=product-slug/` or `/model3d=123/`. Existing slashless links continue through the rewrite rule.

The overlay block is the primary FSE integration. It does not inject itself into, replace, clone, or manage WooCommerce Product Gallery. Product pages load the small controller only, then load `<model-viewer>` and the GLB after the visitor selects **View in 3D**. The standalone page loads its model immediately.

## WooCommerce Product Gallery integration

Wrap WooCommerce's Product Gallery and the VSGE block in a `vsge-product-media` group. The block becomes an overlay only in this wrapper; outside it, the launcher opens a usable in-flow viewer.

```html
<!-- wp:group {"className":"vsge-product-media"} -->
<div class="wp-block-group vsge-product-media">

    <!-- wp:woocommerce/product-gallery {"hoverZoom":false,"fullScreenOnClick":false,"layout":{"type":"flex","flexWrap":"nowrap","orientation":"horizontal","verticalAlignment":"bottom"}} -->
    <div class="wp-block-woocommerce-product-gallery wc-block-product-gallery">
        <!-- wp:woocommerce/product-gallery-thumbnails /-->
        <!-- wp:woocommerce/product-gallery-large-image -->
        <div class="wp-block-woocommerce-product-gallery-large-image wc-block-product-gallery-large-image__inner-blocks">
            <!-- wp:woocommerce/product-image {"showProductLink":false,"showSaleBadge":false} -->
            <div class="is-loading"></div>
            <!-- /wp:woocommerce/product-image -->
            <!-- wp:woocommerce/product-sale-badge /-->
            <!-- wp:woocommerce/product-gallery-large-image-next-previous /-->
        </div>
        <!-- /wp:woocommerce/product-gallery-large-image -->
    </div>
    <!-- /wp:woocommerce/product-gallery -->

    <!-- wp:vsge/3d-model /-->
</div>
<!-- /wp:group -->
```

When 3D opens, the Woo gallery remains mounted and its original `inert`/`aria-hidden` values are restored on return. WooCommerce remains the sole owner of images, thumbnails, navigation, zoom, lightbox, and variation image state. Automatic classic-gallery injection is intentionally deprecated; shortcode and standalone-viewer compatibility remain.

## AR and accessibility

AR modes are delegated to model-viewer in this order: WebXR, Scene Viewer, then Quick Look. There is no user-agent sniffing and AR is only invoked after the visitor presses an AR control. Controls expose labels and state; the QR/instructions use a native dialog and QR generation is deferred until requested.

## Development

```text
npm ci
npm run build
npm run lint:js
npm run lint:css
npm run lint:pkg
npm run test:unit
npm run test:e2e
php tests/php/smoke.php
```

`test:e2e` uses WordPress Playwright tooling and `wp-env`. The supplied test validates activation; a product/media fixture should write the three contracts above before asserting gallery switching, standalone output, block rendering, and shortcode output.

## License

LICENSE DECISION REQUIRED: the repository’s `LICENSE.md` contains GPLv3 text while the existing plugin/package metadata states `GPL-2.0-or-later`. The repository history available in this checkout is not sufficient to resolve ownership or relicensing. No license grant was changed by this modernization.
