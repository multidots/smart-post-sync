<?php
/**
 * Class to send email notification.
 *
 * @package Smart_Post_Sync
 */

namespace Smart_Post_Sync\Inc;

use Smart_Post_Sync\Inc\Traits\Singleton;

/**
 * Class SP_Sync_Email_Notification
 */
class SP_Sync_Email_Notification {
	use Singleton;

	/**
	 * The API settings.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      array    $api_settings   Sync post API settings.
	 */
	private $recipient_email;

	/**
	 * Construct method.
	 */
	protected function __construct() {
		$this->recipient_email = get_option( 'admin_email' );
	}

	/**
	 * Starts the HTML structure for the email notification.
	 *
	 * @return void
	 * @since 1.0
	 */
	private function smart_post_sync_email_html_start() {
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>
		<body>
		<?php
	}

	/**
	 * Closes the HTML body and HTML tags for the email template.
	 *
	 * @return void
	 * @since 1.0
	 */
	private function smart_post_sync_email_html_end() {
		?>
		</body>
		</html>
		<?php
	}

	/**
	 * Sends an email notification when the API response is not valid.
	 *
	 * @param mixed $response The API response.
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_api_response_code( $response ) {
		$subject       = 'Sync Failed - API response is not valid';
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_msg  = wp_remote_retrieve_response_message( $response );
		$response_msg  = ! empty( $response_msg ) ? $response_msg : 'Unknown error occurred';
		ob_start();
		?>
		<div class="email-body">
			<p><?php echo esc_html_e( 'Hello Admin,', 'smart-post-sync' ); ?></p>
			<p><?php echo esc_html_e( 'The sync process could not be completed due to an incorrect API response. Please check the API and try again.', 'smart-post-sync' ); ?></p>
			<p><strong><?php echo esc_html_e( 'Response Code:', 'smart-post-sync' ); ?></strong> <?php echo esc_html( $response_code ); ?></p>
			<p><strong><?php echo esc_html_e( 'API Response Message:', 'smart-post-sync' ); ?></strong> <?php echo esc_html( $response_msg ); ?></p>
		</div>
		<?php
		$message = ob_get_clean();

		$this->smart_post_sync_send_email( $subject, $message );
	}

	/**
	 * Sends an email notification when the API URL is not found during the sync process.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_api_url_not_exist() {
		$subject = 'Sync Failed - API URL does not exist';
		ob_start();
		?>
		<div class="email-body">
			<p><?php echo esc_html_e( 'Hello Admin,', 'smart-post-sync' ); ?></p>
			<p><?php echo esc_html_e( 'The sync process has failed due to the API URL not being found. Please make sure that the API settings are set up correctly.', 'smart-post-sync' ); ?></p>
		</div>
		<?php
		$message = ob_get_clean();

		$this->smart_post_sync_send_email( $subject, $message );
	}

	/**
	 * Inserts an error notification email for failed post sync.
	 *
	 * @param string $post_title The title of the post that failed to sync.
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_insert_post_error( $post_title ) {
		$subject = 'Post Sync Failed - Error while inserting post';
		ob_start();
		?>
		<div class="email-body">
			<p><?php echo esc_html_e( 'Hello Admin,', 'smart-post-sync' ); ?></p>
			<p><?php echo esc_html_e( 'We encountered an issue during the post sync process due to an error while inserting the post. Please review the sync details and try again.', 'smart-post-sync' ); ?></p>
			<p><strong><?php echo esc_html_e( 'Post Title:', 'smart-post-sync' ); ?></strong> <?php echo esc_html( $post_title ); ?></p>
		</div>
		<?php
		$message = ob_get_clean();

		$this->smart_post_sync_send_email( $subject, $message );
	}

	/**
	 * Sends an email notification when the post title is not detected in the API response.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_post_title_not_detected() {
		$subject = 'Sync Failed - Post Title Missing';
		ob_start();
		?>
		<div class="email-body">
			<p><?php echo esc_html_e( 'Hello Admin,', 'smart-post-sync' ); ?></p>
			<p><?php echo esc_html_e( 'We regret to inform you that the sync process has failed because the post title was not found in the response. Please ensure that the post title exists and is correctly mapped in the API response before attempting the sync again.', 'smart-post-sync' ); ?></p>
		</div>
		<?php
		$message = ob_get_clean();

		$this->smart_post_sync_send_email( $subject, $message );
	}

	/**
	 * Sends an email notification when post details are not detected during in the API response.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_post_details_not_detected() {
		$subject = 'Sync Failed - Post details not found';
		ob_start();
		?>
		<div class="email-body">
			<p><?php echo esc_html_e( 'Hello Admin,', 'smart-post-sync' ); ?></p>
			<p><?php echo esc_html_e( 'The sync process has failed because the post details could not be found in the response. Please verify that the required post information is available and correctly mapped in the API response before retrying the sync.', 'smart-post-sync' ); ?></p>
		</div>
		<?php
		$message = ob_get_clean();

		$this->smart_post_sync_send_email( $subject, $message );
	}

	/**
	 * Sends an email notification.
	 *
	 * @param string $subject The subject of the email.
	 * @param string $message The content of the email.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function smart_post_sync_send_email( $subject, $message ) {

		$recipient_email = apply_filters( 'smart_post_sync_email_recipient_email', $this->recipient_email );
		$subject         = apply_filters( 'smart_post_sync_email_subject', $subject );
		$from_email      = apply_filters( 'smart_post_sync_email_from_email', get_bloginfo( 'admin_email' ) );
		$from_name       = get_bloginfo( 'name' );
		$from_name       = ! empty( $from_name ) ? $from_name : 'Smart Post Sync';
		$headers         = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $from_name . ' <' . $from_email . '>',
		);

		ob_start();
		$this->smart_post_sync_email_html_start();
		echo wp_kses_post( $message );
		$this->smart_post_sync_email_html_end();

		$message = ob_get_clean();

		wp_mail( $recipient_email, $subject, $message, $headers ); //phpcs:ignore
	}
}
