<?php
/**
 * Plugin Name: JetWooBuilder - Products Grid Load More
 * Plugin URI: https://github.com/UraraReika/jwb-products-grid-load-more
 * Description: Add ajax load more functionality to JetWooBuilder Products Grid widget.
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-woo-builder
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

define( 'PGLM_PLUGIN_URL', __FILE__ );

if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there! I\'m just a plugin!';
	exit;
}

// Includes
include( 'includes/pglm-enqueue.php' );
include( 'includes/pglm-elementor-controls.php' );
include( 'includes/pglm-ajax-handler.php' );
include( 'includes/pglm-load-more-integration.php' );

new PGLM_Load_More_Integration();

// Enqueue styles and scripts.
add_action( 'elementor/frontend/before_enqueue_scripts', 'pglm_enqueue_scripts' );

// Register controls in widgets.
add_action( 'elementor/element/jet-woo-products/section_general/after_section_end', 'pglm_register_load_more_controls', 999 );

if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	add_action( 'wp_ajax_jet_woo_builder_load_more', 'pglm_load_more_products' );
	add_action( 'wp_ajax_nopriv_jet_woo_builder_load_more', 'pglm_load_more_products' );
}
