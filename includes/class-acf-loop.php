<?php
/**
 * Loop class
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Functions
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Loop {

	/**
	 * Loops
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $loops = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		$this->loops = [];
	}

	/**
	 * Is empty
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function is_empty() {
		return empty( $this->loops );
	}

	/**
	 * Is loop
	 *
	 * This function will return true if a loop exists
	 * for the given array index.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $i
	 * @return boolean
	 */
	public function is_loop( $i = 0 ) {
		return isset( $this->loops[ $i ] );
	}

	/**
	 * Get i
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $i
	 * @return integer
	 */
	public function get_i( $i = 0 ) {

		if ( 'active' === $i ) {
			$i = -1;
		}
		if ( 'previous' === $i ) {
			$i = -2;
		}

		// Allow negative to look at end of loops.
		if ( $i < 0 ) {
			$i = count( $this->loops ) + $i;
		}
		return $i;
	}

	/**
	 * Add loop
	 *
	 * This function will add a new loop
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $loop
	 * @return array
	 */
	public function add_loop( $loop = [] ) {

		// Defaults.
		$loop = wp_parse_args( $loop, [
			'selector' => '',
			'name'     => '',
			'value'    => false,
			'field'    => false,
			'i'        => -1,
			'post_id'  => 0,
			'key'      => ''
		] );

		// Ensure array.
		$loop['value'] = acf_get_array( $loop['value'] );

		/**
		 * Re-index values if this loop starts from index 0.
		 * This allows ajax previews to work ( $_POST data
         * contains random unique array keys).
		 */
		if ( $loop['i'] == -1 ) {
			$loop['value'] = array_values( $loop['value'] );
		}
		$this->loops[] = $loop;

		return $loop;
	}

	/**
	 * Update loop
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $i
	 * @param  string $key
	 * @param  mixed $value
	 * @return boolean
	 */
	public function update_loop( $i = 'active', $key = null, $value = null ) {

		$i = $this->get_i( $i );
		if ( ! $this->is_loop( $i ) ) {
			return false;
		}
		$this->loops[ $i ][ $key ] = $value;

		return true;
	}

	/**
	 * Get loop
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $i
	 * @param  string $key
	 * @return mixed
	 */
	public function get_loop( $i = 'active', $key = null ) {

		// i
		$i = $this->get_i( $i );

		// Stop if no set.
		if ( ! $this->is_loop( $i ) ) {
			return false;
		}

		// Check for key.
		if ( $key !== null ) {
			return $this->loops[ $i ][ $key ];
		}
		return $this->loops[ $i ];
	}

	/**
	 * Remove loop
	 *
	 * This function will remove a loop
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $i
	 * @return boolean
	 */
	public function remove_loop( $i = 'active' ) {

		$i = $this->get_i( $i );
		if ( ! $this->is_loop( $i ) ) {
			return false;
		}
		unset( $this->loops[ $i ] );

		$this->loops = array_values( $this->loops );

		// PHP 7.2 no longer resets array keys for empty value.
		if ( $this->is_empty() ) {
			$this->loops = [];
		}
	}
}
acf()->loop = new ACF_Loop();
