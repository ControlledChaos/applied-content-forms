<?php
/**
 * API helpers
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   API
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is array
 *
 * This function will return true for a non empty array
 *
 * @since  1.0.0
 * @param  array $array
 * @return boolean
 */

function acf_is_array( $array ) {
	return ( is_array( $array ) && ! empty( $array ) );
}

/**
 * Has setting
 *
 * Alias of acf()->has_setting()
 *
 * @since  1.0.0
 * @param  string $name
 * @return method
 */
function acf_has_setting( $name = '' ) {
	return acf()->has_setting( $name );
}

/**
 * Raw setting
 *
 * @since  1.0.0
 * @param  string $name
 * @return method
 */
function acf_raw_setting( $name = '' ) {
	return acf()->get_setting( $name );
}

/**
 * Update setting
 *
 * @since  1.0.0
 * @param  string $name
 * @param  string $value
 * @return method
 */
function acf_update_setting( $name, $value ) {
	$name = acf_validate_setting( $name );
	return acf()->update_setting( $name, $value );
}

/**
 * Update core settings
 *
 * Update the settings from the plugin
 * settings field group.
 *
 * Alias of acf()->update_settings()
 *
 * @since  1.0.0
 * @param  string $name
 * @param  string $value
 * @return method
 */
function acf_update_settings() {
	return acf()->update_settings();
}

/**
 * Updated core settings
 *
 * Whether the settings have been updated from
 * the plugin settings field group.
 *
 * Alias of acf()->updated_settings()
 *
 * @since  1.0.0
 * @param  string $name
 * @param  string $value
 * @return method
 */
function acf_updated_settings() {
	return acf()->updated_settings();
}

/**
 * Validate setting
 *
 * @since  1.0.0
 * @param  string $name
 * @return method
 */
function acf_validate_setting( $name = '' ) {
	return apply_filters( 'acf/validate_setting', $name );
}

/**
 * Get setting
 *
 * @since  1.0.0
 * @param  string $name
 * @param  mixed $value
 * @return mixed
 */
function acf_get_setting( $name, $value = null ) {

	// Validate name.
	$name = acf_validate_setting( $name );

	// Check settings.
	if ( acf_has_setting( $name ) ) {
		$value = acf_raw_setting( $name );
	}

	$value = apply_filters( "acf/settings/{$name}", $value );
	return $value;
}

/**
 * Append setting
 *
 * @since  1.0.0
 * @param  string $name
 * @param  string $value
 * @return function
 */
function acf_append_setting( $name, $value ) {

	$setting = acf_raw_setting( $name );

	// Stop early if not an array.
	if ( ! is_array( $setting ) ) {
		$setting = [];
	}

	$setting[] = $value;
	return acf_update_setting( $name, $setting );
}

// Settings, similar to `acf_` but with the `acfe_` prefix.
function acfe_update_setting( $name, $value ) {
	return acf_update_setting( "acfe/{$name}", $value );
}
function acfe_append_setting( $name, $value ) {
	return acf_append_setting( "acfe/{$name}", $value );
}
function acfe_get_setting( $name, $value = null ) {
	return acf_get_setting( "acfe/{$name}", $value );
}

/**
 * Get data
 *
 * @since  1.0.0
 * @param  string $name
 * @return void
 */
function acf_get_data( $name ) {
	return acf()->get_data( $name );
}

/**
 * Set data
 *
 * @since  1.0.0
 * @param  string $name
 * @param  mixed $value
 * @return void
 */
function acf_set_data( $name, $value ) {
	return acf()->set_data( $name, $value );
}

/**
 * Append data to an existing key
 *
 * @since  1.0.0
 * @param  string $name The data name.
 * @return array $data The data array.
 */
function acf_append_data( $name, $data ) {

	$prev_data = acf()->get_data( $name );
	if ( is_array( $prev_data ) ) {
		$data = array_merge( $prev_data, $data );
	}
	acf()->set_data( $name, $data );
}

/**
 * Init
 *
 * Alias of acf()->init()
 *
 * @since  1.0.0
 * @return void
 */
function acf_init() {
	acf()->init();
}

/**
 * Has done
 *
 * This function will return true if this action has already been done.
 *
 * @since  1.0.0
 * @param  string $name
 * @return boolean
 */
function acf_has_done( $name ) {

	// Return true if already done.
	if ( acf_raw_setting( "has_done_{$name}" ) ) {
		return true;
	}

	// Update setting and return.
	acf_update_setting( "has_done_{$name}", true );
	return false;
}

/**
 * Get external path
 *
 * @since  1.0.0
 * @param  string $file
 * @param  string $path
 * @return string
 */
function acf_get_external_path( $file, $path = '' ) {
    return plugin_dir_path( $file ) . $path;
}

/**
 * Get external directory
 *
 * This function will return the url to a file within an external folder.
 *
 * @since  1.0.0
 * @param  string $file
 * @param  string $path
 * @return string
 */
function acf_get_external_dir( $file, $path = '' ) {
    return acf_plugin_dir_url( $file ) . $path;
}

/**
 * Plugin_ directory URL
 *
 * This function will calculate the url to a plugin folder.
 * Different to the WP plugin_dir_url(), this function can
 * calculate for URLs outside of the plugins folder (theme include).
 *
 * @since  1.0.0
 * @param  string $file
 * @return string
 */
function acf_plugin_dir_url( $file ) {

	$path = plugin_dir_path( $file );
	$path = wp_normalize_path( $path );

	// Check plugins.
	$check_path = wp_normalize_path( realpath( WP_PLUGIN_DIR ) );
	if ( strpos( $path, $check_path ) === 0 ) {
		return str_replace( $check_path, plugins_url(), $path );
	}

	// Check wp-content.
	$check_path = wp_normalize_path( realpath( WP_CONTENT_DIR ) );
	if ( strpos( $path, $check_path ) === 0 ) {
		return str_replace( $check_path, content_url(), $path );
	}

	// Check root.
	$check_path = wp_normalize_path( realpath( ABSPATH ) );
	if ( strpos( $path, $check_path ) === 0 ) {
		return str_replace( $check_path, site_url( '/' ), $path );
	}

    return plugin_dir_url( $file );
}

/**
 * Parse args
 *
 * This function will merge together two arrays and
 * also convert any numeric values to integers.
 *
 * @since  1.0.0
 * @param  array $args
 * @param  array $defaults
 * @return array
 */
function acf_parse_args( $args, $defaults = [] ) {

	$args = wp_parse_args( $args, $defaults );
	$args = acf_parse_types( $args );
	return $args;
}

/**
 * Parse types
 *
 * This function will convert any numeric values to int and trim strings.
 *
 * @since  1.0.0
 * @param  array $array
 * @return array
 */
function acf_parse_types( $array ) {
	return array_map( 'acf_parse_type', $array );
}

/**
 * Parse type
 *
 * @since  1.0.0
 * @param  mixed $v
 * @return mixed
 */
function acf_parse_type( $v ) {

	// Check if is string.
	if ( is_string( $v ) ) {

		// Trim ("Word " = "Word").
		$v = trim( $v );

		// Convert integer strings to integer ("123" = 123).
		if ( is_numeric( $v ) && strval( intval( $v ) ) === $v ) {
			$v = intval( $v );
		}
	}
	return $v;
}

/**
 * Get view
 *
 * This function will load in a file from the 'admin/views' folder
 * and allow variables to be passed through.
 *
 * @since  1.0.0
 * @param  string $path
 * @param  array  $args
 * @return void
 */
function acf_get_view( $path = '', $args = [] ) {

	// Allow view file name shortcut.
	if ( substr( $path, -4 ) !== '.php' ) {
		$path = acf_get_path( "includes/admin/views/{$path}.php" );
	}

	// Include.
	if ( file_exists( $path ) ) {
		extract( $args );
		include( $path );
	}
}

/**
 * Merge attributes
 *
 * @since  1.0.0
 * @param  array $atts
 * @param  array  $extra
 * @return array
 */
function acf_merge_atts( $atts, $extra = [] ) {

	// Stop if no $extra,
	if ( empty( $extra ) ) {
		return $atts;
	}
	$extra = array_map( 'trim', $extra );
	$extra = array_filter( $extra );

	foreach ( $extra as $k => $v ) {

		// Append.
		if ( $k == 'class' || $k == 'style' ) {
			$atts[ $k ] .= ' ' . $v;

		// Merge.
		} else {
			$atts[ $k ] = $v;
		}
	}
	return $atts;
}

/**
 * Nonce input
 *
 * @since  1.0.0
 * @param  string $nonce
 * @return string
 */
function acf_nonce_input( $nonce = '' ) {
	echo '<input type="hidden" name="_acf_nonce" value="' . wp_create_nonce( $nonce ) . '" />';
}

/**
 * Extract variable
 *
 * This function will remove the variable from the array,
 * and return the variable.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  string $key
 * @param  mixed $default
 * @return mixed
 */
function acf_extract_var( &$array, $key, $default = null ) {

	// Uses array_key_exists to extract NULL values (isset will fail).
	if ( is_array( $array ) && array_key_exists( $key, $array ) ) {

		$v = $array[ $key ];
		unset( $array[ $key ] );

		return $v;
	}
	return $default;
}

/**
 * Extract variables
 *
 * This function will remove the vars from the array,
 * and return the variables.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  array $key
 * @return array
 */
function acf_extract_vars( &$array, $keys ) {

	$r = [];
	foreach ( $keys as $key ) {
		$r[ $key ] = acf_extract_var( $array, $key );
	}
	return $r;
}

/**
 * Get sub array
 *
 * This function will return a sub array of data.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  array $keys
 * @return array
 */
function acf_get_sub_array( $array, $keys ) {

	$r = [];
	foreach ( $keys as $key ) {
		$r[ $key ] = $array[ $key ];
	}
	return $r;
}

