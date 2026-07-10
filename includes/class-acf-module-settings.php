<?php
/**
 * Module settings
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Settings
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Module_Settings {

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		$this->settings = get_option( 'acfe', [] );
	}

	/**
	 * Get setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $selector
	 * @param  mixed $default
	 * @return mixed
	 */
	public function get( $selector = null, $default = null ) {
		return $this->array_get( $this->settings, $selector, $default );
	}

	/**
	 * Set setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $selector
	 * @param  mixed $value
	 * @param  boolean $append
	 * @return mixed
	 */
	public function set( $selector = null, $value = null, $append = false ) {

		if ( null === $value ) {
			return false;
		}

		if ( $append ) {
			$this->array_append( $this->settings, $selector, $value );
		} else {
			$this->array_set( $this->settings, $selector, $value );
		}

		$this->update();
		return $this;
	}

	/**
	 * Clear setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $selector
	 * @return mixed
	 */
	public function clear( $selector = null ) {

		$this->array_clear( $this->settings, $selector );
		$this->update();

		return $this;
	}

	/**
	 * Delete setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $selector
	 * @return void
	 */
	public function delete( $selector = null ) {

		if ( false === strpos( $selector, '.' ) ) {
			unset( $this->settings[$selector] );
		} else {
			$this->array_remove( $this->settings, $selector );
		}

		$this->update();
		return $this;
	}

	/**
	 * Append
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $selector
	 * @param  mixed $value
	 * @return method
	 */
	public function append( $selector = null, $value = null ) {

		if ( null === $selector && null === $value ) {
			return false;
		}

		// Allow simple append without selector.
		if ( null === $value ) {
			$value    = $selector;
			$selector = null;
		}
		return $this->set( $selector, $value, true );
	}

	/**
	 * Array get
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $default
	 * @return mixed
	 */
	public function array_get( $array, $key, $default = null ) {

		if ( empty( $key ) ) {
			return $array;
		}

		if ( ! is_array( $key ) ) {
			$key = explode( '.', $key );
		}

		$count = count( $key );
		$i=-1;

		foreach ( $key as $segment ) {

			$i++;
			if ( ! isset( $array[$segment] ) ) {
				continue;
			}

			if ( $i+1 === $count ) {
				return $array[$segment];
			}
			unset( $key[$i] );

			return $this->array_get( $array[$segment], $key, $default );
		}
		return $default;
	}

	/**
	 * Array set
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $value
	 * @return mixed
	 */
	public function array_set( &$array, $key, $value ) {

		if ( empty( $key ) ) {
			return $array = $value;
		}

		$keys = explode( '.', $key );
		while ( count( $keys ) > 1 ) {

			$key = array_shift( $keys );
			if ( ! isset( $array[$key] ) || ! is_array( $array[$key] ) ) {
				$array[$key] = [];
			}
			$array =& $array[$key];
		}
		$array[array_shift( $keys )] = $value;
		return $array;
	}

	/**
	 * Array append
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $value
	 * @return mixed
	 */
	public function array_append( &$array, $key, $value ) {

		$get   = $this->array_get( $array, $key );
		$old   = acf_get_array( $get );
		$value = acf_get_array( $value );
		$value = array_merge( $old, $value );

		$this->array_set( $array, $key, $value );
		return $array;
	}

	/**
	 * Array clear
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @param  string $key
	 * @return array
	 */
	public function array_clear( &$array, $key ) {

		$get = $this->array_get( $array, $key );

		if ( null === $get ) {
			return $array;
		}

		$value = null;
		if ( is_array( $get ) ) {
			$value = [];
		}
		$this->array_set( $array, $key, $value );

		return $array;
	}

	/**
	 * Array remove
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @param  array $keys
	 * @return void
	 */
	public function array_remove( &$array, $keys ) {

		$original =& $array;
		foreach ( (array)$keys as $key ) {

			$parts = explode( '.', $key );
			while ( count( $parts ) > 1 ) {

				$part = array_shift( $parts );
				if ( isset( $array[$part] ) && is_array( $array[$part] ) ) {
					$array =& $array[$part];
				}
			}
			unset( $array[array_shift( $parts)] );

			// Clean up after each pass.
			$array =& $original;
		}
	}

	/**
	 * Update option
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function update() {
		update_option( 'acfe', $this->settings, 'true' );
	}

}

/**
 * Get settings
 *
 * @since  1.0.0
 * @param  string $selector
 * @param  mixed $default
 * @return method
 */
function acfe_get_settings( $selector = null, $default = null ) {
	return acf_get_instance( 'ACF_Module_Settings' )->get( $selector, $default );
}

/**
 * Update setting
 *
 * @since  1.0.0
 * @param  string $selector
 * @param  mixed $value
 * @return method
 */
function acfe_update_settings( $selector = null, $value = null ) {

	if ( null === $value ) {
		$value    = $selector;
		$selector = null;
	}
	return acf_get_instance( 'ACF_Module_Settings' )->set( $selector, $value );
}

/**
 * Delete setting
 *
 * @since  1.0.0
 * @param  string $selector
 * @return method
 */
function acfe_delete_settings( $selector = null ) {
	return acf_get_instance( 'ACF_Module_Settings' )->delete( $selector );
}
