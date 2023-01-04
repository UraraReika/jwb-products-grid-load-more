<?php
/**
 * Plugin Name: JetWooBuilder - Products Grid Load More
 * Plugin URI: https://github.com/UraraReika/jwb-products-grid-load-more
 * Description: Add ajax load more functionality to JetWooBuilder Products Grid widget.
 * Version:     1.2.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-woo-builder
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PGLM_PLUGIN_VERSION', '1.2.0' );

define( 'PGLM_PLUGIN_FILE', __FILE__ );
define( 'PGLM_PLUGIN_PATH', plugin_dir_path( PGLM_PLUGIN_FILE ) );
define( 'PGLM_PLUGIN_URL', plugins_url( '/', PGLM_PLUGIN_FILE ) );

add_action( 'plugins_loaded', 'pglm_plugin_init' );

/**
 * Plugin init.
 *
 * Plugin initialization with required plugins installed and activated check.
 *
 * @since  1.2.0
 *
 * @return void
 */
function pglm_plugin_init() {
	require PGLM_PLUGIN_PATH . 'includes/plugin.php';
}
