<?php
/**
 * World functions
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
 * Get countries
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_countries( $args = [] ) {

	// Default arguments.
	$args = wp_parse_args( $args, [
		'type'          => 'countries',
		'code__in'      => false,
		'name__in'      => false,
		'continent__in' => false,
		'language__in'  => false,
		'currency__in'  => false,
		'orderby'       => false,
		'order'         => 'ASC',
		'offset'        => 0,
		'limit'         => -1,
		'field'         => false,
		'display'       => false,
		'prepend'       => false,
		'append'        => false,
		'groupby'       => false
	] );
	$query = new ACFE_World_Query( $args );

	return $query->data;
}

/**
 * Get country
 *
 * @since  1.0.0
 * @param  string $code
 * @param  string $field
 * @return string
 */
function acf_get_country( $code, $field = '' ) {

	$data = acf_get_countries( [
		'code__in' => $code,
		'limit'    => 1
	] );
	$data = reset( $data );

	if ( $field ) {
		return acf_maybe_get( $data, $field );
	}
	return $data;
}

/**
 * Get languages
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_languages( $args = [] ) {

	// Default arguments.
	$args = wp_parse_args( $args, [
		'type'          => 'languages',
		'name__in'      => false,
		'locale__in'    => false,
		'alt__in'       => false,
		'code__in'      => false,
		'continent__in' => false,
		'country__in'   => false,
		'currency__in'  => false,
		'orderby'       => false,
		'order'         => 'ASC',
		'offset'        => 0,
		'limit'         => -1,
		'field'         => false,
		'display'       => false,
		'prepend'       => false,
		'append'        => false,
		'groupby'       => false
	] );
	$query = new ACFE_World_Query( $args );

	return $query->data;
}

/**
 * Get language
 *
 * @since  1.0.0
 * @param  string $locale
 * @param  string $field
 * @return string
 */
function acf_get_language( $locale, $field = '' ) {

	$data = acf_get_languages( [
		'locale__in' => $locale,
		'limit'      => 1
	] );
	$data = reset( $data );

	if ( $field ) {
		return acf_maybe_get( $data, $field );
	}
	return $data;
}

/**
 * Get currencies
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_currencies( $args = [] ) {

	// Default arguments.
	$args = wp_parse_args( $args, [
		'type'          => 'currencies',
		'name__in'      => false,
		'code__in'      => false,
		'continent__in' => false,
		'country__in'   => false,
		'language__in'  => false,
		'countries'     => false,
		'languages'     => false,
		'orderby'       => false,
		'order'         => 'ASC',
		'offset'        => 0,
		'limit'         => -1,
		'field'         => false,
		'display'       => false,
		'prepend'       => false,
		'append'        => false,
		'groupby'       => false
	] );
	$query = new ACFE_World_Query( $args );

	return $query->data;
}

/**
 * Get currency
 *
 * @since  1.0.0
 * @param  string $code
 * @param  string $field
 * @return string
 */
function acf_get_currency( $code, $field = '' ) {

	$data = acf_get_currencies( [
		'code__in' => $code,
		'limit'    => 1
	] );
	$data = reset( $data );

	if ( $field ) {
		return acf_maybe_get( $data, $field );
	}
	return $data;
}
