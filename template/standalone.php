<?php
/** @var int $vsge_3d_product_id */
$product_id = isset( $GLOBALS['vsge_3d_product_id'] ) ? absint( $GLOBALS['vsge_3d_product_id'] ) : 0;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'model3d' ); ?>>
<?php wp_body_open(); ?>
<main class="vsge-3d-standalone">
	<?php echo \Vsge3DProductViewer\Renderer::render( $product_id, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
