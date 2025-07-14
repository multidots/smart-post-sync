<?php
/**
 * The settings help page for the plugin.
 *
 * @package    Smart_Post_Sync
 * @subpackage Smart_Post_Sync/templates
 * @since      1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="sps-post-sync__help">
	<h2><?php esc_html_e( 'Introduction', 'smart-post-sync' ); ?></h2>
	<p class="sps_test_para"><?php esc_html_e( 'Welcome to the settings help page for Smart Post Sync! This plugin enables you to sync posts from a third-party API to WordPress. Adjust the settings below to configure the plugin to suit your preferences.', 'smart-post-sync' ); ?></p>
	<div class="sps_accordion_main">
		<div class="sps_accordion">
			<div class="sps_accordion_wrap">
				<span class="sps_accordion_title"><?php esc_html_e( 'API Settings', 'smart-post-sync' ); ?></span>
				<span class="icon"></span>
			</div>
			<div class="sps_accordion_content" style="display:none;">
				<ul class="sps-post-sync__help_ul">
					<li><strong><?php esc_html_e( 'API URL:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The complete URL that the plugin will use to make requests to the external API. This URL includes both the base URL and the specific endpoint path. It defines the exact location of the API service and the resource you want to access or manipulate.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Method:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The HTTP method specifies the type of action you want to perform on the API endpoint. It determines how data is sent to or retrieved from the server.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Timeout:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The amount of time (in seconds) that the plugin will wait for a response from the API server before it considers the request to have failed due to a timeout. This setting is crucial for ensuring that the plugin does not hang indefinitely while waiting for a slow or unresponsive API. Increase this value if you are connecting to an API that may take longer to respond due to processing time, network latency, or high server load.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'API Parameters:', 'smart-post-sync' ); ?></strong>
					<?php esc_html_e( 'The parameters that are sent along with an API request to specify or filter the data being requested or to provide additional details required by the API. These parameters are typically sent as key-value pairs and are appended to the URL. Click on the "Add Parameter" button to add one or more parameter fields.', 'smart-post-sync' ); ?>
						<ul class="sps-post-sync__help_inner_ul">
							<li><strong><?php esc_html_e( 'Parameter Name:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The name or key of the parameter required by the API. This is usually specified by the API\'s documentation and indicates what type of data the parameter represents. Enter the exact parameter name as required by the API.', 'smart-post-sync' ); ?></li>
							<li><strong><?php esc_html_e( 'Parameter Value:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The value associated with the parameter name that you want to pass to the API. The value will determine the outcome of the request or how the API processes the request. Enter the specific value that corresponds to the parameter name.', 'smart-post-sync' ); ?></li>
						</ul>
					</li>
				</ul>
				<p><?php esc_html_e( 'Ensure that all parameters and values match the requirements specified in the API documentation.', 'smart-post-sync' ); ?></p>
				<div class="sps-post-sync__help_section">
					<p><strong><?php esc_html_e( 'Example:', 'smart-post-sync' ); ?></strong></p>
					<p><?php esc_html_e( 'By including the parameters as demonstrated below, you will generate a query parameter that appears as follows:', 'smart-post-sync' ); ?></p>
					<p><strong><?php esc_html_e( '?api_key=123abc&limit=50', 'smart-post-sync' ); ?></strong></p>
					<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/api-parameters.png' ); //phpcs:ignore ?>" alt="api-parameter"/>
				</div>
				<ul class="sps-post-sync__help_ul">
					<li><strong><?php esc_html_e( 'Headers:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'HTTP headers are key-value pairs sent with an API request or response. They provide essential information about the request or the server\'s response, such as authentication details, content types, and other metadata. Configuring headers is crucial for APIs that require specific information to process requests correctly. Click on the "Add Header" button to add one or more header fields.', 'smart-post-sync' ); ?></li>
						<ul class="sps-post-sync__help_inner_ul">
							<li><strong><?php esc_html_e( 'Header Key:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The name of the header to be included in the API request. This key is used by the server to interpret the data or action required for the request. Enter the exact header key as required by the API documentation or server specifications.', 'smart-post-sync' ); ?></li>
							<li><strong><?php esc_html_e( 'Header Value:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The value associated with the header key. This value provides additional information or credentials needed by the server to process the request. Enter the specific value that corresponds to the header key. This value typically includes authentication tokens, content types, or other relevant data.', 'smart-post-sync' ); ?></li>
						</ul>
					</li>
				</ul>
				<div class="sps-post-sync__help_section">
					<p><strong><?php esc_html_e( 'Example:', 'smart-post-sync' ); ?></strong></p>
					<p><?php esc_html_e( 'By including the parameters as demonstrated below, you will generate a query parameter that appears as follows:', 'smart-post-sync' ); ?></p>
					<?php
					$headers = [
						'Content-Type' => 'application/json',
						'apiKey'       => '123abc'
					];
					?>
<pre class="sps-post-sync__help-section-pre">
<code class="sps-post-sync__help-section-code">
<?php echo wp_json_encode( $headers, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</code>
</pre>
					<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/headers.png' ); //phpcs:ignore ?>" alt="headers-parameter" />
					<p><?php esc_html_e( 'Do not include a colon when entering the header; the plugin will handle it automatically.', 'smart-post-sync' ); ?></p>
				</div>
				<ul class="sps-post-sync__help_ul">
					<li>
						<strong><?php esc_html_e( 'Request Body & Encode Body:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The request body contains the data sent to the API server when making a request. They will function and be transmitted only when the request', 'smart-post-sync' ); ?> <strong><?php esc_html_e( 'Method', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'is set to', 'smart-post-sync' ); ?> <strong><?php esc_html_e( 'POST', 'smart-post-sync' ); ?></strong><?php esc_html_e( '. It includes key-value pairs representing the information being submitted, such as form inputs, JSON objects, or other structured data. The request body can be formatted in different ways, depending on the API requirements.','smart-post-sync' ); ?>
					</li>
					<ul class="sps-post-sync__help_inner_ul">
						<li><strong><?php esc_html_e( 'Body Key:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The name or key of the data field to be included in the request body. This key identifies the type of data being sent as required by the API.  Enter the exact key name as specified in the API documentation.', 'smart-post-sync' ); ?></li>
						<li>
							<strong><?php esc_html_e( 'Body Value:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'The corresponding value for the body key. This is the actual data you want to send to the server for that specific field.', 'smart-post-sync' ); ?>
						</li>
						<li>
							<strong><?php esc_html_e( 'Body Encoding Type:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'Specifies the format in which the request body is encoded before being sent to the server. The encoding type determines how the data is structured and transmitted.', 'smart-post-sync' ); ?>
						</li>
					</ul>
				</ul>
				<div class="sps-post-sync__help_section">
					<span><strong><?php esc_html_e( 'Example:', 'smart-post-sync' ); ?></strong></span>
					<p><?php esc_html_e( 'Let\'s assume that you need to pass the following JSON data along with the POST request.', 'smart-post-sync' ); ?></p>
					<?php
					$headertwo = [
						'username' => 'john_doe',
						'email'    => 'john@example.com'
					];
					?>
<pre class="sps-post-sync__help-section-pre">
<code class="sps-post-sync__help-section-code">
<?php echo wp_json_encode( $headertwo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
</code>
</pre>
					<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/request-body.png' ); //phpcs:ignore ?>" alt="request-body" />
					<p><?php esc_html_e( 'You only need to enter the key and value in the request body as shown above and select the body encoding type as JSON.', 'smart-post-sync' ); ?></p>
				</div>
			</div>
		</div>
	</div>
	<hr class="hr-separator">
	<h3 class="section-title"><?php esc_html_e( 'Attribute Mapping', 'smart-post-sync' ); ?></h3>
	<div class="sps_accordion_main">
		<div class="sps_accordion">
			<div class="sps_accordion_wrap">
				<span class="sps_accordion_title"><?php esc_html_e( 'General', 'smart-post-sync' ); ?></span>
				<span class="icon"></span>
			</div>
			<div class="sps_accordion_content" style="display:none;">
				<div class="sps-post-sync__help_section">
				<h2><?php esc_html_e( 'Test API Connection', 'smart-post-sync' ); ?></h2>
				<p><?php esc_html_e( 'This section allows you to verify that the API connection is set up correctly and functioning as expected. By testing the API connection, you can ensure that the provided URL, parameters, headers, and body data are correctly configured and that the server is reachable.', 'smart-post-sync' ); ?></p>
				<p><?php esc_html_e( 'After clicking the "Test API" button, the plugin will display the connection statusand the response from the API server. This information helps you understand if the connection is successful or if there are any issues that need to be addressed.', 'smart-post-sync' ); ?></p>
				<p><strong><?php esc_html_e( 'Connection Success Example:', 'smart-post-sync' ); ?></strong></p>
				<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/test-api-connection-success.png' ); //phpcs:ignore ?>" alt="test-api-connection-success" />
				<p><strong><?php esc_html_e( 'Connection Failed Example:', 'smart-post-sync' ); ?></strong></p>
				<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/test-api-connection-fail.png' ); //phpcs:ignore ?>" alt="test-api-connection-fail" />
				<h2><?php esc_html_e( 'Post Attribute Mapping', 'smart-post-sync' ); ?></h2>
				<p><?php esc_html_e( 'In this section, you can map the attributes of your WordPress posts to specific fields from the API response. This allows you to automatically populate your WordPress posts with data retrieved from the API.', 'smart-post-sync' ); ?></p>
				<h3><strong><?php esc_html_e( 'Mapping Fields:', 'smart-post-sync' ); ?></strong></h3>
				<ul class="sps-post-sync__help_ul">
					<li><strong><?php esc_html_e( 'Post Title:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'Enter the API response property that should be used as the title of the WordPress post.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Post Content:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'Enter the API response property that should be used as the content of the WordPress post.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Post Category:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'Enter the API response property that corresponds to the category or categories of the WordPress post. The plugin will automatically handle both comma-separated strings and arrays, ensuring that all relevant categories are assigned correctly.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Post Tag:', 'smart-post-sync' ); ?></strong>
					<?php esc_html_e( 'Enter the API response property that corresponds to the tags of the WordPress post. The plugin will automatically handle both comma-separated strings and arrays, ensuring that all relevant tags are assigned correctly.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Default Post Author:', 'smart-post-sync' ); ?></strong>
					<?php esc_html_e( 'Select a default WordPress user to be assigned as the author of the posts. You can choose from the list of existing users in your WordPress site.', 'smart-post-sync' ); ?></li>
					<li><strong><?php esc_html_e( 'Custom Field:', 'smart-post-sync' ); ?></strong>
					<?php esc_html_e( 'Add one or more custom fields to map additional attributes from the API response to WordPress. Custom fields allow you to store extra data that may not fit into the standard post fields. Click the "Add Field" button to create a new field mapping. Enter the desired custom field name and the corresponding API response property.', 'smart-post-sync' ); ?></li>
				</ul>
				<p><strong><?php esc_html_e( 'Example 1', 'smart-post-sync' ); ?></strong></p>
				<p><?php esc_html_e( 'Let’s assume that following response recieved from the API when you test connection.', 'smart-post-sync' ); ?></p>
				<h3><strong><?php esc_html_e( 'API Response:', 'smart-post-sync' ); ?></strong></h3>
				<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/api-ex-1.png' ); //phpcs:ignore ?>" alt="api-example-one" />
				<p><?php esc_html_e( 'The plugin will automatically detect the post detail repeater/array property or key. So, when mapping attributes, you only need to add the property within the repeater or array property. The response attribute mapping described above will appear as follows.', 'smart-post-sync' ); ?></p>
				<h3><strong><?php esc_html_e( 'Mapping:', 'smart-post-sync' ); ?></strong></h3>
				<ul class="sps-post-sync__help_ul">
					<li>
						<?php esc_html_e( 'Post Title Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Headline', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Post Content Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Body', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Post Category Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Category', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Post Tag Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Labels', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Custom Field & Attribute:', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Labels', 'smart-post-sync' ); ?></strong>
						<ul class="sps-post-sync__help_inner_ul">
							<div class="sps-post-sync__help-inner-ul-left-section">
								<li>
									<?php esc_html_e( 'Custom Field Name => SEO_Name', 'smart-post-sync' ); ?>
								</li>
								<li>
									<?php esc_html_e( 'API Response Attribute =>', 'smart-post-sync' ); ?>
									<strong><?php esc_html_e( 'SeoName', 'smart-post-sync' ); ?></strong>
								</li>
							</div>
							<div class="sps-post-sync__help-inner-ul-right-section">
								<li>
									<?php esc_html_e( 'Custom Field Name => NewsID', 'smart-post-sync' ); ?>
								</li>
								<li>
									<?php esc_html_e( 'API Response Attribute =>', 'smart-post-sync' ); ?>
									<strong><?php esc_html_e( 'NewsID', 'smart-post-sync' ); ?></strong>
								</li>
							</div>
						</ul>
					</li>

				</ul>
				<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/mapping.png' ); //phpcs:ignore ?>" alt="mapping" />
				<p><strong><?php esc_html_e( 'Example 2', 'smart-post-sync' ); ?></strong></p>
				<p><?php esc_html_e( 'If a response post repeater or array property contains a nested property, use a colon(:) to separate the array key from the property name. Let’s assume that you have following response received from the API.', 'smart-post-sync' ); ?></p>
				<h3><strong><?php esc_html_e( 'API Response:', 'smart-post-sync' ); ?></strong></h3>
				<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/api-ex-2.png' ); //phpcs:ignore ?>" alt="api-example-2" />
				<h3><strong><?php esc_html_e( 'Mapping:', 'smart-post-sync' ); ?></strong></h3>
				<ul class="sps-post-sync__help_ul">
					<li>
						<?php esc_html_e( 'Post Title Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Headlines:Main', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Post Content Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Content:Body', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Post Category Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Category', 'smart-post-sync' ); ?></strong>
					</li>
					<li>
						<?php esc_html_e( 'Post Tag Attribute => ', 'smart-post-sync' ); ?>
						<strong><?php esc_html_e( 'Labels', 'smart-post-sync' ); ?></strong>
					</li>
					<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/mapping2.png' ); //phpcs:ignore ?>" alt="mapping-two" />
				</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="sps_accordion_main">
		<div class="sps_accordion">
			<div class="sps_accordion_wrap">
				<span class="sps_accordion_title"><?php esc_html_e( 'Sync Schedule Settings', 'smart-post-sync' ); ?></span>
				<span class="icon"></span>
			</div>
			<div class="sps_accordion_content" style="display:none;">
				<div class="sps-post-sync__help_section">
				<p><?php esc_html_e( 'This section allows you to configure the synchronization schedule for automatically fetching and updating posts from the API based on a defined frequency. You can also specify whether existing posts in WordPress should be updated during each sync.', 'smart-post-sync' ); ?></p>
				<p><strong><?php esc_html_e( 'Choose Sync Frequency:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'Select how often you want the plugin to synchronize data from the API and create or update WordPress posts. This dropdown menu provides various synchronization options using WordPress\'s built-in cron schedules.', 'smart-post-sync' ); ?></p>
				<p><strong><?php esc_html_e( 'Update Existing Post:', 'smart-post-sync' ); ?></strong> <?php esc_html_e( 'A checkbox option that determines whether existing posts should be updated during each sync. If this option is enabled, the plugin will check for existing posts using the post title. If a post with the same title already exists, the plugin will update it with the latest data from the API otherwise create a new post.', 'smart-post-sync' ); ?></p>
				<h2><?php esc_html_e( 'Test Post Sync and Sync Manually', 'smart-post-sync' ); ?></h2>
				<p><?php esc_html_e( 'Once you save the attribute mapping settings, you will see two additional links, "Test Post Sync" and "Sync Manually," under the Attribute Mapping tab.', 'smart-post-sync' ); ?></p>
				<img src="<?php echo esc_url( SMART_POST_SYNC_URL . 'assets/images/test-post-sync-and-sync-manually.png' ); //phpcs:ignore ?>" alt="test-post-sync-and-sync-manually" />
				<h2><?php esc_html_e( 'Test Post Sync', 'smart-post-sync' ); ?></h2>
				<p><?php esc_html_e( 'This section allows you to test your synchronization settings by syncing a single post. When you click the "Test Sync" button, the plugin will perform a test synchronization, fetching data from the API and creating or updating a single post in WordPress according to your settings. If the test sync fails, the plugin will display error messages and send an email notification to the site administrator with the exact reason for the sync failure.', 'smart-post-sync' ); ?></p>
				<h2><?php esc_html_e( 'Sync Manually', 'smart-post-sync' ); ?></h2>
				<p><?php esc_html_e( 'This section allows you to manually initiate a full synchronization of all posts from the API. It is useful when you want to immediately sync data without waiting for the scheduled sync frequency. Click the "Sync Now" button to start syncing all posts from the API based on the current settings and attribute mappings.', 'smart-post-sync' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
