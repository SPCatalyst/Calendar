<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


/**
 * Used to list the events in the backend
 *
 * @since      1.0.0
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/includes
 * @author     Darko Gjorgjijoski <dg@darkog.com>
 */
class SPC_Community_Calendar_Events_List_Table extends WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'singular' => 'event',
			'plural'   => 'events',
			'ajax'     => false
		) );
	}

	function get_table_classes() {
		return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
	}

	/**
	 * Message to show if no designation found
	 *
	 * @return void
	 */
	function no_items() {
		_e( 'No events found', 'spcc' );
	}

	/**
	 * Default column values if no callback found
	 *
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'title':
				return $item['post_title'];

			case 'start':
				return $item['meta']['event_start'];

			case 'end':
				return $item['meta']['event_end'];

			case 'venue':
				return $item['meta']['event_venue'];

			default:
				return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
		}
	}

	/**
	 * Get the column names
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'    => '<input type="checkbox" />',
			'title' => __( 'Title', 'spcc' ),
			'start' => __( 'Start', 'spcc' ),
			'end'   => __( 'End', 'spcc' ),
			'venue' => __( 'Venue', 'spcc' ),

		);

		return $columns;
	}

	/**
	 * Render the designation name column
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	function column_title( $item ) {
		$actions    = array();

		$edit_url   = add_query_arg( 'action', 'spcc_render_edit_form', admin_url( 'admin-ajax.php' ) );
		$edit_url   = add_query_arg( 'nonce', wp_create_nonce( 'spcc-nonce' ), $edit_url );
		$edit_url   = add_query_arg( 'event_id', $item['ID'], $edit_url );
		$delete_url = add_query_arg( 'action', 'spcc_render_delete_form', admin_url( 'admin-ajax.php' ) );
		$delete_url = add_query_arg( 'nonce', wp_create_nonce( 'spcc-nonce' ), $delete_url );
		$delete_url = add_query_arg( 'event_id', $item['ID'], $delete_url );

		$actions['edit']   = sprintf( '<a href="%s" data-id="%d" rel="modal:open" class="event-action event-edit" title="%s">%s</a>', $edit_url, $item['ID'], __( 'Edit this item', 'spcc' ), __( 'Edit', 'spcc' ) );
		$actions['delete'] = sprintf( '<a href="%s" data-id="%d" rel="modal:open" class="event-action event-delete submitdelete" title="%s">%s</a>', $delete_url, $item['ID'], __( 'Delete this item', 'spcc' ), __( 'Delete', 'spcc' ) );

		return sprintf( '<a href="%1$s" rel="modal:open"><strong>%2$s</strong></a> %3$s', $edit_url, $item['post_title'], $this->row_actions( $actions ) );
	}

	/**
	 * Get sortable columns
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Set the bulk actions
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(//'trash' => __( 'Move to Trash', 'spcc' ),
			'delete' => __( 'Delete' ),
		);

		return $actions;
	}

	/**
	 * Render the checkbox column
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="event_id[]" class="event_checkbox" value="%d" />', $item['ID']
		);
	}

	/**
	 * Set the views
	 *
	 * @return array
	 */
	public function get_views() {
		$status_links = array();
		$base_link    = admin_url( 'admin.php?page=spcc_events' );

		$public_active  = ( isset( $_GET['type'] ) && $_GET['type'] === 'public' ) || empty( $_GET['type'] ) ? 'current' : '';
		$private_active = ( isset( $_GET['type'] ) && $_GET['type'] === 'private' ) ? 'current' : '';

		$status_links['public']  = sprintf( '<a href="%s" class="%s">%s</a>', add_query_arg( array( 'type' => 'public' ), $base_link ), $public_active, 'Public' );
		$status_links['private'] = sprintf( '<a href="%s" class="%s">%s</a>', add_query_arg( array( 'type' => 'private' ), $base_link ), $private_active, 'Private' );


		return $status_links;
	}

	/**
	 * Prepare the class items
	 *
	 * @return void
	 */
	function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page          = 5;
		$current_page      = $this->get_pagenum();
		$this->page_status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';

		// only ncessary because we have sample data
		$website_id = get_option( 'spcc_website_id' );
		if ( empty( $website_id ) ) {
			$website_id = time();
		}
		$args = array(
			'page'     => $current_page,
			'per_page' => $per_page,
			'parent'   => $website_id,
		);

		if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
			$args['order']   = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) {
			$args['search'] = sanitize_text_field( $_REQUEST['s'] );
		}

		$repository = new SPC_Community_Calendar_Data_Repository();

		$args['fields'] = 'all';

		$response = $repository->get_events( $args );

		$total_items = (int) $response->get_header( 'X-WP-Total' );

		$this->items = $response->get_items();

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );
	}

}
