<?php
/**
 * General functions
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
	return ! acf_is_dev() && in_array( $post_type, $reserved );
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
	return ! acf_is_dev() && in_array( $taxonomy, $reserved );
}

/**
 * Dashicons icons
 *
 * An array of all CSS classes for the Dashicons
 * icon font and associated icon name.
 *
 * @since  1.0.0
 * @return array
 */
function acf_dashicon_icons() {

	return [
		'## suggested'  => '##' . __( 'General', 'acf' ),
		'admin-post'    => __( 'Pin 1', 'acf' ),
		'sticky'        => __( 'Pin 2', 'acf' ),
		'edit'          => __( 'Pencil', 'acf' ),
		'edit-large'    => __( 'Pencil Large', 'acf' ),
		'edit-page'     => __( 'Edit Page', 'acf' ),
		'admin-page'    => __( 'Pages', 'acf' ),
		'text-page'     => __( 'Text on Page', 'acf' ),
		'admin-generic' => __( 'Gear', 'acf' ),

		'## admin'         => '##' . __( 'CMS Admin', 'acf' ),
		'admin-appearance' => __( 'Appearance', 'acf' ),
		'admin-collapse'   => __( 'Collapse', 'acf' ),
		'admin-comments'   => __( 'Comments', 'acf' ),
		'admin-customizer' => __( 'Customizer', 'acf' ),
		'dashboard'        => __( 'Dashboard', 'acf' ),
		'filter'           => __( 'Filter', 'acf' ),
		'admin-home'       => __( 'Home', 'acf' ),
		'admin-links'      => __( 'Links', 'acf' ),
		'admin-media'      => __( 'Media', 'acf' ),
		'menu'             => __( 'Menu 1', 'acf' ),
		'menu-alt'         => __( 'Menu 2', 'acf' ),
		'menu-alt2'        => __( 'Menu 3', 'acf' ),
		'menu-alt3'        => __( 'Menu 4', 'acf' ),
		'admin-multisite'  => __( 'Multisite', 'acf' ),
		'admin-network'    => __( 'Network', 'acf' ),
		'admin-plugins'    => __( 'Plugins', 'acf' ),
		'plugins-checked'  => __( 'Plugins Checked', 'acf' ),
		'admin-settings'   => __( 'Settings', 'acf' ),
		'admin-site'       => __( 'Site 1', 'acf' ),
		'admin-site-alt'   => __( 'Site 2', 'acf' ),
		'admin-site-alt2'  => __( 'Site 3', 'acf' ),
		'admin-site-alt3'  => __( 'Site 4', 'acf' ),
		'admin-tools'      => __( 'Tools', 'acf' ),
		'admin-users'      => __( 'Users', 'acf' ),
		'welcome-add-page'      => __( 'Welcome Add Page', 'acf' ),
		'welcome-comments'      => __( 'Welcome Comments', 'acf' ),
		'welcome-edit-page'     => __( 'Welcome Edit Page', 'acf' ),
		'welcome-learn-more'    => __( 'Welcome Learn more', 'acf' ),
		'welcome-view-site'     => __( 'Welcome View Site', 'acf' ),
		'welcome-widgets-menus' => __( 'Welcome Widgets Menus', 'acf' ),
		'welcome-write-blog'    => __( 'Welcome Write Blog', 'acf' ),

		'## media'   => '##' . __( 'Media', 'acf' ),
		'book'       => __( 'Book 1', 'acf' ),
		'book-alt'   => __( 'Book 2', 'acf' ),
		'camera'     => __( 'Camera 1', 'acf' ),
		'camera-alt' => __( 'Camera 2', 'acf' ),
		'controls-back'        => __( 'Controls: Back', 'acf' ),
		'controls-forward'     => __( 'Controls: Forward', 'acf' ),
		'controls-pause'       => __( 'Controls: Pause', 'acf' ),
		'controls-play'        => __( 'Controls: Play', 'acf' ),
		'controls-repeat'      => __( 'Controls: Repeat', 'acf' ),
		'controls-skipback'    => __( 'Controls: Skip Back', 'acf' ),
		'controls-skipforward' => __( 'Controls: Skip Forward', 'acf' ),
		'controls-volumeoff'   => __( 'Controls: Volume Off', 'acf' ),
		'controls-volumeon'    => __( 'Controls: Volume On', 'acf' ),
		'images-alt'  => __( 'Images 1', 'acf' ),
		'images-alt2' => __( 'Images 2', 'acf' ),
		'megaphone'   => __( 'Megaphone', 'acf' ),
		'microphone'  => __( 'Microphone', 'acf' ),
		'playlist-audio' => __( 'Playlist: Audio', 'acf' ),
		'playlist-video' => __( 'Playlist: Video', 'acf' ),
		'slides'      => __( 'Slides', 'acf' ),
		'video-alt'   => __( 'Video 1', 'acf' ),
		'video-alt2'  => __( 'Video 2', 'acf' ),
		'video-alt3'  => __( 'Video 3', 'acf' ),

		'## notifications' => '##' . __( 'Notifications', 'acf' ),
		'bell'        => __( 'Bell', 'acf' ),
		'dismiss'     => __( 'Dismiss', 'acf' ),
		'flag'        => __( 'Flag', 'acf' ),
		'minus'       => __( 'Minus', 'acf' ),
		'marker'      => __( 'Marker', 'acf' ),
		'no'          => __( 'No 1', 'acf' ),
		'no-alt'      => __( 'No 2', 'acf' ),
		'plus'        => __( 'Plus 1', 'acf' ),
		'plus-alt'    => __( 'Plus 2', 'acf' ),
		'plus-alt2'   => __( 'Plus 3', 'acf' ),
		'star-empty'  => __( 'Star Empty', 'acf' ),
		'star-filled' => __( 'Star Filled', 'acf' ),
		'star-half'   => __( 'Star Half', 'acf' ),
		'warning'     => __( 'Warning', 'acf' ),
		'yes'         => __( 'Yes 1', 'acf' ),
		'yes-alt'     => __( 'Yes 2', 'acf' ),

		'## misc'   => '##' . __( 'Miscellaneous', 'acf' ),
		'airplane'  => __( 'Airplane', 'acf' ),
		'album'     => __( 'Album', 'acf' ),
		'analytics' => __( 'Analytics', 'acf' ),
		'art'       => __( 'Art', 'acf' ),
		'awards'    => __( 'Awards', 'acf' ),
		'backup'    => __( 'Backup', 'acf' ),
		'bank'      => __( 'Bank', 'acf' ),
		'beer'      => __( 'Beer', 'acf' ),
		'building'  => __( 'Building', 'acf' ),
		'businessman'    => __( 'Businessman', 'acf' ),
		'businesswoman'  => __( 'Businesswoman', 'acf' ),
		'businessperson' => __( 'Businessperson', 'acf' ),
		'calculator'     => __( 'Calculator', 'acf' ),
		'calendar'       => __( 'Calendar 1', 'acf' ),
		'calendar-alt'   => __( 'Calendar 2', 'acf' ),
		'car'    => __( 'Car', 'acf' ),
		'carrot' => __( 'Carrot', 'acf' ),
		'cart'   => __( 'Cart', 'acf' ),
		'category'   => __( 'Category', 'acf' ),
		'chart-area' => __( 'Chart Area', 'acf' ),
		'chart-bar'  => __( 'Chart Bar', 'acf' ),
		'chart-line' => __( 'Chart Line', 'acf' ),
		'chart-pie'  => __( 'Chart Pie', 'acf' ),
		'clipboard'  => __( 'Clipboard', 'acf' ),
		'clock'      => __( 'Clock', 'acf' ),
		'code-standards' => __( 'Code Standards', 'acf' ),
		'coffee'         => __( 'Coffee', 'acf' ),
		'color-picker'   => __( 'Color Picker', 'acf' ),
		'database'         => __( 'Database', 'acf' ),
		'database-add'     => __( 'Database Add', 'acf' ),
		'database-export'  => __( 'Database Export', 'acf' ),
		'database-import'  => __( 'Database Import', 'acf' ),
		'database-remove'  => __( 'Database Remove', 'acf' ),
		'database-view'    => __( 'Database View', 'acf' ),
		'desktop'    => __( 'Desktop', 'acf' ),
		'archive'    => __( 'Document Archive', 'acf' ),
		'download'   => __( 'Download', 'acf' ),
		'drumstick'  => __( 'Drumbstick', 'acf' ),
		'email'      => __( 'Email 1', 'acf' ),
		'email-alt'  => __( 'Email 2', 'acf' ),
		'email-alt2' => __( 'Email 3', 'acf' ),
		'external'     => __( 'External', 'acf' ),
		'feedback'     => __( 'Feedback', 'acf' ),
		'food'   => __( 'Food', 'acf' ),
		'forms'  => __( 'Forms', 'acf' ),
		'fullscreen-alt'      => __( 'Full Screen', 'acf' ),
		'fullscreen-exit-alt' => __( 'Full Screen Exit', 'acf' ),
		'games'      => __( 'Games', 'acf' ),
		'groups'     => __( 'Groups', 'acf' ),
		'hammer'     => __( 'Hammer', 'acf' ),
		'heart'      => __( 'Heart', 'acf' ),
		'hidden'     => __( 'Hidden', 'acf' ),
		'hourglass'  => __( 'Hourglass', 'acf' ),
		'id'         => __( 'ID 1', 'acf' ),
		'id-alt'     => __( 'ID 2', 'acf' ),
		'image-crop'   => __( 'Image: Crop', 'acf' ),
		'image-filter' => __( 'Image: Filter', 'acf' ),
		'image-flip-horizontal' => __( 'Image: Flip Horizontal', 'acf' ),
		'image-flip-vertical'   => __( 'Image: Flip Vertical', 'acf' ),
		'image-rotate'          => __( 'Image: Rotate', 'acf' ),
		'image-rotate-left'     => __( 'Image: Rotate Left', 'acf' ),
		'image-rotate-right'    => __( 'Image: Rotate Right', 'acf' ),
		'index-card'   => __( 'Index Card', 'acf' ),
		'info'         => __( 'Info', 'acf' ),
		'laptop'       => __( 'Laptop', 'acf' ),
		'layout'       => __( 'Layout', 'acf' ),
		'lightbulb'    => __( 'Light Bulb', 'acf' ),
		'location'     => __( 'Location 1', 'acf' ),
		'location-alt' => __( 'Location 2', 'acf' ),
		'lock'   => __( 'Lock', 'acf' ),
		'migrate'     => __( 'Migrate', 'acf' ),
		'money'       => __( 'Money', 'acf' ),
		'money-alt'   => __( 'Dollar Sign', 'acf' ),
		'nametag'     => __( 'Name Tag', 'acf' ),
		'networking'  => __( 'Networking', 'acf' ),
		'open-folder' => __( 'Open Folder', 'acf' ),
		'palmtree'    => __( 'Palm Tree', 'acf' ),
		'paperclip'   => __( 'Paper Clip', 'acf' ),
		'performance' => __( 'Performance', 'acf' ),
		'pets'        => __( 'Pets', 'acf' ),
		'phone'       => __( 'Phone', 'acf' ),
		'portfolio'     => __( 'Portfolio', 'acf' ),
		'post-status'   => __( 'Post-status', 'acf' ),
		'pressthis'     => __( 'Pressthis', 'acf' ),
		'printer'       => __( 'Printer', 'acf' ),
		'privacy'       => __( 'Privacy', 'acf' ),
		'products'      => __( 'Products', 'acf' ),
		'redo'          => __( 'Redo', 'acf' ),
		'rest-api'      => __( 'Rest API', 'acf' ),
		'rss'           => __( 'RSS', 'acf' ),
		'schedule'      => __( 'Schedule', 'acf' ),
		'search'        => __( 'Search', 'acf' ),
		'shield'        => __( 'Shield 1', 'acf' ),
		'shield-alt'    => __( 'Shield 2', 'acf' ),
		'smartphone'    => __( 'Smartphone', 'acf' ),
		'smiley'        => __( 'Smiley', 'acf' ),
		'post-trash'    => __( 'Trash', 'acf' ),
		'sos'           => __( 'SOS', 'acf' ),
		'store'         => __( 'Store Front', 'acf' ),
		'superhero'     => __( 'Superhero 1', 'acf' ),
		'superhero-alt' => __( 'Superhero 2', 'acf' ),
		'tablet'        => __( 'Tablet', 'acf' ),
		'tag'           => __( 'Tag', 'acf' ),
		'tagcloud'      => __( 'Tag Cloud', 'acf' ),
		'testimonial'   => __( 'Testimonial', 'acf' ),
		'text'          => __( 'Text', 'acf' ),
		'thumbs-down'   => __( 'Thumbs down', 'acf' ),
		'thumbs-up'     => __( 'Thumbs up', 'acf' ),
		'tickets'       => __( 'Tickets 1', 'acf' ),
		'tickets-alt'   => __( 'Tickets 2', 'acf' ),
		'translation'   => __( 'Translation', 'acf' ),
		'trash'         => __( 'Trash', 'acf' ),
		'undo'          => __( 'Undo', 'acf' ),
		'universal-access'     => __( 'Universal Access 1', 'acf' ),
		'universal-access-alt' => __( 'Universal Access 2', 'acf' ),
		'unlock'     => __( 'Unlock', 'acf' ),
		'update'     => __( 'Update 1', 'acf' ),
		'update-alt' => __( 'Update 2', 'acf' ),
		'upload'     => __( 'Upload', 'acf' ),
		'vault'      => __( 'Vault', 'acf' ),
		'visibility' => __( 'Visibility', 'acf' ),

		'## editor'          => '##' . __( 'Content Editor', 'acf' ),
		'editor-break'       => __( 'Editor Break', 'acf' ),
		'editor-code'        => __( 'Editor Code', 'acf' ),
		'editor-contract'    => __( 'Editor Contract', 'acf' ),
		'editor-customchar'  => __( 'Editor Custom Character', 'acf' ),
		'editor-distractionfree' => __( 'Editor Full Screen', 'acf' ),
		'editor-expand'      => __( 'Editor Expand', 'acf' ),
		'editor-help'        => __( 'Editor Help', 'acf' ),
		'editor-insertmore'  => __( 'Editor Insert More', 'acf' ),
		'editor-kitchensink' => __( 'Editor Kitchen Sink', 'acf' ),
		'editor-ltr'         => __( 'Editor Left-to-Right', 'acf' ),
		'editor-ol-rtl'      => __( 'Editor Ordered List Left-to-Right', 'acf' ),
		'editor-paragraph'   => __( 'Editor Paragraph', 'acf' ),
		'editor-paste-text'  => __( 'Editor Paste Text', 'acf' ),
		'editor-paste-word'  => __( 'Editor Paste Word', 'acf' ),
		'editor-quote'       => __( 'Editor Quote', 'acf' ),
		'editor-removeformatting' => __( 'Editor Remove Formatting', 'acf' ),
		'editor-rtl'           => __( 'Editor Right-to-Left', 'acf' ),
		'editor-spellcheck'    => __( 'Editor Spell Check', 'acf' ),
		'editor-table'         => __( 'Editor Table', 'acf' ),
		'editor-textcolor'     => __( 'Editor Text Color', 'acf' ),
		'editor-video'         => __( 'Editor Video', 'acf' ),
		'align-center'         => __( 'Image Align Center', 'acf' ),
		'align-left'           => __( 'Image Align Left', 'acf' ),
		'align-none'           => __( 'Image Align None', 'acf' ),
		'align-right'          => __( 'Image Align Right', 'acf' ),
		'editor-aligncenter'   => __( 'Text Align Center', 'acf' ),
		'editor-justify'       => __( 'Text Align Justify', 'acf' ),
		'editor-alignleft'     => __( 'Text Align Left', 'acf' ),
		'editor-alignright'    => __( 'Text Align Right', 'acf' ),
		'editor-bold'          => __( 'Text Bold', 'acf' ),
		'editor-indent'        => __( 'Text Indent', 'acf' ),
		'editor-italic'        => __( 'Text Italic', 'acf' ),
		'editor-ol'            => __( 'Text Ordered List', 'acf' ),
		'editor-outdent'       => __( 'Text Outdent', 'acf' ),
		'editor-strikethrough' => __( 'Text Strike Through', 'acf' ),
		'editor-ul'            => __( 'Text Unordered List', 'acf' ),
		'editor-underline'     => __( 'Text Underline', 'acf' ),
		'editor-unlink'        => __( 'Text Unlink', 'acf' ),

		'## g-editor' => '##' . __( 'Block Editor', 'acf' ),
		'align-full-width' => __( 'Align Full Width', 'acf' ),
		'align-pull-left'  => __( 'Align Pull Left', 'acf' ),
		'align-pull-right' => __( 'Align Pull Right', 'acf' ),
		'align-wide'       => __( 'Align Wide', 'acf' ),
		'button'           => __( 'Button', 'acf' ),
		'cover-image'      => __( 'Cover Image', 'acf' ),
		'cloud'            => __( 'Cloud', 'acf' ),
		'cloud-saved'      => __( 'Cloud Saved', 'acf' ),
		'cloud-upload'     => __( 'Cloud Upload', 'acf' ),
		'columns'          => __( 'Columns', 'acf' ),
		'block-default'    => __( 'Default Block', 'acf' ),
		'ellipsis'      => __( 'Ellipsis', 'acf' ),
		'embed-audio'   => __( 'Embed Audio', 'acf' ),
		'embed-generic' => __( 'Embed Generic', 'acf' ),
		'embed-photo'   => __( 'Embed Photo', 'acf' ),
		'embed-post'    => __( 'Embed Post', 'acf' ),
		'embed-video'   => __( 'Embed Video', 'acf' ),
		'exit'          => __( 'Exit', 'acf' ),
		'heading'       => __( 'Heading', 'acf' ),
		'html'          => __( 'HTML', 'acf' ),
		'info-outline'  => __( 'Info Outline', 'acf' ),
		'insert'        => __( 'Insert', 'acf' ),
		'insert-after'  => __( 'Insert After', 'acf' ),
		'insert-before' => __( 'Insert Before', 'acf' ),
		'remove'        => __( 'Remove', 'acf' ),
		'saved'         => __( 'Saved', 'acf' ),
		'shortcode'     => __( 'Shortcode', 'acf' ),
		'table-col-after'  => __( 'Table Column After', 'acf' ),
		'table-col-before' => __( 'Table Column Before', 'acf' ),
		'table-col-delete' => __( 'Table Column Delete', 'acf' ),
		'table-row-after'  => __( 'Table Row After', 'acf' ),
		'table-row-before' => __( 'Table Row Before', 'acf' ),
		'table-row-delete' => __( 'Table Row Delete', 'acf' ),

		'## sorting'       => '##' . __( 'Sorting', 'acf' ),
		'arrow-up'         => __( 'Arrow Up 1', 'acf' ),
		'arrow-up-alt'     => __( 'Arrow Up 2', 'acf' ),
		'arrow-up-alt2'    => __( 'Arrow Up 3', 'acf' ),
		'arrow-down'       => __( 'Arrow Down 1', 'acf' ),
		'arrow-down-alt'   => __( 'Arrow Down 2', 'acf' ),
		'arrow-down-alt2'  => __( 'Arrow Down 3', 'acf' ),
		'arrow-left'       => __( 'Arrow Left 1', 'acf' ),
		'arrow-left-alt'   => __( 'Arrow Left 2', 'acf' ),
		'arrow-left-alt2'  => __( 'Arrow Left 3', 'acf' ),
		'arrow-right'      => __( 'Arrow Right 1', 'acf' ),
		'arrow-right-alt'  => __( 'Arrow Right 2', 'acf' ),
		'arrow-right-alt2' => __( 'Arrow Right 3', 'acf' ),
		'leftright'     => __( 'Left-Right', 'acf' ),
		'move'          => __( 'Move', 'acf' ),
		'randomize'     => __( 'Randomize', 'acf' ),
		'screenoptions' => __( 'Screen Options', 'acf' ),
		'sort'          => __( 'Sort', 'acf' ),
		'excerpt-view'  => __( 'View: Excerpt', 'acf' ),
		'grid-view'     => __( 'View: Grid', 'acf' ),
		'list-view'     => __( 'View: List', 'acf' ),

		'## format' => '##' . __( 'Post Formats', 'acf' ),
		'format-aside'    => __( 'Format: Aside', 'acf' ),
		'format-audio'    => __( 'Format: Audio', 'acf' ),
		'format-chat'     => __( 'Format: Chat', 'acf' ),
		'format-gallery'  => __( 'Format: Gallery', 'acf' ),
		'format-image'    => __( 'Format: Image', 'acf' ),
		'format-links'    => __( 'Format: Links', 'acf' ),
		'format-quote'    => __( 'Format: Quote', 'acf' ),
		'format-standard' => __( 'Format: Standard', 'acf' ),
		'format-status'   => __( 'Format: Status', 'acf' ),
		'format-video'    => __( 'Format: Video', 'acf' ),

		'## files' => '##' . __( 'File Types', 'acf' ),
		'media-archive'  => __( 'File: Archive', 'acf' ),
		'media-audio'    => __( 'File: Audio', 'acf' ),
		'media-code'     => __( 'File: Code', 'acf' ),
		'media-default'  => __( 'File: Default', 'acf' ),
		'media-document' => __( 'File: Document', 'acf' ),
		'media-interactive' => __( 'File: Interactive', 'acf' ),
		'pdf'               => __( 'File: PDF', 'acf' ),
		'media-spreadsheet' => __( 'File: Spreadsheet', 'acf' ),
		'media-text'  => __( 'File: Text', 'acf' ),
		'media-video' => __( 'File: Video', 'acf' ),

		'## social'  => '##' . __( 'Social Content', 'acf' ),
		'share'      => __( 'Share 1', 'acf' ),
		'share-alt'  => __( 'Share 2', 'acf' ),
		'share-alt2' => __( 'Share 3', 'acf' ),
		'buddicons-bbpress-logo'    => __( 'bbPress Logo', 'acf' ),
		'buddicons-buddypress-logo' => __( 'BuddyPress Logo', 'acf' ),
		'buddicons-activity'  => __( 'Buddicons Activity', 'acf' ),
		'buddicons-community' => __( 'Buddicons Community', 'acf' ),
		'buddicons-forums'    => __( 'bbPress Forums', 'acf' ),
		'buddicons-friends'   => __( 'Buddicons Friends', 'acf' ),
		'buddicons-groups'    => __( 'Buddicons Groups', 'acf' ),
		'buddicons-pm'        => __( 'Buddicons PM', 'acf' ),
		'buddicons-replies'   => __( 'Buddicons Replies', 'acf' ),
		'buddicons-topics'    => __( 'Buddicons Topics', 'acf' ),
		'buddicons-tracking'  => __( 'Buddicons Tracking', 'acf' ),
		'amazon'       => __( 'Amazon', 'acf' ),
		'facebook'     => __( 'Facebook 1', 'acf' ),
		'facebook-alt' => __( 'Facebook 2', 'acf' ),
		'google'       => __( 'Google', 'acf' ),
		'googleplus'   => __( 'Google+', 'acf' ),
		'instagram'    => __( 'Instagram', 'acf' ),
		'linkedin'     => __( 'LinkedIn', 'acf' ),
		'pinterest'    => __( 'Pinterest', 'acf' ),
		'podio'        => __( 'Podio', 'acf' ),
		'reddit'       => __( 'Reddit', 'acf' ),
		'twitch'       => __( 'Twitch', 'acf' ),
		'twitter'      => __( 'Twitter', 'acf' ),
		'spotify'      => __( 'Spotify', 'acf' ),
		'whatsapp'     => __( 'WhatsApp', 'acf' ),
		'xing'         => __( 'Xing', 'acf' ),
		'youtube'      => __( 'YouTube', 'acf' )
	];
}

