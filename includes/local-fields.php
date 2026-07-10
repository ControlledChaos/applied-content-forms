<?php
/**
 * Local fields functions
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

// Register notices stores.
acf_register_store( 'local-fields' );
acf_register_store( 'local-groups' );
acf_register_store( 'local-empty' );

// Register filter.
acf_enable_filter( 'local' );

/**
 * Enable local fields
 *
 * @since  1.0.0
 * @return void
 */
function acf_enable_local() {
	acf_enable_filter( 'local' );
}

/**
 * Disable local
 *
 * @since  1.0.0
 * @return	void
 */
function acf_disable_local() {
	acf_disable_filter( 'local' );
}

/**
 * Is local enabled
 *
 * @since  1.0.0
 * @return	boolean
 */
function acf_is_local_enabled() {
	return ( acf_is_filter_enabled( 'local' ) && acf_get_setting( 'local' ) );
}

/**
 * Get local store
 *
 * Returns either local store or a dummy store for the given name.
 *
 * @since  1.0.0
 * @param  string $name The store name (fields/groups).
 * @return object
 */
function acf_get_local_store( $name = '' ) {

	// Check if enabled.
	if( acf_is_local_enabled() ) {
		return acf_get_store( "local-$name" );

	// Return dummy store if not enabled.
	} else {
		return acf_get_store( 'local-empty' );
	}
}

/**
 * Reset local
 *
 * @since  1.0.0
 * @return void
 */
function acf_reset_local() {
	acf_get_local_store( 'fields' )->reset();
	acf_get_local_store( 'groups' )->reset();
}

/**
 * Get local field groups
 *
 * @since  1.0.0
 * @return method
 */
function acf_get_local_field_groups() {
	return acf_get_local_store( 'groups' )->get();
}

/**
 * Have local field groups
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_have_local_field_groups() {
	if ( acf_get_local_store( 'groups' )->count() ) {
		return true;
	}
	return false;
}

/**
 * Count local field groups
 *
 * @since  1.0.0
 * @return method
 */
function acf_count_local_field_groups() {
	return acf_get_local_store( 'groups' )->count();
}

/**
 * Add local field group
 *
 * @since  1.0.0
 * @param  array $field_group The field group array.
 * @return boolean
 */
function acf_add_local_field_group( $field_group ) {

	// Apply default properties needed for import.
	$field_group = wp_parse_args( $field_group, [
		'key'		=> '',
		'title'		=> '',
		'fields'	=> [],
		'local'		=> 'php'
	] );

	// Generate key if only name is provided.
	if ( ! $field_group['key'] ) {
		$field_group['key'] = 'group_' . acf_slugify( $field_group['title'], '_' );
	}

	// False if field group already exists.
	if ( acf_is_local_field_group( $field_group['key'] ) ) {
		return false;
	}

	/**
	 * Prepare field group for import; adds menu_order
	 * and parent properties to fields.
	 */
	$field_group = acf_prepare_field_group_for_import( $field_group );

	// Extract fields from group.
	$fields = acf_extract_var( $field_group, 'fields' );

	// Add to store.
	acf_get_local_store( 'groups' )->set( $field_group['key'], $field_group );

	// Add fields.
	if ( $fields ) {
		acf_add_local_fields( $fields );
	}

	// Return true on success.
	return true;
}

/**
 * Register field group
 *
 * @see acf_add_local_field_group().
 *
 * @since  1.0.0
 * @param  array $field_group The field group array.
 * @return void
 */
function register_field_group( $field_group ) {
	acf_add_local_field_group( $field_group );
}

/**
 * Remove local field group
 *
 * Removes a field group for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_remove_local_field_group( $key = '' ) {
	return acf_get_local_store( 'groups' )->remove( $key );
}

/**
 * Is local field group
 *
 * Returns true if a field group exists for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_is_local_field_group( $key = '' ) {
	return acf_get_local_store( 'groups' )->has( $key );
}

/**
 * Is local field group key
 *
 * Returns true if a field group exists for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field group group key.
 * @return method
 */
function acf_is_local_field_group_key( $key = '' ) {
	return acf_get_local_store( 'groups' )->is( $key );
}

/**
 * Get local field group
 *
 * Returns a field group for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_get_local_field_group( $key = '' ) {
	return acf_get_local_store( 'groups' )->get( $key );
}

/**
 * Add local fields
 *
 * Adds an array of local fields.
 *
 * @since  1.0.0
 * @param  array $fields An array of un prepared fields.
 * @return void
 */
function acf_add_local_fields( $fields = [] ) {

	// Prepare for import (allows parent fields to offer up children).
	$fields = acf_prepare_fields_for_import( $fields );

	// Add each field.
	foreach ( $fields as $field ) {
		acf_add_local_field( $field, true );
	}
}

/**
 * Get local fields
 *
 * Returns all local fields for the given parent.
 *
 * @since  1.0.0
 * @param  string $parent The parent key.
 * @return method
 */
function acf_get_local_fields( $parent = '' ) {

	// Return children.
	if ( $parent ) {
		return acf_get_local_store( 'fields' )->query( [
			'parent' => $parent
		] );

	// Return all.
	} else {
		return acf_get_local_store( 'fields' )->get();
	}
}

/**
 * Have local fields
 *
 * Returns true if local fields exist.
 *
 * @since  1.0.0
 * @param  string $parent The parent key.
 * @return boolean
 */
