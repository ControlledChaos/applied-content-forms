<?php
/**
 * Screen functions
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
 * Is admin
 *
 * Check if current screen is back end.
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_admin() {
	return ! acf_is_front();
}

/**
 * Is front
 *
 * Check if current screen is front end.
 *
 * @todo Use acf_get_form_data( 'screen' ).
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_front() {

	if ( ! is_admin() || ( is_admin() && wp_doing_ajax() && ( 'acfe_form' === acf_maybe_get_POST( '_acf_screen' ) || 'acf_form' === acf_maybe_get_POST( '_acf_screen' ) ) ) ) {
		return true;
	}
	return false;
}


/**
 * Get plugin screen ID
 *
 * Check if the current admin screen is field group UI, admin tools etc.
 *
 * @since  1.0.0
 * @param  string $page
 * @return string
 */
function acf_get_acf_screen_id( $page = '' ) {

	$prefix = sanitize_title( __( 'Custom Fields', 'acf' ) );

	if ( empty( $page ) ) {
		return $prefix;
	}
	return $prefix . '_page_' . $page;
}

/**
 * Is plugin admin screen
 *
 * Check if the current admin screen is field group UI, admin tools etc.
 *
 * @since  1.0.0
 * @param  boolean $modules
 * @return boolean
 */
function acf_is_admin_screen( $modules = false ) {

	// Stop if not defined.
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	$post_types = [ 'acf-field-group' ];
	$field_group_category = false;

	if ( $modules ) {

		// Reserved.
		$post_types = array_merge( $post_types, acf_get_setting( 'reserved_post_types', [] ) );

		// Field group category.
		$field_group_category = 'post' === $screen->post_type && 'acf-field-group-category' === $screen->taxonomy;
	}

	if ( in_array( $screen->post_type, $post_types ) || $field_group_category ) {
		return true;
	}
	return false;
}

/**
 * Match location rules
 *
 * Match screen data against an array of location.
 *
 * @since  1.0.0
 * @param  $location
 * @param  $screen
 * @return boolean
 */
function acf_match_location_rules( $location, $screen ) {

	// Loop through location groups.
	foreach ( $location as $group ) {

		// Ignore group if no rules.
		if ( empty( $group ) ) {
			continue;
		}

		// Loop over rules and determine if all rules match.
		$match_group = true;
		foreach ( $group as $rule ) {
			if ( ! acf_match_location_rule( $rule, $screen, [] ) ) {
				$match_group = false;
				break;
			}
		}

		if ( $match_group ) {
			return true;
		}
	}
	return false;
}

/**
 * Is dynamic preview
 *
 * Check if currently in flexible content preview or block type preview.
 *
 * @since  1.0.0
 * @global boolean $is_preview
 * @return boolean
 */
function acf_is_dynamic_preview() {

	// Access global variables.
	global $is_preview;

	$return = false;

	// Flexible content.
	if ( isset( $is_preview ) && $is_preview ) {
		$return = true;

	// Block type.
	} elseif ( wp_doing_ajax() && acf_maybe_get_POST( 'query' ) ) {

		$query = acf_maybe_get_POST( 'query' );
		if ( acf_maybe_get( $query, 'preview' ) ) {
			$return = true;
		}
	}
	return apply_filters( 'acfe/is_preview', $return );
}

/**
 * Is Gutenberg
 *
 * Checks if current screen is block editor.
 *
 * @deprecated
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_gutenberg() {
	return acf_is_block_editor();
}

/**
 * Help heading allowed HTML
 *
 * Returns an array of HTML elements allowed in
 * post type contextual help headings.
 *
 * @since  1.0.0
 * @return array
 */
function acf_help_heading_allowed() {

	$allowed = [
		'h3' => [
			'style' => []
		],
		'h4' => [
			'style' => []
		],
		'h5' => [
			'style' => []
		],
		'p'  => [
			'style' => []
		],
		'a'  => [
			'href'  => [],
			'title' => [],
			'style' => []
		],
		'hr'     => [],
		'br'     => [],
		'em'     => [],
		'strong' => [],
		'b'      => [],
		'i'      => [],
		'code'   => [],
		'style'  => []
	];
	return apply_filters( 'acf/help_heading_allowed', $allowed );
}