/**
*  acf_get_post_types
*
*  Returns an array of post type names.
*
*  @date	7/10/13
*  @since	5.0.0
*
*  @param	array $args Optional. An array of key => value arguments to match against the post type objects. Default empty array.
*  @return	array A list of post type names.
*/
/**
 * Get post types
 *
 * Returns an array of post type names.
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_post_types( $args = [] ) {

	$post_types = [];

	// Extract special arg.
	$exclude   = acf_extract_var( $args, 'exclude', [] );
	$exclude[] = 'acf-field';
	$exclude[] = 'acf-field-group';

	// Get post type objects.
	$objects = get_post_types( $args, 'objects' );

	foreach( $objects as $i => $object ) {

		// Stop if is exclude.
		if ( in_array( $i, $exclude ) ) {
			continue;
		}

		// Stop if is builtin private post type.
		if ( $object->_builtin && ! $object->public ) {
			continue;
		}
		$post_types[] = $i;
	}

	$post_types = apply_filters( 'acf/get_post_types', $post_types, $args );
	return $post_types;
}

/**
 * Get pretty post types
 *
 * @since  1.0.0
 * @param  array $post_types
 * @return array
 */
function acf_get_pretty_post_types( $post_types = [] ) {

	// Get post types.
	if ( empty( $post_types ) ) {

		// Get all custom post types.
		$post_types = acf_get_post_types();
	}

	$ref = [];
	$r   = [];

	foreach ( $post_types as $post_type ) {

		$label = acf_get_post_type_label( $post_type );
		$r[ $post_type ] = $label;

		if ( ! isset( $ref[ $label ] ) ) {
			$ref[ $label ] = 0;
		}
		$ref[ $label ]++;
	}

	// Get slugs.
	foreach ( array_keys( $r ) as $i ) {

		$post_type = $r[ $i ];
		if ( $ref[ $post_type ] > 1 ) {
			$r[ $i ] .= ' (' . $i . ')';
		}
	}
	return $r;
}

/**
 * Get post type label
 *
 * This function will return a pretty label for a specific post_type.
 *
 * @since  1.0.0
 * @param  string $post_type
 * @return string
 */
function acf_get_post_type_label( $post_type ) {

	$label = $post_type;

	// Case exists when importing field group from another install and post type does not exist.
	if ( post_type_exists( $post_type ) ) {
		$obj = get_post_type_object( $post_type );
		$label = $obj->labels->singular_name;
	}
	return $label;
}

/**
 * Verify nonce
 *
 * This function will look at the $_POST['_acf_nonce'] value
 * and return true or false.
 *
 * @since  1.0.0
 * @param  string $value
 * @return boolean
 */
function acf_verify_nonce( $value) {

	$nonce = acf_maybe_get_POST( '_acf_nonce' );

	// Stop nonce does not match (post|user|comment|term).
	if ( ! $nonce || ! wp_verify_nonce( $nonce, $value ) ) {
		return false;
	}

	// Reset nonce (only allow 1 save).
	$_POST['_acf_nonce'] = false;

	return true;
}

/**
 * Verify AJAX
 *
 * This function will return true if the current AJAX request is valid
 * It's action will also allow WPML to set the lang and avoid AJAX get_posts issues
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_verify_ajax() {

	$nonce = isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : '';

	// Stop if not ACF nonce
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'acf_nonce' ) ) {
		return false;
	}
	do_action( 'acf/verify_ajax' );

	return true;
}

/**
 * Get image sizes
 *
 * This function will return an array of available image sizes.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_image_sizes() {

	$sizes = [
		'thumbnail' =>	__( 'Thumbnail', 'acf' ),
		'medium'    =>	__( 'Medium', 'acf' ),
		'large'     =>	__( 'Large', 'acf' )
	];

	// Find all sizes.
	$all_sizes = get_intermediate_image_sizes();

	// Add extra registered sizes.
	if ( ! empty( $all_sizes ) ) {

		foreach ( $all_sizes as $size ) {

			// Stop if already in array.
			if ( isset( $sizes[ $size ] ) ) {
				continue;
			}

			// Append to array.
			$label = str_replace( '-', ' ', $size );
			$label = ucwords( $label );
			$sizes[ $size ] = $label;
		}
	}

	foreach ( array_keys( $sizes ) as $s ) {

		$data = acf_get_image_size( $s );
		if ( $data['width'] && $data['height'] ) {
			$sizes[ $s ] .= ' (' . $data['width'] . ' x ' . $data['height'] . ')';
		}
	}
	$sizes['full'] = __( 'Full Size', 'acf' );

	// Filter for third party customization.
	$sizes = apply_filters( 'acf/get_image_sizes', $sizes );
	return $sizes;
}

/**
 * Get image size
 *
 * @since  1.0.0
 * @param  string $s
 * @global array $_wp_additional_image_sizes
 * @return array
 */
function acf_get_image_size( $s = '' ) {

	// Access global variables.
	global $_wp_additional_image_sizes;
	$sizes = $_wp_additional_image_sizes;

	if ( isset( $sizes[$s]['width'] ) ) {
		$width = $sizes[$s]['width'];
	} else {
		$width = get_option( "{$s}_size_w" );
	}

	if ( isset( $sizes[$s]['height'] ) ) {
		$height = $sizes[$s]['height'];
	} else {
		$height = get_option( "{$s}_size_h" );
	}

	$data = [
		'width'  => $width,
		'height' => $height
	];
	return $data;
}

/**
 * Version compare
 *
 * Similar to the version_compare() function but with extra functionality.
 *
 * @since  1.0.0
 * @param  string $left The left version number.
 * @param  string $compare The compare operator.
 * @param  string $right The right version number.
 * @return boolean
 */
function acf_version_compare( $left = '', $compare = '>', $right = '' ) {

	// Detect 'wp' placeholder.
	if ( $left === 'wp' ) {
		global $wp_version;
		$left = $wp_version;
	}
	return version_compare( $left, $right, $compare );
}

/**
 * Get full version
 *
 * This function will remove any '-beta1' or '-RC1'
 * strings from a version
 *
 * @since  1.0.0
 * @param  string $version
 * @return string
 */
function acf_get_full_version( $version = '1' ) {

	// Remove '-beta1' or '-RC1'.
	if ( $pos = strpos( $version, '-' ) ) {
		$version = substr( $version, 0, $pos );
	}
	return $version;
}

/**
 * Get terms
 *
 * This function is a wrapper for the get_terms() function
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_terms( $args ) {

	$args = wp_parse_args( $args, [
		'taxonomy'   => null,
		'hide_empty' => false,
		'update_term_meta_cache' => false
	] );

	// Parameters changed in version 4.5.
	if ( acf_version_compare( 'wp', '<', '4.5' ) ) {
		return get_terms( $args['taxonomy'], $args );
	}
	return get_terms( $args );
}

/**
 * Get taxonomy terms
 *
 * This function will return an array of available taxonomy terms.
 *
 * @since  1.0.0
 * @param  array $taxonomies
 * @return array
 */
function acf_get_taxonomy_terms( $taxonomies = [] ) {

	// Force array.
	$taxonomies = acf_get_array( $taxonomies );

	// Get pretty taxonomy names.
	$taxonomies = acf_get_pretty_taxonomies( $taxonomies );

	$r = [];
	foreach ( array_keys( $taxonomies ) as $taxonomy ) {

		$label = $taxonomies[ $taxonomy ];
		$is_hierarchical = is_taxonomy_hierarchical( $taxonomy );
		$terms = acf_get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false
		] );

		// Stop if no terms.
		if ( empty( $terms ) ) {
			continue;
		}

		// Sort into hierachial order.
		if ( $is_hierarchical ) {
			$terms = _get_term_children( 0, $terms, $taxonomy );
		}

		// Add placeholder.
		$r[ $label ] = [];

		// Add choices.
		foreach ( $terms as $term ) {
			$k = "{$taxonomy}:{$term->slug}";
			$r[ $label ][ $k ] = acf_get_term_title( $term );
		}
	}
	return $r;
}

/*
*  acf_decode_taxonomy_terms
*
*  This function decodes the $taxonomy:$term strings into a nested array
*
*  @type	function
*  @date	27/02/2014
*  @since	5.0.0
*
*  @param	$terms (array)
*  @return	(array)
*/
/**
 * Decode taxonomy terms
 *
 * This function decodes the $taxonomy:$term
 * strings into a nested array.
 *
 * @since  1.0.0
 * @param  mixed $strings
 * @return array
 */
function acf_decode_taxonomy_terms( $strings = false ) {

	// Stop if no terms.
	if ( empty( $strings ) ) {
		return false;
	}
	$terms = [];

	foreach ( $strings as $string ) {

		$data     = acf_decode_taxonomy_term( $string );
		$taxonomy = $data['taxonomy'];
		$term     = $data['term'];

		// Create empty array.
		if ( ! isset( $terms[ $taxonomy ] ) ) {
			$terms[ $taxonomy ] = [];
		}

		// Append.
		$terms[ $taxonomy ][] = $term;
	}
	return $terms;
}

/**
 * Decode taxonomy term
 *
 * This function will return the taxonomy and term slug for a given value.
 *
 * @since  1.0.0
 * @param  string $value
 * @global array $wpdb
 * @return array
 */
function acf_decode_taxonomy_term( $value ) {

	$data = [
		'taxonomy' => '',
		'term'     => ''
	];

	if ( is_numeric( $value ) ) {
		$data['term'] = $value;
	} elseif ( is_string( $value ) ) {

		$value = explode( ':', $value );
		$data['taxonomy'] = isset( $value[0] ) ? $value[0] : '';
		$data['term']     = isset( $value[1] ) ? $value[1] : '';
	} else {
		return false;
	}

	// Allow for term_id (Used by ACF v4).
	if ( is_numeric( $data['term'] ) ) {

		// Access global variables.
		global $wpdb;

		// Find taxonomy.
		if ( ! $data['taxonomy'] ) {
			$data['taxonomy'] = $wpdb->get_var( $wpdb->prepare( "SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_id = %d LIMIT 1", $data['term'] ) );
		}

		// Find term (may have numeric slug '123').
		$term = get_term_by( 'slug', $data['term'], $data['taxonomy'] );

		// Attempt get term via ID (ACF4 uses ID).
		if ( ! $term ) {
			$term = get_term( $data['term'], $data['taxonomy'] );
		}

		// Stop if no term.
		if ( ! $term ) {
			return false;
		}

		$data['taxonomy'] = $term->taxonomy;
		$data['term']     = $term->slug;
	}
	return $data;
}

/**
 * Array
 *
 * Casts the value into an array.
 *
 * @since  1.0.0
 * @param  mixed $val The value to cast.
 * @return array
 */
