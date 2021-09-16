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

/**
 * Enqueue main style file.
 */
function pglm_enqueue_styles() {

	wp_register_style( 'pglm_styles', plugins_url( 'assets/css/styles.css', PGLM_PLUGIN_URL ) );

	wp_enqueue_style( 'pglm_styles' );

}
