<?php
/**
 * Class to sync post from API to WordPress post.
 *
 * @package Smart_Post_Sync
 */

namespace Smart_Post_Sync\Inc;

use Smart_Post_Sync\Inc\Traits\Singleton;

/**
 * Class SP_Sync_Post
 */
class SP_Sync_Post {
	use Singleton;

	/**
	 * The API settings.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      array    $api_settings   Sync post API settings.
	 */
	private $api_settings;

	/**
	 * Attribute mapping for the post fields and API response fields.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $attr_map    Attribute mapping.
	 */
	private $attr_map;

	/**
	 * Test API connection.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      boolean   $test_api    Test API connection.
	 */
	private $test_api = false;

	/**
	 * Indicate whether the request is Ajax or not.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      boolean   $is_ajax    For request is Ajax or not.
	 */
	private $is_ajax = false;

	/**
	 * Indicate whether the request is an initial/first Ajax request or not.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      boolean   $initial_ajax    To determine whether the initial request or not.
	 */
	private $initial_ajax = false;

	/**
	 * Email notification class object.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      object   $email_notification    Email notification class instance.
	 */
	private $email_notification;

	/**
	 * Test sync or not.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      boolean   $is_test_sync    Test sync or not.
	 */
	private $is_test_sync = false;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		// load class.
		$this->smart_post_sync_setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 * @since 1.0
	 */
	protected function smart_post_sync_setup_hooks() {

		// cron action to sync post.
		add_action( 'sps_sync_post_cron', array( $this, 'smart_post_sync_data' ) );

		// Ajax for test api connection.
		add_action( 'wp_ajax_sps_test_api_connection', array( $this, 'smart_post_sync_test_api_connection_callback' ) );

		// Ajax to sync single post for testing.
		add_action( 'wp_ajax_sps_test_sync_post', array( $this, 'smart_post_sync_test_sync_post_callback' ) );

		// Ajax for sync post manually.
		add_action( 'wp_ajax_sps_sync_manual', array( $this, 'smart_post_sync_post_manually_callback' ) );
		$this->email_notification = \Smart_Post_Sync\Inc\SP_Sync_Email_Notification::get_instance();
	}

	/**
	 * Sync post from API to WordPress post.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_data() {

		$this->api_settings = get_option( 'smart_post_sync_settings' );
		$this->attr_map     = get_option( 'smart_post_sync_attr_map' );

		if ( isset( $this->api_settings['sps_api_url'] ) && ! empty( $this->api_settings['sps_api_url'] ) ) {

			$response = '';

			if ( ! $this->test_api && ( ! $this->is_ajax || ! $this->initial_ajax ) ) {
				// Get API response from the option.
				$response = get_option( 'smart_post_sync_response' );
			}

			if ( $response && ! empty( $response ) ) {
				// If the response is already available in the option, use that response.
				$this->smart_post_sync_create_post( $response );
			} else {

				// If the response is not available in the option, get the response from the API.
				if ( isset( $this->api_settings['sps_api_method'] ) && 'POST' === $this->api_settings['sps_api_method'] ) {
					$response = $this->smart_post_sync_post_request();
				} else {
					$response = $this->smart_post_sync_get_request();
				}

				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
					$response     = wp_remote_retrieve_body( $response );
					$content_type = wp_remote_retrieve_header( $response, 'Content-Type' );
					// Check if the response is in XML or JSON format.
					if ( false !== strpos( $content_type, 'text/xml' ) || false !== strpos( $content_type, 'application/xml' ) || false !== strpos( $response, '<?xml' ) ) {
						$data = simplexml_load_string( $response );
					} else {
						$data = json_decode( $response );
					}
					if ( $this->test_api ) {
						// Prepare the test API connection response.
						$this->smart_post_sync_prepare_test_api_response( true, true, $data );
					} else {
						// Create post from the API response.
						$this->smart_post_sync_create_post( $data );
					}
				} elseif ( $this->test_api ) {
					// Prepare the test API connection response.
					$this->smart_post_sync_prepare_test_api_response( true, false, $response );
				} else {
					$this->email_notification->smart_post_sync_api_response_code( $response );
					$this->smart_post_sync_prepare_test_sync_response( false );
					$this->smart_post_sync_manual_sync_fail_response();
				}
			}
		} elseif ( $this->test_api ) {
			// Prepare the test API connection response.
			$this->smart_post_sync_prepare_test_api_response( false, false, array() );
		} else {
			$this->email_notification->smart_post_sync_api_url_not_exist();
			$this->smart_post_sync_prepare_test_sync_response( false );
			$this->smart_post_sync_manual_sync_fail_response();
		}
	}

	/**
	 * Get method to retrieves the data from the API.
	 *
	 * @return mixed The response from the API.
	 * @since 1.0
	 */
	public function smart_post_sync_get_request() {

		$api_url  = $this->smart_post_sync_get_parameter_url();
		$args     = $this->smart_post_sync_request_args();
		$response = wp_remote_get( $api_url, $args );

		return $response;
	}