function acf_array( $val = [] ) {
	return (array) $val;
}

/**
 * A non-array value
 *
 * @since  1.0.0
 * @param  mixed $val The value to review.
 * @return mixed
 */
function acf_unarray( $val ) {
	if ( is_array( $val ) ) {
		return reset( $val );
	}
	return $val;
}

/**
 * Get array
 *
 * This function will force a variable to become an array.
 *
 * @since  1.0.0
 * @param  mixed $var
 * @param  string $delimiter
 * @return array
 */
function acf_get_array( $var = false, $delimiter = '' ) {

	// Array.
	if ( is_array( $var ) ) {
		return $var;
	}

	// Stop if empty.
	if ( acf_is_empty( $var ) ) {
		return [];
	}

	// String.
	if ( is_string( $var ) && $delimiter ) {
		return explode( $delimiter, $var );
	}
	return (array) $var;
}

/**
 * Get numeric
 *
 * This function will return numeric values.
 *
 * @since  1.0.0
 * @param  mixed $value
 * @return mixed
 */
function acf_get_numeric( $value = '' ) {

	$numbers  = [];
	$is_array = is_array( $value );

	foreach ( (array) $value as $v ) {
		if ( is_numeric( $v ) ) {
			$numbers[] = (int) $v;
		}
	}

	// Stop if is empty.
	if ( empty( $numbers ) ) {
		return false;
	}

	// Convert array.
	if ( ! $is_array ) {
		$numbers = $numbers[0];
	}
	return $numbers;
}

/**
 * Get posts
 *
 * Similar to the get_posts() function but with extra functionality.
 *
 * @since  1.0.0
 * @param  array $args The query args.
 * @return array
 */
function acf_get_posts( $args = [] ) {

	$posts = [];

	// Apply default args.
	$args = wp_parse_args( $args, [
		'posts_per_page'         => -1,
		'post_type'              => '',
		'post_status'            => 'any',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false
	] );

	// Avoid default 'post' post_type by providing all public types.
	if ( ! $args['post_type'] ) {
		$args['post_type'] = acf_get_post_types();
	}

	// Check if specific post ID's have been provided.
	if ( $args['post__in'] ) {

		// Clean value into an array of IDs.
		$args['post__in'] = array_map( 'intval', acf_array( $args['post__in'] ) );
	}

	// Query posts.
	$posts = get_posts( $args );

	// Remove any potential empty results.
	$posts = array_filter( $posts );

	// Manually order results.
	if ( $posts && $args['post__in'] ) {

		$order = [];
		foreach ( $posts as $i => $post ) {
			$order[ $i ] = array_search( $post->ID, $args['post__in'] );
		}
		array_multisort( $order, $posts );
	}
	return $posts;
}

/**
 * Query remove post type
 *
 * @since  1.0.0
 * @param  string $sql
 * @global array $wpdb
 * @return array
 */
function _acf_query_remove_post_type( $sql ) {

	// Access global variables.
	global $wpdb;

	// Stop if no 'wp_posts.ID IN'
	if ( strpos( $sql, "$wpdb->posts.ID IN" ) === false ) {
		return $sql;
	}

    // Get bits.
	$glue = 'AND';
	$bits = explode( $glue, $sql );

	// Loop through $where and remove any post_type queries.
	foreach ( $bits as $i => $bit ) {
		if ( strpos( $bit, "$wpdb->posts.post_type" ) !== false ) {
			unset( $bits[ $i ] );
		}
	}

	// Join $where back together.
	$sql = implode( $glue, $bits );

    return $sql;
}

/**
 * Get grouped posts
 *
 * This function will return all posts grouped by post_type.
 * This is handy for select settings.
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_grouped_posts( $args ) {

	$data = [];
	$args = wp_parse_args( $args, [
		'posts_per_page' => -1,
		'paged'          => 0,
		'post_type'      => 'post',
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'post_status'    => 'any',
		'suppress_filters'       => false,
		'update_post_meta_cache' => false,
	] );

	$post_types = acf_get_array( $args['post_type'] );
	$post_types_labels   = acf_get_pretty_post_types( $post_types );
	$is_single_post_type = ( count( $post_types ) == 1 );

	// Attachment doesn't work if it is the only item in an array.
	if ( $is_single_post_type ) {
		$args['post_type'] = reset( $post_types );
	}

	// Add filter to orderby post type.
	if ( ! $is_single_post_type ) {
		add_filter( 'posts_orderby', '_acf_orderby_post_type', 10, 2 );
	}
	$posts = get_posts( $args );

	if ( ! $is_single_post_type ) {
		remove_filter( 'posts_orderby', '_acf_orderby_post_type', 10, 2 );
	}

	foreach ( $post_types as $post_type ) {

		$this_posts = [];
		$this_group = [];

		// Populate $this_posts.
		foreach ( $posts as $post ) {
			if ( $post->post_type == $post_type ) {
				$this_posts[] = $post;
			}
		}

		// Stop if no posts for this post type.
		if ( empty( $this_posts ) ) {
			continue;
		}

		/**
		 * Sort into hierachial order. This will fail if
		 * a search has taken place because parents wont exist.
		 */
		if ( is_post_type_hierarchical( $post_type ) && empty( $args['s'] ) ) {

			$post_id   = $this_posts[0]->ID;
			$parent_id = acf_maybe_get( $args, 'post_parent', 0 );
			$offset    = 0;
			$length    = count( $this_posts );

			// Get all posts from this post type.
			$all_posts = get_posts( array_merge( $args, [
				'posts_per_page' => -1,
				'paged'          => 0,
				'post_type'      => $post_type
			] ) );

			// Find starting point (offset).
			foreach ( $all_posts as $i => $post ) {
				if ( $post->ID == $post_id ) {
					$offset = $i;
					break;
				}
			}
			$ordered_posts = get_page_children( $parent_id, $all_posts );

			/**
			 * Compare aray lengths. If $ordered_posts is smaller than
			 * $all_posts, CMS has lost posts during the get_page_children()
			 * function. This is possible when get_post( $args ) filter out
			 * parents (via taxonomy, meta and other search parameters).
			 */
			if ( count( $ordered_posts ) == count( $all_posts ) ) {
				$this_posts = array_slice( $ordered_posts, $offset, $length );
			}
		}

		// Populate $this_posts.
		foreach ( $this_posts as $post ) {
			$this_group[ $post->ID ] = $post;
		}

		// Group by post type.
		$label = $post_types_labels[ $post_type ];
		$data[ $label ] = $this_group;
	}
	return $data;
}

/**
 * Order by post type
 *
 * @since  1.0.0
 * @param  string $ordeby
 * @param  object $wp_query
 * @global array $wpdb
 * @return string
 */
function _acf_orderby_post_type( $ordeby, $wp_query ) {

	// Access global variables.
	global $wpdb;

	// Get post types.
	$post_types = $wp_query->get( 'post_type' );

	// Prepend SQL.
	if ( is_array( $post_types ) ) {

		$post_types = implode( "','", $post_types );
		$ordeby     = "FIELD({$wpdb->posts}.post_type,'$post_types')," . $ordeby;
	}
	return $ordeby;
}

/**
 * Get_ post title
 *
 * @since  1.0.0
 * @param  integer $post
 * @param  boolean $is_search
 * @return string
 */
function acf_get_post_title( $post = 0, $is_search = false ) {

	$post    = get_post( $post );
	$title   = '';
	$prepend = '';
	$append  = '';

	// Stop if no post.
	if ( ! $post ) {
		return '';
	}

	$title = get_the_title( $post->ID );
	if ( '' === $title ) {
		$title = __( '(no title)', 'acf' );
	}

	if ( get_post_status( $post->ID ) != 'publish' ) {
		$append .= ' (' . get_post_status( $post->ID ) . ')';
	}

	// Ancestors.
	if ( $post->post_type !== 'attachment' ) {

		// Get ancestors.
		$ancestors = get_ancestors( $post->ID, $post->post_type );
		$prepend  .= str_repeat( '- ', count( $ancestors ) );
	}
	$title = $prepend . $title . $append;
	return $title;
}

/**
 * Order by search
 *
 * @since  1.0.0
 * @param  array $array
 * @param  string $search
 * @return array
 */
function acf_order_by_search( $array, $search ) {

	$weights = [];
	$needle  = strtolower( $search );

	// Add key prefix.
	foreach ( array_keys( $array ) as $k ) {
		$array[ '_' . $k ] = acf_extract_var( $array, $k );
	}

	// Add search weight.
	foreach( $array as $k => $v ) {

		$weight   = 0;
		$haystack = strtolower( $v );
		$strpos   = strpos( $haystack, $needle );

		// Detect search match.
		if ( $strpos !== false ) {

			// Set eright to length of match.
			$weight = strlen( $search );

			// Increase weight if match starts at begining of string.
			if ( $strpos == 0 ) {
				$weight++;
			}
		}

		// Append to weights.
		$weights[ $k ] = $weight;
	}

	// Sort the array with menu_order ascending.
	array_multisort( $weights, SORT_DESC, $array );

	// remove key prefix.
	foreach ( array_keys( $array ) as $k ) {
		$array[ substr($k,1) ] = acf_extract_var( $array, $k );
	}
	return $array;
}

/**
 * Get pretty user roles
 *
 * @since  1.0.0
 * @param  mixed $allowed
 * @return array
 */
function acf_get_pretty_user_roles( $allowed = false ) {

	$editable_roles = get_editable_roles();
	$allowed = acf_get_array( $allowed );
	$roles   = [];

	foreach ( $editable_roles as $role_name => $role_details ) {

		// Stop if not allowed.
		if ( ! empty( $allowed ) && ! in_array( $role_name, $allowed ) ) {
			continue;
		}
		$roles[ $role_name ] = translate_user_role( $role_details['name'] );
	}
	return $roles;
}

/**
 * Get grouped users
 *
 * This function will return all users grouped by role.
 * This is handy for select settings.
 *
 * @since  1.0.0
 * @param  array $args
 * @global string $wp_version
 * @global array $wpdb
 * @return array
 */
