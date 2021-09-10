<?php

function pglm_load_more_products() {

	$settings = $_POST['settings'];
	$attributes = [];
	$settings['number'] += 4;

	$shortcode = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );

	$shortcode->set_settings( $settings );

	foreach ( $shortcode->get_atts() as $attr => $data ) {
		$attr_val            = $settings[ $attr ];
		$attr_val            = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
		$attributes[ $attr ] = $attr_val;
	}

	$html = '<div class="pglm-settings-holder" data-load-more-settings="' . htmlspecialchars( json_encode( $attributes ) ) . '">' . $shortcode->do_shortcode( $settings ) . '</div>';

	$response['html'] = $html;

	wp_send_json_success( $response );

}