	/**
	 * POST method to retrieves the data from the API.
	 *
	 * @return mixed The response from the API.
	 * @since 1.0
	 */
	public function smart_post_sync_post_request() {

		$api_url  = $this->smart_post_sync_get_parameter_url();
		$args     = $this->smart_post_sync_request_args();
		$response = wp_remote_request( $api_url, $args );

		return $response;
	}

	/**
	 * Generates the request arguments for the API request.
	 *
	 * @return array The request arguments.
	 * @since 1.0
	 */
	public function smart_post_sync_request_args() {

		$args = array();

		// Check if API headers are set.
		if ( isset( $this->api_settings['sps_api_headers'] ) && is_array( $this->api_settings['sps_api_headers'] ) && count( $this->api_settings['sps_api_headers'] ) > 0 ) {
			$headers       = apply_filters( 'smart_post_sync_before_prepare_headers', $this->api_settings['sps_api_headers'] ); // Apply filter to modify the headers.
			$final_headers = $this->smart_post_sync_prepared_parameters( $headers ); // Prepare headers.
			$final_headers = apply_filters( 'smart_post_sync_after_prepare_parameters', $final_headers, $headers ); // Apply filter to modify the prepared headers.

			$args['headers'] = $final_headers;
		}

		if ( isset( $this->api_settings['sps_api_method'] ) && 'POST' === $this->api_settings['sps_api_method'] ) {
			$args['method'] = 'POST';

			// Check if API body is set.
			if ( isset( $this->api_settings['sps_api_body'] ) && is_array( $this->api_settings['sps_api_body'] ) && count( $this->api_settings['sps_api_body'] ) > 0 ) {
				$body_param = apply_filters( 'smart_post_sync_before_prepare_body', $this->api_settings['sps_api_body'] ); // Apply filter to modify the body param.
				$final_body = $this->smart_post_sync_prepared_parameters( $body_param ); // Prepare body param.
				$final_body = apply_filters( 'smart_post_sync_after_prepare_body', $final_body, $body_param ); // Apply filter to modify the prepared body param.

				if ( is_array( $final_body ) && count( $final_body ) > 0 ) {

					$encode_type = isset( $this->api_settings['sps_api_body_encode_type'] ) ? $this->api_settings['sps_api_body_encode_type'] : 'none';

					// Check the encoding type and encode body param accordingly.
					if ( 'json' === $encode_type ) {
						$final_body = wp_json_encode( $final_body );
					} elseif ( 'url' === $encode_type ) {
						$final_body = http_build_query( $final_body, '', '&' );
					} elseif ( 'base64' === $encode_type ) {
						$final_body = base64_encode( wp_json_encode( $final_body ) ); //phpcs:ignore
					}
					$final_body   = apply_filters( 'smart_post_sync_api_encoded_body', $final_body ); // Apply filter to modify the body param.
					$args['body'] = $final_body;
				}
			}
		}

		$timeout      = isset( $this->api_settings['sps_api_timeout'] ) && absint( $this->api_settings['sps_api_timeout'] ) > 0 ? absint( $this->api_settings['sps_api_timeout'] ) : 10;
		$default_args = array(
			'timeout'   => $timeout,
			'sslverify' => true,
		);

		$args = wp_parse_args( $args, $default_args ); // Merge default args with the args.
		$args = apply_filters( 'smart_post_sync_request_args', $args ); // Apply filter to modify the request args.

		return $args;
	}