/**
 * Get object type
 *
 * Returns a CMS object type.
 *
 * @since  1.0.0
 * @param  string $object_type The object type (post, term, user, etc).
 * @param  string $object_subtype Optional object subtype (post type, taxonomy).
 * @return object
 */
function acf_get_object_type( $object_type, $object_subtype = '' ) {

	$props = [
		'type'    => $object_type,
		'subtype' => $object_subtype,
		'name'    => '',
		'label'   => '',
		'icon'    => ''
	];

	// Set unique identifier as name.
	if ( $object_subtype ) {
		$props['name'] = "$object_type/$object_subtype";
	} else {
		$props['name'] = $object_type;
	}

	// Set label and icon.
	switch ( $object_type ) {
		case 'post':
			if ( $object_subtype ) {
				$post_type = get_post_type_object( $object_subtype );
				if ( $post_type ) {
					$props['label'] = $post_type->labels->name;
					$props['icon']  = acf_with_default( $post_type->menu_icon, 'dashicons-admin-post' );
				} else {
					return false;
				}
			} else {
				$props['label'] = __( 'Posts', 'acf' );
				$props['icon']  = 'dashicons-admin-post';
			}
			break;
		case 'term':
			if ( $object_subtype ) {
				$taxonomy = get_taxonomy( $object_subtype );
				if ( $taxonomy ) {
					$props['label'] = $taxonomy->labels->name;
				} else {
					return false;
				}
			} else {
				$props['label'] = __( 'Taxonomies', 'acf' );
			}
			$props['icon'] = 'dashicons-tag';
			break;
		case 'attachment':
			$props['label'] = __( 'Attachments', 'acf' );
			$props['icon']  = 'dashicons-admin-media';
			break;
		case 'comment':
			$props['label'] = __( 'Comments', 'acf' );
			$props['icon']  = 'dashicons-admin-comments';
			break;
		case 'widget':
			$props['label'] = __( 'Widgets', 'acf' );
			$props['icon']  = 'dashicons-screenoptions';
			break;
		case 'menu':
			$props['label'] = __( 'Menus', 'acf' );
			$props['icon']  = 'dashicons-admin-appearance';
			break;
		case 'menu_item':
			$props['label'] = __( 'Menu items', 'acf' );
			$props['icon']  = 'dashicons-admin-appearance';
			break;
		case 'user':
			$props['label'] = __( 'Users', 'acf' );
			$props['icon']  = 'dashicons-admin-users';
			break;
		case 'option':
			$props['label'] = __( 'Options', 'acf' );
			$props['icon']  = 'dashicons-admin-generic';
			break;
		case 'block':
			$props['label'] = __( 'Blocks', 'acf' );
			$props['icon']  = acf_version_compare( 'wp', '>=', '5.5' ) ? 'dashicons-block-default' : 'dashicons-layout';
			break;
		default:
			return false;
	}

	// Convert to object.
	$object = (object) $props;

	return apply_filters( 'acf/get_object_type', $object, $object_type, $object_subtype );
}

