<?php

/**
 * Fired during plugin activation
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$settings = new SPC_Community_Calendar_Settings();

		if ( ! $settings->has_settings() ) {
			$settings->import();
		}

		$settings->refresh();
		$events_page = $settings->get( 'events_page', null );

		if ( is_null( $events_page ) ) {

			$ID = wp_insert_post( array(
				'post_type'    => 'page',
				'post_title'   => 'Events',
				'post_status'  => 'publish',
				'post_content' => '[community_calendar]'
			) );

			if ( ! is_wp_error( $ID ) ) {
				$settings->save( array(
					'events_page' => $ID,
				) );
			} else {
				error_log( 'SPCC ERROR: Error creating events page. Go to the Settings page to set this manually.' );
			}
		}

		// Set permalinks flushed flag to 0 initially.
		update_option( 'spcc_permalinks_flushed', 0 );

	}

}
