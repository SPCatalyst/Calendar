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
class SPC_Community_Calendar_Events_API extends SPC_Community_Calendar_API {

	/**
	 * Single event
	 *
	 * @param $id
	 *
	 * @param array $params
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_event( $id, $params = array() ) {
		$response = $this->get( "events/{$id}", $params );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

	/**
	 * Returns list of events
	 *
	 * @param $params
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_events( $params = [] ) {
		$response = $this->get( 'events', $params );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

	/**
	 * Creates specific event in the main database
	 *
	 * @param $data
	 *
	 * @param $files
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function create( $data, $files = array() ) {

		$data['api_token'] = get_option( 'spcc_token' );

		$response = $this->post_multipart( 'events', $data, $files, [ 'X-API-KEY' => $data['api_token'] ] );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

	/**
	 * Updates specific event in the main database
	 *
	 * @param $id
	 * @param $data
	 *
	 * @param array $files
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function update( $id, $data, $files = array() ) {

		$data['api_token'] = get_option( 'spcc_token' );

		$response = $this->post_multipart( "events/{$id}", $data, $files, [ 'X-API-KEY' => $data['api_token'] ] );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

	/**
	 * Updates specific event in the main database
	 *
	 * @param $id
	 * @param $data
	 *
	 * @param array $files
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function destroy( $id ) {

		$data['api_token'] = get_option( 'spcc_token' );

		$response = $this->delete( "events/{$id}", [ 'X-API-KEY' => $data['api_token'] ] );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

}