/**
 * Decode post ID
 *
 * Decodes a post_id value such as 1 or "user_1" into
 * an array containing the type and ID.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @return array
 */
function acf_decode_post_id( $post_id = 0 ) {

	$type = '';
	$id   = 0;

	// Interpret numeric value (123).
	if ( is_numeric( $post_id ) ) {
		$type = 'post';
		$id   = $post_id;

	// Interpret string value ("user_123" or "option").
	} elseif ( is_string( $post_id ) ) {
		$i = strrpos( $post_id, '_' );
		if ( $i > 0 ) {
			$type = substr( $post_id, 0, $i );
			$id   = substr( $post_id, $i + 1 );
		} else {
			$type = $post_id;
			$id   = '';
		}

	// Handle incorrect param type.
	} else {
		return compact( 'type', 'id' );
	}

	// Validate props based on param format.
	$format = $type . '_' . ( is_numeric( $id ) ? '%d' : '%s' );
	switch ( $format ) {
		case 'post_%d':
			$type = 'post';
			$id   = absint( $id );
			break;
		case 'term_%d':
			$type = 'term';
			$id   = absint( $id );
			break;
		case 'attachment_%d':
			$type = 'post';
			$id   = absint( $id );
			break;
		case 'comment_%d':
			$type = 'comment';
			$id = absint( $id );
			break;
		case 'widget_%s':
		case 'widget_%d':
			$type = 'option';
			$id   = $post_id;
			break;
		case 'menu_%d':
			$type = 'term';
			$id   = absint( $id );
			break;
		case 'menu_item_%d':
			$type = 'post';
			$id   = absint( $id );
			break;
		case 'user_%d':
			$type = 'user';
			$id   = absint( $id );
			break;
		case 'block_%s':
			$type = 'block';
			$id   = $post_id;
			break;
		case 'option_%s':
			$type = 'option';
			$id   = $post_id;
			break;
		case 'blog_%d':
		case 'site_%d':
			// Allow backwards compatibility for custom taxonomies.
			$type = taxonomy_exists( $type ) ? 'term' : 'blog';
			$id = absint( $id );
			break;
		default:
			// Check for taxonomy name.
			if ( taxonomy_exists( $type ) && is_numeric( $id ) ) {
				$type = 'term';
				$id   = absint( $id );
				break;
			}

			// Treat unknown post_id format as an option.
			$type = 'option';
			$id   = $post_id;
			break;
	}

	return apply_filters( 'acf/decode_post_id', compact( 'type', 'id' ), $post_id );
}

