<?php
/**
 * User functions
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
 * Get users
 *
 * Similar to the get_users() function but with extra functionality.
 *
 * @since  1.0.0
 * @param  array $args The query args.
 * @return array
 */
function acf_get_users( $args = [] ) {

	// Get users.
	$users = get_users( $args );

	// Maintain order.
	if ( $users && $args['include'] ) {

		// Generate order array.
		$order = [];
		foreach ( $users as $i => $user ) {
			$order[ $i ] = array_search( $user->ID, $args['include'] );
		}

		// Sort results.
		array_multisort( $order, $users );
	}
	return $users;
}

/**
 * Get user result
 *
 * Returns a result containing "id" and "text" for the given user.
 *
 * @since  1.0.0
 * @param  WP_User $user The user object.
 * @return array
 */
function acf_get_user_result( $user ) {

	$id   = $user->ID;
	$text = $user->user_login;

	// Add name.
	if ( $user->first_name && $user->last_name ) {
		$text .= " ({$user->first_name} {$user->last_name})";
	} elseif ( $user->first_name ) {
		$text .= " ({$user->first_name})";
	}
	return compact( 'id', 'text' );
}

/**
 * Get user role labels
 *
 * Returns an array of user roles in the format "name => label".
 *
 * @since  1.0.0
 * @param  array $roles A specific array of roles.
 * @return array
 */
function acf_get_user_role_labels( $roles = [] ) {

	$all_roles = wp_roles()->get_names();

	// Load all roles if none provided.
	if ( empty( $roles ) ) {
		$roles = array_keys( $all_roles );
	}

	// Loop over roles and popular labels.
	$labels = [];
	foreach ( $roles as $role ) {
		if ( isset( $all_roles[ $role ] ) ) {
			$labels[ $role ] = translate_user_role( $all_roles[ $role ] );
		}
	}
	return $labels;
}

/**
 * Allow unfiltered HTML
 *
 * Returns true if the current user is allowed to save unfiltered HTML.
 *
 * @since  1.0.0
 * @param  void
 * @return bool
 */
function acf_allow_unfiltered_html() {
	$allow_unfiltered_html = current_user_can( 'unfiltered_html' );
	return apply_filters( 'acf/allow_unfiltered_html', $allow_unfiltered_html );
}
