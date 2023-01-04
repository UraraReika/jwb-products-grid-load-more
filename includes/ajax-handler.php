<?php

namespace JWB_PGLM;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ajax_Handler {

	public function __construct() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_jet_woo_builder_load_more', [ $this, 'load_more_products' ] );
			add_action( 'wp_ajax_nopriv_jet_woo_builder_load_more', [ $this, 'load_more_products' ] );
		}
	}

	/**
	 * Load more products.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function load_more_products() {

		$settings = $_POST['settings'] ?? [];
		$query    = $_POST['query'] ?? [];

		if ( ! empty( $query ) ) {
			add_filter( 'pre_get_posts', [ $this, 'add_request_query_args' ] );

			if ( isset( $settings['use_current_query'] ) && filter_var( $settings['use_current_query'], FILTER_VALIDATE_BOOLEAN ) ) {
				global $wp_query;
				$wp_query = new \WP_Query( $query );
			}
		} else {
			$settings['number'] += $_POST['per_page'] ?? 4;
		}

		$shortcode = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );

		$shortcode->set_settings( $settings );

		$response['html'] = $shortcode->do_shortcode( $settings );

		wp_send_json_success( $response );

	}

	/**
	 * Add request query args.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param object $query WP query instance.
	 *
	 * @return void
	 */
	public function add_request_query_args( $query ) {

		$request_query = $_REQUEST['query'] ?? [];

		if ( empty( $request_query ) ) {
			return;
		}

		foreach ( $request_query as $query_var => $value ) {
			$query->set( $query_var, $value );
		}

		$query->set( 'posts_per_page', $query->get( 'posts_per_page' ) + $_REQUEST['per_page'] ?? 4 );

	}

}