/**
 * Get registered image sizes
 *
 * Clone of wp_get_registered_image_subsizes.
 *
 * @since  1.0.0
 * @param  mixed $filter
 * @return mixed
 */
function acf_get_registered_image_sizes( $filter = false ) {

	$additional = wp_get_additional_image_sizes();
	$all_sizes  = [];
	$wp_sizes   = get_intermediate_image_sizes();
	$wp_sizes[] = 'full';

	foreach ( $wp_sizes as $size_name ) {

		if ( $filter && $size_name !== $filter ) {
			continue;
		}
		$size_data = [
			'name'   => $size_name,
			'width'  => 0,
			'height' => 0,
			'crop'   => false
		];

		// For sizes added by plugins and themes.
		if ( isset( $additional[ $size_name ]['width'] ) ) {
			$size_data['width'] = (int) $additional[ $size_name ]['width'];

		// For default sizes set in options.
		} else {
			$size_data['width'] = (int) get_option( "{$size_name}_size_w" );
		}

		if ( isset( $additional[ $size_name ]['height'] ) ) {
			$size_data['height'] = (int) $additional[ $size_name ]['height'];
		} else {
			$size_data['height'] = (int) get_option( "{$size_name}_size_h" );
		}

		if ( isset( $additional[ $size_name ]['crop'] ) ) {
			$size_data['crop'] = $additional[ $size_name ]['crop'];
		} else {
			$size_data['crop'] = get_option( "{$size_name}_crop" );
		}

		if ( ! is_array( $size_data['crop'] ) || empty( $size_data['crop'] ) ) {
			$size_data['crop'] = (bool) $size_data['crop'];
		}
		$all_sizes[ $size_name ] = $size_data;
	}

	if ( $filter && isset( $all_sizes[ $filter ] ) ) {
		return $all_sizes[ $filter ];
	}
	return $all_sizes;
}

