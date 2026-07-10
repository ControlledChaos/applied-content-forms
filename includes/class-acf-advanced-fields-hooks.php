<?php
/**
 * Advanced fields hooks
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

class ACF_Advanced_Fields_Hooks {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_filter( 'acf/load_field', [ $this, 'load_field' ], 15 );
		add_action( 'acf/render_field', [ $this, 'replace_render_field' ], 9 );
		add_action( 'acf/render_field', [ $this, 'render_field' ], 15 );
	}

	/**
	 * Load field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return string
	 */
	public function load_field( $field ) {

		if ( ! $this->validate_hook( $field, 'load_field' ) ) {
			return $field;
		}
		$field = call_user_func_array( $field['callback']['load_field'], [ $field ] );

		return $field;
	}

	/**
	 * Replace render field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return void
	 */
	public function replace_render_field( $field ) {

		if ( ! $this->validate_hook( $field, 'replace_render_field' ) ) {
			return;
		}

		call_user_func_array( $field['callback']['replace_render_field'], [ $field ] );

		$field_class = acf_get_field_type( $field['type'] );
		$field_key   = $field['key'];

		if ( method_exists( $field_class, 'render_field' ) ) {

			add_action( "acf/render_field/type={$field['type']}", function( $field ) use ( $field_class, $field_key ) {

				if ( ! has_action( "acf/render_field/type={$field['type']}", [ $field_class, 'render_field' ] ) ) {

					add_action( "acf/render_field/type={$field['type']}", [ $field_class, 'render_field' ], 9 );
				}

				if ( $field_key !=== $field['key'] ) {
					return;
				}
				remove_action( "acf/render_field/type={$field['type']}", [ $field_class, 'render_field' ], 9 );
			}, 8 );
		}
	}

	/**
	 * Render field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return void
	 */
	public function render_field( $field ) {

		if ( ! $this->validate_hook( $field, 'render_field' ) ) {
			return;
		}
		call_user_func_array( $field['callback']['render_field'], [ $field ] );
	}

	/**
	 * Validate hook
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @param  string $hook_name
	 * @return boolean
	 */
	public function validate_hook( $field, $hook_name ) {

		if ( ! isset( $field['callback'][$hook_name] ) || ! is_callable( $field['callback'][$hook_name] ) ) {
			return false;
		}
		return true;
	}
}
acf_new_instance( 'ACF_Advanced_Fields_Hook' );
