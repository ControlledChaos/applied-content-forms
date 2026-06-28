<?php
/**
 * Helper functions
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
 * Is empty
 *
 * Returns true if the value provided is considered "empty".
 * Allows numbers such as 0.
 *
 * @since  1.0.0
 * @param  mixed $var The value to check.
 * @return boolean
 */
function acf_is_empty( $var ) {
	return ( ! $var && ! is_numeric( $var ) );
}

/**
 * Not empty
 *
 * Returns true if the value provided is considered "not empty".
 * Allows numbers such as 0.
 *
 * @since  1.0.0
 * @param  mixed $var The value to check.
 * @return boolean
 */
function acf_not_empty( $var ) {
	return ( $var || is_numeric( $var ) );
}

/**
 * Unique ID
 *
 * Returns a unique numeric based id.
 *
 * @since  1.0.0
 * @param  string $prefix The id prefix. Defaults to 'acf'.
 * @global integer $acf_uniqid
 * @return string
 */
function acf_uniqid( $prefix = 'acf' ) {

	// Access global variables.
	global $acf_uniqid;

	if ( ! isset( $acf_uniqid ) ) {
		$acf_uniqid = 1;
	}
	return $prefix . '-' . $acf_uniqid++;
}

/**
 * Merge attributes
 *
 * Merges together two arrays but with extra functionality to append class names.
 *
 * @since  1.0.0
 * @param  array $array1 An array of attributes.
 * @param  array $array2 An array of attributes.
 * @return array
 */
function acf_merge_attributes( $array1, $array2 ) {

	// Merge together attributes.
	$array3 = array_merge( $array1, $array2 );

	// Append together special attributes.
	foreach ( [ 'class', 'style' ] as $key ) {
		if ( isset( $array1[$key] ) && isset( $array2[$key] ) ) {
			$array3[$key] = trim( $array1[$key] ) . ' ' . trim( $array2[$key] );
		}
	}
	return $array3;
}

/**
 * Cache key
 *
 * Returns a filtered cache key.
 *
 * @since  1.0.0
 * @param  string $key The cache key.
 * @return string
 */
function acf_cache_key( $key = '' ) {
	return apply_filters( 'acf/get_cache_key', $key, $key );
}

/**
 * Request_ args
 *
 * Returns an array of $_REQUEST values using the provided defaults.
 *
 * @since  1.0.0
 * @param  array $args An array of args.
 * @return array
 */
function acf_request_args( $args = [] ) {
	foreach ( $args as $k => $v ) {
		$args[ $k ] = isset( $_REQUEST[ $k ] ) ? $_REQUEST[ $k ] : $args[ $k ];
	}
	return $args;
}

/**
 * Returns a single $_REQUEST arg with fallback.
 *
 * @since  1.0.0
 * @param  string $key The property name.
 * @param  mixed $default The default value to fallback to.
 * @return mixed
 */
function acf_request_arg( $name = '', $default = null ) {
	return isset( $_REQUEST[ $name ] ) ? $_REQUEST[ $name ] : $default;
}

// Register store.
acf_register_store( 'filters' );

/**
 * Enable filter
 *
 * Enables a filter with the given name.
 *
 * @since  1.0.0
 * @param  string name The modifier name.
 * @return void
 */
function acf_enable_filter( $name = '' ) {
	acf_get_store( 'filters' )->set( $name, true );
}

/**
 * Disable filter
 *
 * Disables a filter with the given name.
 *
 * @since  1.0.0
 * @param  string name The modifier name.
 * @return void
 */
function acf_disable_filter( $name = '' ) {
	acf_get_store( 'filters' )->set( $name, false );
}

/**
 * Is filter enabled
 *
 * Returns the state of a filter for the given name.
 *
 * @since  1.0.0
 * @param  string name The modifier name.
 * @return array
 */
function acf_is_filter_enabled( $name = '' ) {
	return acf_get_store( 'filters' )->get( $name );
}

/**
 * Get filters
 *
 * Returns an array of filters in their current state.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_filters() {
	return acf_get_store( 'filters' )->get();
}

/**
 * Set filters
 *
 * Sets an array of filter states.
 *
 * @since  1.0.0
 * @param  array $filters An Array of modifiers
 * @return array
 */
function acf_set_filters( $filters = [] ) {
	acf_get_store( 'filters' )->set( $filters );
}

