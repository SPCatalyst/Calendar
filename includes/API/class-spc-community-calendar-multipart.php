<?php

/**
 * Class SPC_Community_Calendar_Multipart
 */
class SPC_Community_Calendar_Multipart {

	const EOL = "\r\n";

	private $_data = '';
	private $_mime_boundary;

	public function __construct() {
		$this->_mime_boundary = md5( microtime( true ) );
	}

	/**
	 * Add part header
	 */
	private function _addPartHeader() {
		$this->_data .= '--' . $this->_mime_boundary . self::EOL;
	}

	/**
	 * Add array
	 * @param $data
	 * @param string $prefix
	 */
	public function addArray( $data, $prefix = '' ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( !empty($prefix) ) {
					$this->addArray( $value, $prefix . '[' . $key . ']' );
				} else {
					$this->addArray( $value, $key );
				}
			} else {
				if ( !empty($prefix) ) {
					$this->addPart( $prefix . '[' . ( is_numeric( $key ) ? '' : $key ) . ']', $value );
				} else {
					$this->addPart( $key, $value );
				}
			}
		}
	}

	/**
	 * Add part
	 * @param $key
	 * @param $value
	 */
	public function addPart( $key, $value ) {
		$this->_addPartHeader();
		$this->_data .= 'Content-Disposition: form-data; name="' . $key . '"' . self::EOL;
		$this->_data .= self::EOL;
		$this->_data .= $value . self::EOL;
	}

	/**
	 * Add a file
	 * @param $key
	 * @param $filename
	 * @param null $type
	 * @param null $content
	 */
	public function addFile( $key, $filename, $content = null, $type = null ) {

		if ( is_null( $type ) ) {
			$type = mime_content_type( $filename );
		}
		$this->_addPartHeader();
		$this->_data .= 'Content-Disposition: form-data; name="' . $key . '"; filename="' . basename( $filename ) . '"' . self::EOL;
		$this->_data .= 'Content-Type: ' . $type . self::EOL;
		$this->_data .= 'Content-Transfer-Encoding: binary' . self::EOL;
		$this->_data .= self::EOL;
		if ( ! $content ) {
			$this->_data .= file_get_contents( $filename ) . self::EOL;
		} else {
			$this->_data .= $content . self::EOL;
		}
	}

	/**
	 * Returns content type
	 * @return string
	 */
	public function contentType() {
		return 'multipart/form-data; boundary=' . $this->_mime_boundary;
	}

	/**
	 * Returns data
	 * @return string
	 */
	public function data() {
		// add the final content boundary
		return $this->_data .= '--' . $this->_mime_boundary . '--' . self::EOL . self::EOL;
	}
}