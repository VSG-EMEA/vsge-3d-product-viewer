<?php
/**
 * the classic editor view for the 3D model page
 */
function vsge_3d_model_classic_render() {
	get_header();

	echo VSGE_MV_PLUGIN_URL . '/template/single-3d-model.php';

	get_footer();
}