	/**
	 * Retrieves the parameter URL for the API request.
	 *
	 * This method constructs the parameter URL for the API request based on the API settings and parameters.
	 * If the API parameters are set, it applies filters to modify the parameters, prepares the parameters,
	 * and adds them to the API URL using the `add_query_arg()` function.
	 *
	 * @return string The parameter URL for the API request.
	 * @since 1.0
	 */
	public function smart_post_sync_get_parameter_url() {

		// API URL.
		$api_url = $this->api_settings['sps_api_url'];

		// Check if API parameters are set.
		if ( isset( $this->api_settings['sps_api_params'] ) && is_array( $this->api_settings['sps_api_params'] ) && count( $this->api_settings['sps_api_params'] ) > 0 ) {
			$params       = apply_filters( 'smart_post_sync_before_prepare_parameters', $this->api_settings['sps_api_params'] ); // Apply filter to modify the parameters.
			$final_params = $this->smart_post_sync_prepared_parameters( $params ); // Prepare parameters.
			$final_params = apply_filters( 'smart_post_sync_after_prepare_parameters', $final_params, $params ); // Apply filter to modify the prepared parameters.
			$api_url      = add_query_arg( $final_params, $api_url ); // Add parameters to the API URL.
		}

		return $api_url;
	}

	/**
	 * Prepare the parameters for syncing posts.
	 *
	 * This method takes an array of parameters and prepares the final parameters for syncing posts.
	 * It loops through the parameters and checks if the 'name' and 'value' keys are set and not empty.
	 * If both conditions are met, it sanitizes the 'name' and 'value' using the `sanitize_text_field` function
	 * and adds them to the final parameters array.
	 *
	 * @param array $params The array of parameters to be prepared.
	 * @return array The final parameters array.
	 * @since 1.0
	 */
	public function smart_post_sync_prepared_parameters( $params ) {

		$final_params = array();

		// loop through the parameters and prepare final parameters.
		foreach ( $params as $value ) {
			if ( ( isset( $value['name'] ) && ! empty( $value['name'] ) ) && ( isset( $value['value'] ) && ( ! empty( $value['value'] ) || '0' === (string) $value['value'] ) ) ) {
				$final_params[ sanitize_text_field( $value['name'] ) ] = sanitize_text_field( stripslashes( $value['value'] ) );
			}
		}

		return $final_params;
	}

