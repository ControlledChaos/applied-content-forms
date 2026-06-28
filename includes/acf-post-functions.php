<?php
/**
 * Post functions
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
 * Get post templates
 *
 * Returns available templates for each post type.
 *
 * @since  1.0.0
 * @param  void
 * @return array
 */
function acf_get_post_templates() {

	// Check store.
	$cache = acf_get_data( 'post_templates' );
	if ( $cache !== null ) {
		return $cache;
	}

	// Initialize templates with default placeholder for pages.
	$post_templates = [];
	$post_templates['page'] = [];

	// Loop over post types and append their templates.
	if ( method_exists( 'WP_Theme', 'get_page_templates' ) ) {
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			$templates = wp_get_theme()->get_page_templates( null, $post_type );
			if ( $templates ) {
				$post_templates[ $post_type ] = $templates;
			}
		}
	}

	// Update store.
	acf_set_data( 'post_templates', $post_templates );

	// Return templates.
	return $post_templates;
}

/**
 * acfe_get_post_type_objects
 *
 * Query & retrieve post types objects
 *
 * @param array $args
 *
 * @return array
 */
function acfe_get_post_type_objects($args = array()){

	// vars
	$return = array();

	// Post Types
	$posts_types = acf_get_post_types($args);

	// Choices
	if(!empty($posts_types)){

		foreach($posts_types as $post_type){

			$post_type_object = get_post_type_object($post_type);

			$return[ $post_type_object->name ] = $post_type_object;

		}

	}

	return $return;

}

/**
 * acfe_get_pretty_post_statuses
 *
 * Similar to acf_get_pretty_post_types() but for Post Statuses
 *
 * @param array $posts_statuses
 *
 * @return array
 */
function acfe_get_pretty_post_statuses($posts_statuses = array()){

	if(empty($posts_statuses)){

		$posts_statuses = get_post_stati(array(), 'names');

	}

	$return = array();

	// Choices
	if(!empty($posts_statuses)){

		foreach($posts_statuses as $post_status){

			$post_status_object = get_post_status_object($post_status);

			$return[$post_status_object->name] = $post_status_object->label . ' (' . $post_status_object->name . ')';

		}

	}

	return $return;

}