function acf_get_grouped_users( $args = [] ) {

	$r    = [];
	$args = wp_parse_args( $args, [
		'users_per_page' => -1,
		'paged'          => 0,
		'role'           => '',
		'orderby'        => 'login',
		'order'          => 'ASC',
	] );

	$i     = 0;
	$min   = 0;
	$max   = 0;
	$paged = acf_extract_var( $args, 'paged' );
	$users_per_page = acf_extract_var( $args, 'users_per_page' );

	if ( $users_per_page > 0 ) {

		// Prevent paged from being -1.
		$paged = max( 0, $paged );

		// Set min / max.
		$min = ( ( $paged-1 ) * $users_per_page ) + 1; //  1, 11
		$max = ( $paged * $users_per_page ); // 10,	20
	}

	// Find array of post_type.
	$user_roles = acf_get_pretty_user_roles( $args['role'] );

	if ( is_array( $args['role'] ) ) {

		// Access global variables.
   		global $wp_version, $wpdb;

		$roles = acf_extract_var( $args, 'role' );

		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			$args['role__in'] = $roles;
		} else {
			$blog_id    = get_current_blog_id();
			$meta_query = [ 'relation' => 'OR' ];

			foreach ( $roles as $role ) {

				$meta_query[] = [
					'key'     => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
					'value'   => '"' . $role . '"',
					'compare' => 'LIKE',
				];
			}
			$args['meta_query'] = $meta_query;
		}
	}

	$users = get_users( $args );
	foreach( $user_roles as $user_role_name => $user_role_label ) {

		$this_users = [];
		$this_group = [];

		foreach ( array_keys( $users ) as $key ) {

			// Stop if not correct role.
			if ( ! in_array( $user_role_name, $users[ $key ]->roles ) ) {
				continue;
			}

			// Extract user.
			$user = acf_extract_var( $users, $key );
			$i++;

			// Stop if too low.
			if ( $min && $i < $min ) {
				continue;
			}

			// Stop if too high (don't bother looking at any more users).
			if ( $max && $i > $max ) {
				break;
			}

			// Group by post type.
			$this_users[ $user->ID ] = $user;
		}

		// Stop if no posts for this post type.
		if ( empty( $this_users ) ) {
			continue;
		}

		// Append.
		$r[ $user_role_label ] = $this_users;
	}
	return $r;
}

/**
 * JSON encode
 *
 * Returns json_encode() ready for file / database use.
 *
 * @since  1.0.0
 * @param  array $json The array of data to encode.
 * @return string
 */
function acf_json_encode( $json ) {
	return json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
}

/**
 * String exists
 *
 * @since  1.0.0
 * @param  string $needle
 * @param  string $haystack
 * @return boolean
 */
function acf_str_exists( $needle, $haystack ) {

	// Return true if $haystack contains the $needle.
	if ( is_string( $haystack ) && strpos( $haystack, $needle ) !== false ) {
		return true;
	}
	return false;
}

/**
 * Debug
 *
 * @since  1.0.0
 * @return void
 */
function acf_debug() {

	$args = func_get_args();
	$s    = array_shift( $args );
	$o    = '';
	$nl   = "\r\n";

	// Start script.
	$o .= '<script type="text/javascript">' . $nl;
	$o .= 'console.log("' . $s . '"';

	if ( ! empty( $args ) ) {

		foreach ( $args as $arg ) {

			if ( is_object( $arg ) || is_array( $arg ) ) {
				$arg = json_encode( $arg );
			} elseif ( is_bool( $arg ) ) {
				$arg = $arg ? 'true' : 'false';
			} elseif ( is_string( $arg ) ) {
				$arg = '"' . $arg . '"';
			}
			$o .= ', ' . $arg;
		}
	}
	$o .= ');' . $nl;

	// End script.
	$o .= '</script>' . $nl;

	echo $o;
}

/**
 * Debug start
 *
 * @since  1.0.0
 * @return void
 */
function acf_debug_start() {
	acf_update_setting( 'debug_start', memory_get_usage() );
}

/**
 * Debug end
 *
 * @since  1.0.0
 * @return string
 */
function acf_debug_end() {

	$start = acf_get_setting( 'debug_start' );
	$end   = memory_get_usage();

	return $end - $start;
}

/**
 * Encode choices
 *
 * @since  1.0.0
 * @param  array $array
 * @param  boolean $show_keys
 * @return mixed
 */
function acf_encode_choices( $array = [], $show_keys = true ) {

	// Stop if not array (maybe a single string).
	if ( ! is_array( $array ) ) {
		return $array;
	}

	// Stop if empty array
	if ( empty( $array ) ) {
		return '';
	}

	$string = '';

	// If allowed to show keys (good for choices, not for default values).
	if ( $show_keys ) {

		foreach( $array as $k => $v ) {

			// Ignore if key and value are the same.
			if ( strval( $k ) == strval( $v ) ) {
				continue;
			}

			// Show key in the value.
			$array[ $k ] = $k . ' : ' . $v;
		}
	}

	$string = implode( "\n", $array );
	return $string;
}

/**
 * Decode choices
 *
 * @since  1.0.0
 * @param  string $string
 * @param  boolean $array_keys
 * @return array
 */
function acf_decode_choices( $string = '', $array_keys = false ) {

	// Stop if already array.
	if ( is_array( $string ) ) {
		return $string;

	// Allow numeric values (same as string).
	} elseif ( is_numeric( $string ) ) {
		// Do nothing

	// Stop if not a string.
	} elseif ( ! is_string( $string ) ) {
		return [];

	// Stop if is empty string.
	} elseif ( $string === '' ) {
		return [];
	}

	$array = [];
	$lines = explode( "\n", $string );

	foreach ( $lines as $line ) {

		$k = trim( $line );
		$v = trim( $line );

		// Look for ' : '
		if ( acf_str_exists( ' : ', $line ) ) {
			$line = explode( ' : ', $line );
			$k    = trim( $line[0] );
			$v    = trim( $line[1] );
		}
		$array[ $k ] = $v;
	}

	// Return only array keys? (good for checkbox default_value).
	if ( $array_keys ) {
		return array_keys( $array );
	}
	return $array;
}

/**
 * String replace
 *
 * This function will replace an array of strings much like str_replace.
 * The difference is the extra logic to avoid replacing a string
 * that has alread been replaced. This is very useful for replacing
 * date characters as they overlap with eachother.
 *
 * @since  1.0.0
 * @param  string $string
 * @param  array $search_replace
 * @return string
 */
function acf_str_replace( $string = '', $search_replace = [] ) {

	$ignore = [];

	// Remove potential empty search to avoid PHP error.
	unset( $search_replace[''] );

	// Loop over conversions.
	foreach ( $search_replace as $search => $replace ) {

		// Ignore this search, it was a previous replace.
		if ( in_array( $search, $ignore ) ) {
			continue;
		}

		// Stop if subsctring not found.
		if ( strpos( $string, $search ) === false ) {
			continue;
		}

		// Replace.
		$string   = str_replace( $search, $replace, $string );
		$ignore[] = $replace;
	}
	return $string;
}

/**
 * Update date & time formats.
 *
 * These settings contain an association of format
 * strings from PHP => JS
 */
acf_update_setting( 'php_to_js_date_formats', [

	// Year.
	'Y'	=> 'yy', // Numeric, 4 digits 1999, 2003
	'y'	=> 'y',	 // Numeric, 2 digits 99, 03

	// Month.
	'm'	=> 'mm', // Numeric, with leading zeros 01–12
	'n'	=> 'm',  // Numeric, without leading zeros 1–12
	'F'	=> 'MM', // Textual full January – December
	'M'	=> 'M',  // Textual three letters Jan - Dec

	// Weekday.
	'l'	=> 'DD', // Full name (lowercase 'L') Sunday – Saturday
	'D'	=> 'D',  // Three letter name Mon – Sun

	// Day of Month.
	'd'	=> 'dd', // Numeric, with leading zeros 01–31
	'j'	=> 'd',  // Numeric, without leading zeros 1–31
	'S'	=> '',   // The English suffix for the day of the month  st, nd or th in the 1st, 2nd or 15th.
] );

acf_update_setting( 'php_to_js_time_formats', [

	'a' => 'tt', // Lowercase Ante meridiem and Post meridiem am or pm
	'A' => 'TT', // Uppercase Ante meridiem and Post meridiem AM or PM
	'h' => 'hh', // 12-hour format of an hour with leading zeros 01 through 12
	'g' => 'h',  // 12-hour format of an hour without leading zeros 1 through 12
	'H' => 'HH', // 24-hour format of an hour with leading zeros 00 through 23
	'G' => 'H',  // 24-hour format of an hour without leading zeros 0 through 23
	'i' => 'mm', // Minutes with leading zeros 00 to 59
	's' => 'ss', // Seconds, with leading zeros 00 through 59
] );

/**
 * Split date time
 *
 * @since  1.0.0
 * @param  string $date_time
 * @return array
 */
function acf_split_date_time( $date_time = '' ) {

	$php_date = acf_get_setting( 'php_to_js_date_formats' );
	$php_time = acf_get_setting( 'php_to_js_time_formats' );
	$chars    = str_split( $date_time );
	$type     = 'date';

	$data = [
		'date' => '',
		'time' => ''
	];

	foreach ( $chars as $i => $c ) {

		// Find type.
		// Allow misc characters to append to previous type.
		if ( isset( $php_date[ $c ] ) ) {
			$type = 'date';
		} elseif ( isset( $php_time[ $c ] ) ) {
			$type = 'time';
		}
		$data[ $type ] .= $c;
	}
	$data['date'] = trim( $data['date'] );
	$data['time'] = trim( $data['time'] );

	return $data;
}

/**
 * Convert date to PHP
 *
 * This fucntion converts a date format string from JS to PHP.
 *
 * @since  1.0.0
 * @param  string $date
 * @return string
 */
function acf_convert_date_to_php( $date = '' ) {

	$php_to_js = acf_get_setting( 'php_to_js_date_formats' );
	$js_to_php = array_flip( $php_to_js );

	return acf_str_replace( $date, $js_to_php );
}

/**
 * Convert date to JS
 *
 * This fucntion converts a date format string from PHP to JS.
 *
 * @since  1.0.0
 * @param  string $date
 * @return string
 */
function acf_convert_date_to_js( $date = '' ) {
	$php_to_js = acf_get_setting( 'php_to_js_date_formats' );
	return acf_str_replace( $date, $php_to_js );
}