	/**
	 * Creates a post using the provided API data.
	 *
	 * @param mixed $data The data to create the post.
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_create_post( $data ) {

		$final_data = array();
		$data_key   = '';
		if ( is_object( $data ) ) {
			$data_key = $this->smart_post_sync_detect_object_post_key( $data );
			if ( ! empty( $data_key ) ) {
				$final_data = $data->{$data_key};
			}
		} elseif ( is_array( $data ) ) {
			// array means the data is already in the correct format.
			$final_data = $data;
		}

		if ( is_array( $final_data ) && count( $final_data ) > 0 ) {

			$title_attr_map   = isset( $this->attr_map['sps_api_attr_title'] ) ? $this->attr_map['sps_api_attr_title'] : '';
			$content_attr_map = isset( $this->attr_map['sps_api_attr_content'] ) ? $this->attr_map['sps_api_attr_content'] : '';
			$cat_attr_map     = isset( $this->attr_map['sps_api_attr_category'] ) ? $this->attr_map['sps_api_attr_category'] : '';
			$tag_attr_map     = isset( $this->attr_map['sps_api_attr_tag'] ) ? $this->attr_map['sps_api_attr_tag'] : '';
			$cf_attr_map      = isset( $this->attr_map['sps_api_attr_cf'] ) ? $this->attr_map['sps_api_attr_cf'] : '';
			$update_post      = isset( $this->attr_map['sps_api_sync_post_update'] ) ? $this->attr_map['sps_api_sync_post_update'] : '';
			$default_author   = isset( $this->attr_map['sps_api_post_author'] ) ? $this->attr_map['sps_api_post_author'] : '';
			$cnt              = 0;
			$total_items      = count( $final_data );

			foreach ( $final_data as $key => $post_data ) {

				$new_post = array(
					'post_status' => 'publish',
					'post_type'   => 'post',
				);

				if ( ! empty( $title_attr_map ) ) {
					$title = $this->smart_post_sync_get_mapping_data( $title_attr_map, $post_data );
					$title = wp_strip_all_tags( $title );
					if ( ! empty( $title ) ) {
						$new_post['post_title'] = $title;
						if ( $update_post ) {
							// include post_exists function.
							if ( ! function_exists( 'post_exists' ) ) {
								require_once ABSPATH . 'wp-admin/includes/post.php';
							}
							$existing_post_id = post_exists( $title, '', '', 'post' );
							$new_post['ID']   = $existing_post_id;
						}
					}
				}

				// Content attribute mapping.
				if ( ! empty( $content_attr_map ) ) {
					$new_post['post_content'] = $this->smart_post_sync_get_mapping_data( $content_attr_map, $post_data );
				}

				// Category attribute mapping.
				if ( ! empty( $cat_attr_map ) ) {
					$categories = $this->smart_post_sync_get_mapping_data( $cat_attr_map, $post_data );
					if ( ! empty( $categories ) ) {
						$categories                = is_array( $categories ) ? $categories : explode( ',', $categories );
						$cat_ids                   = $this->smart_post_sync_get_term_id( $categories, 'category' );
						$new_post['post_category'] = $cat_ids;
					}
				}

				// Tag attribute mapping.
				if ( ! empty( $tag_attr_map ) ) {
					$tags = $this->smart_post_sync_get_mapping_data( $tag_attr_map, $post_data );
					if ( ! empty( $tags ) ) {
						$tags                   = is_array( $tags ) ? $tags : explode( ',', $tags );
						$new_post['tags_input'] = $tags;
					}
				}

				// Custom fields attribute mapping add to the meta input.
				if ( ! empty( $cf_attr_map ) && is_array( $cf_attr_map ) ) {
					$meta_inputs = array();
					foreach ( $cf_attr_map as $cf_field ) {
						if ( ( isset( $cf_field['name'] ) && ! empty( $cf_field['name'] ) ) && ( isset( $cf_field['value'] ) && ! empty( $cf_field['value'] ) ) ) {
							$cf_value = $this->smart_post_sync_get_mapping_data( $cf_field['value'], $post_data );
							if ( ! empty( $cf_value ) ) {
								$meta_inputs[ $cf_field['name'] ] = $cf_value;
							}
						}
					}
					if ( count( $meta_inputs ) > 0 ) {
						$new_post['meta_input'] = $meta_inputs;
					}
				}

				if ( ! empty( $default_author ) ) {
					$new_post['post_author'] = $default_author;
				}

				$new_post = apply_filters( 'smart_post_sync_api_post_data', $new_post, $post_data ); // Apply filter to modify the insert post data.

				if ( ! empty( $new_post['post_title'] ) ) {

					$post_id = wp_insert_post( $new_post );

					if ( is_wp_error( $post_id ) ) {
						$this->email_notification->smart_post_sync_insert_post_error( $new_post['post_title'] );
						$this->smart_post_sync_prepare_test_sync_response( false );
					} else {
						$this->smart_post_sync_prepare_test_sync_response( true );
						if ( $this->is_test_sync ) {
							break;
						}
					}
				} else {
					$this->email_notification->smart_post_sync_post_title_not_detected();
					$this->smart_post_sync_prepare_test_sync_response( false );
					$this->smart_post_sync_manual_sync_fail_response();
					break;
				}

				// Removing the post data from the actual array.
				if ( ! empty( $data_key ) && is_object( $data ) ) {
					unset( $data->{$data_key}[ $key ] );
				} else {
					unset( $data[ $key ] );
				}

				++$cnt;

				if ( $this->is_ajax && ( 2 === $cnt || $cnt >= $total_items ) ) {
					// Update the option data with the remaining post data.
					$this->smart_post_sync_update_option_data( $data, $data_key );

					// send success response when request is Ajax.
					$args = array(
						'added'       => $cnt,
						'total_items' => $total_items,
					);
					wp_send_json_success( $args );
					break;
				}
			}
			// Update the option data with the remaining post data.
			$this->smart_post_sync_update_option_data( $data, $data_key );
		} else {
			$this->email_notification->smart_post_sync_post_details_not_detected();
			$this->smart_post_sync_prepare_test_sync_response( false );
			$this->smart_post_sync_manual_sync_fail_response();
		}
	}

	/**
	 * Updates the option data for syncing posts.
	 *
	 * This method updates the option data for syncing posts based on the provided data and data key.
	 * If the remaining data is an array with more than 0 elements, it updates the 'smart_post_sync_response' option with the remaining data.
	 * Otherwise, it deletes the 'smart_post_sync_response' option.
	 *
	 * @param mixed  $data The data to be updated.
	 * @param string $data_key The auto detected object key for response (optional).
	 * @return void
	 * @since 1.0
	 */
	private function smart_post_sync_update_option_data( $data, $data_key = '' ) {
		$remaining_data = ! empty( $data_key ) && is_object( $data ) ? $data->{$data_key} : $data;
		if ( is_array( $remaining_data ) && count( $remaining_data ) > 0 ) {
			update_option( 'smart_post_sync_response', $remaining_data, false );
		} else {
			delete_option( 'smart_post_sync_response' );
		}
	}

