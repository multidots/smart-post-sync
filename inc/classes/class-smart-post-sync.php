<?php
/**
 * The core plugin class.
 *
 * @since      1.0
 * @package    Smart_Post_Sync
 * @subpackage Smart_Post_Sync/includes
 * @author     Multidots <info@multidots.com>
 */

namespace Smart_Post_Sync\Inc;

use Smart_Post_Sync\Inc\Traits\Singleton;

/**
 * Main class File.
 */
class Smart_Post_Sync {


	use Singleton;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      Smart_Post_Sync_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0
	 */
	public function __construct() {
		if ( defined( 'SMART_POST_SYNC_VERSION' ) ) {
			$this->version = SMART_POST_SYNC_VERSION;
		} else {
			$this->version = '1.0';
		}
		$this->plugin_name = 'smart-post-sync';

		SP_Sync_Admin::get_instance();
		SP_Sync_I18::get_instance();
		SP_Sync_Post::get_instance();
		SP_Sync_Email_Notification::get_instance();
	}
}