/**
 * Convert time to PHP
 *
 * This fucntion converts a time format string from JS to PHP.
 *
 * @since  1.0.0
 * @param  string $time
 * @return string
 */
function acf_convert_time_to_php( $time = '' ) {

	$php_to_js = acf_get_setting( 'php_to_js_time_formats' );
	$js_to_php = array_flip( $php_to_js );

	return acf_str_replace( $time, $js_to_php );
}

/**
 * Convert time to JS
 *
 * This fucntion converts a time format string from PHP to JS.
 *
 * @since  1.0.0
 * @param  string $time
 * @return string
 */
function acf_convert_time_to_js( $time = '' ) {

	$php_to_js = acf_get_setting( 'php_to_js_time_formats' );

	return acf_str_replace( $time, $php_to_js );
}

/**
 * Update user setting
 *
 * @since  1.0.0
 * @param  string $name
 * @param  mixed $value
 * @return mixed
 */
function acf_update_user_setting( $name, $value ) {

	$user_id  = get_current_user_id();
	$settings = get_user_meta( $user_id, 'acf_user_settings', true );
	$settings = acf_get_array( $settings );

	// Delete setting (allow 0 to save).
	if ( acf_is_empty( $value ) ) {
		unset( $settings[ $name ] );
	} else {
		$settings[ $name ] = $value;
	}

	// Update user data.
	return update_metadata( 'user', $user_id, 'acf_user_settings', $settings );
}

/**
 * Get user setting
 *
 * @since  1.0.0
 * @param  string  $name
 * @param  boolean $default
 * @return mixed
 */
function acf_get_user_setting( $name = '', $default = false ) {

	$user_id  = get_current_user_id();
	$settings = get_user_meta( $user_id, 'acf_user_settings', true );
	$settings = acf_get_array( $settings );

	// Stop if no settings.
	if ( ! isset( $settings[$name] ) ) {
		return $default;
	}
	return $settings[$name];
}

/**
 * In array
 *
 * @since  1.0.0
 * @param  mixed  $value
 * @param  boolean $array
 * @return boolean
 */
function acf_in_array( $value = '', $array = false ) {

	// Stop if not array.
	if ( ! is_array( $array ) ) {
		return false;
	}
	return in_array( $value, $array );
}

/**
 * Get valid post ID
 *
 * This function will return a valid post ID based
 * on the current screen / parameter.
 *
 * @since  1.0.0
 * @param  integer $post_id
 * @return integer
 */
function acf_get_valid_post_id( $post_id = 0 ) {

	// Allow filter to short-circuit load_value logic.
	$preload = apply_filters( 'acf/pre_load_post_id', null, $post_id );
    if ( $preload !== null ) {
	    return $preload;
    }

	$_post_id = $post_id;

	// If not $post_id, load queried object.
	if ( ! $post_id ) {

		// Try for global post (needed for setup_postdata).
		$post_id = (int) get_the_ID();

		// Try for current screen.
		if ( ! $post_id ) {
			$post_id = get_queried_object();
		}
	}

	// $post_id may be an object.
	// @todo Compare class types instead.
	if ( is_object( $post_id ) ) {

		if ( isset( $post_id->post_type, $post_id->ID ) ) {
			$post_id = $post_id->ID;
		} elseif ( isset( $post_id->roles, $post_id->ID ) ) {
			$post_id = 'user_' . $post_id->ID;
		} elseif ( isset( $post_id->taxonomy, $post_id->term_id ) ) {
			$post_id = 'term_' . $post_id->term_id;
		} elseif ( isset( $post_id->comment_ID ) ) {
			$post_id = 'comment_' . $post_id->comment_ID;
		} else {
			$post_id = 0;
		}
	}

	// Allow for option == options.
	if ( 'option' === $post_id ) {
		$post_id = 'options';
	}

	// Append language code.
	if ( 'options' == $post_id ) {

		$dl = acf_get_setting( 'default_language' );
		$cl = acf_get_setting( 'current_language' );

		if ( $cl && $cl !== $dl ) {
			$post_id .= '_' . $cl;
		}
	}

	// Filter for third party.
	$post_id = apply_filters( 'acf/validate_post_id', $post_id, $_post_id );
	return $post_id;
}

/**
 * Get post ID info
 *
 * This function will return the type and
 * ID for a given $post ID string,
 *
 * @since  1.0.0
 * @param  mixed $post_id
 * @return array
 */
function acf_get_post_id_info( $post_id = 0 ) {

	$info = [
		'type' => 'post',
		'id'   => 0
	];

	if ( ! $post_id ) {
		return $info;
	}

	if ( is_numeric( $post_id ) ) {
		$info['id'] = (int) $post_id;
	} elseif ( is_string( $post_id ) ) {

		$glue = '_';
		$type = explode( $glue, $post_id );
		$id   = array_pop( $type );
		$type = implode( $glue, $type );
		$meta = [ 'post', 'user', 'comment', 'term' ];

		// Avoid scenario where taxonomy exists with name of meta type.
		if ( ! in_array( $type, $meta ) && acf_isset_termmeta( $type ) ) {
			$type = 'term';
		}

		if ( is_numeric( $id ) && in_array( $type, $meta ) ) {
			$info['type'] = $type;
			$info['id']   = (int) $id;
		} else {
			$info['type'] = 'option';
			$info['id']   = $post_id;
		}
	}
	return apply_filters( 'acf/get_post_id_info', $info, $post_id );
}

/**
 * Is set termmeta
 *
 *  This function will return true if the termmeta table exists
 * @link https://developer.wordpress.org/reference/functions/get_term_meta/
 *
 * @since  1.0.0
 * @param  string $taxonomy
 * @return boolean
 */
function acf_isset_termmeta( $taxonomy = '' ) {

	// Stop if no table.
	if ( get_option( 'db_version' ) < 34370 ) {
		return false;
	}

	// Check taxonomy.
	if ( $taxonomy && ! taxonomy_exists( $taxonomy ) ) {
		return false;
	}
	return true;
}

/**
 * Upload files
 *
 * This function will walk througfh the $_FILES data and upload each found.
 *
 * @since  1.0.0
 * @param  array $ancestors
 * @return void
 */
function acf_upload_files( $ancestors = [] ) {

	$file = [
		'name'     => '',
		'type'     => '',
		'tmp_name' => '',
		'error'    => '',
		'size'     => ''
	];

	// Populate with $_FILES data.
	foreach ( array_keys( $file ) as $k ) {
		$file[ $k ] = $_FILES['acf'][ $k ];
	}

	// Walk through ancestors.
	if ( ! empty( $ancestors ) ) {
		foreach ( $ancestors as $a ) {
			foreach ( array_keys( $file ) as $k ) {
				$file[ $k ] = $file[ $k ][ $a ];
			}
		}
	}

	// Is array?
	if ( is_array( $file['name'] ) ) {
		foreach ( array_keys( $file['name'] ) as $k ) {
			$_ancestors = array_merge( $ancestors, [ $k ] );
			acf_upload_files( $_ancestors );
		}
		return;
	}

	// Stop if file has error (no file uploaded).
	if ( $file['error'] ) {
		return;
	}

	// Assign global _acfuploader for media validation.
	$_POST['_acfuploader'] = end( $ancestors );

	// File found.
	$attachment_id = acf_upload_file( $file );

	// Update $_POST.
	array_unshift( $ancestors, 'acf' );
	acf_update_nested_array( $_POST, $ancestors, $attachment_id );
}

/**
 * Upload file
 *
 * This function will uploade a $_FILE.
 *
 * @since  1.0.0
 * @param  array $uploaded_file
 * @return integer
 */
function acf_upload_file( $uploaded_file ) {

	if ( ! isset( $wp_did_header ) ) {
		require_once( ABSPATH . '/wp-load.php' );
	}
	require_once( ABSPATH . '/wp-admin/includes/media.php' ); // Video functions.
	require_once( ABSPATH . '/wp-admin/includes/file.php' );
	require_once( ABSPATH . '/wp-admin/includes/image.php' );

	// Required for wp_handle_upload() to upload the file.
	$upload_overrides = [ 'test_form' => false ];

	// Upload.
	$file = wp_handle_upload( $uploaded_file, $upload_overrides );

	// Stop if upload failed.
	if ( isset( $file['error'] ) ) {
		return $file['error'];
	}

	$url  = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$filename = basename( $file );

	// Construct the object array.
	$object = [
		'post_title'     => $filename,
		'post_mime_type' => $type,
		'guid'           => $url
	];

	// Save the data.
	$id = wp_insert_attachment( $object, $file );

	// Add the meta-data.
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

	// For replication.
	do_action( 'wp_create_file_in_uploads', $file, $id );

	return $id;
}

/**
 * Update nested array
 *
 * This function will update a nested array value.
 * Useful for modifying the $_POST array.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  array $ancestors
 * @param  mixed $value
 * @return mixed
 */
function acf_update_nested_array( &$array, $ancestors, $value ) {

	// If no more ancestors, update the current var.
	if ( empty( $ancestors ) ) {
		$array = $value;
		return true;
	}

	// Shift the next ancestor from the array.
	$k = array_shift( $ancestors );

	// If exists.
	if ( isset( $array[ $k ] ) ) {
		return acf_update_nested_array( $array[ $k ], $ancestors, $value );
	}
	return false;
}

/**
 * Is screen
 *
 * This function will return true if all args are matched
 * for the current screen.
 *
 * @since  1.0.0
 * @param  string $id
 * @return mixed
 */
function acf_is_screen( $id = '' ) {

	// Stop if not defined.
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$current_screen = get_current_screen();
	if ( ! $current_screen ) {
		return false;
	} elseif ( is_array( $id ) ) {
		return in_array( $current_screen->id, $id );
	} else {
		return ( $id === $current_screen->id );
	}
}

/**
 * Maybe get functions
 *
 * These functions will return a var if it exists in an array.
 *
 * @since  1.0.0
 * @param  array $array The array to look within
 * @param  string $key The array key to look for.
 *                Nested values may be found using '/'.
 * @param  mixed $default The value returned if not found
 * @return mixed
 */
