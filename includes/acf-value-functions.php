<?php
/**
 * Value functions
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

// Register store.
acf_register_store( 'values' )->prop( 'multisite', true );

/**
 * Get reference
 *
 * Retrieves the field key for a given field name and post_id.
 *
 * @since  1.0.0
 * @param  string $field_name The name of the field. eg 'sub_heading'.
 * @param  mixed $post_id The post_id of which the value is saved against.
 * @return string The field key.
 */
function acf_get_reference( $field_name, $post_id ) {

	// Allow filter to short-circuit load_value logic.
	$reference = apply_filters( 'acf/pre_load_reference', null, $field_name, $post_id );
    if ( $reference !== null ) {
	    return $reference;
    }

	// Get hidden meta for this field name.
	$reference = acf_get_metadata( $post_id, $field_name, true );

	return apply_filters( 'acf/load_reference', $reference, $field_name, $post_id );
}

/**
 * Retrieves the value for a given field and post_id.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @param  array $field The field array.
 * @return mixed
 */
function acf_get_value( $post_id, $field ) {

	// Allow filter to short-circuit load_value logic.
	$value = apply_filters( 'acf/pre_load_value', null, $post_id, $field );
    if ( $value !== null ) {
	    return $value;
    }

    // Get field name.
    $field_name = $field['name'];

    // Check store.
	$store = acf_get_store( 'values' );
	if ( $store->has( "$post_id:$field_name" ) ) {
		return $store->get( "$post_id:$field_name" );
	}

	// Load value from database.
	$value = acf_get_metadata( $post_id, $field_name );

	// Use field's default_value if no meta was found.
	if ( $value === null && isset( $field['default_value'] ) ) {
		$value = $field['default_value'];
	}

	$value = apply_filters( "acf/load_value", $value, $post_id, $field );

	// Update store.
	$store->set( "$post_id:$field_name", $value );

	// Return value.
	return $value;
}

// Register variation.
acf_add_filter_variations( 'acf/load_value', [ 'type', 'name', 'key' ], 2 );

/**
 * Format value
 *
 * Returns a formatted version of the provided value.
 *
 * @since  1.0.0
 * @param  mixed $value The field value.
 * @param  mixed $post_id The post id.
 * @param  array $field The field array.
 * @return mixed.
 */
function acf_format_value( $value, $post_id, $field ) {

	// Allow filter to short-circuit load_value logic.
	$check = apply_filters( 'acf/pre_format_value', null, $value, $post_id, $field );
    if ( $check !== null ) {
	    return $check;
    }

    // Get field name.
    $field_name = $field['name'];

    // Check store.
	$store = acf_get_store( 'values' );
	if ( $store->has( "$post_id:$field_name:formatted" ) ) {
		return $store->get( "$post_id:$field_name:formatted" );
	}

	$value = apply_filters( "acf/format_value", $value, $post_id, $field );

	// Update store.
	$store->set( "$post_id:$field_name:formatted", $value );

	// Return value.
	return $value;
}

// Register variation.
acf_add_filter_variations( 'acf/format_value', [ 'type', 'name', 'key' ], 2 );

/**
 * Update value
 *
 * Updates the value for a given field and post_id.
 *
 * @since  1.0.0
 * @param  mixed $value The new value.
 * @param  mixed $post_id The post id.
 * @param  array $field The field array.
 * @return boolean.
 */
function acf_update_value( $value, $post_id, $field ) {

	// Allow filter to short-circuit update_value logic.
	$check = apply_filters( 'acf/pre_update_value', null, $value, $post_id, $field );
	if ( $check !== null ) {
		 return $check;
	}
	$value = apply_filters( 'acf/update_value', $value, $post_id, $field, $value );

	// Allow null to delete value.
	if ( null === $value ) {
		return acf_delete_value( $post_id, $field );
	}

	// Update meta.
	$return = acf_update_metadata( $post_id, $field['name'], $value );

	// Update reference.
	acf_update_metadata( $post_id, $field['name'], $field['key'], true );

	// Delete stored data.
	acf_flush_value_cache( $post_id, $field['name'] );

	// Return update status.
	return $return;
}

// Register variation.
acf_add_filter_variations( 'acf/update_value', [ 'type', 'name', 'key' ], 2 );

/**
 * Update values
 *
 * Updates an array of values.
 *
 * @since  1.0.0
 * @param  array values The array of values.
 * @param  mixed $post_id The post id.
 * @return void
 */
function acf_update_values( $values, $post_id ) {

	// Loop over values.
	foreach ( $values as $key => $value ) {

		// Get field.
		$field = acf_get_field( $key );

		// Update value.
		if ( $field ) {
			acf_update_value( $value, $post_id, $field );
		}
	}
}

/**
 * Flush value cache
 *
 * Deletes all cached data for this value.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @param  string $field_name The field name.
 * @return void
 */
function acf_flush_value_cache( $post_id = 0, $field_name = '' ) {

	// Delete stored data.
	acf_get_store( 'values' )
		->remove( "$post_id:$field_name" )
		->remove( "$post_id:$field_name:formatted" );
}

/**
 * Delete value
 *
 * Deletes the value for a given field and post_id.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @param  array $field The field array.
 * @return boolean.
 */
function acf_delete_value( $post_id, $field ) {

	do_action( 'acf/delete_value', $post_id, $field['name'], $field );

	// Delete meta.
	$return = acf_delete_metadata( $post_id, $field['name'] );

	// Delete reference.
	acf_delete_metadata( $post_id, $field['name'], true );

	// Delete stored data.
	acf_flush_value_cache( $post_id, $field['name'] );

	// Return delete status.
	return $return;
}

// Register variation.
acf_add_filter_variations( 'acf/delete_value', [ 'type', 'name', 'key' ], 2 );

/**
 * Preview value
 *
 * Return a human friendly 'preview' for a given field value.
 *
 * @since  1.0.0
 * @param  mixed $value The new value.
 * @param  mixed $post_id The post id.
 * @param  array $field The field array.
 * @return boolean.
 */
function acf_preview_value( $value, $post_id, $field ) {
	return apply_filters( 'acf/preview_value', $value, $post_id, $field );
}

// Register variation.
acf_add_filter_variations( 'acf/preview_value', [ 'type', 'name', 'key' ], 2 );
