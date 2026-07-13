<?php
/**
 * Validation functions
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
 * Add validation error
 *
 * @since  1.0.0
 * @param  string $selector
 * @param  string $message
 * @return mixed
 */
function acf_add_validation_error( $selector = '', $message = '' ) {

	if ( empty( $selector ) ) {
		return acf()->validation->add_error( '', $message );
	}

	if ( acf_is_field_key( $selector ) ) {
		return add_filter( "acf/validate_value/key={$selector}", function() use( $message ) {
			return $message;
		} );
	}

	$field = acf_get_field( $selector );
	if ( $form = acf_get_form_data( 'acfe/form' ) ) {

		$fields = [];
		$field_groups = acf_get_array( $form['field_groups'] );

		foreach ( $field_groups as $key ) {
			$fields = array_merge( $fields, acf_get_fields( $key ) );
		}

		foreach ( $fields as $_field ) {
			if ( $_field['name'] !== $selector ) {
				continue;
			}
			$field = $_field;
			break;
		}
	}

	$row = acf_get_loop();
	if ( $row && acf_maybe_get( $row, 'selector' ) !== 'acfe_form_actions' ) {
		$field = acf_get_sub_field( $selector, $row['field'] );
	}

	if ( ! $field ) {
		return acf()->validation->add_error( '', $message );
	}
	add_filter( "acf/validate_value/key={$field['key']}", function() use( $message ) {
		return $message;
	} );
	return false;
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