function acf_maybe_get( $array = [], $key = 0, $default = null ) {
	if ( is_object( $array ) ) {
		return isset( $array->{$key} ) ? $array->{$key} : $default;
	}
	return isset( $array[$key] ) ? $array[$key] : $default;
}
function acf_maybe_get_POST( $key = '', $default = null ) {
	return isset( $_POST[$key] ) ? $_POST[$key] : $default;
}
function acf_maybe_get_REQUEST( $key = '', $default = null ) {
	return isset( $_REQUEST[$key] ) ? $_REQUEST[$key] : $default;
}
function acf_maybe_get_GET( $key = '', $default = null ) {
	return isset( $_GET[$key] ) ? $_GET[$key] : $default;
}

/**
 * Get attachment
 *
 * Returns an array of attachment data.
 *
 * @since  1.0.0
 * @param  mixed The attachment ID or object.
 * @return mixed
 */
function acf_get_attachment( $attachment ) {

	// Allow filter to short-circuit load attachment logic.
	// Alternatively, this filter may be used to switch blogs for multisite media functionality.
	$response = apply_filters( "acf/pre_load_attachment", null, $attachment );
	if ( $response !== null ) {
		return $response;
	}

	// Get the attachment post object.
	$attachment = get_post( $attachment );
	if ( ! $attachment ) {
		return false;
	}
	if ( $attachment->post_type !== 'attachment' ) {
		return false;
	}

	// Load various attachment details.
	$meta = wp_get_attachment_metadata( $attachment->ID );
	$attached_file = get_attached_file( $attachment->ID );
	if ( strpos( $attachment->post_mime_type, '/' ) !== false ) {
		list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
	} else {
		list( $type, $subtype ) = [ $attachment->post_mime_type, '' ];
	}

	// Generate response.
	$response = [
		'ID'          => $attachment->ID,
		'id'          => $attachment->ID,
		'title'       => $attachment->post_title,
		'filename'    => wp_basename( $attached_file ),
		'filesize'    => 0,
		'url'         => wp_get_attachment_url( $attachment->ID ),
		'link'        => get_attachment_link( $attachment->ID ),
		'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'author'      => $attachment->post_author,
		'description' => $attachment->post_content,
		'caption'     => $attachment->post_excerpt,
		'name'        => $attachment->post_name,
        'status'      => $attachment->post_status,
        'uploaded_to' => $attachment->post_parent,
        'date'        => $attachment->post_date_gmt,
		'modified'    => $attachment->post_modified_gmt,
		'menu_order'  => $attachment->menu_order,
		'mime_type'   => $attachment->post_mime_type,
        'type'        => $type,
        'subtype'     => $subtype,
        'icon'        => wp_mime_type_icon( $attachment->ID )
	];

	// Append filesize data.
	if ( isset( $meta['filesize'] ) ) {
		$response['filesize'] = $meta['filesize'];
	} elseif ( file_exists( $attached_file ) ) {
		$response['filesize'] = filesize( $attached_file );
	}

	// Restrict the loading of image "sizes".
	$sizes_id = 0;

	// Type specific logic.
	switch ( $type ) {
		case 'image':
			$sizes_id = $attachment->ID;
			$src      = wp_get_attachment_image_src( $attachment->ID, 'full' );
			if ( $src ) {
				$response['url']    = $src[0];
				$response['width']  = $src[1];
				$response['height'] = $src[2];
			}
			break;
		case 'video':
			$response['width']  = acf_maybe_get( $meta, 'width', 0 );
			$response['height'] = acf_maybe_get( $meta, 'height', 0 );
			if ( $featured_id == get_post_thumbnail_id( $attachment->ID ) ) {
				$sizes_id = $featured_id;
			}
			break;
		case 'audio':
			if ( $featured_id = get_post_thumbnail_id( $attachment->ID ) ) {
				$sizes_id = $featured_id;
			}
			break;
	}

	// Load array of image sizes.
	if ( $sizes_id ) {

		$sizes = get_intermediate_image_sizes();
		$sizes_data = [];

		foreach ( $sizes as $size ) {

			$src = wp_get_attachment_image_src( $sizes_id, $size );
			if ( $src ) {
				$sizes_data[ $size ] = $src[0];
				$sizes_data[ $size . '-width' ]  = $src[1];
				$sizes_data[ $size . '-height' ] = $src[2];
			}
		}
		$response['sizes'] = $sizes_data;
	}
	return apply_filters( 'acf/load_attachment', $response, $attachment, $meta );
}

/**
 * Get truncated
 *
 * @since  1.0.0
 * @param  string $text
 * @param  integer $length
 * @return string
 */
function acf_get_truncated( $text, $length = 64 ) {

	$text       = trim( $text );
	$the_length = strlen( $text );
	$return     = substr( $text, 0, ( $length - 3 ) );

	if ( $the_length > ( $length - 3 ) ) {
		$return .= '...';
	}
	return $return;
}

/**
 * Current user can admin
 *
 * This function will return true if the current user
 * can administrate the ACF field groups.
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_current_user_can_admin() {

	if ( acf_get_setting( 'show_admin' ) && current_user_can( acf_get_setting( 'capability' ) ) ) {
		return true;
	}
	return false;
}

/**
 * Get filesize
 *
 * This function will return a numeric value of bytes
 * for a given filesize string.
 *
 * @since  1.0.0
 * @param  mixed $size
 * @return integer
 */
function acf_get_filesize( $size = 1 ) {

	$unit  = 'MB';
	$units = [
		'TB' => 4,
		'GB' => 3,
		'MB' => 2,
		'KB' => 1,
	];

	// Look for $unit within the $size parameter (123 KB).
	if ( is_string( $size ) ) {

		$custom = strtoupper( substr( $size, -2 ) );
		foreach ( $units as $k => $v ) {
			if ( $custom === $k ) {
				$unit = $k;
				$size = substr( $size, 0, -2 );
			}
		}
	}
	$bytes = floatval( $size ) * pow( 1024, $units[$unit] );
	return $bytes;
}

/**
 * Format filesize
 *
 * This function will return a formatted string containing
 * the filesize and unit.
 *
 * @since  1.0.0
 * @param  mixed $size
 * @return string
 */
function acf_format_filesize( $size = 1 ) {

	$bytes = acf_get_filesize( $size );
	$units = [
		'TB' => 4,
		'GB' => 3,
		'MB' => 2,
		'KB' => 1,
	];

	// Loop through units.
	foreach ( $units as $k => $v ) {

		$result = $bytes / pow( 1024, $v );
		if ( $result >= 1 ) {
			return $result . ' ' . $k;
		}
	}
	return $bytes . ' B';
}

/**
 * Get valid terms
 *
 * This function will replace old terms with new split term IDs.
 *
 * @since  1.0.0
 * @param  mixed $terms
 * @param  string $taxonomy
 * @return array
 */
function acf_get_valid_terms( $terms = false, $taxonomy = 'category' ) {

	$terms = acf_get_array( $terms );
	$terms = array_map( 'intval', $terms );

	if ( ! function_exists( 'wp_get_split_term' ) || empty( $terms ) ) {
		return $terms;
	}

	// Attempt to find new terms.
	foreach ( $terms as $i => $term_id ) {

		$new_term_id = wp_get_split_term( $term_id, $taxonomy );
		if ( $new_term_id ) {
			$terms[ $i ] = $new_term_id;
		}
	}
	return $terms;
}

/**
 * Validate attachment
 *
 * This function will validate an attachment based on
 * a field's resrictions and return an array of errors.
 *
 * @since  1.0.0
 * @param  array $attachment
 * @param  array $field
 * @param  string $context
 * @return array
 */
function acf_validate_attachment( $attachment, $field, $context = 'prepare' ) {

	$errors = [];
	$file   = [
		'type'   => '',
		'width'  => 0,
		'height' => 0,
		'size'   => 0
	];

	if ( $context == 'upload' ) {

		$file['type'] = pathinfo( $attachment['name'], PATHINFO_EXTENSION );
		$file['size'] = filesize( $attachment['tmp_name'] );

		if ( strpos( $attachment['type'], 'image' ) !== false ) {
			$size = getimagesize( $attachment['tmp_name'] );
			$file['width']  = acf_maybe_get( $size, 0 );
			$file['height'] = acf_maybe_get( $size, 1 );
		}
	} elseif ( $context == 'prepare' ) {

		$file['type']   = pathinfo( $attachment['url'], PATHINFO_EXTENSION );
		$file['size']   = acf_maybe_get( $attachment, 'filesizeInBytes', 0 );
		$file['width']  = acf_maybe_get( $attachment, 'width', 0 );
		$file['height'] = acf_maybe_get( $attachment, 'height', 0 );
	} else {

		$file = array_merge( $file, $attachment );
		$file['type'] = pathinfo( $attachment['url'], PATHINFO_EXTENSION );
	}

	if ( $file['width'] || $file['height'] ) {

		$min_width = (int) acf_maybe_get( $field, 'min_width', 0 );
		$max_width = (int) acf_maybe_get( $field, 'max_width', 0 );

		if ( $file['width'] ) {
			if( $min_width && $file['width'] < $min_width ) {

				$errors['min_width'] = sprintf( __( 'Image width must be at least %dpx.', 'acf' ), $min_width );

			} elseif ( $max_width && $file['width'] > $max_width ) {

				$errors['max_width'] = sprintf( __( 'Image width must not exceed %dpx.', 'acf' ), $max_width );
			}
		}

		$min_height = (int) acf_maybe_get( $field, 'min_height', 0 );
		$max_height = (int) acf_maybe_get( $field, 'max_height', 0 );

		if ( $file['height'] ) {
			if ( $min_height && $file['height'] < $min_height ) {

				$errors['min_height'] = sprintf( __( 'Image height must be at least %dpx.', 'acf' ), $min_height );

			}  elseif ( $max_height && $file['height'] > $max_height ) {

				$errors['max_height'] = sprintf( __( 'Image height must not exceed %dpx.', 'acf' ), $max_height );
			}
		}
	}

	if ( $file['size'] ) {

		$min_size = acf_maybe_get( $field, 'min_size', 0 );
		$max_size = acf_maybe_get( $field, 'max_size', 0 );

		if ( $min_size && $file['size'] < acf_get_filesize( $min_size ) ) {

			$errors['min_size'] = sprintf( __( 'File size must be at least %s.', 'acf' ), acf_format_filesize( $min_size ) );

		} elseif ( $max_size && $file['size'] > acf_get_filesize( $max_size ) ) {

			$errors['max_size'] = sprintf( __( 'File size must not exceed %s.', 'acf' ), acf_format_filesize( $max_size ) );
		}
	}

	// File type.
	if ( $file['type'] ) {

		$mime_types   = acf_maybe_get( $field, 'mime_types', '' );
		$file['type'] = strtolower( $file['type'] );
		$mime_types   = strtolower( $mime_types );
		$mime_types   = str_replace( [ ' ', '.' ], '', $mime_types );
		$mime_types   = explode( ',', $mime_types ); // Split pieces.
		$mime_types   = array_filter( $mime_types ); // Remove empty pieces.

		if ( ! empty( $mime_types ) && ! in_array( $file['type'], $mime_types ) ) {

			// Glue together last two types.
			if ( count( $mime_types ) > 1 ) {

				$last1 = array_pop( $mime_types );
				$last2 = array_pop( $mime_types );

				$mime_types[] = $last2 . ' ' . __( 'or', 'acf' ) . ' ' . $last1;
			}
			$errors['mime_types'] = sprintf( __( 'File type must be %s.', 'acf' ), implode( ', ', $mime_types ) );
		}
	}

	$errors = apply_filters( "acf/validate_attachment/type={$field['type']}",	$errors, $file, $attachment, $field, $context );
	$errors = apply_filters( "acf/validate_attachment/name={$field['_name']}", 	$errors, $file, $attachment, $field, $context );
	$errors = apply_filters( "acf/validate_attachment/key={$field['key']}", 	$errors, $file, $attachment, $field, $context );
	$errors = apply_filters( 'acf/validate_attachment', 						$errors, $file, $attachment, $field, $context );

	return $errors;
}

