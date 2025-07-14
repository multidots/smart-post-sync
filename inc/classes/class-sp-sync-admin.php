<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smart_Post_Sync
 * @subpackage Smart_Post_Sync/admin
 * @author     Multidots <info@multidots.com>
 */

namespace Smart_Post_Sync\Inc;

use Smart_Post_Sync\Inc\Traits\Singleton;

/**
 * Admin class file.
 */
class SP_Sync_Admin {

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
	 * The API settings.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      array    $smart_post_sync_options   Sync post API settings.
	 */
	private $smart_post_sync_options;

	/**
	 * Attribute mapping for the post fields and API response fields.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $smart_post_sync_attr    Attribute mapping.
	 */
	private $smart_post_sync_attr;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 */
	public function __construct() {
		if ( defined( 'SMART_POST_SYNC_VERSION' ) ) {
			$this->version = SMART_POST_SYNC_VERSION;
		} else {
			$this->version = '1.0';
		}
		$this->smart_post_sync_setup_admin_hooks();
	}
	/**
	 * Function is used to define admin hooks.
	 *
	 * @since   1.0
	 */
	public function smart_post_sync_setup_admin_hooks() {
		add_action( 'admin_menu', array( $this, 'smart_post_sync_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'smart_post_sync_page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'smart_post_sync_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'smart_post_sync_enqueue_scripts' ) );
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook_suffix The current admin page.
	 * @since    1.0
	 */
	public function smart_post_sync_enqueue_styles( $hook_suffix ) {

		if ( 'toplevel_page_smart-post-sync' === $hook_suffix ) {
			wp_register_style( 'smart-post-sync', SMART_POST_SYNC_URL . 'assets/build/admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'smart-post-sync' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook_suffix The current admin page.
	 * @since    1.0
	 */
	public function smart_post_sync_enqueue_scripts( $hook_suffix ) {

		if ( 'toplevel_page_smart-post-sync' === $hook_suffix ) {
			wp_register_script( 'smart-post-sync', SMART_POST_SYNC_URL . 'assets/build/admin.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'smart-post-sync' );
			wp_localize_script(
				'smart-post-sync',
				'wpsConfig',
				array(
					'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'sps_ajax_nonce' ),
				)
			);
		}
	}

	/**
	 * Function is used to create plugin page
	 */
	public function smart_post_sync_add_plugin_page() {

		add_menu_page(
			__( 'Smart Post Sync', 'smart-post-sync' ),
			__( 'Smart Post Sync', 'smart-post-sync' ),
			'manage_options',
			'smart-post-sync',
			array( $this, 'smart_post_sync_create_admin_page' ),
			SMART_POST_SYNC_LOGO_ICON
			// 'dashicons-update-alt'
		);
	}

	/**
	 * Function is used to create admin page
	 */
	public function smart_post_sync_create_admin_page() {
		$this->smart_post_sync_options = get_option( 'smart_post_sync_settings' );
		$this->smart_post_sync_attr    = get_option( 'smart_post_sync_attr_map' );
		$get_tab                    = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$active_tab                 = isset( $get_tab ) && ! empty( $get_tab ) ? $get_tab : 'sps-api-settings';
		$get_attr_subtab            = filter_input( INPUT_GET, 'attr_subtab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$active_attr_subtab         = isset( $get_attr_subtab ) && ! empty( $get_attr_subtab ) ? $get_attr_subtab : 'general';
		?>
		<div class="sps-wrap">
			<div id="sps-header" class="sps-header">
				<div class="sps-header__left">
					<h2 class="sps-header_title">Smart Post Sync</h2>
				</div>
				<div class="sps-header__right">
					<a href="https://www.multidots.com/" target="_blank" class="md-logo"> <img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/MD-Logo.svg'); //phpcs:ignore ?>" width="130" height="75" class="sps-header__logo" alt="md logo"> </a>
				</div>
			</div>	
			<div class="sps-post-sync-wrap wrap">
				<h2 class="nav-tab-wrapper">
					<a href="?page=smart-post-sync&tab=sps-api-settings" class="nav-tab <?php echo esc_attr( 'sps-api-settings' === $active_tab ? 'nav-tab-active' : '' ); ?>"><span><?php echo esc_html__( 'API Settings', 'smart-post-sync' ); ?></span></a>
					<?php
					if ( isset( $this->smart_post_sync_options['sps_api_url'] ) && ! empty( $this->smart_post_sync_options['sps_api_url'] ) ) {
						?>
						<a href="?page=smart-post-sync&tab=sps-attribute-mapping" class="nav-tab <?php echo esc_attr( 'sps-attribute-mapping' === $active_tab ? 'nav-tab-active' : '' ); ?>"><span><?php echo esc_html__( 'Attribute Mapping', 'smart-post-sync' ); ?></span></a>
						<?php
					}
					?>
					<a href="?page=smart-post-sync&tab=sps-help" class="nav-tab <?php echo esc_attr( 'sps-help' === $active_tab ? 'nav-tab-active' : '' ); ?>"><span><?php echo esc_html__( 'Help', 'smart-post-sync' ); ?></span></a>
				</h2>
				<?php settings_errors(); ?>
				<form method="post" class="sps-post-sync-wrap__form" action="options.php">
					<?php
					if ( 'sps-api-settings' === $active_tab ) {
						settings_fields( 'smart_post_sync_settings_group' );
						do_settings_sections( 'smart-post-sync-admin' );
						submit_button();
					} elseif ( 'sps-attribute-mapping' === $active_tab ) {
						if ( isset( $this->smart_post_sync_attr['sps_api_attr_title'] ) && ! empty( $this->smart_post_sync_attr['sps_api_attr_title'] ) ) {
							?>                    
							<ul class="subsubsub">
								<li><a href="?page=smart-post-sync&tab=sps-attribute-mapping&attr_subtab=general" class="<?php echo esc_attr( 'general' === $active_attr_subtab ? 'current' : '' ); ?>"><?php esc_html_e( 'General', 'smart-post-sync' ); ?></a> | </li>
								<li><a href="?page=smart-post-sync&tab=sps-attribute-mapping&attr_subtab=test-post-sync" class="<?php echo esc_attr( 'test-post-sync' === $active_attr_subtab ? 'current' : '' ); ?>"><?php esc_html_e( 'Test Post Sync', 'smart-post-sync' ); ?></a> | </li>
								<li><a href="?page=smart-post-sync&tab=sps-attribute-mapping&attr_subtab=sync-manually" class="<?php echo esc_attr( 'sync-manually' === $active_attr_subtab ? 'current' : '' ); ?>"><?php esc_html_e( 'Sync Manually', 'smart-post-sync' ); ?></a></li>
							</ul>
							<br class="clear">
							<?php
						}
						
						if ( 'general' === $active_attr_subtab ) {
							settings_fields( 'smart_post_sync_attr_group' );
							?>
							<h2 class="sps_test_api_heading"><?php esc_html_e( 'Test API Connection', 'smart-post-sync' ); ?></h2>
							<p><?php esc_html_e( 'Test the connection to ensure the API is reachable.', 'smart-post-sync' ); ?></p>
							<div class="sps_test_api">
								<button type="button" class="button button-primary sps_test_api_btn" id="test-api"><?php esc_html_e( 'Test API', 'smart-post-sync' ); ?></button>
								<div class="api-response"></div>
							</div>
							<?php
							do_settings_sections( 'smart-post-sync-attr' );
							submit_button();
						} elseif ( 'test-post-sync' === $active_attr_subtab ) {
							if ( isset( $this->smart_post_sync_attr['sps_api_attr_title'] ) && ! empty( $this->smart_post_sync_attr['sps_api_attr_title'] ) ) {
								?>
								<div class="sps_post_sync_seprator">
									<h2><?php esc_html_e( 'Test Post Sync', 'smart-post-sync' ); ?></h2>
									<p class="sps_test_para"><?php esc_html_e( 'To sync a single post and test the connection and attribute mapping, click the button below to sync just one post from the API.', 'smart-post-sync' ); ?></p>
									<div class="sps_test_sync">
										<button type="button" class="button button-primary sps_test_sync_btn" id="test-sync"><?php esc_html_e( 'Test Sync', 'smart-post-sync' ); ?></button>
										<div class="sync-response"></div>
									</div>
								</div>
								<?php
							} else {
								echo '<h2 class="sps_header_error">' . esc_html__( 'The API Attribute "Title" is not set. Please configure the title attribute to proceed', 'smart-post-sync' ) . '</h2>';
							}
						} elseif ( 'sync-manually' === $active_attr_subtab ) {
							?>
							<div class="sps_post_sync_seprator">
								<h2><?php esc_html_e( 'Sync Manually', 'smart-post-sync' ); ?></h2>
								<p class="sps_test_para"><?php esc_html_e( 'To synchronize posts manually from the API, click the "Sync Now" button below.', 'smart-post-sync' ); ?></p>
								<div class="sps_sync_manual">
									<button type="button" class="button button-primary sps_sync_manual_btn" id="sync-manual"><?php esc_html_e( 'Sync Now', 'smart-post-sync' ); ?></button>
									<div class="sync-manual-response hidden">
										<div class="sync-msg-wrap">
											<p class="sync-msg-res"><?php esc_html_e( 'Do not close the page. Sync in progress...', 'smart-post-sync' ); ?></p>
											<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'smart-post-sync' ); ?></span></button>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
					} elseif ( 'sps-help' === $active_tab ) {
						include_once SMART_POST_SYNC_DIR . 'inc/templates/setting-help.php';
					}
					?>
				</form>
			</div>
			<div class="sps-footer dv"><p>Crafted by the experts at <a href="https://www.multidots.com/" target="_blank">Multidots</a>, designed for professionals who build with WordPress.</p>
			</div>
		</div>
		
		<?php
	}

	/**
	 * Function is used register settings.
	 */
	public function smart_post_sync_page_init() {

		register_setting(
			'smart_post_sync_settings_group',
			'smart_post_sync_settings',
			array( $this, 'smart_post_sync_sanitize' )
		);

		add_settings_section(
			'smart_post_sync_setting_section',
			'',
			array( $this, 'smart_post_sync_section_info' ),
			'smart-post-sync-admin'
		);

		add_settings_field(
			'sps_api_url',
			__( 'API URL', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Enter the endpoint URL for the API request.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_url_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Enter the endpoint URL for the API request.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Enter the endpoint URL for the API request.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_method',
			__( 'Method', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Select the HTTP method for the request (e.g., GET, POST).', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_method_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Select the HTTP method for the request (e.g., GET, POST).', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Select the HTTP method for the request (e.g., GET, POST).', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_timeout',
			__( 'Timeout', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Set the maximum duration to wait for a response before aborting the request.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_timeout_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Set the maximum duration to wait for a response before aborting the request.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Set the maximum duration to wait for a response before aborting the request.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_params',
			__( 'API Parameters', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'List query parameters or data fields to customize the API request.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_params_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'List query parameters or data fields to customize the API request.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'List query parameters or data fields to customize the API request.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_headers',
			__( 'Headers', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Define key-value pairs for HTTP headers to include in the request.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_headers_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Define key-value pairs for HTTP headers to include in the request.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Define key-value pairs for HTTP headers to include in the request.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_body',
			__( 'Request Body', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Specify the content or data to be sent in the request body.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_body_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Specify the content or data to be sent in the request body.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Specify the content or data to be sent in the request body.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_body_encode_type',
			__( 'Encode Body', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Choose the encoding format for the post body content.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_body_encode_type_callback' ),
			'smart-post-sync-admin',
			'smart_post_sync_setting_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Choose the encoding format for the post body content.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Choose the encoding format for the post body content.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		// Attribute Mapping Section.
		register_setting(
			'smart_post_sync_attr_group',
			'smart_post_sync_attr_map',
			array( $this, 'smart_post_sync_sanitize' )
		);

		add_settings_section(
			'smart_post_sync_attr_section',
			__( 'Post Attribute Mapping', 'smart-post-sync' ),
			array( $this, 'smart_post_sync_attr_section_info' ),
			'smart-post-sync-attr',
		);

		add_settings_section(
			'smart_post_sync_schedule',
			__( 'Sync Schedule Settings', 'smart-post-sync' ),
			array( $this, 'smart_post_sync_schedule_section_info' ),
			'smart-post-sync-attr'
		);

		add_settings_field(
			'sps_api_attr_title',
			__( 'Post Title Attribute(*)', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Sets the title of the post; a required field for proper identification.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_attr_title_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_attr_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Sets the title of the post; a required field for proper identification.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Sets the title of the post; a required field for proper identification.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_attr_content',
			__( 'Post Content Attribute', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Defines the main content of the post for display and formatting.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_attr_content_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_attr_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Defines the main content of the post for display and formatting.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Defines the main content of the post for display and formatting.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_attr_category',
			__( 'Post Category Attribute', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( "Specifies the post's tag attribute for enhanced categorization.", 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_attr_category_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_attr_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( "Specifies the post's tag attribute for enhanced categorization.", 'smart-post-sync' ),
				'tip'       => esc_attr__( "Specifies the post's tag attribute for enhanced categorization.", 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_attr_tag',
			__( 'Post Tag Attribute', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Specify the attribute for tagging the post, used for categorization or metadata.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_attr_tag_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_attr_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Specify the attribute for tagging the post, used for categorization or metadata.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Specify the attribute for tagging the post, used for categorization or metadata.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_post_author',
			__( 'Default Post Author', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Choose the default author for new posts.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_default_post_author_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_attr_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Choose the default author for new posts.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Choose the default author for new posts.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_attr_cf',
			__( 'Custom Field & Attribute', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Define additional fields and attributes for customizing post data.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_attr_cf_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_attr_section',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Define additional fields and attributes for customizing post data.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Define additional fields and attributes for customizing post data.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_sync_interval',
			__( 'Choose Sync Frequency', 'smart-post-sync' ) . '<span class="sps-help-tip"><span class="sps-help-inner-tip" tabindex="0" aria-label="' . esc_attr__( 'Select how often the data should be synchronized.', 'smart-post-sync' ) . '"></span></span>',
			array( $this, 'smart_post_sync_api_sync_interval_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_schedule',
			array(
				'label_for' => 'sps_api_url_id',
				'desc'      => __( 'Select how often the data should be synchronized.', 'smart-post-sync' ),
				'tip'       => esc_attr__( 'Select how often the data should be synchronized.', 'smart-post-sync' ),
				'desc_tip'  => true,
				'autoload'  => false,
			)
		);

		add_settings_field(
			'sps_api_sync_post_update',
			__( 'Update Existing Post', 'smart-post-sync' ),
			array( $this, 'smart_post_sync_api_sync_post_update_callback' ),
			'smart-post-sync-attr',
			'smart_post_sync_schedule'
		);
	}

	/**
	 * Sanitizes the input values for Smart Post Sync settings.
	 *
	 * @param array $input The input values to sanitize.
	 * @return array The sanitized values.
	 */
	public function smart_post_sync_sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['sps_api_url'] ) ) {
			$sanitary_values['sps_api_url'] = sanitize_url( $input['sps_api_url'] );
		}
		if ( isset( $input['sps_api_method'] ) ) {
			$sanitary_values['sps_api_method'] = sanitize_text_field( $input['sps_api_method'] );
		}
		if ( isset( $input['sps_api_timeout'] ) ) {
			$sanitary_values['sps_api_timeout'] = sanitize_text_field( $input['sps_api_timeout'] );
		}
		if ( isset( $input['sps_api_params'] ) ) {
			$sanitary_values['sps_api_params'] = $this->smart_post_sync_sanitize_repeater_fields( $input['sps_api_params'] );
		}
		if ( isset( $input['sps_api_headers'] ) ) {
			$sanitary_values['sps_api_headers'] = $this->smart_post_sync_sanitize_repeater_fields( $input['sps_api_headers'] );
		}
		if ( isset( $input['sps_api_body'] ) ) {
			$sanitary_values['sps_api_body'] = $this->smart_post_sync_sanitize_repeater_fields( $input['sps_api_body'] );
		}
		if ( isset( $input['sps_api_body_encode_type'] ) ) {
			$sanitary_values['sps_api_body_encode_type'] = sanitize_text_field( $input['sps_api_body_encode_type'] );
		}

		if ( isset( $input['sps_api_attr_title'] ) ) {
			$sanitary_values['sps_api_attr_title'] = sanitize_text_field( $input['sps_api_attr_title'] );
		}
		if ( isset( $input['sps_api_attr_content'] ) ) {
			$sanitary_values['sps_api_attr_content'] = sanitize_text_field( $input['sps_api_attr_content'] );
		}
		if ( isset( $input['sps_api_attr_category'] ) ) {
			$sanitary_values['sps_api_attr_category'] = sanitize_text_field( $input['sps_api_attr_category'] );
		}
		if ( isset( $input['sps_api_attr_tag'] ) ) {
			$sanitary_values['sps_api_attr_tag'] = sanitize_text_field( $input['sps_api_attr_tag'] );
		}
		if ( isset( $input['sps_api_post_author'] ) ) {
			$sanitary_values['sps_api_post_author'] = sanitize_text_field( $input['sps_api_post_author'] );
		}
		if ( isset( $input['sps_api_attr_cf'] ) ) {
			$sanitary_values['sps_api_attr_cf'] = $this->smart_post_sync_sanitize_repeater_fields( $input['sps_api_attr_cf'] );
		}
		if ( isset( $input['sps_api_sync_interval'] ) ) {

			$current_attr     = get_option( 'smart_post_sync_attr_map' );
			$current_interval = isset( $current_attr['sps_api_sync_interval'] ) ? $current_attr['sps_api_sync_interval'] : 'none';

			$sanitary_values['sps_api_sync_interval'] = sanitize_text_field( $input['sps_api_sync_interval'] );

			// Clear cron job if interval is changed.
			if ( ! empty( $current_interval ) && $current_interval !== $sanitary_values['sps_api_sync_interval'] ) {
				wp_clear_scheduled_hook( 'sps_sync_post_cron' );
			}
			// Schedule cron job if interval is set.
			if ( ! wp_next_scheduled( 'sps_sync_post_cron' ) && 'none' !== $sanitary_values['sps_api_sync_interval'] ) {
				wp_schedule_event( time(), $sanitary_values['sps_api_sync_interval'], 'sps_sync_post_cron' );
			}
		}
		if ( isset( $input['sps_api_sync_post_update'] ) ) {
			$sanitary_values['sps_api_sync_post_update'] = rest_sanitize_boolean( $input['sps_api_sync_post_update'] );
		}

		return $sanitary_values;
	}

	/**
	 * Sanitizes the repeater fields.
	 *
	 * This method takes an array of parameters and sanitizes the 'name' and 'value' fields using the `sanitize_text_field` function.
	 *
	 * @param array $parameters The array of parameters to be sanitized.
	 * @return array The sanitized array of parameters.
	 * @since 1.0
	 */
	private function smart_post_sync_sanitize_repeater_fields( $parameters ) {
		$param_data = array();
		foreach ( $parameters as $value ) {
			$param_data[] = array(
				'name'  => sanitize_text_field( $value['name'] ),
				'value' => sanitize_text_field( $value['value'] ),
			);
		}
		return $param_data;
	}

	/**
	 * Used to show section info for Api setting.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_section_info() {
	}

	/**
	 * Used to show section info for post attribute mapping.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_attr_section_info() {
		echo '<p>' . esc_html__( 'Configure the attributes for syncing posts between systems.', 'smart-post-sync' ) . '</p>';
	}

	/**
	 * Used to show section info for sync schedule settings.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_schedule_section_info() {
		echo '<p>' . esc_html__( 'Set up the schedule for syncing posts.', 'smart-post-sync' ) . '</p>';
	}

	/**
	 * API URL field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_url_callback() {
		printf(
			'<input class="regular-text" type="url" name="smart_post_sync_settings[sps_api_url]" id="sps_api_url" value="%s" required>',
			isset( $this->smart_post_sync_options['sps_api_url'] ) ? esc_attr( $this->smart_post_sync_options['sps_api_url'] ) : ''
		);
	}

	/**
	 * API method field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_method_callback() {
		?>
		<select class="sps_select_box" name="smart_post_sync_settings[sps_api_method]" id="sps_api_method">
			<option value="GET" <?php selected( 'GET', isset( $this->smart_post_sync_options['sps_api_method'] ) ? $this->smart_post_sync_options['sps_api_method'] : '' ); ?>>GET</option>
			<option value="POST" <?php selected( 'POST', isset( $this->smart_post_sync_options['sps_api_method'] ) ? $this->smart_post_sync_options['sps_api_method'] : '' ); ?>>POST</option>
		</select>
		<?php
	}

	/**
	 * API timeout field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_timeout_callback() {
		printf(
			'<input class="regular-text" type="number" name="smart_post_sync_settings[sps_api_timeout]" id="sps_api_timeout" value="%s">',
			isset( $this->smart_post_sync_options['sps_api_timeout'] ) ? esc_attr( $this->smart_post_sync_options['sps_api_timeout'] ) : '10'
		);
	}

	/**
	 * API parameter field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_params_callback() {
		$parameters = isset( $this->smart_post_sync_options['sps_api_params'] ) ? $this->smart_post_sync_options['sps_api_params'] : array();
		?>
		<div class="sps_parameter_wrap params-wrap">
			<div class="sps_api_main api-params" id="api-params">
				<?php
				if ( is_array( $parameters ) && count( $parameters ) > 0 ) {
					foreach ( $parameters as $key => $value ) {
						?>
						<div class="sps_api_main_field api-param">
							<input class="regular-text" type="text" name="smart_post_sync_settings[sps_api_params][<?php echo esc_attr( $key ); ?>][name]"  value="<?php echo esc_attr( $value['name'] ); ?>" placeholder="Parameter Name">
							<input class="regular-text" type="text" name="smart_post_sync_settings[sps_api_params][<?php echo esc_attr( $key ); ?>][value]" value="<?php echo esc_attr( $value['value'] ); ?>" placeholder="Parameter Value">
							<div class="sps_del_btn remove-field">
								<button class="button button-link delete-field">
									<span class="dashicons dashicons-no"></span> <!-- Using Dashicons for a delete icon -->
								</button>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
			<button class="button sps_icon_plus_btn" id="add-param"><i class="sps_icon_plus"></i>Add Parameter</button>
		</div>
		<?php
	}

	/**
	 * API headers field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_headers_callback() {
		$parameters = isset( $this->smart_post_sync_options['sps_api_headers'] ) ? $this->smart_post_sync_options['sps_api_headers'] : array();
		?>
		<div class="sps_parameter_wrap headers-wrap">
			<div class="sps_api_main api-headers" id="api-headers">
				<?php
				if ( is_array( $parameters ) && count( $parameters ) > 0 ) {
					foreach ( $parameters as $key => $value ) {
						?>
						<div class="sps_api_main_field api-header">
							<input class="regular-text" type="text" name="smart_post_sync_settings[sps_api_headers][<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" placeholder="Key">
							<input class="regular-text" type="text" name="smart_post_sync_settings[sps_api_headers][<?php echo esc_attr( $key ); ?>][value]" value="<?php echo esc_attr( $value['value'] ); ?>" placeholder="Value">
							<div class="sps_del_btn remove-field">
								<button class="button button-link delete-field">
									<span class="dashicons dashicons-no"></span> <!-- Using Dashicons for a delete icon -->
								</button>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
			<button class="button sps_icon_plus_btn" id="add-header"><i class="sps_icon_plus"></i>Add Header</button>
		</div>
		<?php
	}

	/**
	 * API body field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_body_callback() {
		$parameters = isset( $this->smart_post_sync_options['sps_api_body'] ) ? $this->smart_post_sync_options['sps_api_body'] : array();
		?>
		<div class="sps_parameter_wrap body-wrap">
			<div class="sps_api_main api-body-request" id="api-body-request">
				<?php
				if ( is_array( $parameters ) && count( $parameters ) > 0 ) {
					foreach ( $parameters as $key => $value ) {
						?>
						<div class="sps_api_main_field api-body">
							<input class="regular-text" type="text" name="smart_post_sync_settings[sps_api_body][<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" placeholder="Key">
							<input class="regular-text" type="text" name="smart_post_sync_settings[sps_api_body][<?php echo esc_attr( $key ); ?>][value]" value="<?php echo esc_attr( $value['value'] ); ?>" placeholder="Value">
							<div class="sps_del_btn remove-field">
								<button class="button button-link delete-field">
									<span class="dashicons dashicons-no"></span> <!-- Using Dashicons for a delete icon -->
								</button>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
			<button class="button sps_icon_plus_btn" id="add-body"><i class="sps_icon_plus"></i>Add Body</button>
		</div>
		<?php
	}

	/**
	 * API body encode type field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_body_encode_type_callback() {
		?>
		<select class="sps_select_box" name="smart_post_sync_settings[sps_api_body_encode_type]" id="sps_api_body_encode_type" >
			<option value="none" <?php selected( 'none', isset( $this->smart_post_sync_options['sps_api_body_encode_type'] ) ? $this->smart_post_sync_options['sps_api_body_encode_type'] : '' ); ?>>No encoding (raw)</option>
			<option value="base64" <?php selected( 'base64', isset( $this->smart_post_sync_options['sps_api_body_encode_type'] ) ? $this->smart_post_sync_options['sps_api_body_encode_type'] : '' ); ?>>Base64 encode</option>
			<option value="json" <?php selected( 'json', isset( $this->smart_post_sync_options['sps_api_body_encode_type'] ) ? $this->smart_post_sync_options['sps_api_body_encode_type'] : '' ); ?>>JSON encode</option>
			<option value="url" <?php selected( 'url', isset( $this->smart_post_sync_options['sps_api_body_encode_type'] ) ? $this->smart_post_sync_options['sps_api_body_encode_type'] : '' ); ?>>URL encode (x-www-form-urlencoded)</option>
		</select>
		<?php
	}

	/**
	 * Title attribute mapping field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_attr_title_callback() {
		printf(
			'<input class="regular-text" type="text" name="smart_post_sync_attr_map[sps_api_attr_title]" id="sps_api_attr_title" value="%s" placeholder="API Response Attribute" required>',
			isset( $this->smart_post_sync_attr['sps_api_attr_title'] ) ? esc_attr( $this->smart_post_sync_attr['sps_api_attr_title'] ) : ''
		);
	}

	/**
	 * Content attribute mapping field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_attr_content_callback() {
		printf(
			'<input class="regular-text" type="text" name="smart_post_sync_attr_map[sps_api_attr_content]" id="sps_api_attr_content" value="%s" placeholder="API Response Attribute">',
			isset( $this->smart_post_sync_attr['sps_api_attr_content'] ) ? esc_attr( $this->smart_post_sync_attr['sps_api_attr_content'] ) : ''
		);
	}

	/**
	 * Category attribute mapping field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_attr_category_callback() {
		printf(
			'<input class="regular-text" type="text" name="smart_post_sync_attr_map[sps_api_attr_category]" id="sps_api_attr_category" value="%s" placeholder="API Response Attribute">',
			isset( $this->smart_post_sync_attr['sps_api_attr_category'] ) ? esc_attr( $this->smart_post_sync_attr['sps_api_attr_category'] ) : ''
		);
	}

	/**
	 * Tag attribute mapping field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_attr_tag_callback() {
		printf(
			'<input class="regular-text" type="text" name="smart_post_sync_attr_map[sps_api_attr_tag]" id="sps_api_attr_tag" value="%s" placeholder="API Response Attribute">',
			isset( $this->smart_post_sync_attr['sps_api_attr_tag'] ) ? esc_attr( $this->smart_post_sync_attr['sps_api_attr_tag'] ) : ''
		);
	}

	/**
	 * Post default author dropdown.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_default_post_author_callback() {
		$args = array(
			'show_option_all' => 'Select Author',
			'name'            => 'smart_post_sync_attr_map[sps_api_post_author]',
			'id'              => 'sps_api_post_author',
			'selected'        => isset( $this->smart_post_sync_attr['sps_api_post_author'] ) ? $this->smart_post_sync_attr['sps_api_post_author'] : '0',
			'class'           => 'sps_select_box',
		);
		wp_dropdown_users( $args );
	}

	/**
	 * Custom field attribute mapping field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_attr_cf_callback() {
		$custom_fields = isset( $this->smart_post_sync_attr['sps_api_attr_cf'] ) ? $this->smart_post_sync_attr['sps_api_attr_cf'] : array();
		?>
		<div class="sps_parameter_wrap custom-field-wrap">
			<div class="sps_api_main api-custom-fields" id="api-custom-fields">
				<?php
				if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
					foreach ( $custom_fields as $key => $value ) {
						?>
						<div class="sps_api_main_field api-custom-field">
							<input class="regular-text" type="text" name="smart_post_sync_attr_map[sps_api_attr_cf][<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" placeholder="Custom field name">
							<input class="regular-text" type="text" name="smart_post_sync_attr_map[sps_api_attr_cf][<?php echo esc_attr( $key ); ?>][value]" value="<?php echo esc_attr( $value['value'] ); ?>" placeholder="API Response Attribute">
							<div class="sps_del_btn remove-field">
								<button class="button button-link delete-field">
									<span class="dashicons dashicons-no"></span> <!-- Using Dashicons for a delete icon -->
								</button>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
			<button class="button sps_icon_plus_btn" id="add-field"><i class="sps_icon_plus"></i>Add Field</button>
		</div>
		<?php
	}

	/**
	 * Cron interval field callback function.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_sync_interval_callback() {
		$wp_intervals = wp_get_schedules();
		if ( is_array( $wp_intervals ) && count( $wp_intervals ) > 0 ) {
			?>
			<select class="sps_select_box" name="smart_post_sync_attr_map[sps_api_sync_interval]" id="sps_api_sync_interval">
				<option value="none">Select</option>
				<?php
				foreach ( $wp_intervals as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, isset( $this->smart_post_sync_attr['sps_api_sync_interval'] ) ? $this->smart_post_sync_attr['sps_api_sync_interval'] : '' ); ?>><?php echo esc_html( $value['display'] ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
		}
	}

	/**
	 * Setting field for update existing post during sync.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_api_sync_post_update_callback() {
		?>
		<input type="checkbox" name="smart_post_sync_attr_map[sps_api_sync_post_update]" id="sps_api_sync_post_update" value="1" <?php checked( 1, isset( $this->smart_post_sync_attr['sps_api_sync_post_update'] ) ? $this->smart_post_sync_attr['sps_api_sync_post_update'] : 0 ); ?>>
		<span><strong><?php echo esc_html_e( 'Important:', 'smart-post-sync' ); ?></strong> <?php echo esc_html_e( 'If the setting is enabled, auto-sync will check and update the existing post.', 'smart-post-sync' ); ?></span>
		<p><strong><?php echo esc_html_e( 'Note:', 'smart-post-sync' ); ?></strong> <?php echo esc_html_e( 'The post title field is used to check whether the post exists.', 'smart-post-sync' ); ?></p>
		<?php
	}
}
