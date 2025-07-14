<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.multidots.com/
 * @since             1.0
 * @package           Smart_Post_Sync
 *
 * @wordpress-plugin
 * Plugin Name:       Smart Post Sync
 * Description:       Get posts from external API's and sync with WordPress post.
 * Version:           1.0
 * Author:            Multidots
 * Author URI:        https://www.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smart-post-sync
 * Domain Path:       /languages
 */

namespace Smart_Post_Sync;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SMART_POST_SYNC_VERSION', '1.0' );
define( 'SMART_POST_SYNC_URL', plugin_dir_url( __FILE__ ) );
define( 'SMART_POST_SYNC_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMART_POST_SYNC_LOGO_ICON', SMART_POST_SYNC_URL . 'assets/images/smart-post-admin.svg' );


// Load the autoloader.
require_once plugin_dir_path( __FILE__ ) . '/inc/helpers/autoloader.php';


/**
 * Begins execution of the plugin.
 *
 * @since    1.0
 */
function run_md_scaffold() {
	new \Smart_Post_Sync\Inc\Smart_Post_Sync();
}
run_md_scaffold();
