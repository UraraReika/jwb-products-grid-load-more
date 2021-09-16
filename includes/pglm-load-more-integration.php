<?php

/**
 * Set custom load more functionality attributes for Products Grid widget.
 *
 * @param $attributes
 * @param $settings
 *
 * @return string
 */
function pglm_get_widget_attributes( $attributes, $settings, $shortcode ) {

	$load_more        = isset( $settings['enable_load_more'] ) ? filter_var( $settings['enable_load_more'], FILTER_VALIDATE_BOOLEAN ) : false;
	$carousel_enabled = isset( $settings['carousel_enabled'] ) ? filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;

	if ( ! $load_more ) {
		return $attributes;
	}

	if ( $carousel_enabled ) {
		return $attributes;
	}

	$attrs             = [];
	$default_settings  = [];
	$pglm_query        = null;
	$products_per_page = null;

	if ( isset( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] && isset( $_REQUEST['settings'] ) ) {
		$default_settings  = $_REQUEST['settings'];
		$pglm_query        = jet_smart_filters()->query->get_query_args();
		$products_per_page = $pglm_query['posts_per_page'];
	}

	if ( isset( $_REQUEST['action'] ) && 'jet_woo_builder_load_more' === $_REQUEST['action'] && isset( $_REQUEST['settings'] ) ) {
		$default_settings  = $_REQUEST['settings'];
		$pglm_query        = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : false;
		$products_per_page = $_REQUEST['productsPerPage'];

		if ( $pglm_query ) {
			$pglm_query['posts_per_page'] += $products_per_page;
		}
	}

	global $pglm_object, $pglm_stored_settings;

	if ( $pglm_object ) {
		foreach ( $pglm_stored_settings as $key ) {
			if ( false !== strpos( $key, 'selected_' ) ) {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? htmlspecialchars( $pglm_object->__render_icon( str_replace( 'selected_', '', $key ), '%s', '', false ) ) : '';
			} else {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			}
		}

		// Compatibility with compare and wishlist plugin.
		$default_settings['_widget_id'] = $pglm_object->get_id();
		$products_per_page              = $settings['number'];
	}

	foreach ( $shortcode->get_atts() as $attr => $data ) {
		$attr_val       = $settings[ $attr ];
		$attr_val       = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
		$attrs[ $attr ] = $attr_val;
	}

	$attributes .= sprintf(
		' data-load-more-settings="%s" %s %s ',
		htmlspecialchars( json_encode( array_merge( $default_settings, $attrs ) ) ),
		! empty( $pglm_query ) ? 'data-load-more-query="' . htmlspecialchars( json_encode( $pglm_query ) ) . '"' : '',
		! empty( $products_per_page ) ? 'data-product-per-page="' . $products_per_page . '"' : ''
	);

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

	global $pglm_stored_settings;

	$pglm_stored_settings = array_merge( $list, $custom_icon_settings );

	return $pglm_stored_settings;

}
