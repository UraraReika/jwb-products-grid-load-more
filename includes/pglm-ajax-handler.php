<?php

/**
 * Handle ajax request.
 */
function pglm_load_more_products() {

	$settings           = $_POST['settings'];
	$settings['number'] += 4;

	$shortcode = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );

	$shortcode->set_settings( $settings );

	$response['html'] = $shortcode->do_shortcode( $settings );

	wp_send_json_success( $response );

}