function acf_have_local_fields( $parent = '' ) {
	if ( acf_get_local_fields( $parent ) ) {
		return true;
	}
	return false;
}

/**
 * Count local fields
 *
 * Returns the number of local fields for the given parent.
 *
 * @since  1.0.0
 * @param  string $parent The parent key.
 * @return integer
 */
function acf_count_local_fields( $parent = '' ) {
	return count( acf_get_local_fields( $parent ) );
}

/**
 * Add local field
 *
 * @since  1.0.0
 * @param  array $field The field array.
 * @param  boolean $prepared Whether or not the field has already
 *                           been prepared for import.
 * @return	void
 */
function acf_add_local_field( $field, $prepared = false ) {

	// Apply default properties needed for import.
	$field = wp_parse_args( $field, [
		'key'    => '',
		'name'   => '',
		'type'   => '',
		'parent' => ''
	] );

	// Generate key if only name is provided.
	if ( ! $field['key'] ) {
		$field['key'] = 'field_' . $field['name'];
	}

	// If called directly, allow sub fields to be correctly prepared.
	if ( ! $prepared ) {
		return acf_add_local_fields( [ $field ] );
	}

	// Extract attributes.
	$key  = $field['key'];
	$name = $field['name'];

	// Allow sub field to be added multipel times to different parents.
	$store = acf_get_local_store( 'fields' );
	if ( $store->is( $key ) ) {
		$old_key = _acf_generate_local_key( $store->get( $key ) );
		$new_key = _acf_generate_local_key( $field );
		if ( $old_key !== $new_key ) {
			$key = $new_key;
		}
	}

	// Add field.
	$store->set( $key, $field )->alias( $key, $name );
}

/**
 * Generate local key
 *
 * Generates a unique key based on the field's parent.
 *
 * @since  1.0.0
 * @param  string $key The field key.
 * @return boolean
 */
function _acf_generate_local_key( $field ) {

	if ( ! acf_get_setting( 'local' ) ) {
		return;
	}
	return "{$field['key']}:{$field['parent']}";
}

/**
 * Remove local field
 *
 * Removes a field for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field key.
 * @return method
 */
function acf_remove_local_field( $key = '' ) {
	return acf_get_local_store( 'fields' )->remove( $key );
}

/**
 * Is local field
 *
 * Returns true if a field exists for the given key or name.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_is_local_field( $key = '' ) {
	return acf_get_local_store( 'fields' )->has( $key );
}

/**
 * Is local field key
 *
 * Returns true if a field exists for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_is_local_field_key( $key = '' ) {
	return acf_get_local_store( 'fields' )->is( $key );
}

/**
 * Get local field
 *
 * Returns a field for the given key.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_get_local_field( $key = '' ) {
	return acf_get_local_store( 'fields' )->get( $key );
}

/**
 * Apply get local field groups
 *
 * Appends local field groups to the provided array.
 *
 * @since  1.0.0
 * @param  array $field_groups An array of field groups.
 * @return array
 */
function _acf_apply_get_local_field_groups( $groups = [] ) {

	// Get local groups.
	$local = acf_get_local_field_groups();
	if ( $local ) {

		// Generate map of "index" => "key" data.
		$map = wp_list_pluck( $groups, 'key' );

		// Loop over groups and update/append local.
		foreach ( $local as $group ) {

			/**
			 * Get group allowing cache and filters to run.
			 *
			 * $group = acf_get_field_group( $group['key'] );
			 */

			// Update.
			$i = array_search( $group['key'], $map );
			if ( $i !== false ) {
				unset( $group['ID'] );
				$groups[ $i ] = array_merge( $groups[ $i ], $group );

			// Append.
			} else {
				$groups[] = acf_get_field_group( $group['key'] );
			}
		}

		// Sort list via menu_order and title.
		$groups = wp_list_sort( $groups, [ 'menu_order' => 'ASC', 'title' => 'ASC' ] );
	}
	return $groups;
}
add_filter( 'acf/load_field_groups', '_acf_apply_get_local_field_groups', 20, 1 );

/**
 * Apply is local field_key
 *
 * Returns true if is a local key.
 *
 * @since  1.0.0
 * @param  boolean $boolean The result.
 * @param  string $id The identifier.
 * @return boolean
 */
function _acf_apply_is_local_field_key( $boolean, $id ) {
	return acf_is_local_field_key( $id );
}

// Hook into filter.
add_filter( 'acf/is_field_key', '_acf_apply_is_local_field_key', 20, 2 );

/**
 * Apply is local field group key
 *
 * Returns true if is a local key.
 *
 * @since  1.0.0
 * @param  boolean $boolean The result.
 * @param  string $id The identifier.
 * @return boolean
 */
function _acf_apply_is_local_field_group_key( $boolean, $id ) {
	return acf_is_local_field_group_key( $id );
}
add_filter( 'acf/is_field_group_key', '_acf_apply_is_local_field_group_key', 20, 2 );

/**
 * Do prepare local fields
 *
 * Local fields that are added too early will not be correctly prepared by the field type class.
 *
 * @since  1.0.0
 * @return void
 */
function _acf_do_prepare_local_fields() {

	// Get fields.
	$fields = acf_get_local_fields();

	/**
	 * If fields have been registered early,
	 * re-add to correctly prepare them.
	 */
	if ( $fields ) {
		acf_add_local_fields( $fields );
	}
}
add_action( 'acf/include_fields', '_acf_do_prepare_local_fields', 0, 1 );
