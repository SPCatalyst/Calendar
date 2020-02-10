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
			'address'     => 'required',
			'country'     => 'required',
			'city'        => 'required',
			'state'       => 'required',
			'postal_code' => 'required',
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
				'address2',
				'cost',
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
			'address'     => 'required',
			'country'     => 'required',
			'city'        => 'required',
			'state'       => 'required',
			'postal_code' => 'required',
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
				'address2',
				'cost',
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

	/**
	 * Render quick view
	 */
	public function render_quick_view() {

		$event_id = isset( $_GET['event_id'] ) && ! empty( $_GET['event_id'] ) ? $_GET['event_id'] : null;

		$repo = new SPC_Community_Calendar_Data_Repository();

		$response = $repo->get_event( $event_id, array( 'fields' => 'all' ) );

		$event = $response->get_item();

		if ( is_null( $event ) || empty( $event ) ) {
			return '';
		}

		echo spcc_get_view( 'quick-view', array( 'event' => $event ) );

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