<?php

class PGLM_Load_More_Integration {

	public $settings = null;

	public function __construct() {
		add_action( 'jet-woo-builder/shortcodes/jet-woo-products/loop-start', [ $this, 'pglm_add_load_more_open_wrapper' ] );
		add_action( 'jet-woo-builder/shortcodes/jet-woo-products/loop-end', [ $this, 'pglm_add_load_more_close_wrapper' ] );
		add_filter( 'jet-woo-builder/jet-woo-products-grid/settings', [ $this, 'pglm_get_widget_settings' ] );
	}

	public function pglm_add_load_more_open_wrapper() {

		if ( ! $this->settings ) {
			return;
		}

		$shortcode = jet_woo_builder_shortcodes()->get_shortcode( 'jet-woo-products' );
		$attributes = [];
		$settings = $this->settings;

		$shortcode->set_settings( $settings );

		foreach ( $shortcode->get_atts() as $attr => $data ) {
			$attr_val            = $settings[ $attr ];
			$attr_val            = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
			$attributes[ $attr ] = $attr_val;
		}

		echo '<div class="pglm-settings-holder" data-load-more-settings="' . htmlspecialchars( json_encode( $attributes ) ) . '">';

	}

	public function pglm_add_load_more_close_wrapper() {

		if ( ! $this->settings ) {
			return;
		}

		echo '</div>';

	}

	public function pglm_get_widget_settings( $settings ) {

		if ( ! $this->settings ) {
			$this->settings = $settings;
		}

		return $settings;

	}

}

