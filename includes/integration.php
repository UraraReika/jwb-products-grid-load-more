<?php

namespace JWB_PGLM;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Integration {

	public $page     = null;
	public $paged    = null;
	public $settings = [];

	public function __construct() {

		// Register controls in widget.
		add_action( 'elementor/element/jet-woo-products/section_general/after_section_end', [ $this, 'register_controls' ], 999 );

		// Set Custom widget data attributes.
		add_filter( 'jet-woo-builder/templates/jet-woo-products/widget-attributes', [ $this, 'set_widget_attributes' ], 10, 4 );
		add_filter( 'jet-woo-builder/shortcodes/jet-woo-products/final-query-args', function( $query_args ) {
			$query      = new \WP_Query( $query_args );
			//error_log(json_encode( $query ));
			return $query_args;
		} );

		add_action( 'jet-woo-builder/query/load-more', function ( $query, $shortcode ) {

			global $wp_query;

			//error_log(json_encode( $query ));
			//error_log(json_encode( $wp_query ));
			/*error_log(json_encode( $query ));
			error_log(json_encode( $query->query_vars ));
			error_log(json_encode( $query->max_num_pages ));*/

			$this->page  = $query->get( 'paged' ) ? $query->get( 'paged' ) : 1;
			$this->paged = $query->max_num_pages;
			//var_dump( 1 );

		}, 10, 2 );

		add_action( 'elementor/widget/before_render_content', function ( $widget ) {

			if ( 'jet-woo-products' !== $widget->get_name() ) {
				return;
			}

			$widget_settings = $widget->get_settings();
			$stored_settings = $this->settings_to_store();
			$settings        = [];

			foreach ( $stored_settings as $key ) {
				if ( false !== strpos( $key, 'selected_' ) ) {
					$settings[ $key ] = isset( $widget_settings[ $key ] ) ? htmlspecialchars( $widget->__render_icon( str_replace( 'selected_', '', $key ), '%s', '', false ) ) : '';
				} else {
					$settings[ $key ] = $widget_settings[ $key ] ?? '';
				}
			}

			$settings['_widget_id'] = $widget->get_id();

			$this->settings = $settings;

		} );

	}

	/**
	 * Register controls.
	 *
	 * Add load more controls to Products Grid widget after general section.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param object $obj Widget instance.
	 *
	 * @return void
	 */
	public function register_controls( $obj ) {

		$obj->start_controls_section(
			'section_load_more',
			[
				'label' => __( 'Load More', 'jet-woo-builder' ),
			]
		);

		$obj->add_control(
			'enable_load_more',
			[
				'type'               => \Elementor\Controls_Manager::SWITCHER,
				'label'              => __( 'Enable Load More', 'jet-woo-builder' ),
				'frontend_available' => true,
			]
		);

		$obj->add_control(
			'load_more_type',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => __( 'Load More Type', 'jet-woo-builder' ),
				'options'            => [
					'click'  => __( 'By Button Click', 'jet-woo-builder' ),
					'scroll' => __( 'By Page Scrolling', 'jet-woo-builder' ),
				],
				'default'            => 'click',
				'frontend_available' => true,
				'condition'          => [
					'enable_load_more' => 'yes',
				],
			]
		);

		$obj->add_control(
			'load_more_trigger_id',
			[
				'type'               => \Elementor\Controls_Manager::TEXT,
				'label'              => __( 'Load More Trigger ID', 'jet-woo-builder' ),
				'frontend_available' => true,
				'condition'          => [
					'enable_load_more' => 'yes',
					'load_more_type'   => 'click',
				],
			]
		);

		$obj->end_controls_section();

	}

	public function set_widget_attributes( $attrs, $settings, $query, $shortcode ) {

		//error_log($settings['number']);

		$load_more = ! empty( $settings['enable_load_more'] ) ? filter_var( $settings['enable_load_more'], FILTER_VALIDATE_BOOLEAN ) : false;
		$carousel  = ! empty( $settings['carousel_enabled'] ) ? filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;

		if ( ! $load_more || $carousel ) {
			return $attrs;
		}

		$per_page           = intval( $settings['number'] );
		$settings = $this->get_stored_settings( $settings, $shortcode );

		$attrs .= sprintf(
			' data-load-more-settings="%s" %s data-product-per-page="%s" data-products-page="%s"  data-products-pages="%s" ',
			htmlspecialchars( json_encode( $settings ) ),
			'',
			$per_page,
			$this->page,
			$this->paged
		);

		//var_dump( 2 );
		/*var_dump( $attrs );
		var_dump( $query );
		var_dump( $settings );*/

		return $attrs;

	}

	public function get_stored_settings( $settings, $shortcode ) {

		if ( empty( $this->settings ) ) {
			return $settings;
		}

		$stored_settings = [];

		foreach ( $shortcode->get_atts() as $key => $value ) {
			if ( 'enable_thumb_effect' === $key ) {
				continue;
			}

			$stored_settings[ $key ] = ! is_array( $settings[ $key ] ) ? $settings[ $key ] : implode( ',', $settings[ $key ] );
		}

		return array_merge( $stored_settings, $this->settings );

	}

	public function settings_to_store() {
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

}