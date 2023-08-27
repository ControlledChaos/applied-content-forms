<?php
/**
 * Functions for the content intro page
 *
 * @package    ACF
 * @subpackage Admin
 * @category   Functions
 * @since      1.0.0
 */

namespace ACF\Admin\Intro_Functions;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF post types
 *
 * Post types registered by ACF and
 * enabled by website options.
 *
 * @since  1.0.0
 * @return array
 */
function acf_builtin_post_types() {

	// Begin array with non-optional field groups.
	$types = [ 'acf-field-group' ];

	// If dynamic post types.
	if ( acf_get_setting( 'acfe/modules/post_types' ) ) {
		$types = array_merge( $types, [ 'acfe-dpt' ] );
	}

	// If dynamic taxonomies.
	if ( acf_get_setting( 'acfe/modules/taxonomies' ) ) {
		$types = array_merge( $types, [ 'acfe-dt' ] );
	}

	// If dynamic block types.
	if ( acf_get_setting( 'acfe/modules/block_types' ) ) {
		$types = array_merge( $types, [ 'acfe-dbt' ] );
	}

	// If dynamic block types.
	if ( acf_get_setting( 'acfe/modules/forms' ) ) {
		$types = array_merge( $types, [ 'acfe-form' ] );
	}

	// If dynamic block types.
	if ( acf_get_setting( 'acfe/modules/templates' ) ) {
		$types = array_merge( $types, [ 'acfe-template' ] );
	}

	return apply_filters( 'acf_builtin_post_types', $types );
}

/**
 * Custom post types query
 *
 * @since  1.0.0
 * @return array Returns an array of queried post types.
 */
function custom_post_types() {

	// Query public post types not built into the CMS.
	$query = [
		'public'   => true,
		'_builtin' => false
	];

	/**
	 * Return post types query.
	 * Escape namespace for native function.
	 */
	return \get_post_types( $query, 'names', 'and' );
}

/**
 * Get post type
 *
 * Gets registered post types for grid display.
 *
 * @since  1.0.0
 * @return array
 */
function get_post_types() {

	// Get built-in and enabled ACF types
	$acf_types  = acf_builtin_post_types();

	/**
	 * Native post types
	 *
	 * This array has the opinion that users should
	 * upload and manage media to be published prior
	 * to creating the post or page where the media
	 * file will be used. Thus the `attachment` post
	 * type is first, also placing it first in the
	 * intro page grid.
	 *
	 * If you don't like this, a filter is applied.
	 */
	$native = [ 'attachment', 'post', 'page' ];

	// Get custom post types.
	$custom = custom_post_types();

	// Merge custom post with native types.
	$registered = array_merge( $native, $custom );

	// Merge plugin post types with native and custom types.
	$types = array_merge( $registered, $acf_types );

	return apply_filters( 'acf_get_post_types', $types );
}

/**
 * Get post type data
 *
 * Gets data from the registered post types for grid display.
 *
 * @since  1.0.0
 * @return array
 */
function get_post_type_data() {

	// Get queried post types and set up return fallback.
	$types = get_post_types();
	$data  = null;

	// Loop through post types, if any found in custom query.
	if ( $types ) {
		foreach ( $types as $key => $type ) {

			/**
			 * Set up variables.
			 *
			 * $object  Get the post type object
			 * $count   Count the posts of the type.
			 * $publish Number of post with publish status.
			 * $cap     User capability for the post type.
			 * $icon    The default menu if not set.
			 */
			$object  = get_post_type_object( $type );
			$count   = wp_count_posts( $type, '' );
			$publish = $count->publish;
			$cap     = $object->cap->edit_posts;
			$icon    = 'dashicons-admin-post';

			// Override default icon if set for the type.
			if ( $object->menu_icon ) {
				$icon = $object->menu_icon;
			}

			// The attachment post type doesn't use `publish` status.
			if ( 'attachment' === $type ) {
				$publish = $count->inherit;
			}

			// Set return array keys and values.
			$data[$key]['slug']  = $object->name;
			$data[$key]['cap']   = $cap;
			$data[$key]['name']  = $object->labels->menu_name;
			$data[$key]['icon']  = $icon;
			$data[$key]['count'] = intval( $publish );
		}
	}
	return $data;
}

/**
 * Post types grid
 *
 * The HTML markup for the grid of post types
 * on the first, default tab of the content screen.
 *
 * @since  1.0.0
 * @return void
 */
function post_types_grid() {

	// Array of data from relevant post types.
	$types = get_post_type_data();

	// Begin grid wrapping element and list.
	$html = '<div class="acf-tab-grid"><ul>';

	foreach ( $types as $type ) {

		// Post count bubble tooltip.
		$tooltip = $type['count'] . ' ' . __( 'published', 'acf' );
		if ( 'attachment' === $type['slug'] ) {
			$tooltip = $type['count'] . ' ' . __( 'uploads', 'acf' );
		}

		// Post count bubble in grid item heading.
		if ( $type['count'] > 0 ) {
			$count = sprintf(
				'<span class="acf-js-tooltip post-count" role="tooltip" title="%s">%s</span>',
				$tooltip,
				$type['count']
			);

		// Print an add new link if count is zero.
		} else {
			$count = sprintf(
				'<span class="acf-js-tooltip post-count" role="tooltip" title="%s"><a href="%s" class="add-new">&plus;</a></span>',
				__( 'Add New', 'acf' ),
				admin_url( 'post-new.php?type_type=' . $type['slug'] )
			);
		}

		// Add new post link, different for `post` and `attachment`.
		$add_new = admin_url( 'post-new.php?post_type=' . $type['slug'] );
		if ( 'post' === $type['slug'] ) {
			$add_new = admin_url( 'post-new.php' );
		} elseif ( 'attachment' === $type['slug'] ) {
			$add_new = admin_url( 'media-new.php' );
		}

		// Manage posts link, different for `post` and `attachment`.
		$manage = admin_url( 'edit.php?post_type=' . $type['slug'] );
		if ( 'post' === $type['slug'] ) {
			$manage = admin_url( 'edit.php' );
		} elseif ( 'attachment' === $type['slug'] ) {
			$manage = admin_url( 'upload.php' );
		}

		// List item markup.
		$html .= '<li>';
		$html .= sprintf(
			'<h3>%s %s</h3>',
			$type['name'],
			$count
		);
		$html .= '<figure>';
		$html .= sprintf(
			'<div class="acf-tab-grid-icon dashicons %s"></div>',
			$type['icon']
		);
		$html .= sprintf(
			'<figcaption><a href="%s">%s</a><a href="%s">%s</a></figcaption>',
			$add_new,
			__( 'Add New', 'acf' ),
			$manage,
			__( 'Manage', 'acf' )
		);
		$html .= '</figure>';
		$html .= '</li>';
	}

	// End list and wrapping element.
	$html .= '</ul></div>';

	// Print the markup.
	echo $html;
}
add_action( 'acf/post_types_grid', __NAMESPACE__ . '\post_types_grid' );
