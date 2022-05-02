<?php

/**
 * Default query.
 *
 * Store default query args.
 *
 * @since 1.1.0
 *
 * @param $query
 *
 * @return array
 */
function get_default_query( $query ):array {

	$default_query = [
		'post_type'      => $query->get( 'post_type' ),
		'wc_query'       => $query->get( 'wc_query' ),
		'tax_query'      => $query->get( 'tax_query' ),
		'orderby'        => $query->get( 'orderby' ),
		'order'          => $query->get( 'order' ),
		'paged'          => $query->get( 'paged' ),
		'posts_per_page' => $query->get( 'posts_per_page' ),
	];

	if ( $query->get( 'taxonomy' ) ) {
		$default_query['taxonomy'] = $query->get( 'taxonomy' );
		$default_query['term']     = $query->get( 'term' );
	}

	if ( is_search() ) {
		$default_query['s'] = $query->get( 's' );
	}

	return $default_query;

}

/**
 * Load more attributes.
 *
 * Set custom load more functionality attributes for Products Grid widget.
 *
 * @since 1.0.0
 *
 * @param string $attributes
 * @param array  $settings
 * @param object $query
 * @param object $shortcode
 *
 * @return string Custom attributes for proper functionality.
 */
function pglm_get_widget_attributes( string $attributes, array $settings, $query, $shortcode ):string {

	$load_more        = isset( $settings['enable_load_more'] ) ? filter_var( $settings['enable_load_more'], FILTER_VALIDATE_BOOLEAN ) : false;
	$carousel_enabled = isset( $settings['carousel_enabled'] ) ? filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;

	if ( ! $load_more || $carousel_enabled ) {
		return $attributes;
	}

	$attrs             = [];
	$default_settings  = [];
	$jsf_query         = function_exists( 'jet_smart_filters' ) ? jet_smart_filters()->query->get_query_args() : false;
	$pglm_query        = $jsf_query ? $jsf_query : null;
	$products_per_page = intval( $settings['number'] );
	$products_page     = $query->get( 'paged' ) ? $query->get( 'paged' ) : 1;
	$products_pages    = $query->max_num_pages;

	if ( isset( $settings['use_current_query'] ) && 'yes' === $settings['use_current_query'] ) {
		$pglm_query = get_default_query( $query );
	}

	if ( isset( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] && isset( $_REQUEST['settings'] ) ) {
		$default_settings = $_REQUEST['settings'];
		$pglm_query       = $jsf_query;
		$request_query    = new \WP_Query( array_merge( $query->query_vars, $pglm_query ) );
		$products_page    = $request_query->query_vars['paged'] ? $request_query->query_vars['paged'] : 1;
		$products_pages   = $request_query->max_num_pages;
	}

	if ( isset( $_REQUEST['action'] ) && 'jet_woo_builder_load_more' === $_REQUEST['action'] && isset( $_REQUEST['settings'] ) ) {
		$default_settings  = $_REQUEST['settings'];
		$pglm_query        = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : false;
		$products_per_page = $_REQUEST['productsPerPage'];
		$products_page     = $_REQUEST['page'];
		$products_pages    = $_REQUEST['pages'];

		if ( $pglm_query ) {
			if ( isset( $pglm_query['posts_per_page'] ) ) {
				$pglm_query['posts_per_page'] += $products_per_page;
			} else {
				$pglm_query['posts_per_page'] = $settings['number'] + $products_per_page;
			}
		}
	}

	global $pglm_object, $pglm_stored_settings;

	if ( ! $pglm_stored_settings ) {
		$pglm_stored_settings = pglm_settings_to_store();
	}

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
	}

	foreach ( $shortcode->get_atts() as $attr => $data ) {
		if ( 'enable_thumb_effect' === $attr ) {
			continue;
		}

		$attr_val       = $settings[ $attr ];
		$attr_val       = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
		$attrs[ $attr ] = $attr_val;
	}

	$attributes .= sprintf(
		' data-load-more-settings="%s" %s data-product-per-page="%s" data-products-page="%s"  data-products-pages="%s" ',
		htmlspecialchars( json_encode( array_merge( $default_settings, $attrs ) ) ),
		! empty( $pglm_query ) ? 'data-load-more-query="' . htmlspecialchars( json_encode( $pglm_query ) ) . '"' : '',
		$products_per_page,
		$products_page,
		$products_pages
	);

	return $attributes;

}

/**
 * Set global widget variable.
 *
 * @since 1.0.0
 *
 * @param object $widget
 *
 * @return void
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
 * Filter settings to store.
 *
 * Set load more settings for JetSmartFilter and global variable.
 *
 * @since 1.0.0
 *
 * @param array $list
 *
 * @return array List of settings.
 */
function pglm_set_widget_setting_to_store( array $list ) {

	$custom_icon_settings = [
		'enable_load_more',
		'load_more_type',
		'load_more_trigger_id',
	];

	global $pglm_stored_settings;

	$pglm_stored_settings = array_merge( $list, $custom_icon_settings );

	return $pglm_stored_settings;

}

/**
 * Settings to store.
 *
 * Returns widget stored settings that used after ajax request.
 *
 * @since 1.0.0
 *
 * @return array List of settings.
 */
function pglm_settings_to_store() {
	return apply_filters( 'jet-woo-builder/products-grid/load-more/settings-list', [
		'show_compare',
		'compare_button_order',
		'compare_button_order_tablet',
		'compare_button_order_mobile',
		'compare_button_icon_normal',
		'selected_compare_button_icon_normal',
		'compare_button_label_normal',
		'compare_button_icon_added',
		'selected_compare_button_icon_added',
		'compare_button_label_added',
		'compare_use_button_icon',
		'compare_button_icon_position',
		'compare_use_as_remove_button',
		'show_wishlist',
		'wishlist_button_order',
		'wishlist_button_order_tablet',
		'wishlist_button_order_mobile',
		'wishlist_button_icon_normal',
		'selected_wishlist_button_icon_normal',
		'wishlist_button_label_normal',
		'wishlist_button_icon_added',
		'selected_wishlist_button_icon_added',
		'wishlist_button_label_added',
		'wishlist_use_button_icon',
		'wishlist_button_icon_position',
		'wishlist_use_as_remove_button',
		'show_quickview',
		'quickview_button_order',
		'quickview_button_icon_normal',
		'selected_quickview_button_icon_normal',
		'quickview_button_label_normal',
		'quickview_use_button_icon',
		'quickview_button_icon_position',
		'jet_woo_builder_qv',
		'jet_woo_builder_qv_template',
		'jet_woo_builder_cart_popup',
		'jet_woo_builder_cart_popup_template',
		'carousel_enabled',
		'carousel_direction',
		'prev_arrow',
		'selected_prev_arrow',
		'next_arrow',
		'selected_next_arrow',
		'enable_custom_query',
		'custom_query_id',
		'enable_load_more',
		'load_more_type',
		'load_more_trigger_id',
	] );
}

/**
 * Trigger widget for loader.
 *
 * @since 1.0.0
 *
 * @param array  $args
 * @param object $shortcode
 *
 * @return mixed
 */
function pglm_trigger( $args, $shortcode ) {

	$query_id = $shortcode->get_attr( '_element_id' );

	if ( ! $query_id ) {
		$query_id = 'default';
	}

	$args['no_found_rows'] = false;

	return $args;

}
