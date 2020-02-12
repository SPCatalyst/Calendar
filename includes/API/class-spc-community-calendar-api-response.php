<?php
/**
 * This is used to define the master API response
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes/API
 */

/**
 * This is used to define the master API response
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes/API
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_API_Response {

	public $code;
	public $item;
	public $items;
	public $errors;
	public $raw;

	/**
	 * Set the response code
	 *
	 * @param $code
	 */
	public function set_code( $code ) {
		$this->code = $code;
	}

	/**
	 * Set the response item
	 *
	 * @param $item
	 */
	public function set_item( $item ) {
		$this->item = $item;
	}

	/**
	 * Set the response items
	 *
	 * @param $items
	 */
	public function set_items( $items ) {
		$this->items = $items;
	}

	/**
	 * Set the response errors
	 *
	 * @param $errors
	 */
	public function set_errors( $errors ) {
		if ( ! is_array( $errors ) ) {
			$errors = array( $errors );
		}
		$this->errors = $errors;
	}

	/**
	 * Returns code
	 * @return mixed
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Returns item
	 * @return mixed
	 */
	public function get_item() {
		return $this->item;
	}

	/**
	 * Return items
	 * @return mixed
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Returns errors
	 * @return mixed
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Is error?
	 * @return bool
	 */
	public function is_error() {
		return is_array( $this->errors ) && count( $this->errors ) > 0;
	}

	/**
	 * Returns specific header.
	 *
	 * @param $header
	 *
	 * @return |null
	 */
	public function get_header( $header ) {
		return isset( $this->raw['headers'][ $header ] ) ? $this->raw['headers'][ $header ] : null;
	}

	/**
	 * Returns specific item param
	 *
	 * @param $key
	 *
	 * @return null
	 */
	public function get_item_param( $key ) {
		return isset( $this->item[ $key ] ) ? $this->item[ $key ] : null;
	}

	/**
	 * Create response object
	 *
	 * @param $response
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public static function create( $response ) {

		$new_response = new self();

		if ( is_wp_error( $response ) || $response instanceof WP_Error ) {
			$code  = $response->get_error_code();
			$error = $response->get_error_message();
			$new_response->set_code( $code );
			$new_response->set_errors( $error );
		} else {
			$new_response->errors = self::get_response_data( $response, 'errors', array() );
			$new_response->items  = self::get_response_data( $response, 'items', array() );
			$new_response->item   = self::get_response_data( $response, 'item', null );
			$new_response->code   = self::get_response_data( $response, 'code', null );
			$message              = self::get_response_data( $response, 'message', null );
			$new_response->raw    = $response;

			if ( ! empty( $message ) ) {
				$new_response->set_errors( $message );
			}
		}

		return $new_response;

	}


	/**
	 * Returns the response data.
	 *
	 * @param $response
	 *
	 * @param $key
	 *
	 * @param null $default
	 *
	 * @return array
	 */
	public static function get_response_data( $response, $key = null, $default = null ) {
		$body = wp_remote_retrieve_body( $response );
		$data = $default;
		if ( ! empty( $body ) ) {
			$decoded = json_decode( $body, true );
			if ( is_null( $key ) ) {
				$data = $decoded;
			} else {
				if ( isset( $decoded[ $key ] ) ) {
					$data = $decoded[ $key ];
				}
			}

		}

		return $data;
	}

}