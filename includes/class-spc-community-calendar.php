<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SPC_Community_Calendar_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SPC_COMMUNITY_CALENDAR_VERSION' ) ) {
			$this->version = SPC_COMMUNITY_CALENDAR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'spc-community-calendar';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_shortcodes();
		$this->configure_update_checker();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SPC_Community_Calendar_Loader. Orchestrates the hooks of the plugin.
	 * - SPC_Community_Calendar_i18n. Defines internationalization functionality.
	 * - SPC_Community_Calendar_Admin. Defines all hooks for the admin area.
	 * - SPC_Community_Calendar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/PUC/plugin-update-checker.php';

		/**
		 * The class responsible for formatting the API response
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helpers.php';

		/**
		 * The class responsible for formatting the API response
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-event.php';

		/**
		 * The class responsible for formatting the API response
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-spc-community-calendar-multipart.php';

		/**
		 * The class responsible for formatting the API response
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-spc-community-calendar-api-response.php';

		/**
		 * The class responsible for working with the API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-spc-community-calendar-api.php';

		/**
		 * The class responsible for working with the Events API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-spc-community-calendar-events-api.php';

		/**
		 * The class responsible for working with the Categories API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-spc-community-calendar-categories-api.php';

		/**
		 * The class responsible for working with the Categories API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/API/class-spc-community-calendar-filters-api.php';

		/**
		 * The class responsible for working with the Data
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-data-repository.php';

		/**
		 * The class responsible for working with the settings
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-settings.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-events-list.php';

		/**
		 * The class responsible for defining all ajax actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-spc-community-calendar-ajax.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-spc-community-calendar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-spc-community-calendar-public.php';

		$this->loader = new SPC_Community_Calendar_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SPC_Community_Calendar_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new SPC_Community_Calendar_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new SPC_Community_Calendar_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'footer_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SPC_Community_Calendar_Public( $this->get_plugin_name(), $this->get_version() );

		$plugin_public->init_custom_rewrites();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'print_theme_stylesheet' );
		$this->loader->add_action( 'init', $plugin_public, 'register_post_types' );

		$ajax_actions = new SPC_Community_Calendar_AJAX();

		$this->loader->add_action( 'wp_ajax_spcc_render_edit_form', $ajax_actions, 'render_edit_form' );
		$this->loader->add_action( 'wp_ajax_spcc_render_delete_form', $ajax_actions, 'render_delete_form' );

		$this->loader->add_action( 'wp_ajax_spcc_render_form_dynamic', $ajax_actions, 'render_event_form_dynamic' );

		$this->loader->add_action( 'wp_ajax_spcc_create_event', $ajax_actions, 'handle_create_event' );
		$this->loader->add_action( 'wp_ajax_spcc_update_event', $ajax_actions, 'handle_update_event' );
		$this->loader->add_action( 'wp_ajax_spcc_delete_event', $ajax_actions, 'handle_delete_event' );

		$this->loader->add_action( 'wp_ajax_spcc_render_quickview', $ajax_actions, 'render_quick_view' );
		$this->loader->add_action( 'wp_ajax_nopriv_spcc_render_quickview', $ajax_actions, 'render_quick_view' );
	}

	/**
	 * Register all the shortcodes
	 */
	private function define_shortcodes() {
		$plugin_public = new SPC_Community_Calendar_Public( $this->get_plugin_name(), $this->get_version() );

		add_shortcode( 'community_calendar', array( $plugin_public, 'shortcode_community_calendar' ) );
	}

	/**
	 * Configures the plugin update checker to watch the Github repo.
	 */
	public function configure_update_checker() {
		$updater = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/SPCatalyst/Calendar',
			SPCC_ROOT_FILE,
			'spc-community-calendar'
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    SPC_Community_Calendar_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
