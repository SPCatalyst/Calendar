<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/admin
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Admin {

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
	 * Is plugin page?
	 * @var bool
	 */
	private $is_plugin_page;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name    = $plugin_name;
		$this->version        = $version;
		$this->is_plugin_page = isset( $_GET['page'] ) && $_GET['page'] === 'spcc_events' || isset( $_GET['page'] ) && $_GET['page'] === 'spcc_events_settings';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'jquery-datetimepicker', plugin_dir_url( __FILE__ ) . 'resources/datepicker/datepicker.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'jquery-modal', plugin_dir_url( __FILE__ ) . 'resources/jquery-modal/jquery.modal.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/spc-community-calendar-admin.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( $this->is_plugin_page ) {

			wp_enqueue_media();

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_script( 'jquery-datetimepicker', plugin_dir_url( __FILE__ ) . 'resources/datepicker/datepicker.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'jquery-datetimepicker-en', plugin_dir_url( __FILE__ ) . 'resources/datepicker/i18n/datepicker.en.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'jquery-modal', plugin_dir_url( __FILE__ ) . 'resources/jquery-modal/jquery.modal.min.js', array( 'jquery' ), $this->version, false );

			wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'resources/select2/select2.min.css', null, $this->version, 'all' );
			wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'resources/select2/select2.min.js', array( 'jquery' ), $this->version, false );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/spc-community-calendar-admin.js', array( 'jquery' ), filemtime( plugin_dir_path( __FILE__ ) . 'js/spc-community-calendar-admin.js' ), true );
			wp_localize_script( $this->plugin_name, 'SPCC', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'spcc-nonce' ),
			) );
		}
	}

	/**
	 * Registers the admin menu
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'Events', 'spcc' ),
			'Events',
			'manage_options',
			'spcc_events',
			function () {
				include( 'partials/events.php' );
			},
			'dashicons-calendar',
			5
		);


		add_submenu_page(
			'spcc_events',
			'Settings',
			'Settings',
			'manage_options',
			'spcc_events_settings',
			function () {
				include( 'partials/settings.php' );
			}
		);
	}


	/**
	 * Add the required javascript
	 * @return void
	 */
	public function footer_scripts() {
		if ( ! $this->is_plugin_page ) {
			return;
		}
		?>
        <script>
            (function ($) {
                // The "Upload" button
                $(document).on('click', '.upload_image_button', function () {
                    var send_attachment_bkp = wp.media.editor.send.attachment;
                    var button = $(this);
                    wp.media.editor.send.attachment = function (props, attachment) {
                        $(button).parent().prev().attr('src', attachment.url);
                        $(button).prev().val(attachment.id);
                        wp.media.editor.send.attachment = send_attachment_bkp;
                    };
                    wp.media.editor.open(button);
                    return false;
                });
                // The "Remove" button (remove the value from input type='hidden')
                $(document).on('click', '.remove_image_button', function () {
                    var answer = confirm('Are you sure?');
                    if (answer) {
                        var src = $(this).parent().prev().attr('data-src');
                        $(this).parent().prev().attr('src', src);
                        $(this).prev().prev().val('');
                    }
                    return false;
                });
            })(jQuery);
        </script>
		<?php
	}

}
