<?php
/**
 * Advanced values hooks
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

class ACF_Advanced_Values_Hooks {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_filter( 'acf/load_value', [ $this, 'load_value' ], 15, 3 );
		add_filter( 'acf/update_value', [ $this, 'update_value' ], 15, 3 );
		add_filter( 'acf/format_value', [ $this, 'format_value' ], 15, 3 );
		add_filter( 'acf/validate_value', [ $this, 'validate_value' ], 15, 4 );
		add_action( 'acf/delete_value', [ $this, 'delete_value' ], 15, 3 );
	}

	/**
	 * Load value
	 *
	 * @since  1.0.0
	 * @param  mixed $value
	 * @param  integer $post_id
	 * @param  string $field
	 * @return mixed
	 */
	public function load_value( $value, $post_id, $field ) {

		if ( ! $this->validate_hook( $field, 'load_value' ) ) {
			return $value;
		}
		$value = call_user_func_array( $field['callback']['load_value'], [ $value, $post_id, $field ] );

		return $value;
	}

	/**
	 * Update value
	 *
	 * @since  1.0.0
	 * @param  mixed $value
	 * @param  integer $post_id
	 * @param  string $field
	 * @return mixed
	 */
	public function update_value( $value, $post_id, $field ) {

		if ( ! $this->validate_hook( $field, 'update_value' ) ) {
			return $value;
		}

		$value = call_user_func_array( $field['callback']['update_value'], [ $value, $post_id, $field ] );

		return $value;
	}

	/**
	 * Format value
	 *
	 * @since  1.0.0
	 * @param  mixed $value
	 * @param  integer $post_id
	 * @param  string $field
	 * @return mixed
	 */
	public function format_value( $value, $post_id, $field ) {

		if ( ! $this->validate_hook( $field, 'format_value' ) ) {
			return $value;
		}

		$value = call_user_func_array( $field['callback']['format_value'], [ $value, $post_id, $field ] );

		return $value;
	}

	/**
	 * Validate value
	 *
	 * @since  1.0.0
	 * @param  mixed $valid
	 * @param  mixed $value
	 * @param  string $field
	 * @param  string $input
	 * @return mixed
	 */
	public function validate_value( $valid, $value, $field, $input ) {

		if ( ! $this->validate_hook( $field, 'validate_value' ) ) {
			return $valid;
		}

		$valid = call_user_func_array( $field['callback']['validate_value'], [ $valid, $value, $field, $input ] );

		return $valid;
	}

	/**
	 * Delete value
	 *
	 * @since  1.0.0
	 * @param  integer $post_id
	 * @param  string $field_name
	 * @param  string $field
	 * @return void
	 */
	public function delete_value( $post_id, $field_name, $field ) {

		if ( ! $this->validate_hook( $field, 'delete_value' ) ) {
			return;
		}

		call_user_func_array( $field['callback']['delete_value'], [ $post_id, $field_name, $field ] );
	}

	/**
	 * Validate hook
	 *
	 * @since  1.0.0
	 * @param  string $field
	 * @param  string $hook_name
	 * @return boolean
	 */
	public function validate_hook( $field, $hook_name ) {

		if ( ! isset( $field['callback'][$hook_name] ) || !is_callable( $field['callback'][$hook_name] ) ) {
			return false;
		}
		return true;
	}
}
acf_new_instance( 'ACF_Advanced_Values_Hook' );