/**
 * Disable filters
 *
 * Disables all filters and returns the previous state.
 *
 * @since  1.0.0
 * @return array
 */
function acf_disable_filters() {

	// Get state.
	$prev_state = acf_get_filters();

	// Set all modifiers as false.
	acf_set_filters( array_map( '__return_false', $prev_state ) );

	// Return prev state.
	return $prev_state;
}

/**
 * Enable filters
 *
 * Enables all or an array of specific filters and returns the previous state.
 *
 * @since  1.0.0
 * @param  array $filters An Array of modifiers
 * @return array
 */
function acf_enable_filters( $filters = [] ) {

	// Get state.
	$prev_state = acf_get_filters();

	// Allow specific filters to be enabled.
	if ( $filters ) {
		acf_set_filters( $filters );

	// Set all modifiers as true.
	} else {
		acf_set_filters( array_map( '__return_true', $prev_state ) );
	}
	return $prev_state;
}

/**
 * ID value
 *
 * Parses the provided value for an ID.
 *
 * @since  1.0.0
 * @param  mixed $value A value to parse.
 * @return integer
 */
function acf_idval( $value ) {

	// Check if value is numeric.
	if ( is_numeric( $value ) ) {
		return (int) $value;

	// Check if value is array.
	} elseif ( is_array( $value ) ) {
		return (int) isset( $value['ID'] ) ? $value['ID'] : 0;

	// Check if value is object.
	} elseif ( is_object( $value ) ) {
		return (int) isset( $value->ID ) ? $value->ID : 0;
	}
	return 0;
}

/**
 * Maybe ID value
 *
 * Checks value for potential id value.
 *
 * @since  1.0.0
 * @param  mixed $value A value to parse.
 * @return mixed
 */
function acf_maybe_idval( $value ) {
	if ( $id = acf_idval( $value ) ) {
		return $id;
	}
	return $value;
}

/**
 * Number value
 *
 * Casts the provided value as either an integer or
 * float using a simple hack.
 *
 * @since  1.0.0
 * @param  mixed $value A value to parse.
 * @return mixed
 */
function acf_numval( $value ) {
	return ( intval( $value ) == floatval( $value ) ) ? intval( $value ) : floatval( $value );
}

/**
 * IDify
 *
 * Returns an id attribute friendly string.
 *
 * @since  1.0.0
 * @param  string $str The string to convert.
 * @return string
 */
function acf_idify( $str = '' ) {
	return str_replace( [ '][', '[', ']' ], [ '-', '-', '' ], strtolower( $str ) );
}

/**
 * Slugify
 *
 * Returns a slug friendly string.
 *
 * @since  1.0.0
 * @param  string $str The string to convert.
 * @param  string $glue The glue between each slug piece.
 * @return string
 */
function acf_slugify( $str = '', $glue = '-' ) {
	return str_replace( [ '_', '-', '/', ' ' ], $glue, strtolower( $str ) );
}

/**
 * Punctify
 *
 * Returns a string with correct full stop punctuation.
 *
 * @since  1.0.0
 * @param  string $str The string to format.
 * @return string
 */
function acf_punctify( $str = '' ) {
	if ( substr( trim( strip_tags( $str ) ), -1 ) !== '.' ) {
		return trim( $str ) . '.';
	}
	return trim( $str );
}

/**
 * Did event
 *
 * Returns true if ACF already did an event.
 *
 * @since  1.0.0
 * @param  string $name The name of the event.
 * @return boolean
 */
function acf_did( $name ) {

	// Return true if already did the event (preventing event).
	if ( acf_get_data( "acf_did_$name" ) ) {
		return true;

	// Otherwise, update store and return false (allowing event).
	} else {
		acf_set_data( "acf_did_$name", true );
		return false;
	}
}

/**
 * String length
 *
 * Returns the length of a string that has been submitted via $_POST.
 *
 * Uses the following process:
 * 1. Unslash the string because posted values will be slashed.
 * 2. Decode special characters because wp_kses() will normalize entities.
 * 3. Treat line-breaks as a single character instead of two.
 * 4. Use mb_strlen() to accommodate special characters.
 *
 * @since  1.0.0
 * @param  string $str The string to review.
 * @return integer
 */
