<?php

/**
 * Handle ajax request.
 */
function pglm_load_more_products() {

	$settings          = $_POST['settings'];
	$products_per_page = $_POST['productsPerPage'];
	$query             = isset( $_POST['query'] ) ? $_POST['query'] : false;

	if ( $query ) {
		add_filter( 'pre_get_posts', 'add_query_args', 10 );
	} else {
		$settings['number'] += $products_per_page;
	}

	$shortcode = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );

	$shortcode->set_settings( $settings );

	$response['html'] = $shortcode->do_shortcode( $settings );

	wp_send_json_success( $response );

}

/**
 * Handle query request.
 *
 * @param $query
 */
function add_query_args( $query ) {

	$pglm_query             = $_REQUEST['query'];
	$pglm_products_per_page = $_REQUEST['productsPerPage'];

	foreach ( $pglm_query as $query_var => $value ) {
		if ( in_array( $query_var, [ 'tax_query', 'meta_query' ] ) ) {
			$current = $query->get( $query_var );

			if ( ! empty( $current ) ) {
				$value = array_merge( $current, $value );
			}

			$query->set( $query_var, $value );
		} else {
			$query->set( $query_var, $value );
		}
	}

	$posts_per_page = $query->get( 'posts_per_page' );
	$posts_per_page += $pglm_products_per_page;

	$query->set( 'posts_per_page', $posts_per_page );

}