	/**
	 * Create or Retrieves the term IDs for the given term names and taxonomy.
	 *
	 * @param array  $terms_name The array of term names.
	 * @param string $taxonomy   The taxonomy name.
	 *
	 * @return array The array of term IDs.
	 * @since 1.0
	 */
	public function smart_post_sync_get_term_id( $terms_name, $taxonomy ) {
		$cat_ids = array();

		if ( is_array( $terms_name ) && ! empty( $taxonomy ) ) {
			foreach ( $terms_name as $term_name ) {
				$cat_id = term_exists( $term_name, $taxonomy );
				if ( $cat_id ) {
					$cat_ids[] = $cat_id['term_id'];
				} else {
					$cat_id = wp_insert_term( $term_name, $taxonomy );
					if ( ! is_wp_error( $cat_id ) ) {
						$cat_ids[] = $cat_id['term_id'];
					}
				}
			}
		}

		return $cat_ids;
	}

	/**
	 * This function is used to detect the object key from the API response data.
	 *
	 * @param mixed $data The data to detect the object key from.
	 * @return string The detected object key.
	 * @since 1.0
	 */
	public function smart_post_sync_detect_object_post_key( $data ) {
		$all_keys = array_keys( (array) $data );
		$data_key = '';
		if ( is_array( $all_keys ) && count( $all_keys ) > 0 ) {
			foreach ( $all_keys as $key ) {
				if ( is_array( $data->{$key} ) ) {
					$data_key = $key;
					break;
				}
			}
		}
		$data_key = apply_filters( 'smart_post_sync_api_response_object_key', $data_key, $data ); // Apply filter to modify the detected object key.
		return $data_key;
	}

	/**
	 * Retrieves the mapped data based on the attribute mapping and input data.
	 *
	 * @param string $attr_map The attribute mapping string.
	 * @param mixed  $data     The input data.
	 *
	 * @return mixed The mapped data.
	 * @since 1.0
	 */
	public function smart_post_sync_get_mapping_data( $attr_map, $data ) {

		// Split the map string into individual parts.
		$parts     = explode( ':', $attr_map );
		$post_data = $data;

		// Iterate through each part of the path.
		foreach ( $parts as $part ) {
			if ( is_array( $post_data ) || is_object( $post_data ) ) {
				// Check if the current level is an array or object.
				if ( is_numeric( $part ) ) {
					// Convert the index from string to integer if it's numeric.
					$part = (int) $part;
				}
				// Access the next level.
				if ( is_array( $post_data ) ) {
					// For arrays.
					if ( isset( $post_data[ $part ] ) ) {
						$post_data = $post_data[ $part ];
					} else {
						// Handle missing index.
						$post_data = null;
						break;
					}
				} elseif ( is_object( $post_data ) ) {
					// For objects.
					if ( property_exists( $post_data, $part ) ) {
						$post_data = $post_data->$part;
					} else {
						// Handle missing property.
						$post_data = null;
						break;
					}
				}
			} else {
				// Handle case where $current is neither an array nor an object.
				$post_data = null;
				break;
			}
		}

		$post_data = apply_filters( 'smart_post_sync_api_attr_mapping_data', $post_data, $attr_map, $data ); // Apply filter to modify the mapped data.

		return $post_data;
	}

	/**
	 * Callback function to test API connection.
	 *
	 * @since 1.0
	 */
	public function smart_post_sync_test_api_connection_callback() {

		check_ajax_referer( 'sps_ajax_nonce', 'nonce' );

		$this->test_api = true;

		$this->smart_post_sync_data();
	}

