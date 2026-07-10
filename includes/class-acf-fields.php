<?php
/**
 * Fields functions
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

class ACF_Fields {

	/**
	 * Field type instances
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $types = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {}

	/**
	 * Register field type
	 *
	 * This function will register a field type instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $class
	 * @return void
	 */
	public function register_field_type( $class ) {

		// Allow instance.
		if  ( $class instanceOf ACF_Field ) {
			$this->types[ $class->name ] = $class;

		// Allow class name.
		} else {
			$instance = new $class();
			$this->types[ $instance->name ] = $instance;
		}
	}

	/**
	 * Get field type
	 *
	 * This function will return a field type instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function get_field_type( $name ) {
		return isset( $this->types[$name] ) ? $this->types[$name] : null;
	}

	/**
	 * Is field type
	 *
	 * This function will return true if a field type exists.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return boolean
	 */
	public function is_field_type( $name ) {
		return isset( $this->types[$name] );
	}

	/**
	 * Register field type info
	 *
	 * This function will store a basic array of info
	 * about the field type to later be overridden by
	 * the above register_field_type function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $info
	 * @return void
	 */
	public function register_field_type_info( $info ) {
		$instance = (object) $info;
		$this->types[ $instance->name ] = $instance;
	}

	/**
	 * Get field types
	 *
	 * This function will return an array of all field types.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_field_types() {
		return $this->types;
	}
}

acf()->fields = acf_new_instance( 'ACF_Fields' );
