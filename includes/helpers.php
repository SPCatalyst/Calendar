<?php
/**
 * Format array to key => value pairs
 *
 * @param $arr
 * @param $key
 * @param $value
 *
 * @return array
 */
function spcc_array_key_value( $arr, $key, $value ) {
	if ( ! is_array( $arr ) ) {
		return array();
	}

	$new_arr = array();

	foreach ( $arr as $item ) {
		if ( isset( $item[ $key ] ) && isset( $item[ $value ] ) ) {
			$new_arr[ $item[ $key ] ] = $item[ $value ];
		}
	}

	return $new_arr;
}

/**
 * Used to upload single image into WP, returns false or the attachment_id that is created in the db
 * Example input element: <input type=file name=file_handler/>
 *
 * @param $file_handler
 * @param $post_id
 *
 * @return bool|int|WP_Error
 */
function spcc_handle_media_upload( $file_handler, $post_id ) {
	if ( ! file_exists( $_FILES[ $file_handler ]['tmp_name'] ) || ! is_uploaded_file( $_FILES[ $file_handler ]['tmp_name'] ) ) {
		return false;
	}
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	$attachment_id = media_handle_upload( $file_handler, $post_id );
	if ( ! is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	} else {
		return false;
	}
}

/**
 * Array ONLY.
 *
 * @param $data
 * @param $only
 *
 * @return array
 */
function spcc_array_only( $data, $only ) {
	$new_data = array();
	foreach ( $data as $key => $value ) {
		if ( in_array( $key, $only ) ) {
			$new_data[ $key ] = $value;
		}
	}

	return $new_data;
}

/**
 * Array EXCEPT
 *
 * @param $data
 * @param $except
 *
 * @return array
 */
function spcc_array_except( $data, $except ) {
	$new_data = array();
	foreach ( $data as $key => $value ) {
		if ( ! in_array( $key, $except ) ) {
			$new_data[ $key ] = $value;
		}
	}

	return $new_data;
}

/**
 * Build multipart request
 *
 * @param $fields
 * @param $files
 *
 * @return array
 */
function spcc_build_multipart_request( $fields, $files ) {

	$multipart = new SPC_Community_Calendar_Multipart();
	$multipart->addArray( $fields );
	foreach ( $files as $key => $file ) {
		$multipart->addFile( $key, $file );
	}

	return array(
		'content-type' => $multipart->contentType(),
		'body'         => $multipart->data(),
	);
}

/**
 * @param $remote_event_ID
 *
 * @return string|null
 */
function spcc_retrieve_local_event( $remote_event_ID ) {
	global $wpdb;
	$query  = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='remote_id' AND meta_value=%d", $remote_event_ID );
	$result = $wpdb->get_var( $query );

	return $result;
}


/**
 * Render a view
 *
 * @param $view
 * @param $data
 */
function spcc_view( $view, $data = array() ) {
	$path = SPCC_ROOT_PATH . 'public/partials/' . $view . '.php';
	if ( file_exists( $path ) ) {
		if ( ! empty( $data ) ) {
			extract( $data );
		}
		include( $path );
	}
}

/**
 * Return a view
 *
 * @param $view
 * @param array $data
 *
 * @return false|string
 */
function spcc_get_view( $view, $data = array() ) {
	ob_start();
	spcc_view( $view, $data );

	return ob_get_clean();
}

/**
 * Return the current page url
 * @return string
 */
function spcc_current_page_url() {
	global $wp;
	$url = home_url( $wp->request );
	if ( isset( $_SERVER['QUERY_STRING'] ) ) {
		$url = add_query_arg( $_SERVER['QUERY_STRING'], '', $url );
	}

	return $url;
}


/**
 * Return $_GET var
 *
 * @param $key
 * @param null $default
 *
 * @return mixed|null
 */
function spcc_get_var( $key, $default = null ) {
	return isset( $_GET[ $key ] ) ? sanitize_text_field( $_GET[ $key ] ) : $default;
}


/**
 * Custom image upload field
 *
 * @param $key
 * @param $current_value
 * @param $placeholder
 */
function spcc_custom_upload_field( $key, $current_value, $placeholder = '' ) {

	$media_id = $current_value;
	if ( ! empty( $media_id ) && is_numeric( $media_id ) ) {
		$current_src = wp_get_attachment_image_src( $media_id, 'thumbnail' );
		$current_src = $current_src[0];
	} else {
		$current_src = $placeholder;
		$media_id    = '';
	}
	?>
    <div class="upload">
        <img data-src="<?php echo $placeholder; ?>" src="<?php echo $current_src; ?>" width="120px"/>
        <div>
            <input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $media_id; ?>"/>
            <button type="submit" class="upload_image_button button">Upload</button>
            <button type="submit" class="remove_image_button button">&times;</button>
        </div>
    </div>
	<?php
}

/**
 * Submit link
 *
 * @return string
 */
function spcc_get_submit_link() {
    return 'https://stpetecatalyst.com/contribute/event';
}




/**
 * Convert base64 encoded image into a file and move it to proper WP uploads directory.
 **/
function decode_image_to_uploads($base64_string)
{
	$temporary_file = wp_tempnam();
	file_put_contents($temporary_file, base64_decode($base64_string));
	$filename = 'headway-imported-image.jpg';
	$file = array('name' => $filename, 'tmp_name' => $temporary_file);
	$upload = wp_handle_sideload($file, array('test_form' => false));
	if (isset($upload['error'])) {
		@unlink($temporary_file);
	}
	return $upload;
}

/**
 * Format phone number
 * @param $phone
 * @return string
 */
function spcc_format_phone($phone) {
	if ($phone) {
		$_phone = trim(str_replace(array('+1', ' ', '-', '(', ')', '+'), array('', '', '', '', '',''), $phone));
		if (strlen($_phone) === 10) {
			$area_code = substr($_phone, 0, 3);
			$number_p1 = substr($_phone, 3, 3);
			$number_p2 = substr($_phone, 6, 4);
			$phone = "({$area_code}) {$number_p1}-{$number_p2}";
		}
	}
	return $phone;
}

/**
 * Display featured events
 * @param $atts
 *
 * @return string
 */
function spcc_featured_events($atts) {
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