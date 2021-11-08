<?php
/**
 * Plugin Name: JetWooBuilder - Products Grid Load More
 * Plugin URI: https://github.com/UraraReika/jwb-products-grid-load-more
 * Description: Add ajax load more functionality to JetWooBuilder Products Grid widget.
 * Version:     1.1.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-woo-builder
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there! I\'m just a plugin!';
	exit; // If this file is called directly, abort.
}

define( 'PGLM_PLUGIN_URL', __FILE__ );

// Includes
include( 'includes/pglm-enqueue.php' );
include( 'includes/pglm-elementor-controls.php' );
include( 'includes/pglm-ajax-handler.php' );
include( 'includes/pglm-load-more-integration.php' );

// Enqueue styles and scripts.
add_action( 'elementor/frontend/before_enqueue_scripts', 'pglm_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'pglm_enqueue_styles' );

// Register controls in widget.
add_action( 'elementor/element/jet-woo-products/section_general/after_section_end', 'pglm_register_load_more_controls', 999 );

// Set default widget object.
add_action( 'elementor/widget/before_render_content', 'pglm_store_default_widget_object', 0 );

// Handle Ajax request.
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	add_action( 'wp_ajax_jet_woo_builder_load_more', 'pglm_load_more_products' );
	add_action( 'wp_ajax_nopriv_jet_woo_builder_load_more', 'pglm_load_more_products' );
}

// Set Custom widget data attributes.
add_filter( 'jet-woo-builder/templates/jet-woo-products/widget-attributes', 'pglm_get_widget_attributes', 10, 4 );

// Set JetSmartFilter settings to store.
add_filter( 'jet-smart-filters/providers/jet-woo-products-grid/settings-list', 'pglm_set_widget_setting_to_store' );

// Trigger widget for loader.
add_filter( 'jet-woo-builder/shortcodes/jet-woo-products/query-args', 'pglm_trigger', 10, 2 );
