<?php

/**
 * This is used to define the master API data calls
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes/API
 */

/**
 * This is used to define the master API data calls
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes/API
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_API {

	/**
	 * The api base
	 * @var string
	 */
	protected $api_base = 'https://stpetecatalyst.com/wp-json/communitycalendar/v1/';


	/**
	 * Performs a GET request
	 *
	 * @param $uri
	 * @param $data
	 * @param array $headers
	 *
	 * @return array|WP_Error
	 */
	public function get( $uri, $data = [], $headers = [] ) {

		$url = $this->api_base . $uri;

		$request = [];

		if ( ! empty( $data ) ) {
			//$request['body'] = $data;
			foreach ( $data as $key => $value ) {
				$url = add_query_arg( $key, $value, $url );
			}
		}

		if ( ! empty( $headers ) ) {
			$request['headers'] = $headers;
		}

		$request['timeout'] = 7200;

		$response = wp_remote_get( $url, $request );

		return $response;

	}

	/**
	 * Performs a POST request
	 *
	 * @param $uri
	 * @param $data
	 * @param array $headers
	 *
	 * @return array|WP_Error
	 */
	public function post( $uri, $data, $headers = [] ) {

		$url = $this->api_base . $uri;

		$request = [];

		if ( ! empty( $data ) ) {
			$request['body'] = $data;
		}

		if ( ! empty( $headers ) ) {
			$request['headers'] = $headers;
		}

		$request['timeout'] = 7200;

		$response = wp_remote_post( $url, $request );

		return $response;
	}

	/**
	 * Performs a POST request
	 *
	 * @param $uri
	 * @param $data
	 * @param array $files
	 *
	 * @param array $headers
	 *
	 * @return array|WP_Error
	 */
	public function post_multipart( $uri, $data, $files = array(), $headers = array() ) {

		$url = $this->api_base . $uri;

		$request = [];

		$multipart_info = spcc_build_multipart_request( $data, $files );

		if ( ! empty( $multipart_info['body'] ) ) {
			$request['body'] = $multipart_info['body'];
		}

		if ( ! empty( $multipart_info['content-type'] ) ) {
			$headers['content-type'] = $multipart_info['content-type'];
		}

		if ( ! empty( $headers ) ) {
			$request['headers'] = $headers;
		}

		$request['timeout'] = 7200;

		$response = wp_remote_post( $url, $request );

		return $response;
	}

	/**
	 * Performs a DELETE request
	 *
	 * @param $uri
	 * @param $headers
	 *
	 * @return array|WP_Error
	 */
	public function delete( $uri, $headers ) {

		$url = $this->api_base . $uri;

		$request = [ 'method' => 'DELETE' ];

		if ( ! empty( $headers ) ) {
			$request['headers'] = $headers;
		}

		$request['timeout'] = 7200;

		$response = wp_remote_request( $url, $request );

		return $response;
	}

	/**
	 * Returns the account
	 */
	public function get_account() {
		$token    = $this->get_account_token();
		$response = $this->get( 'account', array( 'api_token' => $token ) );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

	/**
	 * Register account
	 *
	 * @param $data
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function register_account( $data ) {
		$response = $this->post( 'account', $data );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

	/**
	 * Returns the account token
	 */
	public function get_account_token() {
		$token = get_option( 'spcc_token' );

		return $token;
	}


}