<?php
/**
 * Options page functions
 *
 * @package    Applied Content Forms
 * @subpackage Pro
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  An alias of acf_options_page()->add_page()
 *
 * @since  1.0.0
 * @param  $page mixed
 * @return array
 */
function acf_add_options_page( $page = '' ) {
	return acf_options_page()->add_page( $page );
}

/**
 * An alias of acf_options_page()->add_sub_page()
 *
 * @since  1.0.0
 * @param  string $page
 * @return array
 */
function acf_add_options_sub_page( $page = '' ) {
	return acf_options_page()->add_sub_page( $page );
}

/**
 * An alias of acf_options_page()->update_page()
 *
 * @since  1.0.0
 * @param  string $slug
 * @param  array  $data
 * @return array
 */
function acf_update_options_page( $slug = '', $data = [] ) {
	return acf_options_page()->update_page( $slug, $data );
}

/**
 * Get options page
 *
 * Returns an options page's settings
 *
 * @since  1.0.0
 * @param  string $slug
 * @return array
 */
function acf_get_options_page( $slug ) {

	$page = acf_options_page()->get_page( $slug );

	// Stop if no page.
	if ( ! $page ) {
		return false;
	}
	return apply_filters( 'acf/get_options_page', $page, $slug );
}

/**
 * Get options pages
 *
 * Returns all options page settings.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_options_pages() {

	// Access global variables.
	global $_wp_last_utility_menu;

	$pages = acf_options_page()->get_pages();
	if ( empty( $pages ) ) {
		return false;
	}

	// Apply a filter to each page.
	foreach( $pages as $slug => &$page ) {
		$page = acf_get_options_page( $slug );
	}

	// Calculate parent => child redirects.
	foreach( $pages as $slug => &$page ) {

		// Stop if is a child.
		if ( $page['parent_slug'] ) {
			continue;
		}

		// Add missing position.
		if ( ! $page['position'] ) {
			$_wp_last_utility_menu++;
			$page['position'] = $_wp_last_utility_menu;
		}

		// Stop if no redirect.
		if ( ! $page['redirect'] ) {
			continue;
		}

		$parent = $page['menu_slug'];
		$child  = '';

		// Update children.
		foreach( $pages as &$sub_page ) {

			// Stop if not child of this parent.
			if ( $sub_page['parent_slug'] !== $parent ) {
				continue;
			}

			// Set child (only once).
			if ( ! $child ) {
				$child = $sub_page['menu_slug'];
			}

			// Update parent_slug to the first child.
			$sub_page['parent_slug'] = $child;
		}

		// Finally, update parent menu_slug.
		if ( $child ) {
			$page['menu_slug'] = $child;
		}
	}
	return apply_filters( 'acf/get_options_pages', $pages );
}

/**
 * Set options page title
 *
 * @since  1.0.0
 * @param  string $title
 * @return void
 */
function acf_set_options_page_title( $title = '' ) {

	$title = __( 'Options' );
	acf_update_options_page( 'acf-options', [
		'page_title' => $title,
		'menu_title' => $title
	] );
}

/**
 * Options page menu name
 *
 * @since  1.0.0
 * @param  string $title
 * @return void
 */
function acf_set_options_page_menu( $title = '' ) {

	$title = __( 'Options' );
	acf_update_options_page( 'acf-options', [
		'menu_title' => $title
	] );
}

/**
 * Set options page capability
 *
 * Used to customize the options page capability.
 * Defaults to 'edit_posts'.
 *
 * @since  1.0.0
 * @param  string $capability
 * @return void
 */
function acf_set_options_page_capability( $capability = 'edit_posts' ) {
	acf_update_options_page( 'acf-options', [
		'capability' => $capability
	] );
}

/**
 * Register options page
 *
 * This is an old function which is now referencing
 * the 'acf_add_options_sub_page' function.
 *
 * @param  string $page
 * @return void
 */
function register_options_page( $page = '' ) {
	acf_add_options_sub_page( $page );
}
