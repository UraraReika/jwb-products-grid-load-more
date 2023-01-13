<?php

namespace JWB_PGLM;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Integration {

	/**
	 * Query.
	 *
	 * Hold current query.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @var null
	 */
	public $query = null;

	/**
	 * Page.
	 *
	 * Hold current page number.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @var null
	 */
	public $page = null;

	/**
	 * Paged
	 *
	 * Hold maximum page count.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @var null
	 */
	public $paged = null;

	/**
	 * Settings.
	 *
	 * Hold default widget settings.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @var array
	 */
	public $settings = [];

	public function __construct() {

		// Register controls in widget.
		add_action( 'elementor/element/jet-woo-products/section_general/after_section_end', [ $this, 'register_controls' ], 999 );

		// Set Custom widget data attributes.
		add_filter( 'jet-woo-builder/templates/jet-woo-products/widget-attributes', [ $this, 'set_widget_attributes' ], 10, 4 );

		// Set JetSmartFilter settings to store.
		add_filter( 'jet-smart-filters/providers/jet-woo-products-grid/settings-list', [ $this, 'set_provider_stored_settings_list' ] );

		// Query arguments handling.
		add_filter( 'jet-woo-builder/shortcodes/jet-woo-products/final-query-args', [ $this, 'handle_query_args' ] );
		add_action( 'jet-engine/query-builder/listings/on-query', [ $this, 'handle_custom_query' ] );

		// Set default settings to store.
		add_action( 'elementor/widget/before_render_content', [ $this, 'set_stored_settings' ] );

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
				'label'              => __( 'Enable', 'jet-woo-builder' ),
				'frontend_available' => true,
			]
		);

		$obj->add_control(
			'load_more_type',
			[
				'type'               => \Elementor\Controls_Manager::SELECT,
				'label'              => __( 'Type', 'jet-woo-builder' ),
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
				'label'              => __( 'Trigger ID', 'jet-woo-builder' ),
				'frontend_available' => true,
				'condition'          => [
					'enable_load_more' => 'yes',
					'load_more_type'   => 'click',
				],
			]
		);

		$obj->end_controls_section();

	}

	/**
	 * Handle query args.
	 *
	 * Handle current query arguments.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param array $query_args Query arguments list.
	 *
	 * @return mixed
	 */
	public function handle_query_args( $query_args ) {

		$query = new \WP_Query( $query_args );

		$this->query = $query;
		$this->page  = $query->get( 'paged' ) ? $query->get( 'paged' ) : 1;
		$this->paged = $query->max_num_pages;

		return $query_args;

	}

	/**
	 * Handle custom query.
	 *
	 * Save JetEngine custom query instance for further handling.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param object $query JetEngine custom query instance.
	 *
	 * @return void
	 */
	public function handle_custom_query( $query ) {
		$this->query = $query;
	}

	/**
	 * Default query.
	 *
	 * Store default query args.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @param object $query WP Query inctance.
	 *
	 * @return array
	 */
	function get_default_query( $query ) {

		$default_query = [
			'post_type'      => 'product',
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
	 * Set widget attributes.
	 *
	 * Returns custom attributes for load more functionality.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $attrs     Attributes string.
	 * @param array  $settings  Settings list.
	 * @param array  $products  Products list.
	 * @param object $shortcode Widget shortcode instance.
	 *
	 * @return string
	 */
	public function set_widget_attributes( $attrs, $settings, $products, $shortcode ) {

		$load_more = ! empty( $settings['enable_load_more'] ) ? filter_var( $settings['enable_load_more'], FILTER_VALIDATE_BOOLEAN ) : false;
		$carousel  = ! empty( $settings['carousel_enabled'] ) ? filter_var( $settings['carousel_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;

		if ( ! $load_more || $carousel ) {
			return $attrs;
		}

		$products_per_page = $settings['number'] ?? 4;
		$query             = [];

		if ( isset( $settings['enable_custom_query'] ) && filter_var( $settings['enable_custom_query'], FILTER_VALIDATE_BOOLEAN ) ) {
			if ( empty( $settings['custom_query_id'] ) ) {
				return $attrs;
			}

			$custom_query = new \WP_Query( $this->query->get_query_args() );

			$custom_query->set( 'posts_per_page', $this->query->get_items_page_count() );

			$query             = $this->get_default_query( $custom_query );
			$products_per_page = $this->query->get_items_page_count();
			$this->page        = $this->query->get_current_items_page();
			$this->paged       = $this->query->get_items_pages_count();
		}

		if ( isset( $settings['use_current_query'] ) && filter_var( $settings['use_current_query'], FILTER_VALIDATE_BOOLEAN ) ) {
			$query = $this->get_default_query( $this->query );
		}

		if ( isset( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] ) {
			$query = jet_smart_filters()->query->get_query_args();
		}

		if ( isset( $_REQUEST['action'] ) && 'jet_woo_builder_load_more' === $_REQUEST['action'] ) {
			$query             = $_REQUEST['query'] ?? [];
			$products_per_page = $_REQUEST['per_page'] ?? 4;
			$this->page        = $_REQUEST['page'] ?? 1;
			$this->paged       = $_REQUEST['pages'] ?? 1;

			if ( ! empty( $query ) ) {
				$query['posts_per_page'] = ( $query['posts_per_page'] ?? $settings['number'] ) + $products_per_page;
			}
		}

		$settings       = $this->get_stored_settings( $settings, $shortcode );
		$load_more_data = [
			'settings'          => $settings,
			'query'             => $query,
			'products_per_page' => $products_per_page,
			'page'              => $this->page,
			'pages'             => $this->paged,
		];

		$attrs .= sprintf( ' data-load-more="%s" ', htmlspecialchars( json_encode( $load_more_data ) ) );

		return $attrs;

	}

	/**
	 * Set stored settings.
	 *
	 * Handle widget settings and store them in global variable.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param object $widget Widget instance.
	 */
	public function set_stored_settings( $widget ) {

		if ( 'jet-woo-products' !== $widget->get_name() ) {
			return;
		}

		$widget_settings = $widget->get_settings();
		$settings_list   = $this->get_stored_settings_list();
		$settings        = [];

		foreach ( $settings_list as $key ) {
			if ( false !== strpos( $key, 'selected_' ) ) {
				$settings[ $key ] = isset( $widget_settings[ $key ] ) ? htmlspecialchars( $widget->__render_icon( str_replace( 'selected_', '', $key ), '%s', '', false ) ) : '';
			} else {
				$settings[ $key ] = $widget_settings[ $key ] ?? '';
			}
		}

		$settings['_widget_id'] = $widget->get_id();

		$this->settings = $settings;

	}

	/**
	 * Get stored settings.
	 *
	 * Return all the necessary settings.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param array  $settings  Settings list.
	 * @param object $shortcode Widget shortcode instance.
	 *
	 * @return array
	 */
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

	/**
	 * Get stored settings list.
	 *
	 * Returns list of the widget settings to store.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @return mixed|void
	 */
	public function get_stored_settings_list() {
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
	 * Set provider stored settings list.
	 *
	 * Set load more settings for JetSmartFilter provider.
	 *
	 * @since  1.2.0
	 * @access public
	 *
	 * @param array $list List of settings list to store.
	 *
	 * @return array
	 */
	public function set_provider_stored_settings_list( $list ) {
		return array_merge( $this->get_stored_settings_list(), $list );
	}

}