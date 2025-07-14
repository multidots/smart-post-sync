<?php
/**
 * The localization functionality of the plugin.
 *
 * @package    Smart_Post_Sync
 * @author     Multidots <info@multidots.com>
 */

namespace Smart_Post_Sync\Inc;

use Smart_Post_Sync\Inc\Traits\Singleton;

/**
 * I18 class file.
 */
class SP_Sync_I18 {

	use Singleton;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 */
	public function __construct() {
		$this->smart_post_sync_setup_local_hooks();
	}

	/**
	 * Function is used to setup local hooks.
	 */
	public function smart_post_sync_setup_local_hooks() {
		add_action( 'plugins_loaded', array( $this, 'smart_post_sync_set_locale' ) );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smart_Post_Sync_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	public function smart_post_sync_set_locale() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'smart-post-sync' );
		load_textdomain( 'smart-post-sync', plugin_dir_path( dirname( __DIR__ ) ) . '/languages/' . $locale . '.mo' );
	}
}
