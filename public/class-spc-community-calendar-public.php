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

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SPC_Community_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SPC_Community_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spc-community-calendar-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SPC_Community_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SPC_Community_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spc-community-calendar-public.js', array( 'jquery' ), $this->version, false );

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
	 * Display the calendar
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function shortcode_community_calendar($atts) {
		return spcc_get_view('calendar');
	}

}