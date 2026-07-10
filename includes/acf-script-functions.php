<?php
/**
 * Script functions
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

// Register scripts store.
acf_register_store( 'acfe-scripts' );

/**
 * Get scripts
 *
 * @since  1.0.0
 * @return method
 */
function acf_get_scripts() {
	return acf_get_store( 'acfe-scripts' )->get();
}

/**
 * Get script
 *
 * @since  1.0.0
 * @return method
 */
function acf_get_script( $name = '' ) {
	return acf_get_store( 'acfe-scripts' )->get( $name );
}

/**
 * Remove script
 *
 * @since  1.0.0
 * @return method
 */
function acf_remove_script( $name = '' ) {
	return acf_get_store( 'acfe-scripts' )->remove( $name );
}

/**
 * Have scripts
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_have_scripts() {
	if ( acf_get_store( 'acfe-scripts' )->count() ) {
		return true;
	}
	return false;
}

/**
 * Is script
 *
 * @since  1.0.0
 * @return method
 */
function acf_is_script( $name = '' ) {
	return acf_get_store( 'acfe-scripts' )->has( $name );
}

/**
 * Count scripts
 *
 * @since  1.0.0
 * @return method
 */
function acf_count_scripts() {
	return acf_get_store( 'acfe-scripts' )->count();
}

/**
 * Get scripts categories
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_scripts_categories() {

	$scripts    = acf_get_scripts();
	$categories = [];

	foreach( $scripts as $script ) {

		if ( ! $script->category || in_array( $script->category, $categories ) ) {
			continue;
		}
		$categories[] = $script->category;
	}
	return $categories;
}

/**
 * Register script
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_register_script( $class ) {

	$instance = $class;
	if ( ! $instance instanceOf acfe_script ) {
		$instance = new $class();
	}

	if ( empty( $instance->name ) ) {
		return false;
	}

	if ( ! current_user_can( $instance->capability ) ) {
		return false;
	}

	if ( ! $instance->active ) {
		return false;
	}

	acf_get_store( 'acfe-scripts' )->set( $instance->name, $instance );

	return true;
}