/**
 * Remove class filter
 *
 * Removes hook from inaccessible PHP class.
 * @link https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
 *
 * @since  1.0.0
 * @param  string $tag
 * @param  string $class_name
 * @param  string $method_name
 * @param  integer $priority
 * @global array $wp_filter
 * @return boolean
 */
function acf_remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

	// Access global variables.
	global $wp_filter;

	// Check that filter actually exists first.
	if ( ! isset( $wp_filter[ $tag ] ) ) {
		return FALSE;
	}

	/**
	 * To be backwards compatible, set $callbacks equal
	 * to the correct array as a reference so $wp_filter
	 * is updated.
	 */
	if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {

		// Create $fob object from filter tag to use below.
		$fob       = $wp_filter[ $tag ];
		$callbacks = &$wp_filter[ $tag ]->callbacks;
	} else {
		$callbacks = &$wp_filter[ $tag ];
	}

	// Exit if there aren't any callbacks for specified priority.
	if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
		return false;
	}

	// Loop through each filter for the specified priority to look for class & method.
	foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

		// Filter should always be an array - array( $this, 'method' ), if not go to next.
		if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
			continue;
		}

		// If first value in array is not an object, it can't be a class.
		if ( ! is_object( $filter['function'][0] ) ) {
			continue;
		}

		// Method doesn't match the one looked for, go to next.
		if ( $filter['function'][1] !== $method_name ) {
			continue;
		}

		// Method matched, now check the class.
		if ( get_class( $filter['function'][0] ) === $class_name ) {

			if ( isset( $fob ) ) {

				// Handles removing filter, reseting callback priority keys mid-iteration, etc.
				$fob->remove_filter( $tag, $filter['function'], $priority );

			} else {

				// Use legacy removal process (pre 4.7).
				unset( $callbacks[ $priority ][ $filter_id ] );

				// If it was the only filter in that priority, unset that priority.
				if ( empty( $callbacks[ $priority ] ) ) {
					unset( $callbacks[ $priority ] );
				}

				// If the only filter for that tag, set the tag to an empty array.
				if ( empty( $callbacks ) ) {
					$callbacks = [];
				}

				// Remove this filter from merged_filters, which specifies if filters have been sorted
				unset( $GLOBALS['merged_filters'][ $tag ] );
			}
			return true;
		}
	}
	return false;
}

/**
 * Remove class action
 *
 * @since  1.0.0
 * @param  string $tag
 * @param  string $class_name
 * @param  string $method_name
 * @param  integer $priority
 * @return boolean
 */
function acf_remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
	return acf_remove_class_filter( $tag, $class_name, $method_name, $priority );
}
