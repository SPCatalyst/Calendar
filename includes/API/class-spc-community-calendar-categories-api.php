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
class SPC_Community_Calendar_Categories_API extends SPC_Community_Calendar_API {

	/**
	 * Returns list of categories
	 *
	 * @param array $params
	 *
	 * @return SPC_Community_Calendar_API_Response
	 */
	public function get_categories( $params = array() ) {
		$response = $this->get( 'categories', $params );

		return SPC_Community_Calendar_API_Response::create( $response );
	}

}