function acf_strlen( $str ) {
	return mb_strlen( str_replace( "\r\n", "\n", wp_specialchars_decode( wp_unslash( $str ) ) ) );
}

/**
 * With default
 *
 * Returns a value with default fallback.
 *
 * @since  1.0.0
 * @param  mixed $value The value.
 * @param  mixed $default_value The default value.
 * @return mixed
 */
function acf_with_default( $value, $default_value ) {
	return $value ? $value : $default_value;
}

/**
 * Doing action
 *
 * Returns the current priority of a running action.
 *
 * @since  1.0.0
 * @param  string $action The action name.
 * @global array $wp_filter
 * @return mixed
 */
function acf_doing_action( $action ) {

	// Access global variables.
	global $wp_filter;

	if ( isset( $wp_filter[ $action ] ) ) {
		return $wp_filter[ $action ]->current_priority();
	}
	return false;
}

/**
 * Get current URL
 *
 * @since  1.0.0
 * @return string
 */
function acf_get_current_url() {

	// Ensure props exist to avoid PHP Notice during CLI commands.
	if ( isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) {
		return ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	return '';
}

/**
 * Is JSON
 *
 * Check if the string is a JSON input
 * https://stackoverflow.com/a/6041773
 *
 * @since  1.0.0
 * @param  string $string
 * @return boolean
 */
function acf_is_json( $string ) {

	// In case string = 1 or is not string.
	if ( is_numeric( $string ) || ! is_string( $string ) ) {
		return false;
	}
	json_decode( $string );

	return json_last_error() == JSON_ERROR_NONE;
}

/**
 * Array keys recursive
 *
 * @since  1.0.0
 * @param  array $array
 * @return array
 */
function acf_array_keys_r( $array ) {

	$keys = array_keys( $array );
	foreach ( $array as $i ) {
		if ( ! is_array( $i ) ) {
			continue;
		}
		$keys = array_merge( $keys, acf_array_keys_r( $i ) );
	}
	return $keys;
}

/**
 * Starts with
 *
 * Checks if a strings starts with something.
 *
 * @since  1.0.0
 * @param  string $haystack
 * @param  string $needle
 * @return boolean
 */
function acf_starts_with( $haystack, $needle ) {
	$length = strlen( $needle );
	return ( substr( $haystack, 0, $length ) === $needle );
}

/**
 * Ends with
 *
 * Checks if a strings ends with something.
 *
 * @since  1.0.0
 * @param  string $haystack
 * @param  string $needle
 * @return boolean
 */
function acf_ends_with( $haystack, $needle ) {

	$length = strlen( $needle );
	if ( $length == 0 ) {
		return true;
	}
	return ( substr( $haystack, -$length ) === $needle );
}

/**
 * Array insert before
 *
 * Insert data before a specific array key
 *
 * @since  1.0.0
 * @param  string $key
 * @param  array $array
 * @param  mixed $new_key
 * @param  mixed $new_value
 * @return array
 */
function acf_array_insert_before( $key, array &$array, $new_key, $new_value ) {

	if ( ! array_key_exists( $key, $array ) ) {
		return $array;
	}
	$new = [];

	foreach ( $array as $k => $value ) {
		if ( $k === $key ) {
			$new[$new_key] = $new_value;
		}
		$new[$k] = $value;
	}
	return $new;
}

/**
 * Array insert after
 *
 * Insert data after a specific array key
 *
 * @since  1.0.0
 * @param  string $key
 * @param  array $array
 * @param  mixed $new_key
 * @param  mixed $new_value
 * @return array
 */
function acf_array_insert_after( $key, array &$array, $new_key, $new_value ) {

	if ( ! array_key_exists( $key, $array ) ) {
		return $array;
	}
	$new = [];

	foreach ( $array as $k => $value ) {
		$new[$k] = $value;
		if ( $k === $key ) {
			$new[$new_key] = $new_value;
		}
	}
	return $new;
}

/**
 * Array move
 *
 * Moves an array key from position $a to $b.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  integer $a
 * @param  integer $b
 * @return void
 */
function acf_array_move( &$array, $a, $b ) {
	$out = array_splice( $array, $a, 1 );
	array_splice( $array, $b, 0, $out );
}

/**
 * Array to string
 *
 * Convert an array to a string.
 *
 * @since  1.0.0
 * @param  array $array
 * @return mixed
 */
function acf_array_to_string( $array = [] ) {

	if ( ! is_array( $array ) ) {
		return $array;
	}

	if ( empty( $array ) ) {
		return false;
	}

	if ( acf_is_sequential_array( $array ) ) {
		foreach ( $array as $k => $v ) {
			if ( ! is_string( $v ) ) {
				continue;
			}
			return $v;
		}
	} elseif ( acf_is_associative_array( $array ) ) {
		foreach ( $array as $k => $v ) {
			if ( ! is_string( $v ) ) {
				continue;
			}
			return $v;
		}
	}
	return false;
}

/**
 * Unset
 *
 * Safely remove an array key.
 *
 * @since  1.0.0
 * @param  array $array
 * @param  string $key
 * @return void
 */
function acf_unset( &$array, $key ) {
	if ( isset( $array[$key] ) ) {
		unset( $array[$key] );
	}
}

/**
 * Get IP
 *
 * @since  1.0.0
 * @return mixed
 */
function acf_get_ip() {

	$ip = false;
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {

		$ip = filter_var( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ), FILTER_VALIDATE_IP );

	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

		// Can include more than 1 IP, first is the public one.
		$ips = explode( ',', wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		if ( is_array( $ips ) ) {
			$ip = filter_var( $ips[0], FILTER_VALIDATE_IP );
		}
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
	}

	if ( $ip !== false ) {
		$ip = $ip;
	} else {
		$ip = '127.0.0.1';
	}

	// Fix potential CSV return.
	$ip_array = explode( ',', $ip );
	$ip_array = array_map( 'trim', $ip_array );

	return $ip_array[0];
}

/**
 * Number suffix
 *
 * Adds 1"st", 2"nd", 3"rd" to number.
 *
 * @since  1.0.0
 * @param  integer $num
 * @return mixed
 */
function acf_number_suffix( $num ) {

	// English locale options.
	$locale = [
		'en',
		'en_AU',
		'en_CA',
		'en_GB',
		'en_NZ',
		'en_US',
		'en_ZA'
	];

	// Only apply suffix to English.
	if ( ! in_array( get_locale(), $locale ) ) {
		return $num;
	}

	if ( ! in_array( ( $num % 100 ), [ 11, 12, 13 ] ) ) {
		switch ( $num % 10 ) {
			case 1:  return $num . 'st';
			case 2:  return $num . 'nd';
			case 3:  return $num . 'rd';
		}
	}
	return $num . 'th';
}

/**
 * Is dev
 *
 * Checks if the developer mode is enabled.
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_dev() {
	return acf_get_setting( 'dev_mode', false ) || ( defined( 'ACF_DEV' ) && ACF_DEV );
}

/**
 * Is super dev
 *
 * From forked plugin. Not used.
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_super_dev() {
	return acf_get_setting( 'acf/super_dev', false ) || ( defined( 'ACF_super_dev' ) && ACF_super_dev );
}

/**
 * Is reserved post type
 *
 * @since  1.0.0
 * @param  string $post_type
 * @return boolean
 */
function acf_is_post_type_reserved( $post_type ) {
	$reserved = acf_get_setting( 'reserved_post_types', [] );
	return in_array( $post_type, $reserved );
}

/**
 * Is reserved post type in dev mode
 *
 * @since  1.0.0
 * @param  string $post_type
 * @return boolean
 */
function acf_is_post_type_reserved_dev( $post_type ) {
	$reserved = acf_get_setting( 'reserved_post_types', [] );
	return ! acf_is_super_dev() && in_array( $post_type, $reserved );
}

/**
 * Is reserved taxonomy
 *
 * @since  1.0.0
 * @param  string $taxonomy
 * @return boolean
 */
function acf_is_taxonomy_reserved( $taxonomy ) {
	$reserved = acf_get_setting( 'reserved_taxonomies', [] );
	return in_array( $taxonomy, $reserved );
}

/**
 * Is reserved taxonomy in dev mode
 *
 * @since  1.0.0
 * @param  string $taxonomy
 * @return boolean
 */
function acf_is_taxonomy_reserved_dev( $taxonomy ) {
	$reserved = acf_get_setting( 'reserved_taxonomies', [] );
	return ! acf_is_super_dev() && in_array( $taxonomy, $reserved );
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
