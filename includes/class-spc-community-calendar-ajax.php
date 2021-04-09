<?php

use Rakit\Validation\Validator;

/**
 * This class is used to handle the ajax actions
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_AJAX {

	public function disconnect() {
		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		delete_option( 'spcc_token' );
		delete_option( 'spcc_website_id' );

		wp_send_json_success();
	}

	public function request_access() {
		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		$message = isset($_POST['message']) ? $_POST['message'] : '';

		if(empty($message)) {
			wp_send_json_error( array( 'errors' => array( __( 'Please enter valid message.', 'spcc' ) ) ) );
		} else {
			$api = new SPC_Community_Calendar_API();
			$response = $api->request_access($message);
			if($response->is_error()) {
				$message = __( 'Please correct the following errors:' );
				$errors  = $response->get_errors();
				wp_send_json_error( array( 'message' => $message, 'errors' => $errors ) );
			} else {
				update_option('spcc_access_request', wp_date('Y-m-d H:i:s'));
				wp_send_json_success(array('message' => __('Request sent successfully.')));
			}
		}
	}

	public function handle_login() {

		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		$repo = new SPC_Community_Calendar_Data_Repository();

		$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
		$pass  = isset( $_POST['password'] ) ? $_POST['password'] : '';

		$api      = new SPC_Community_Calendar_API();
		$response = $api->login_account( $email, $pass );

		if ( $response->is_error() ) {
			$message = __( 'Please correct the following errors:' );
			$errors  = $response->get_errors();
			wp_send_json_error( array( 'message' => $message, 'errors' => $errors ) );
		} else {

			$html = $this->load_settings();
			$message = __( 'Authentication successful!' );
			$item    = $response->get_item();

			update_option( 'spcc_token', $item['token'] );
			update_option( 'spcc_website_id', $item['website_id'] );
			$account = $repo->get_account();
			wp_send_json_success( array(
				'message' => $message,
				'account' => $account,
				'html' => $html,
			) );
		}

	}


	public function handle_register() {

		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		$repo = new SPC_Community_Calendar_Data_Repository();

		$validator  = new Validator();
		$validation = $validator->validate( $_POST + $_FILES, [
			'name'     => 'required',
			'email'    => 'required|email',
			'website'  => 'required|url',
			'password' => 'required|min:6',
		] );
		if ( $validation->fails() ) {
			$message = __( 'Please correct the following errors' );
			$errors  = $validation->errors()->all();
			wp_send_json_error( array( 'message' => $message, 'errors' => $errors ) );
		} else {
			$data     = array(
				'name'     => $_POST['name'],
				'email'    => $_POST['email'],
				'website'  => $_POST['website'],
				'password' => $_POST['password'],
			);
			$api      = new SPC_Community_Calendar_API();
			$response = $api->register_account( $data );
			if ( $response->is_error() ) {
				$message = __( 'Please correct the following errors:' );
				$errors  = $response->get_errors();
				wp_send_json_error( array( 'message' => $message, 'errors' => $errors ) );
			} else {

				$html = $this->load_settings();

				$message = __( 'Your registration was successful!' );
				$item    = $response->get_item();
				update_option( 'spcc_token', $item['token'] );
				update_option( 'spcc_website_id', $item['website_id'] );
				$account = $repo->get_account();

				wp_send_json_success( array( 'message' => $message, 'account' => $account, 'html' => $html ) );

			}
		}

	}

	private function load_settings() {
		ob_start();
		include(trailingslashit(SPCC_ROOT_PATH) .'admin/partials/form-settings.php');
		$html = ob_get_clean();
		return $html;
	}

	public function handle_settings() {

		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		$settings = new SPC_Community_Calendar_Settings();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$settings->saveRequest();
			wp_send_json_success( array(
				'message' => __( 'Settings saved successfully!' ),
			) );
		}

		wp_send_json_error();
		exit;
	}


	public function handle_create_event() {

		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		$validator  = new Validator();
		$rules      = array(
			'type'        => 'required|in:private,public',
			'title'       => 'required',
			'description' => 'required',
			'start'       => 'required',
			'end'         => 'required',
			'venue'       => 'required',
			'address'     => 'required_if:attendance,physical',
			'city'        => 'required_if:attendance,physical',
			'state'       => 'required_if:attendance,physical',
			'postal_code' => 'required_if:attendance,physical',
			'country'     => 'required_if:attendance,physical',
			'attendance'  => 'required|in:physical,virtual',
			'website'     => 'required_if:attendance,virtual',
			'image'       => 'required',
			'category'    => 'required',
		);
		$validation = $validator->validate( $_REQUEST + $_FILES, $rules );

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errors' => $validation->errors()->all() ) );
		} else {

			$type = $_REQUEST['type'];

			$attachment_id   = spcc_handle_media_upload( 'image', null );
			$attachment_path = $attachment_id ? get_attached_file( $attachment_id ) : null;
			$is_private      = $type == 'public';

			$valid_keys = spcc_array_except( array_keys( $rules ), [ 'image' ] );
			$valid_keys = array_merge( $valid_keys, array(
				'parking',
				'address2',
				'cost',
				'tickets_url',
				'host',
				'facebook',
				'twitter',
				'instagram',
				'website',
				'pinterest'
			) );
			$data       = spcc_array_only( $_REQUEST, $valid_keys );

			$data['type'] = $type;

			$files = array();
			if ( ! is_null( $attachment_path ) ) {
				$files = array(
					'image' => $attachment_path,
				);
			}

			$events_api = new SPC_Community_Calendar_Events_API();
			$response   = $events_api->create( $data, $files );
			if ( $response->is_error() ) {
				wp_send_json_error( array(
					'message' => 'Please correct the following errors',
					'errors'  => $response->get_errors()
				) );
			} else {
				$event   = $response->get_item();
				$ID      = isset( $event['ID'] ) ? $event['ID'] : null;
				$status  = isset( $event['post_status'] ) ? $event['post_status'] : '';
				$message = $status === 'draft' ? __( 'The event was submitted successfully and currently awaits approval.' ) : __( 'The event is saved successfully.' );
				if ( $is_private ) {
					$event_ID = wp_insert_post( array(
						'post_type'    => SPCC_PT_EVENT,
						'post_status'  => 'publish',
						'post_title'   => $_REQUEST['title'],
						'post_content' => $_REQUEST['content'],
					) );
					if ( ! is_wp_error( $event_ID ) ) {
						foreach ( $data as $key => $value ) {
							update_post_meta( $event_ID, 'event_' . $key, $value );
						}
						set_post_thumbnail( $event_ID, $attachment_id );
						update_post_meta( $event_ID, 'remote_event_id', $ID );
					}
				} else {
					wp_delete_attachment( $attachment_id, true );
				}
				wp_send_json_success( array( 'message' => $message, 'item' => $event ) );
			}
		}

	}

	public function handle_update_event() {
		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		if ( ! isset( $_REQUEST['event_id'] ) || empty( $_REQUEST['event_id'] ) || ! is_numeric( $_REQUEST['event_id'] ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Invalid Event.', 'spcc' ) ) ) );
		}

		$event_ID = sanitize_text_field( $_REQUEST['event_id'] );

		$validator  = new Validator();
		$rules      = array(
			'type'        => 'required|in:private,public',
			'title'       => 'required',
			'description' => 'required',
			'start'       => 'required',
			'end'         => 'required',
			'venue'       => 'required',
			'address'     => 'required_if:attendance,physical',
			'city'        => 'required_if:attendance,physical',
			'state'       => 'required_if:attendance,physical',
			'postal_code' => 'required_if:attendance,physical',
			'country'     => 'required_if:attendance,physical',
			'attendance'  => 'required|in:physical,virtual',
			'website'     => 'required_if:attendance,virtual',
			'image'       => 'required',
			'category'    => 'required',
		);
		$validation = $validator->validate( $_REQUEST + $_FILES, $rules );

		if ( $validation->fails() ) {
			wp_send_json_error( array( 'errors' => $validation->errors()->all() ) );
		} else {

			$type = $_REQUEST['type'];

			$attachment_id   = spcc_handle_media_upload( 'image', null );
			$attachment_path = $attachment_id ? get_attached_file( $attachment_id ) : null;
			$is_private      = $type == 'public';

			$valid_keys = spcc_array_except( array_keys( $rules ), [ 'image' ] );
			$valid_keys = array_merge( $valid_keys, array(
				'parking',
				'address2',
				'cost',
				'tickets_url',
				'host',
				'facebook',
				'twitter',
				'instagram',
				'website',
				'pinterest'
			) );
			$data       = spcc_array_only( $_REQUEST, $valid_keys );

			$data['type'] = $type;

			$files = array();
			if ( ! is_null( $attachment_path ) ) {
				$files = array(
					'image' => $attachment_path,
				);
			}

			$events_api = new SPC_Community_Calendar_Events_API();
			$response   = $events_api->update( $event_ID, $data, $files );
			if ( $response->is_error() ) {
				wp_send_json_error( array(
					'message' => 'Please correct the following errors',
					'errors'  => $response->get_errors()
				) );
			} else {
				$event   = $response->get_item();
				$ID      = isset( $event['ID'] ) ? $event['ID'] : null;
				$status  = isset( $event['post_status'] ) ? $event['post_status'] : '';
				$message = $status === 'draft' ? __( 'The event is updated successfully and currently awaits approval.' ) : __( 'The event is updated successfully.' );
				if ( $is_private ) {
					$event_ID = spcc_retrieve_local_event( $ID );
					if ( is_numeric( $event_ID ) ) {
						foreach ( $data as $key => $value ) {
							update_post_meta( $event_ID, 'event_' . $key, $value );
						}
						$old_attachment_id = get_post_thumbnail_id( $event_ID );
						if ( $old_attachment_id != $attachment_id ) {
							wp_delete_attachment( $old_attachment_id, true );
						}
						set_post_thumbnail( $event_ID, $attachment_id );
						update_post_meta( $event_ID, 'remote_event_id', $ID );
					}
				} else {
					wp_delete_attachment( $attachment_id, true );
				}
				wp_send_json_success( array( 'message' => $message, 'item' => $event ) );
			}
		}
	}

	public function handle_delete_event() {
		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Access denied.', 'spcc' ) ) ) );
		}

		if ( ! isset( $_REQUEST['event_id'] ) || empty( $_REQUEST['event_id'] ) || ! is_numeric( $_REQUEST['event_id'] ) ) {
			wp_send_json_error( array( 'errors' => array( __( 'Invalid Event.', 'spcc' ) ) ) );
		}
		$event_ID = sanitize_text_field( $_REQUEST['event_id'] );

		$events_api = new SPC_Community_Calendar_Events_API();
		$response   = $events_api->destroy( $event_ID );

		if ( $response->is_error() ) {
			wp_send_json_error( array(
				'message' => 'Error deleting event',
				'errors'  => $response->get_errors()
			) );
		} else {
			$local_event_ID = spcc_retrieve_local_event( $event_ID );
			if ( is_numeric( $local_event_ID ) ) {
				wp_delete_post( $local_event_ID, true );
			}
			wp_send_json_success( array( 'message' => __( 'Event deleted successfully.' ) ) );
		}
	}

	public function render_edit_form() {
		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			die( 'Access Denied' );
		}
		if ( ! isset( $_GET['event_id'] ) ) {
			die( 'Invalid Event' );
		}
		$id       = sanitize_text_field( $_GET['event_id'] );
		$api      = new SPC_Community_Calendar_Events_API();
		$response = $api->get_event( $id, array( 'fields' => 'all' ) );

		if ( $response->is_error() ) {
			die( 'Error retrieving event.' );
		}

		$event = $response->get_item();
		include SPCC_ROOT_PATH . 'admin/partials/form-edit.php';
		die;
	}

	public function render_delete_form() {
		if ( ! $this->check_referer( 'spcc-nonce', 'nonce' ) ) {
			die( 'Access Denied' );
		}
		if ( ! isset( $_GET['event_id'] ) ) {
			die( 'Invalid Event' );
		}
		$id       = sanitize_text_field( $_GET['event_id'] );
		$api      = new SPC_Community_Calendar_Events_API();
		$response = $api->get_event( $id, array( 'fields' => 'all' ) );

		if ( $response->is_error() ) {
			die( 'Error retrieving event.' );
		}

		$event = $response->get_item();
		include SPCC_ROOT_PATH . 'admin/partials/form-delete.php';
		die;
	}


	public function render_event_form_dynamic() {

		$type = isset( $_POST['attendance'] ) ? $_POST['attendance'] : 'physical';

		$path = SPCC_ROOT_PATH . 'admin/partials/form-create-' . $type . '.php';

		if ( file_exists( $path ) ) {
			include( $path );
		}

		die;

	}


    /**
     * Share post via email
     */
    public function share_post_via_email() {
        if ( ! isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( $_REQUEST['nonce'], 'spcc_nonce' ) ) {
            wp_send_json_error( array(
                'message' => 'Security check failed'
            ) );
            exit;
        }

        $link = isset( $_POST['share_post_url'] ) ? $_POST['share_post_url'] : '';
        $email   = isset( $_POST['share_email'] ) ? $_POST['share_email'] : '';


        if ( empty( $email ) || false === filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            wp_send_json_error( array( 'message' => 'Empty or invalid email address! Please provide valid email address.' ) );
            exit;
        }

        $email_text = 'Someone shared the following this post with you: ' . $link;
        wp_mail($email, 'Someone shared a post with you.', $email_text);
        wp_send_json_success(array(
            'message' => 'Post shared successfully.'
        ));
        exit;
    }

	/**
	 * Check the ajax referer
	 *
	 * @param $nonce_name
	 * @param $query_parameter_key
	 *
	 * @return bool|int
	 */
	function check_referer( $nonce_name, $query_parameter_key ) {
		return check_ajax_referer( $nonce_name, $query_parameter_key, false );
	}

}