	/**
	 * Prepares the test API response.
	 *
	 * @param bool  $is_api_res Whether the API response is available.
	 * @param bool  $success    Whether the API connection was successful.
	 * @param mixed $data       The API response data.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_prepare_test_api_response( $is_api_res, $success, $data ) {
		ob_start();
		?>
		<div class="sps_api_test_res_main">
			<?php
			if ( $is_api_res ) {
				?>
				<div class="sps_api_test_status">
					<?php
					$status_code = '';
					if ( $success ) {
						?>
						<div id="setting-error-settings_updated" class="sps_api_test_res_success notice notice-success is-dismissible">
							<p><?php echo esc_html_e( 'API connection successful', 'smart-post-sync' ); ?></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>
						<?php
					} else {
						$response_msg = wp_remote_retrieve_response_message( $data );
						$response_msg = ! empty( $response_msg ) ? $response_msg : 'Unknown error occurred';
						$status_code  = wp_remote_retrieve_response_code( $data );
						$data         = wp_remote_retrieve_body( $data );
						?>
						<div id="setting-error-settings_updated" class="sps_api_test_res_fail notice notice-error is-dismissible">
							<p><?php echo esc_html( $response_msg ); ?></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
						</div>
						<?php
					}
					$this->smart_post_sync_test_api_output( $data, $status_code );
					?>
				</div>
				<?php
			} else {
				?>
				<div class="sps_api_test_status">
					<div class="sps_api_test_res_fail">
						<h3><?php echo esc_html_e( 'Failed', 'smart-post-sync' ); ?></h3>
					</div>
					<p class="warning"><?php echo esc_html_e( 'The API URL not found. Please ensure the API settings are configured correctly.', 'smart-post-sync' ); ?></p>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		$html = ob_get_clean();

		wp_send_json_success( $html );
	}

	/**
	 * Output the API response.
	 *
	 * This method is used to display the API response in a formatted manner.
	 *
	 * @param mixed $data The data to be displayed.
	 * @param int   $status_code The status code of the API response.
	 * @return void
	 * @since 1.0
	 */
	private function smart_post_sync_test_api_output( $data, $status_code ) {
		?>
		<div class="sps_api_test_output_main">
			<h4><?php esc_html_e( 'API Response', 'smart-post-sync' ); ?></h4>
			<div class="sps_api_test_output">
				<?php
				if ( 405 === $status_code ) {
					?>
					<div>
					<?php
				} else {
					?>
					<pre class="sps_api_test_success_reponse">
					<?php
				}
				print_r( $data );//phpcs:ignore
				if ( 405 === $status_code ) {
					?>
					</div>
					<?php
				} else {
					?>
					</pre>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Callback function for testing post synchronization.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_test_sync_post_callback() {

		check_ajax_referer( 'sps_ajax_nonce', 'nonce' );

		$this->is_test_sync = true;

		$this->smart_post_sync_data();
	}

	/**
	 * Callback function to sync post manually.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_post_manually_callback() {

		check_ajax_referer( 'sps_ajax_nonce', 'nonce' );

		$initial            = filter_input( INPUT_POST, 'initial', FILTER_VALIDATE_BOOL );
		$this->is_ajax      = true;
		$this->initial_ajax = isset( $initial ) ? $initial : false;
		$this->smart_post_sync_data();
	}

	/**
	 * Manual sync failed response.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_manual_sync_fail_response() {

		if ( $this->is_ajax ) {
			$msg = 'The manual sync has failed. Please check your email for further details.';
			wp_send_json_error( $msg );
		}
	}

	/**
	 * Prepares the test sync response.
	 *
	 * This method is responsible for preparing the response for a test sync operation.
	 * If the test sync is successful, it sends a success JSON response with a message.
	 * If the test sync fails, it sends an error JSON response with a message.
	 *
	 * @param bool $success Whether the test sync was successful or not.
	 * @return void
	 */
	public function smart_post_sync_prepare_test_sync_response( $success ) {

		if ( $this->is_test_sync ) {

			if ( $success ) {
				$msg = 'The single post sync test was successful. Please go to the post listing to verify that everything has synced correctly.';
				wp_send_json_success( $msg );
			} else {
				$msg = 'The single post sync test has failed. Please check your email for more details.';
				wp_send_json_error( $msg );
			}
		}
	}
}
