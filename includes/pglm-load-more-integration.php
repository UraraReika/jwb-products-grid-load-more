<?php

class PGLM_Load_More_Integration {

	public $settings = null;
	public $widget   = null;

	public function __construct() {
		add_action( 'jet-woo-builder/shortcodes/jet-woo-products/loop-start', [ $this, 'pglm_add_load_more_open_wrapper' ] );
		add_action( 'jet-woo-builder/shortcodes/jet-woo-products/loop-end', [ $this, 'pglm_add_load_more_close_wrapper' ] );
		add_filter( 'jet-woo-builder/jet-woo-products-grid/settings', [ $this, 'pglm_get_widget_settings' ], 999, 2 );
	}

	/**
	 * Add open wrapper to widget with settings.
	 */
	public function pglm_add_load_more_open_wrapper() {

		if ( ! $this->settings ) {
			return;
		}

		$shortcode        = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );
		$attributes       = [];
		$settings         = $this->settings;
		$store_settings   = $this->settings_to_store();
		$default_settings = [];

		foreach ( $store_settings as $key ) {
			if ( false !== strpos( $key, 'selected_' ) ) {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? htmlspecialchars( $this->widget->__render_icon( str_replace( 'selected_', '', $key ), '%s', '', false ) ) : '';
			} else {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			}
		}

		// Compatibility with compare and wishlist plugin.
		$default_settings['_widget_id'] = $this->widget->get_id();

		$shortcode->set_settings( $settings );

		foreach ( $shortcode->get_atts() as $attr => $data ) {
			$attr_val            = $settings[ $attr ];
			$attr_val            = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
			$attributes[ $attr ] = $attr_val;
		}

		echo sprintf( '<div class="pglm-settings-holder" data-load-more-settings="%s">', htmlspecialchars( json_encode( array_merge( $attributes, $default_settings ) ) ) );

	}

	/**
	 * Add closing wrapper.
	 */
	public function pglm_add_load_more_close_wrapper() {

		if ( ! $this->settings ) {
			return;
		}

		echo '</div>';

	}

	/**
	 * Get settings from widget.
	 *
	 * @param $settings
	 * @param $widget
	 *
	 * @return mixed
	 */
	public function pglm_get_widget_settings( $settings, $widget ) {

		if ( ! $this->settings ) {
			$this->settings = $settings;
		}

		if ( ! $this->widget ) {
			$this->widget = $widget;
		}

		return $settings;

	}

	/**
	 * Returns settings to store list
	 *
	 * @return array
	 */
	public function settings_to_store() {
		return [
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
		];
	}

}

