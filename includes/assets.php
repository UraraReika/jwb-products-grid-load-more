<?php

namespace JWB_PGLM;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Assets {

	public function __construct() {
		// Enqueue styles and scripts.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue assets.
	 *
	 * Enqueue plugin style and script files.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return void
	 */
	function enqueue_assets() {

		wp_enqueue_script(
			'pglm_frontend',
			PGLM_PLUGIN_URL . 'assets/js/frontend.js',
			[ 'jquery' ],
			PGLM_PLUGIN_VERSION,
			true
		);

		wp_enqueue_style(
			'pglm_frontend',
			PGLM_PLUGIN_URL . 'assets/css/styles.css',
			[],
			PGLM_PLUGIN_VERSION
		);

	}

}