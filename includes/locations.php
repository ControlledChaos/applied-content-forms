<?php
/**
 * Locations functions
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

// Register store.
acf_register_store( 'location-types' );

/**
 * Register a location type
 *
 * @since  1.0.0
 * @param  string $class_name The location class name.
 * @return mixed
 */
function acf_register_location_type( $class_name ) {

	$store = acf_get_store( 'location-types' );

	// Check class exists.
	if ( ! class_exists( $class_name ) ) {
		$message = sprintf( __( 'Class "%s" does not exist.', 'acf' ), $class_name );
		_doing_it_wrong( __FUNCTION__, $message, '5.9.0' );
		return false;
	}

	// Create instance.
	$location_type = new $class_name();
	$name = $location_type->name;

	// Check location type is unique.
	if ( $store->has( $name ) ) {
		$message = sprintf( __( 'Location type "%s" is already registered.', 'acf' ), $name );
		_doing_it_wrong( __FUNCTION__, $message, '5.9.0' );
		return false;
	}

	// Add to store.
	$store->set( $name, $location_type );

	do_action( 'acf/registered_location_type', $name, $location_type );

	// Return location type instance.
	return $location_type;
}

/**
 * Get location types
 *
 * Returns an array of all registered location types.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_location_types() {
	return acf_get_store( 'location-types' )->get();
}

/**
 * Returns a location type for the given name.
 *
 * @since  1.0.0
 * @param  string $name The location type name.
 * @return mixed
 */
function acf_get_location_type( $name ) {
	return acf_get_store( 'location-types' )->get( $name );
}

/**
 * Get location rule types
 *
 * Returns a grouped array of all location rule types.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_location_rule_types() {

	$types = [];

	// Default categories.
	$categories = [
		'post'  => __( 'Post', 'acf' ),
		'page'  => __( 'Page', 'acf' ),
		'user'  => __( 'User', 'acf' ),
		'forms' => __( 'Forms', 'acf' )
	];

	// Loop over all location types and append to $type.
	$location_types = acf_get_location_types();
	foreach ( $location_types as $location_type ) {

		// Ignore if not public.
		if ( ! $location_type->public ) {
			continue;
		}

		// Find category label from category name.
		$category = $location_type->category;
		if ( isset( $categories[ $category ] ) ) {
			$category = $categories[ $category ];
		}

		// Append.
		$types[ $category ][ $location_type->name ] = esc_html( $location_type->label );
	}
	return apply_filters( 'acf/location/rule_types', $types );
}

/**
 * Validate location rule
 *
 * Returns a validated location rule with all props.
 *
 * @since  1.0.0
 * @param  array $rule The location rule.
 * @return array
 */
function acf_validate_location_rule( $rule = [] ) {

	// Apply defaults.
	$rule = wp_parse_args( $rule, [
		'id'       => '',
		'group'    => '',
		'param'    => '',
		'operator' => '==',
		'value'    => ''
	] );

	$rule = apply_filters( "acf/location/validate_rule/type={$rule['param']}", $rule );
	$rule = apply_filters( "acf/location/validate_rule", $rule );

	return $rule;
}

/**
 * Get location rule operators
 *
 * Returns an array of operators for a given rule.
 *
 * @since  1.0.0
 * @param  array $rule The location rule.
 * @return array
 */
function acf_get_location_rule_operators( $rule ) {

	$operators = ACF_Location :: get_operators( $rule );

	// Get operators from location type since 5.9.
	$location_type = acf_get_location_type( $rule['param'] );
	if ( $location_type ) {
		$operators = $location_type->get_operators( $rule );
	}

	$operators = apply_filters( "acf/location/rule_operators/type={$rule['param']}", $operators, $rule );
	$operators = apply_filters( "acf/location/rule_operators/{$rule['param']}", $operators, $rule );
	$operators = apply_filters( "acf/location/rule_operators", $operators, $rule );

	return $operators;
}

/**
 * Get location rule values
 *
 * Returns an array of values for a given rule.
 *
 * @since  1.0.0
 * @param  array $rule The location rule.
 * @return array
 */
function acf_get_location_rule_values( $rule ) {

	$values = [];

	// Get values from location type since 5.9.
	$location_type = acf_get_location_type( $rule['param'] );
	if ( $location_type ) {
		$values = $location_type->get_values( $rule );
	}

	$values = apply_filters( "acf/location/rule_values/type={$rule['param']}", $values, $rule );
	$values = apply_filters( "acf/location/rule_values/{$rule['param']}", $values, $rule );
	$values = apply_filters( "acf/location/rule_values", $values, $rule );

	return $values;
}

/**
 * Match location rule
 *
 * Returns true if the provided rule matches the screen args.
 *
 * @since  1.0.0
 * @param  array $rule The location rule.
 * @param  array $screen The screen args.
 * @param  array $field The field group array.
 * @return boolean
 */
function acf_match_location_rule( $rule, $screen, $field_group ) {

	$result = false;

	// Get result from location type since 5.9.
	$location_type = acf_get_location_type( $rule['param'] );
	if ( $location_type ) {
		$result = $location_type->match( $rule, $screen, $field_group );
	}

	$result = apply_filters( "acf/location/match_rule/type={$rule['param']}", $result, $rule, $screen, $field_group );
	$result = apply_filters( "acf/location/match_rule", $result, $rule, $screen, $field_group );
	$result = apply_filters( "acf/location/rule_match/{$rule['param']}", $result, $rule, $screen, $field_group );
	$result = apply_filters( "acf/location/rule_match", $result, $rule, $screen, $field_group );

	return $result;
}

/**
 * Get location screen
 *
 * Returns an array of screen args to be used against matching rules.
 *
 * @since  1.0.0
 * @param  array $screen The screen args.
 * @param  array $deprecated The field group array.
 * @return array
 */
function acf_get_location_screen( $screen = [], $deprecated = false ) {

	// Apply defaults.
	$screen = wp_parse_args( $screen, [
		'lang' => acf_get_setting( 'current_language' ),
		'ajax' => false
	] );

	return apply_filters( 'acf/location/screen', $screen, $deprecated );
}

/**
 * Alias of acf_register_location_type()
 *
 * @since  1.0.0
 * @param  string $class_name The location class name.
 * @return mixed
 */
function acf_register_location_rule( $class_name ) {
	return acf_register_location_type( $class_name );
}

/**
 * Alias of acf_get_location_type()
 *
 * @since  1.0.0
 * @param  string $class_name The location class name.
 * @return mixed
 */
function acf_get_location_rule( $name ) {
	return acf_get_location_type( $name );
}

/**
 * Alias of acf_validate_location_rule()
 *
 * @since  1.0.0
 * @param  array $rule The location rule.
 * @return array
 */
function acf_get_valid_location_rule( $rule ) {
	return acf_validate_location_rule( $rule );
}
