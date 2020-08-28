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
	 */
	public function __construct() {
		$this->load();
	}

	/**
	 * Refreshes the settings
	 */
	public function refresh() {
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
		if ( ! is_array( $this->data ) ) {
			$this->data = array();
		}
		$this->data = array_merge( $this->data, $values );
		update_option( $this->key, $this->data );

		return true;
	}

	/**
	 * Save the current settings request.
	 *
	 * @return bool
	 */
	public function saveRequest() {

		$old_page = $this->get('events_page', null);

		$values = array();
		$skip = array();
		foreach ( $this->get_allowed_settings() as $key ) {

			if(in_array($key, $skip)) {
				continue;
			}

			if ( ! isset( $_POST[ $key ] ) ) {
				$_POST[ $key ] = null;
			}
			$values[ $key ] = is_array( $_POST[ $key ] ) ? $_POST[ $key ] : sanitize_text_field( $_POST[ $key ] );
		}

		if(isset($values['events_page']) && $old_page != $values['events_page']) {
			flush_rewrite_rules(true);
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
			'preferred_view',
			'color_schemes',
			'type',
			'visibility',
			'logo',
			'google_maps_key',
			'maps_provider',
			'events_page',
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

		$logo = null;
		if ( isset( $data['logo'] ) && ! empty( $data['logo'] ) && strpos( $data['logo'], ',' ) !== false ) {

			$img_base64 = $data['logo'];

			unset( $data['logo'] );

			$upload_dir  = wp_upload_dir();
			$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
			$img         = explode( ',', $img_base64 );

			$mime = $this->find_get_extension( $img[0] );

			$decoded         = base64_decode( $img[1] );
			$filename        = 'spc-company-logo' . '.' . $mime['ext'];
			$hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

			$full_path = $upload_path . $hashed_filename;

			file_put_contents( $full_path, $decoded );

			if ( ! function_exists( 'wp_handle_sideload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			if ( ! function_exists( 'wp_get_current_user' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );

			}

			$file             = array();
			$file['error']    = '';
			$file['tmp_name'] = $full_path;
			$file['name']     = $hashed_filename;
			$file['type']     = $mime['mime_type'];
			$file['size']     = filesize( $full_path );
			$file_return      = wp_handle_sideload( $file, array( 'test_form' => false ) );

			$filename   = $file_return['file'];
			$attachment = array(
				'post_mime_type' => $file_return['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'guid'           => $upload_dir['url'] . '/' . basename( $filename )
			);
			$attach_id  = wp_insert_attachment( $attachment, $filename );
			if ( ! is_wp_error( $attach_id ) ) {
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				$data['logo'] = $attach_id;
			}


		}

		error_log( 'SPCC INFO: Importing settings: ' . json_encode( $data ) );

		return $this->save( $data );

	}

	/**
	 * @param $base64_header
	 *
	 * @return array|null
	 */
	private function find_get_extension( $base64_header ) {

		$mime_type = str_replace( 'data:', '', $base64_header );
		$mime_type = str_replace( ';base64', '', $mime_type );

		$mimes = array(
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'atom'    => 'application/atom+xml',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'cdf'     => 'application/x-netcdf',
			'cgm'     => 'image/cgm',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'csv'     => 'text/csv',
			'dcr'     => 'application/x-director',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dmg'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'doc'     => 'application/msword',
			'dtd'     => 'application/xml-dtd',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'gif'     => 'image/gif',
			'gram'    => 'application/srgs',
			'grxml'   => 'application/srgs+xml',
			'gtar'    => 'application/x-gtar',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ico'     => 'image/x-icon',
			'ics'     => 'text/calendar',
			'ief'     => 'image/ief',
			'ifb'     => 'text/calendar',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'json'    => 'application/json',
			'kar'     => 'audio/midi',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'man'     => 'application/x-troff-man',
			'mathml'  => 'application/mathml+xml',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'oda'     => 'application/oda',
			'ogg'     => 'application/ogg',
			'pbm'     => 'image/x-portable-bitmap',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'ppm'     => 'image/x-portable-pixmap',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'ra'      => 'audio/x-pn-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rdf'     => 'application/rdf+xml',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'application/vnd.rn-realmedia',
			'roff'    => 'application/x-troff',
			'rss'     => 'application/rss+xml',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'svg'     => 'image/svg+xml',
			'svgz'    => 'image/svg+xml',
			'swf'     => 'application/x-shockwave-flash',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'vxml'    => 'application/voicexml+xml',
			'wav'     => 'audio/x-wav',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wrl'     => 'model/vrml',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xls'     => 'application/vnd.ms-excel',
			'xml'     => 'application/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'application/xml',
			'xslt'    => 'application/xslt+xml',
			'xul'     => 'application/vnd.mozilla.xul+xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip'
		);


		$result = array( 'ext' => null, 'mime_type' => null );

		foreach ( $mimes as $ext => $mime_type_name ) {
			if ( $mime_type === $mime_type_name ) {
				$result = array( 'ext' => $ext, 'mime_type' => $mime_type_name );
			}
		}

		return $result;

	}

}
