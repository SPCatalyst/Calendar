<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://darkog.com
 * @since             1.0.0
 * @package           SPC_Community_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       SPC Community Calendar
 * Plugin URI:        https://stpetecatalyst.com
 * Description:       The catalyst events calendar
 * Version:           0.9.2
 * Author:            Darko Gjorgjijoski (stpetecatalyst.com)
 * Author URI:        https://darkog.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spc-community-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SPC_COMMUNITY_CALENDAR_VERSION', '0.9.2' );
define( 'SPCC_PT_EVENT', 'spcc-event' );
define( 'SPCC_ROOT_PATH', plugin_dir_path( __FILE__ ) );
define( 'SPCC_ROOT_FILE', __FILE__ );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spc-community-calendar-activator.php
 */
function activate_spc_community_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spc-community-calendar-activator.php';
	SPC_Community_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spc-community-calendar-deactivator.php
 */
function deactivate_spc_community_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spc-community-calendar-deactivator.php';
	SPC_Community_Calendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_spc_community_calendar' );
register_deactivation_hook( __FILE__, 'deactivate_spc_community_calendar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-spc-community-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_spc_community_calendar() {

	$plugin = new SPC_Community_Calendar();
	$plugin->run();

}

run_spc_community_calendar();


/*add_action( 'init', function () {

	$c_http = new SPC_Community_Calendar_Categories_API();
	$e_http = new SPC_Community_Calendar_Events_API();


	print '<pre>';
	$test = 0;


	// GET CATEGORIES
	if ( $test == 1 ) {
		$cats = $c_http->get_categories();
		var_dump( $cats );
	}


	// GET EVENTS
	if ( $test == 2 ) {
		$events = $e_http->get_events( [ 'per_page' => 10, 'page' => 1 ] );
		var_dump( $events );
	}

	// CREATE EVENT
	if ( $test == 3 ) {
		$event = $e_http->create( '2a577bd9ffb02cadfcb4fea5842064b8', [
			'title'       => 'Some event name',
			'description' => 'Some event description',
			'start'       => '2020-01-20',
			'end'         => '2020-01-25',
			'venue'       => 'Venue Demo',
			'address'     => 'Address Demo 145',
			'city'        => 'St Petersburg',
			'state'       => 'FL',
			'postal_code' => '12818',
			'country'     => 'US',
			'image'       => '',
		] );

		var_dump( $event->get_errors() );
	}


	if ( $test > 0 ) {
		die;
	}

} );*/
