<?php

namespace JWB_PGLM;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Plugin {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since  1.2.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Plugin constructor.
	 *
	 * Initializing JWB Products Grid Load More plugin.
	 *
	 * @since  1.2.0
	 * @access private
	 */
	private function __construct() {
		add_action( 'init', [ $this, 'init' ], -999 );
	}

	/**
	 * Init.
	 *
	 * Initialize JWB Products Grid Load More plugin. Initialize JWB Products Grid Load More components.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function init() {
		$this->init_components();
	}

	/**
	 * Init components.
	 *
	 * Initialize JWB Products Grid Load More components. Initialize all the components that run
	 * JWB Products Grid Load More.
	 *
	 * @since  1.2.0
	 * @access private
	 */
	private function init_components() {

		require PGLM_PLUGIN_PATH . 'includes/ajax-handler.php';
		require PGLM_PLUGIN_PATH . 'includes/assets.php';
		require PGLM_PLUGIN_PATH . 'includes/integration.php';

		new Ajax_Handler();
		new Assets();
		new Integration();

	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since  1.2.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}

Plugin::instance();