<?php
/**
 * Handle deprecated functions
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

// Register deprecated filters ( $deprecated, $version, $replacement ).
acf_add_deprecated_filter( 'acf/settings/export_textdomain', '5.3.3', 'acf/settings/l10n_textdomain' );
acf_add_deprecated_filter( 'acf/settings/export_translate', '5.3.3', 'acf/settings/l10n_field' );
acf_add_deprecated_filter( 'acf/settings/export_translate', '5.3.3', 'acf/settings/l10n_field_group' );
acf_add_deprecated_filter( 'acf/settings/dir', '5.6.8', 'acf/settings/url' );
acf_add_deprecated_filter( 'acf/get_valid_field', '5.5.6', 'acf/validate_field' );
acf_add_deprecated_filter( 'acf/get_valid_field_group', '5.5.6', 'acf/validate_field_group' );
acf_add_deprecated_filter( 'acf/get_valid_post_id', '5.5.6', 'acf/validate_post_id' );
acf_add_deprecated_filter( 'acf/get_field_reference', '5.6.5', 'acf/load_reference' );
acf_add_deprecated_filter( 'acf/get_field_group', '5.7.11', 'acf/load_field_group' );
acf_add_deprecated_filter( 'acf/get_field_groups', '5.7.11', 'acf/load_field_groups' );
acf_add_deprecated_filter( 'acf/get_fields', '5.7.11', 'acf/load_fields' );

// Register variations for deprecated filters.
acf_add_filter_variations( 'acf/get_valid_field', [ 'type' ], 0 );

/**
 * Render field wrap label
 *
 * Renders the field's label.
 *
 * @since  1.0.0
 * @param  array $field The field array.
 * @return void
 */
function acf_render_field_wrap_label( $field ) {

	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'acf_render_field_label()' );

	// Render.
	acf_render_field_label( $field );
}

/**
 * Render field wrap description
 *
 * Renders the field's instructions.
 *
 * @since  1.0.0
 * @param  array $field The field array.
 * @return void
 */
function acf_render_field_wrap_description( $field ) {

	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'acf_render_field_instructions()' );

	// Render.
	acf_render_field_instructions( $field );
}

/**
 * Get fields by id
 *
 * Returns and array of fields for the given $parent_id.
 *
 * @since  1.0.0
 * @param  integer $parent_id The parent ID.
 * @return array
 */
function acf_get_fields_by_id( $parent_id = 0 ) {

	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'acf_get_fields()' );

	// Return fields.
	return acf_get_fields(array( 'ID' => $parent_id, 'key' => "group_$parent_id" ));
}

/**
 * Update option
 *
 * A wrapper for the WP update_option but provides logic for a 'no' autoload.
 *
 * @since  1.0.0
 * @param  string $option The option name.
 * @param  string $value The option value.
 * @param  string $autoload An optional autoload value.
 * @return boolean
 */
function acf_update_option( $option = '', $value = '', $autoload = null ) {

	// Warning.
	_deprecated_function( __FUNCTION__, '5.7.11', 'update_option()' );

	// Update.
	if ( $autoload === null ) {
		$autoload = (bool) acf_get_setting( 'autoload' );
	}
	return update_option( $option, $value, $autoload );
}

/**
 * Get field reference
 *
 * Finds the field key for a given field name and post_id.
 *
 * @since  1.0.0
 * @param  string $field_name The name of the field. eg 'sub_heading'
 * @param  mixed $post_id The post_id of which the value is saved against
 * @return string The field key
 */
function acf_get_field_reference( $field_name, $post_id ) {

	// Warning.
	_deprecated_function( __FUNCTION__, '5.6.8', 'acf_get_reference()' );

	// Return reference.
	return acf_get_reference( $field_name, $post_id );
}

/**
 * Get directory
 *
 * Returns the plugin url to a specified file.
 *
 * @since  1.0.0
 * @param string $filename The specified file.
 * @return string
 */
function acf_get_dir( $filename = '' ) {

	// Warning.
	_deprecated_function( __FUNCTION__, '5.6.8', 'acf_get_url()' );

	// Return.
	return acf_get_url( $filename );
}
