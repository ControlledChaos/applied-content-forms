<?php
/**
 * Term functions
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
 * Get taxonomy objects
 *
 * Query & retrieve taxonomies objects.
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acfe_get_taxonomy_objects( $args = [] ) {

	$return     = [];
	$taxonomies = acf_get_taxonomies( $args );

	if ( ! empty( $taxonomies ) ) {
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			$return[$taxonomy_object->name] = $taxonomy_object;
		}
	}
	return $return;
}

/**
 * Get taxonomy terms IDs
 *
 * Similar to acf_get_taxonomy_terms()
 * Returns "array('256' => 'Category name')" instead of
 * "array('category:category_name' => 'Category name')"
 *
 * @since  1.0.0
 * @param  array $taxonomies
 * @return array
 */
function acfe_get_taxonomy_terms_ids( $taxonomies = [] ) {

	// Force array.
	$taxonomies = acf_get_array( $taxonomies );

	// Get pretty taxonomy names.
	$taxonomies = acf_get_taxonomy_labels( $taxonomies );

	$r = [];
	foreach ( array_keys( $taxonomies ) as $taxonomy ) {

		$label = $taxonomies[$taxonomy];
		$is_hierarchical = is_taxonomy_hierarchical( $taxonomy );

		$terms = acf_get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false
		] );

		// Stop if no terms.
		if ( empty( $terms ) ) {
			continue;
		}

		// Sort into hierarchial order.
		if ( $is_hierarchical ) {
			$terms = _get_term_children( 0, $terms, $taxonomy );
		}

		// Add a placeholder.
		$r[ $label ] = [];

		// Add choices.
		foreach ( $terms as $term ) {
			$k = "{$term->term_id}";
			$r[$label][$k] = acf_get_term_title( $term );
		}
	}
	return $r;
}

/**
 * Get term level
 *
 * Retrieve the term level number.
 *
 * @since  1.0.0
 * @param  $term
 * @param  $taxonomy
 * @return integer
 */
function acfe_get_term_level( $term, $taxonomy ) {
	$ancestors = get_ancestors( $term, $taxonomy );
	return count( $ancestors ) + 1;
}
