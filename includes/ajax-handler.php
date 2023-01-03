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

	function load_more_products() {

		$settings          = $_POST['settings'];
		$settings['number'] += $_POST['productsPerPage'];
		//error_log(print_r($settings));
		/*$products_per_page = $_POST['productsPerPage'];
		$query             = isset( $_POST['query'] ) ? $_POST['query'] : false;

		if ( $query ) {
			add_filter( 'pre_get_posts', 'add_query_args', 10, 1 );

			if ( isset( $settings['use_current_query'] ) && 'yes' === $settings['use_current_query'] ) {
				global $wp_query;

				$wp_query = new \WP_Query( $query );
			}
		} else {
			$settings['number'] += $products_per_page;
		}*/

		$shortcode = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );

		$shortcode->set_settings( $settings );

		$response['html'] = $shortcode->do_shortcode( $settings );

		wp_send_json_success( $response );

	}

}
