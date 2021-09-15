<?php

/**
 * Set custom load more functionality attributes for Products Grid widget.
 *
 * @param $attributes
 * @param $settings
 *
 * @return string
 */
function pglm_get_widget_attributes( $attributes, $settings ) {

	$load_more        = isset( $settings['enable_load_more'] ) ? filter_var( $settings['enable_load_more'], FILTER_VALIDATE_BOOLEAN ) : false;
	$carousel_enabled = isset( $settings['carousel_enabled'] ) ? filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;

	if ( ! $load_more ) {
		return $attributes;
	}

	if ( $carousel_enabled ) {
		return $attributes;
	}

	$shortcode        = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );
	$attrs            = [];
	$default_settings = [];

	if ( isset( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] && isset( $_REQUEST['settings'] ) ) {
		$default_settings = $_REQUEST['settings'];
	}

	if ( isset( $_REQUEST['action'] ) && 'jet_woo_builder_load_more' === $_REQUEST['action'] && isset( $_REQUEST['settings'] ) ) {
		$default_settings = $_REQUEST['settings'];
	}

	global $pglm_object, $stored_settings;

	if ( $pglm_object ) {
		foreach ( $stored_settings as $key ) {
			if ( false !== strpos( $key, 'selected_' ) ) {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? htmlspecialchars( $pglm_object->__render_icon( str_replace( 'selected_', '', $key ), '%s', '', false ) ) : '';
			} else {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			}
		}

		// Compatibility with compare and wishlist plugin.
		$default_settings['_widget_id'] = $pglm_object->get_id();
	}

	foreach ( $shortcode->get_atts() as $attr => $data ) {
		$attr_val       = $settings[ $attr ];
		$attr_val       = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
		$attrs[ $attr ] = $attr_val;
	}

	$attributes .= sprintf( ' data-load-more-settings="%s" ', htmlspecialchars( json_encode( array_merge( $default_settings, $attrs ) ) ) );

	return $attributes;

}

/**
 * Set global widget variable.
 *
 * @param $widget
 */
function pglm_store_default_widget_object( $widget ) {

	if ( 'jet-woo-products' !== $widget->get_name() ) {
		return;
	}

	global $pglm_object;

	$settings  = $widget->get_settings();
	$load_more = isset( $settings['enable_load_more'] ) ? filter_var( $settings['enable_load_more'], FILTER_VALIDATE_BOOLEAN ) : false;

	if ( $load_more ) {
		$pglm_object = $widget;
	}

}

/**
 * Set load more settings for JetSmartFilter and global variable.
 *
 * @param $list
 *
 * @return array
 */
function pglm_set_widget_setting_to_store( $list ) {

	$custom_icon_settings = [
		'enable_load_more',
		'load_more_type',
		'load_more_trigger_id',
	];

	global $stored_settings;

	$stored_settings = array_merge( $list, $custom_icon_settings );

	return $stored_settings;

}
