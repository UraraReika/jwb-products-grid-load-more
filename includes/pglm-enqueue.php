<?php

/**
 * Enqueue main script file.
 */
function pglm_enqueue_scripts() {

	wp_register_script(
		'pglm_main',
		plugins_url( 'assets/js/main.js', PGLM_PLUGIN_URL ),
		[ 'jquery', 'elementor-frontend' ],
		'1.0.0',
		true
	);

	wp_enqueue_script( 'pglm_main' );

}