/**
 * Settings uploader
 *
 * @since  1.0.0
 * @param  string $uploader
 * @return string
 */
function _acf_settings_uploader( $uploader ) {

	// If can't upload files.
	if ( ! current_user_can( 'upload_files' ) ) {
		$uploader = 'basic';
	}
	return $uploader;
}
add_filter( 'acf/settings/uploader', '_acf_settings_uploader' );

/**
 * Translate
 *
 * This function will translate a string using the new
 * 'l10n_textdomain' setting.
 * Also works for arrays which is great for fields
 * select -> choices.
 *
 * @since  1.0.0
 * @param  string $string
 * @return string
 */
function acf_translate( $string ) {

	$l10n = acf_get_setting( 'l10n' );
	$textdomain = acf_get_setting( 'l10n_textdomain' );

	if ( ! $l10n ) {
		return $string;
	}

	// Stop if no textdomain.
	if ( ! $textdomain ) {
		return $string;
	}

	// Is array.
	if ( is_array( $string ) ) {
		return array_map( 'acf_translate', $string );
	}

	// Stop if not string.
	if ( ! is_string( $string ) ) {
		return $string;
	}

	// Stop if empty.
	if ( $string === '' ) {
		return $string;
	}

	// Allow for var_export export.
	if ( acf_get_setting( 'l10n_var_export' ) ) {

		// Stop if already translated.
		if ( substr( $string, 0, 7 ) === '!!__(!!' ) {
			return $string;
		}
		return "!!__(!!'" .  $string . "!!', !!'" . $textdomain . "!!')!!";
	}
	return __( $string, $textdomain );
}

/**
 * Maybe add action
 *
 * This function will determine if the action has already run before adding / calling the function.
 *
 * @since  1.0.0
 * @param  string $tag
 * @param  string $function_to_add
 * @param  integer $priority
 * @param  integer $accepted_args
 * @return void
 */
function acf_maybe_add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {

	/**
	 * If action has already run, execute it. If currently doing action,
	 * allow $tag to be added as per usual to allow $priority ordering
	 * needed for third party asset compatibility.
	 */
	if ( did_action( $tag ) && ! doing_action( $tag ) ) {
		call_user_func( $function_to_add );

	} else {
		add_action( $tag, $function_to_add, $priority, $accepted_args );
	}
}

/**
 * Is row collapsed
 *
 * This function will return true if the field's row is collapsed.
 *
 * @since  1.0.0
 * @param  string $field_key
 * @param  integer $row_index
 * @return boolean
 */
function acf_is_row_collapsed( $field_key = '', $row_index = 0 ) {

	$collapsed = acf_get_user_setting( 'collapsed_' . $field_key, '' );

	// Cookie fallback ( version < 5.3.2 ).
	if ( $collapsed === '' ) {

		$collapsed = acf_extract_var( $_COOKIE, "acf_collapsed_{$field_key}", '' );
		$collapsed = str_replace( '|', ',', $collapsed );

		acf_update_user_setting( 'collapsed_' . $field_key, $collapsed );
	}
	$collapsed = explode( ',', $collapsed );
	$collapsed = array_filter( $collapsed, 'is_numeric' );

	return in_array( $row_index, $collapsed );
}

/**
 * Get attachment image
 *
 * @since  1.0.0
 * @param  integer $attachment_id
 * @param  string $size
 * @return string
 */
function acf_get_attachment_image( $attachment_id = 0, $size = 'thumbnail' ) {

	$url = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
	$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

	// Stop if no URL.
	if ( ! $url ) {
		return '';
	}
	$value = '<img src="' . $url . '" alt="' . $alt . '" />';
}

/**
 * Get post thumbnail
 *
 * This function will return a thumbnail image URL for a given post.
 *
 * @since  1.0.0
 * @param  object $post
 * @param  string $size
 * @return string
 */
function acf_get_post_thumbnail( $post = null, $size = 'thumbnail' ) {

	$data = [
		'url'  => '',
		'type' => '',
		'html' => ''
	];
	$post = get_post( $post );

	// Stop if no post.
	if ( ! $post ) {
		return $data;
	}

	$thumb_id  = $post->ID;
	$mime_type = acf_maybe_get( explode( '/', $post->post_mime_type ), 0 );

	if ( 'attachment' === $post->post_type ) {
		if ( 'audio' === $mime_type || 'video' === $mime_type ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
		}
	} else {
		$thumb_id = get_post_thumbnail_id( $post->ID );
	}

	$data['url'] = wp_get_attachment_image_src( $thumb_id, $size );
	$data['url'] = acf_maybe_get( $data['url'], 0 );

	if ( ! $data['url'] && 'attachment' === $post->post_type ) {

		$data['url']  = wp_mime_type_icon( $post->ID );
		$data['type'] = 'icon';
	}

	$data['html'] = '<img src="' . $data['url'] . '" alt="" />';

	return $data;
}

/**
 * Get browser
 *
 * Returns the name of the current browser.
 *
 * @since  1.0.0
 * @return string
 */
function acf_get_browser() {

	// Check server var.
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$agent = $_SERVER['HTTP_USER_AGENT'];

		// Loop over search terms.
		$browsers = [
			'Firefox' => 'firefox',
			'Trident' => 'msie',
			'MSIE'    => 'msie',
			'Edge'    => 'edge',
			'Chrome'  => 'chrome',
			'Safari'  => 'safari',
		];
		foreach ( $browsers as $k => $v ) {
			if ( strpos( $agent, $k ) !== false ) {
				return $v;
			}
		}
	}
	return '';
}

/**
 * Is AJAX
 *
 * This function will reutrn true if performing a wp AJAX call.
 *
 * @since  1.0.0
 * @param  string $action
 * @return boolean
 */
function acf_is_ajax( $action = '' ) {

	$is_ajax = false;
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$is_ajax = true;
	}

	// Check $action.
	if ( $action && acf_maybe_get( $_POST, 'action' ) !== $action ) {
		$is_ajax = false;
	}
	return $is_ajax;
}

/**
 * Format date
 *
 * This function will accept a date value and return it in a formatted string.
 *
 * @since  1.0.0
 * @param  string $value
 * @param  string $format
 * @return string
 */
function acf_format_date( $value, $format ) {

	// Stop if no value.
	if ( ! $value ) {
		return $value;
	}

	$unixtimestamp = 0;

	// Numeric (either unix or YYYYMMDD).
	if ( is_numeric( $value ) && strlen( $value ) !== 8 ) {
		$unixtimestamp = $value;
	} else {
		$unixtimestamp = strtotime( $value );
	}
	return date_i18n( $format, $unixtimestamp );
}

/**
 * Clear log
 *
 * Deletes the debug.log file.
 *
 * @since  1.0.0
 * @return void
 */
function acf_clear_log() {
	unlink( WP_CONTENT_DIR . '/debug.log' );
}

/**
 * Log
 *
 * @since  1.0.0
 * @return void
 */
function acf_log() {

	$args = func_get_args();

	foreach( $args as $i => $arg ) {

		if ( is_array( $arg ) || is_object( $arg ) ) {
			$arg = print_r( $arg, true );
		} elseif ( is_bool( $arg ) ) {
			$arg = 'bool(' . ( $arg ? 'true' : 'false' ) . ')';
		}
		$args[ $i ] = $arg;
	}
	error_log( implode( ' ', $args ) );
}

/**
 * Dev log
 *
 * Used to log variables only if ACF_DEV is defined
 *
 * @since  1.0.0
 * @return void
 */
function acf_dev_log() {
	if ( defined( 'ACF_DEV' ) && ACF_DEV ) {
		call_user_func_array( 'acf_log', func_get_args() );
	}
}

/**
 * Doing
 *
 * This function will tell ACF what task it is doing.
 *
 * @since  1.0.0
 * @param  string $event
 * @param  string $context
 * @return void
 */
function acf_doing( $event = '', $context = '' ) {
	acf_update_setting( 'doing', $event );
	acf_update_setting( 'doing_context', $context );
}

/**
 * Is doing
 *
 * This function can be used to state what ACF is doing, or to check.
 *
 * @since  1.0.0
 * @param  string $event
 * @param  string $context
 * @return boolean
 */
function acf_is_doing( $event = '', $context = '' ) {

	$doing = false;
	if ( acf_get_setting( 'doing' ) === $event ) {
		$doing = true;
	}

	// Context.
	if ( $context && acf_get_setting( 'doing_context' ) !== $context ) {
		$doing = false;
	}
	return $doing;
}

