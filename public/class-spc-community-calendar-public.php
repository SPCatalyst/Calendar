<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/public
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Public {

	const MAPS_PROVIDER_GOOGLE = 'google';
	const MAPS_PROVIDER_LEAFLET = 'leaflet';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The active maps provider
	 *
	 * @var int
	 */
	private $maps_provider;

	/**
	 * The settings bundle
	 *
	 * @var SPC_Community_Calendar_Settings
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->version       = $version;
		$this->settings      = new SPC_Community_Calendar_Settings();
		$this->maps_provider = $this->settings->get( 'maps_provider', self::MAPS_PROVIDER_LEAFLET );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-grid', plugin_dir_url( __FILE__ ) . 'css/grid.css', array(), null, 'all' );
		wp_enqueue_style( $this->plugin_name . '-iconfont', plugin_dir_url( __FILE__ ) . 'resources/iconfont/css/spccicons.css', array(), null, 'all' );
		$last_updated = filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'css/spc-community-calendar-public.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spc-community-calendar-public.css', array(), $last_updated, 'all' );
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'resources/jquery-ui/jquery-ui.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'remodal', plugin_dir_url( __FILE__ ) . 'resources/remodal/remodal-default-theme.css', array(), $this->version, 'all' );

		if ( $this->maps_provider === self::MAPS_PROVIDER_LEAFLET ) {
			wp_enqueue_style( 'leaflet', plugin_dir_url( __FILE__ ) . 'resources/leaflet/leaflet.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'remodal', plugin_dir_url( __FILE__ ) . 'resources/remodal/remodal.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'loadingoverlay', plugin_dir_url( __FILE__ ) . 'resources/loadingoverlay.min.js', array( 'jquery' ), null, true );

		$last_updated = filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'js/spc-community-calendar-public.js' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spc-community-calendar-public.js', array( 'jquery' ), $last_updated, true );

		wp_localize_script( $this->plugin_name, 'SPCC', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'spcc_nonce' ),
		) );

		if ( $this->maps_provider === self::MAPS_PROVIDER_GOOGLE ) {
			$settings = new SPC_Community_Calendar_Settings();
			wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?key=' . $settings->get( 'google_maps_key' ), null, null, true );
			$last_updated = filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'js/spc-community-calendar-gmaps.js' );
			wp_enqueue_script( $this->plugin_name . '-gmaps', plugin_dir_url( __FILE__ ) . 'js/spc-community-calendar-gmaps.js', array( 'jquery' ), $last_updated, true );
		} else if ( $this->maps_provider === self::MAPS_PROVIDER_LEAFLET ) {
			$last_updated = filemtime( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'js/spc-community-calendar-leaflet.js' );
			wp_enqueue_script( 'leaflet', plugin_dir_url( __FILE__ ) . 'resources/leaflet/leaflet.js', array( 'jquery' ), $last_updated, true );
			wp_enqueue_script( $this->plugin_name . '-leaflet', plugin_dir_url( __FILE__ ) . 'js/spc-community-calendar-leaflet.js', array( 'jquery' ), $last_updated, true );
		}
	}


	/**
	 * Register post types
	 */
	public function register_post_types() {
		$labels  = array(
			'name'                  => _x( 'Events', 'Post Type General Name', 'spc-cc' ),
			'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'spc-cc' ),
			'menu_name'             => __( 'Events', 'spc-cc' ),
			'name_admin_bar'        => __( 'Event', 'spc-cc' ),
			'archives'              => __( 'Item Archives', 'spc-cc' ),
			'attributes'            => __( 'Item Attributes', 'spc-cc' ),
			'parent_item_colon'     => __( 'Parent Item:', 'spc-cc' ),
			'all_items'             => __( 'All Items', 'spc-cc' ),
			'add_new_item'          => __( 'Add New Item', 'spc-cc' ),
			'add_new'               => __( 'Add New', 'spc-cc' ),
			'new_item'              => __( 'New Item', 'spc-cc' ),
			'edit_item'             => __( 'Edit Item', 'spc-cc' ),
			'update_item'           => __( 'Update Item', 'spc-cc' ),
			'view_item'             => __( 'View Item', 'spc-cc' ),
			'view_items'            => __( 'View Items', 'spc-cc' ),
			'search_items'          => __( 'Search Item', 'spc-cc' ),
			'not_found'             => __( 'Not found', 'spc-cc' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'spc-cc' ),
			'featured_image'        => __( 'Featured Image', 'spc-cc' ),
			'set_featured_image'    => __( 'Set featured image', 'spc-cc' ),
			'remove_featured_image' => __( 'Remove featured image', 'spc-cc' ),
			'use_featured_image'    => __( 'Use as featured image', 'spc-cc' ),
			'insert_into_item'      => __( 'Insert into item', 'spc-cc' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'spc-cc' ),
			'items_list'            => __( 'Items list', 'spc-cc' ),
			'items_list_navigation' => __( 'Items list navigation', 'spc-cc' ),
			'filter_items_list'     => __( 'Filter items list', 'spc-cc' ),
		);
		$rewrite = array(
			'slug'       => 'events',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$args    = array(
			'label'               => __( 'Event', 'spc-cc' ),
			'description'         => __( 'Event Description', 'spc-cc' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
			'rest_base'           => 'spc-cc',
		);
		register_post_type( SPCC_PT_EVENT, $args );
	}

	/**
	 * Display events grid
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function shortcode_community_calendar_mini( $atts ) {
		$atts = shortcode_atts( array(
			'type'     => 'any', // Possible Values: private | featured | any
			'per_page' => 4,
		), $atts );

		// Static config
		$config = array(
			'per_page' => $atts['per_page'],
			'fields'   => 'all',
			'orderby'  => 'date',
			'order'    => 'asc',
		);
		if ( $atts['type'] === 'private' ) {
			$config['parent'] = get_option( 'spcc_website_id' );
		} else if ( $atts['type'] === 'featured' ) {
			$config['featured'] = 1;
		}

		$repo   = new SPC_Community_Calendar_Data_Repository();
		$query  = $repo->get_events( $config );
		$events = $query->get_items();

		return '<div class="spcc-featured-events">' . spcc_get_view( 'events-grid-home', array(
				'events_list' => $events,
				'config'      => $config
			) ) . '</div>';
	}


	/**
	 * Display the calendar
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function shortcode_community_calendar( $atts ) {

		global $wp_query;

		$single_event_name = get_query_var( 'spccevent' );
		if ( ! empty( $single_event_name ) ) {
			return spcc_get_view( 'single' );
		} else {
			return spcc_get_view( 'events' );
		}
	}


	public function print_theme_stylesheet() {

		$settings = new SPC_Community_Calendar_Settings();
		$color    = $settings->get( 'color_schemes', array() );

		$primary = isset( $color[0] ) ? $color[0] : '#3EE4dB';

		?>
        <style type="text/css">
            .spcc-post-actions a i.spcc-icon {
                color: <?php echo $primary; ?>;
            }

            .spcc-inline-list a.active {
                color: <?php echo $primary; ?>;
            }

            .spcc-btn-primary {
                background-color: <?php echo $primary; ?>;
            }

            .spcc-events-main--filters ul li > a.current {
                color: <?php echo $primary; ?>;
            }

            .spcc-nav-links a {
                color: <?php echo $primary; ?> !important;
            }

            .spcc-event-details-val {
                color: <?php echo $primary; ?>;
            }

            .spcc-event-socials li a {
                background: <?php echo $primary; ?>;
            }

            .spcc-submit-box .spcc-h3 {
                color: <?php echo $primary; ?>;
            }
        </style>
		<?php
	}


	/**
	 * Setup the single event vars
	 *
	 * @param $vars
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'spccevent';

		return $vars;
	}


	/**
	 * Initializes the rewrite rules
	 */
	public function add_rewrite_rules() {

		$settings = new SPC_Community_Calendar_Settings();

		$events_page_ID = $settings->get( 'events_page', null );
		if ( is_null( $events_page_ID ) ) {
			error_log( 'SPCC ERROR: Events page NOT set! Please try activating and deactivating the SPC Community Calendar plugin.' );

			return;
		}
		$slug = get_post_field( 'post_name', $events_page_ID );
		if ( empty( $slug ) ) {
			error_log( 'SPCC ERROR: Events page slug invalid.' );

			return;
		}

		add_rewrite_rule( $slug . '/([^/]+)/?$', 'index.php?pagename=' . $slug . '&spccevent=$matches[1]', 'top' );

		// Hard flush, if needed.
		$permalinks_flushed = get_option( 'spcc_permalinks_flushed' );
		if ( ! $permalinks_flushed || empty( $permalinks_flushed ) ) {
			flush_rewrite_rules( true );
			update_option( 'spcc_permalinks_flushed', 1 );
		}
	}


	/**
	 * Set the event object od trigger 404
	 */
	public function set_event_object() {

		global $spccevent;
		$event_name = get_query_var( 'spccevent', null );
		if ( ! is_null( $event_name ) ) {
			$repo     = new SPC_Community_Calendar_Data_Repository();
			$response = $repo->get_event_by_slug( $event_name, array( 'fields' => 'all' ) );

			if ( $response->is_error() ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
				exit();
			} else {
				$spccevent = new SPC_Community_Calendar_Event( $response->get_item() );
			}
		}
	}

	/**
	 * Initialize the custom rewrites.
	 */
	public function init_custom_rewrites() {
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0, 1 );
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'set_event_object' ) );
	}


}
