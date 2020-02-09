<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Settings {

	private $key = 'spcc_settings';
	private $data = array();

	/**
	 * SPC_Community_Calendar_Settings constructor.
	 *
	 * @param bool $load
	 */
	public function __construct() {
		$this->load();
	}

	/**
	 * Return single setting
	 *
	 * @param $key
	 * @param null $default
	 *
	 * @return array|object|string|null
	 */
	public function get( $key, $default = null ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;
	}

	/**
	 * Load the settings
	 */
	public function load() {
		$this->data = get_option( $this->key );
	}

	/**
	 * Saves the data
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function save( $data ) {

		$values = array();
		if ( is_array( $data ) ) {
			foreach ( $this->get_allowed_settings() as $key ) {
				if ( isset( $data[ $key ] ) ) {
					$values[ $key ] = $data[ $key ];
				}
			}
		}
		if ( empty( $values ) ) {
			return false;
		}
		$this->data = $values;
		update_option( $this->key, $this->data );

		return true;
	}

	/**
	 * Save the current settings request.
	 *
	 * @return bool
	 */
	public function saveRequest() {
		$values = array();
		foreach ( $this->get_allowed_settings() as $key ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$values[ $key ] = is_array( $_POST[ $key ] ) ? $_POST[ $key ] : sanitize_text_field( $_POST[ $key ] );
		}

		return $this->save( $values );
	}

	/**
	 * Check if the plugin has saved settings
	 * @return bool
	 */
	public function has_settings() {
		return ! empty( $this->data );
	}

	/**
	 * Purges all the settings
	 */
	public function purge() {
		delete_option( $this->key );
		$this->data = array();
	}

	/**
	 * Returns the allowed settings
	 * @return array
	 */
	public function get_allowed_settings() {
		return array(
			'preferred_categories',
			'preferred_filters',
			'color_scheme',
			'type',
			'visibility',
			'logo',
		);
	}


	/**
	 * Converts the current settings to JSON
	 * @return false|string
	 */
	public function to_json() {
		if ( ! is_array( $this->data ) ) {
			$this->data = array();
		}

		return json_encode( $this->data, JSON_PRETTY_PRINT );
	}

	/**
	 * Converts the current settings to Array
	 * @return array
	 */
	public function to_array() {
		if ( ! is_array( $this->data ) ) {
			$this->data = array();
		}

		return $this->data;
	}


	/**
	 * Import form json
	 *
	 * @param $json_file
	 *
	 * @return bool
	 */
	public function import( $json_file = null ) {

		if ( is_null( $json_file ) ) {
			$json_file = trailingslashit( SPCC_ROOT_PATH ) . 'config.json';
		}

		if ( ! file_exists( $json_file ) ) {
			return false;
		}
		ob_start();
		include $json_file;
		$json = ob_get_clean();
		$json = trim( $json );
		$data = @json_decode( $json, true );
		if ( ! is_array( $data ) ) {
			$data = array();
		}

		return $this->save( $data );

	}

}