/**
 * Is plugin active
 *
 * This function will return true if the ACF plugin is active.
 * May be included within a theme or other plugin.
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_plugin_active() {

	$basename = acf_get_setting( 'basename' );

	// Ensure is_plugin_active() exists (not on frontend).
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	return is_plugin_active( $basename );
}

/**
 * Send AJAX results
 *
 * This function will print JSON data for a Select2 AJAX query
 *
 * @since  1.0.0
 * @param  array $response
 * @return void
 */
function acf_send_ajax_results( $response ) {

	$response = wp_parse_args( $response, [
		'results' => [],
		'more'    => false,
		'limit'   => 0
	] );

	if ( $response['limit'] && $response['results'] ) {

		$total = 0;
		foreach ( $response['results'] as $result ) {
			$total++;

			if ( ! empty( $result['children'] ) ) {
				$total += count( $result['children'] );
			}
		}

		if ( $total >= $response['limit'] ) {
			$response['more'] = true;
		}
	}
	wp_send_json( $response );
}

/**
 * Is sequential array
 *
 * This function will return true if the array contains only numeric keys.
 *
 * @link http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 *
 * @since  1.0.0
 * @param  array $array
 * @return boolean
 */
function acf_is_sequential_array( $array ) {

	// Stop if not array.
	if ( ! is_array( $array ) ) {
		return false;
	}

	foreach ( $array as $key => $value ) {

		// Stop if is string.
		if ( is_string( $key ) ) {
			return false;
		}
	}
	return true;
}

/**
 * Is associative array
 *
 * This function will return true if the array contains
 * one or more string keys.
 *
 * @link http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 *
 * @since  1.0.0
 * @param  array $array
 * @return boolean
 */
function acf_is_associative_array( $array ) {

	// Stop if not array.
	if ( ! is_array( $array ) ) {
		return false;
	}

	foreach( $array as $key => $value ) {

		// Stop if is string.
		if ( is_string( $key ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Add array key prefix
 *
 * This function will add a prefix to all array keys.
 * Useful to preserve numeric keys when performing array_multisort.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  string $prefix
 * @return array
 */
function acf_add_array_key_prefix( $array, $prefix ) {

	$array2 = [];

	foreach( $array as $k => $v ) {
		$k2 = $prefix . $k;
	    $array2[ $k2 ] = $v;
	}
	return $array2;
}

/**
 * Remove array key prefix
 *
 * This function will remove a prefix to all array keys.
 * Useful to preserve numeric keys when performing array_multisort.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  string $prefix
 * @return array
 */
function acf_remove_array_key_prefix( $array, $prefix ) {

	$array2 = [];
	$l = strlen( $prefix );

	foreach ( $array as $k => $v ) {
		$k2 = ( substr( $k, 0, $l ) === $prefix ) ? substr( $k, $l ) : $k;
	    $array2[ $k2 ] = $v;
	}
	return $array2;
}

/**
 * Strip URL protocol
 *
 * This function will remove the proticol from a URL.
 * Used to allow licences to remain active if a site
 * is switched to https.
 *
 * @since  1.0.0
 * @param  string $url
 * @return string
 */
function acf_strip_protocol( $url ) {
	return str_replace( [ 'http://','https://' ], '', $url );
}

/**
 * Connect attachment to post
 *
 * This function will connect an attacment (image, etc.) to the post
 * Used to connect attachements uploaded directly to media that
 * have not been attaced to a post.
 *
 * @since  1.0.0
 * @param  integer $attachment_id The attachment ID.
 * @param  integer $post_id The post ID.
 * @return boolean True if attachment was connected.
 */
function acf_connect_attachment_to_post( $attachment_id = 0, $post_id = 0 ) {

	// Stop if $attachment_id is not valid.
	if ( ! $attachment_id || ! is_numeric( $attachment_id ) ) {
		return false;
	}

	// Stop if $post_id is not valid.
	if ( ! $post_id || ! is_numeric( $post_id ) ) {
		return false;
	}

	if ( ! apply_filters( 'acf/connect_attachment_to_post', true, $attachment_id, $post_id ) ) {
		return false;
	}

	$post = get_post( $attachment_id );
	if ( $post && 'attachment' == $post->post_type && $post->post_parent == 0 ) {
		wp_update_post( [ 'ID' => $post->ID, 'post_parent' => $post_id ] );
		return true;
	}
	return true;
}

/**
 * Encrypt
 *
 * This function will encrypt a string using PHP.
 *
 * @link https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/
 *
 * @since  1.0.0
 * @param  string $data
 * @return string
 */
function acf_encrypt( $data = '' ) {

	// Stop if no encrypt function.
	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return base64_encode( $data );
	}

	$key = wp_hash( 'acf_encrypt' );
    $iv  = openssl_random_pseudo_bytes( openssl_cipher_iv_length('aes-256-cbc' ) );

    /**
	 * Encrypt the data using AES 256 encryption in CBC mode using
	 * our encryption key and initialization vector.
	 */
    $encrypted_data = openssl_encrypt( $data, 'aes-256-cbc', $key, 0, $iv );

    /**
	 * The $iv is just as important as the key for decrypting, so save it with
	 * our encrypted data using a unique separator (::).
	 */
    return base64_encode( $encrypted_data . '::' . $iv );
}

/**
 * Decrypt
 *
 * This function will decrypt an encrypted string using PHP
 *
 * @link https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/
 *
 * @since  1.0.0
 * @param  string $data
 * @return string
 */
function acf_decrypt( $data = '' ) {

	// Stop if no decrypt function.
	if ( ! function_exists( 'openssl_decrypt' ) ) {
		return base64_decode( $data );
	}

	// Generate a key.
	$key = wp_hash( 'acf_encrypt' );

    // Split the encrypted data from our IV, the unique separator used was "::".
    list( $encrypted_data, $iv ) = explode( '::', base64_decode( $data ), 2 );

    return openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );
}

/**
 * Parse Markdown
 *
 * A very basic regex-based Markdown parser function.
 * @link https://gist.github.com/jbroadway/2836900
 *
 * @since  1.0.0
 * @param  string $text
 * @return string
 */
function acf_parse_markdown( $text = '' ) {

	$text  = trim( $text );
	$rules = [
		'/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href="$2">$1</a>',
		'/=== (.+?) ===/'  => '<h2>$1</h2>',
		'/== (.+?) ==/'    => '<h3>$1</h3>',
		'/= (.+?) =/'      => '<h4>$1</h4>',
		'/(\*\*)(.*?)\1/'  => '<strong>$2</strong>',
		'/(\*)(.*?)\1/'    => '<em>$2</em>',
		'/`(.*?)`/'        => '<code>$1</code>',
		'/\n\*(.*)/'       => "\n<ul>\n\t<li>$1</li>\n</ul>",
		'/\n[0-9]+\.(.*)/' => "\n<ol>\n\t<li>$1</li>\n</ol>",
		'/<\/ul>\s?<ul>/'  => '',
		'/<\/ol>\s?<ol>/'  => ''
	];
	foreach ( $rules as $k => $v ) {
		$text = preg_replace( $k, $v, $text );
	}

	$text = wpautop( $text );
	return $text;
}

/**
 * Get sites
 *
 * Returns an array of sites for a network.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_sites() {

	$results = [];
	$sites   = get_sites( [ 'number' => 0 ] );
	if ( $sites ) {
		foreach ( $sites as $site ) {
	        $results[] = get_site( $site )->to_array();
	    }
	}
	return $results;
}

/**
 * Convert rules to groups
 *
 * Converts an array of rules from ACF4 to an array of groups for ACF5.
 *
 * @since  1.0.0
 * @param  array $rules An array of rules.
 * @param  string $anyorall The anyorall setting used in ACF4.
 *                          Defaults to 'any'.
 * @return array
 */
function acf_convert_rules_to_groups( $rules, $anyorall = 'any' ) {

	$groups = [];
	$index  = 0;

	foreach ( $rules as $rule ) {

		$group = acf_extract_var( $rule, 'group_no' );
		$order = acf_extract_var( $rule, 'order_no' );

		// Calculate group if not defined.
		if ( $group === null ) {
			$group = $index;

			// Use $anyorall to determine if a new group is needed.
			if ( $anyorall == 'any' ) {
				$index++;
			}
		}

		// Calculate order if not defined.
		if ( $order === null ) {
			$order = isset( $groups[ $group ] ) ? count( $groups[ $group ] ) : 0;
		}

		// Append to group.
		$groups[ $group ][ $order ] = $rule;

		// Sort groups.
		ksort( $groups[ $group ] );
	}

	// Sort groups.
	ksort( $groups );
	return $groups;
}

/**
 * Register AJAX
 *
 * Registers an AJAX callback.
 *
 * @since  1.0.0
 * @param  string $name The AJAX action name.
 * @param  array $callback The callback function or array.
 * @param  boolean $public Whether to allow access to non logged in users.
 * @return void
 */
function acf_register_ajax( $name = '', $callback = false, $public = false ) {

	$action = "acf/ajax/$name";

	// Add action for logged-in users.
	add_action( "wp_ajax_$action", $callback );

	// Add action for non logged-in users.
	if ( $public ) {
		add_action( "wp_ajax_nopriv_$action", $callback );
	}
}

/**
* String to camel case
*
* Converts a string into camelCase.
*
* @link https://stackoverflow.com/questions/31274782/convert-array-keys-from-underscore-case-to-camelcase-recursively
*
* @since  1.0.0
* @param  string $string The string ot convert.
* @return string
*/
function acf_str_camel_case( $string = '' ) {
	return lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $string ) ) ) );
}

/**
 * Array to camel case
 *
 * Converts all aray keys to camelCase.
 *
 * @since  1.0.0
 * @param  array $array The array to convert.
 * @return array
 */
function acf_array_camel_case( $array = [] ) {

	$array2 = [];
	foreach( $array as $k => $v ) {
		$array2[ acf_str_camel_case($k) ] = $v;
	}
	return $array2;
}

/**
 * Is block editor
 *
 * Returns true if the current screen is using the block editor.
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_block_editor() {

	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen && method_exists( $screen, 'is_block_editor' ) ) {
			return $screen->is_block_editor();
		}
	}

	// Check if a block is currently fetched (edit mode).
	if ( 'acf/ajax/fetch-block' === acf_maybe_get_POST( 'action' ) ) {
		return true;
	}
	return false;
}
