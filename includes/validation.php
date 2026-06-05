<?php
/**
 * Validation
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

class acf_validation {

	/**
	 * Errors
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $errors;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		$this->errors = [];

		// AJAX.
		add_action( 'wp_ajax_acf/validate_save_post', [ $this, 'ajax_validate_save_post' ] );
		add_action( 'wp_ajax_nopriv_acf/validate_save_post', [ $this, 'ajax_validate_save_post' ] );
		add_action( 'acf/validate_save_post', [ $this, 'acf_validate_save_post' ], 5 );
	}

	/**
	 * Add error
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  string $input
	 * @param  string $message
	 * @return integer
	 */
	public function add_error( $input, $message ) {

		// Add to array.
		$this->errors[] = [
			'input'   => $input,
			'message' => $message
		];
	}

	/**
	 * Get error
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  string $input
	 * @return mixed
	 */
	public function get_error( $input ) {

		// Stop if no errors.
		if ( empty( $this->errors ) ) {
			return false;
		}

		// Loop.
		foreach ( $this->errors as $error ) {
			if ( $error['input'] === $input ) {
				return $error;
			}
		}
		return false;
	}

	/**
	 * Get errors
	 *
	 * @since 1.0.0
	 * @access public
	 * @return mixed
	 */
	public function get_errors() {

		// Stop if no errors.
		if ( empty( $this->errors ) ) {
			return false;
		}
		return $this->errors;
	}

	/**
	 * Reset errors
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function reset_errors() {
		$this->errors = [];
	}

	/**
	 * AJAX validate save post
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function ajax_validate_save_post() {

		if ( ! acf_verify_ajax() ) {
			die( 'AJAX post save not validated.' );
		}

		$json = [
			'valid'  => 1,
			'errors' => 0
		];

		// Success.
		if ( acf_validate_save_post() ) {
			wp_send_json_success( $json );
		}

		// Update vars.
		$json['valid']  = 0;
		$json['errors'] = acf_get_validation_errors();

		wp_send_json_success( $json );
	}

	/**
	 * AJAX validate save post
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function acf_validate_save_post() {

		// Stop if no $_POST.
		if ( empty( $_POST['acf'] ) ) {
			return;
		}
		acf_validate_values( $_POST['acf'], 'acf' );
	}
}

// Initialize.
acf()->validation = new acf_validation();

/**
 * Public functions
 *
 * Alias of acf()->validation->function()
 *
 * @since  1.0.0
 * @param  string $input
 * @param  string $message
 * @return void
 */
function acf_add_validation_error( $input, $message = '' ) {
	return acf()->validation->add_error( $input, $message );
}
function acf_get_validation_errors() {
	return acf()->validation->get_errors();
}
function acf_get_validation_error() {
	return acf()->validation->get_error( $input );
}
function acf_reset_validation_errors() {
	return acf()->validation->reset_errors();
}

/**
 * Validate save post
 *
 * @since  1.0.0
 * @param  boolean $show_errors
 * @return boolean
 */
function acf_validate_save_post( $show_errors = false ) {

	do_action( 'acf/validate_save_post' );

	$errors = acf_get_validation_errors();

	// Stop if no errors.
	if ( ! $errors ) {
		return true;
	}

	// Show errors.
	if ( $show_errors ) {

		$message  = '<h2>' . __( 'Validation failed', 'acf' ) . '</h2>';
		$message .= '<ul>';
		foreach ( $errors as $error ) {
			$message .= '<li>' . $error['message'] . '</li>';
		}
		$message .= '</ul>';

		wp_die( $message, __( 'Validation failed.', 'acf' ) );
	}
	return false;
}

/**
 * Validate values
 *
 * @since  1.0.0
 * @param  array $values
 * @param  string $input_prefix
 * @return void
 */
function acf_validate_values( $values, $input_prefix = '' ) {

	// Stop if empty.
	if ( empty( $values ) ) {
		return;
	}

	// Loop.
	foreach ( $values as $key => $value ) {

		$field = acf_get_field( $key );
		$input = $input_prefix . '[' . $key . ']';

		// Stop if not found.
		if ( ! $field ) {
			continue;
		}
		acf_validate_value( $value, $field, $input );
	}
}

/**
 * Validate value
 *
 * @param  mixed $value
 * @param  string $field
 * @param  string $input
 * @return boolean
 */
function acf_validate_value( $value, $field, $input ) {

	$valid   = true;
	$message = sprintf( __( '%s value is required', 'acf' ), $field['label'] );

	if ( $field['required'] ) {

		/**
		 * Valid is set to false if the value is empty,
		 * but allow 0 as a valid value
		 */
		if ( empty( $value ) && ! is_numeric( $value ) ) {
			$valid = false;
		}
	}

	$valid = apply_filters( "acf/validate_value/type={$field['type']}",		$valid, $value, $field, $input );
	$valid = apply_filters( "acf/validate_value/name={$field['_name']}", 	$valid, $value, $field, $input );
	$valid = apply_filters( "acf/validate_value/key={$field['key']}", 		$valid, $value, $field, $input );
	$valid = apply_filters( "acf/validate_value", 							$valid, $value, $field, $input );


	// Allow $valid to be a custom error message.
	if ( ! empty( $valid ) && is_string( $valid ) ) {
		$message = $valid;
		$valid   = false;
	}

	if ( ! $valid ) {
		acf_add_validation_error( $input, $message );
		return false;
	}
	return true;
}
