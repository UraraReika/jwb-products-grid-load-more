<?php

namespace JWB_PGLM;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Assets {

	public function __construct() {
		// Enqueue styles and scripts.
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue plugin scripts files.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return void
	 */
	function enqueue_scripts() {
		wp_enqueue_script(
			'pglm_main',
			PGLM_PLUGIN_URL . 'assets/js/main.js',
			[ 'jquery', 'elementor-frontend' ],
			PGLM_PLUGIN_VERSION,
			true
		);
	}

	/**
	 * Enqueue styles.
	 *
	 * Enqueue plugin styles files.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return void
	 */
	function enqueue_styles() {
		wp_enqueue_style(
			'pglm_styles',
			PGLM_PLUGIN_URL . 'assets/css/styles.css',
			[],
			PGLM_PLUGIN_VERSION
		);
	}

}