<?php

/**
 * This class is used as a bridge between the api and the data required.
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Data_Repository {

	protected $api;
	/**
	 * Instance of the Events api
	 * @var SPC_Community_Calendar_Events_API
	 */
	protected $events_api;

	/**
	 * @var SPC_Community_Calendar_Categories_API
	 */
	protected $categories_api;
	/**
	 * @var SPC_Community_Calendar_Filters_API
	 */
	protected $filters_api;

	/**
	 * Initializes the data repository
	 *
	 * SPC_Community_Calendar_Data_Repository constructor.
	 */
	public function __construct() {
		$this->api            = new SPC_Community_Calendar_API();
		$this->events_api     = new SPC_Community_Calendar_Events_API();
		$this->categories_api = new SPC_Community_Calendar_Categories_API();
		$this->filters_api    = new SPC_Community_Calendar_Filters_API();
	}

	/**
	 * Returns single event from storage
	 *
	 * @param $id
	 * @param array $params
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_event( $id, $params = array() ) {
		$event = $this->events_api->get_event($id, $params);

		return $event;
	}

	/**
	 * Returns the events
	 *
	 * @param array $params
	 *
	 * @param string $format
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_events( $params = array() ) {
		$events = $this->events_api->get_events( $params );

		return $events;
	}

	/**
	 * Returns the categories
	 *
	 * @param array $params
	 *
	 * @param string $format
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_categories( $params = array() ) {
		$categories = $this->categories_api->get_categories( $params );

		return $categories;
	}

	/**
	 * Returns
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function get_categories_collection( $params = array() ) {
		$response = $this->get_categories();
		$items    = method_exists( $response, 'get_items' ) && is_array( $response->get_items() ) ? $response->get_items() : [];

		return $items;
	}

	/**
	 * Returns the filters
	 *
	 * @param array $params
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_filters( $params = array() ) {
		$filters = $this->filters_api->get_filters( $params );

		return $filters;
	}

	/**
	 * Returns the filters formatted
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function get_filters_collection( $params = array() ) {
		$response = $this->get_filters();
		$items    = method_exists( $response, 'get_items' ) && is_array( $response->get_items() ) ? $response->get_items() : [];

		return $items;
	}


	public function get_account() {
		$account = $this->api->get_account();

		return $account;
	}


	public function set_cache( $key, $data, $ttl ) {

	}

	public function remove_cache( $key ) {

	}

	public function purge_cache() {

	}

}