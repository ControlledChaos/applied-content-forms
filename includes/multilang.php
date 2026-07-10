<?php
/**
 * Multilang functions
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
 * Is multilang enabled
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_multilang() {
	return acf_get_instance( 'ACF_Multilang' )->is_multilang;
}

/**
 * Get multilang data
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_multilang() {

	$wpml     = acf_get_instance( 'ACF_Multilang' )->is_wpml;
	$polylang = acf_get_instance( 'ACF_Multilang' )->is_polylang;

	$data = [
		'dl'       => acf_get_setting('default_language'),
		'cl'       => acf_get_setting('current_language'),
		'wpml'     => $wpml,
		'polylang' => $polylang,
	];
	return $data;
}

/**
 * Get languages
 *
 * @since  1.0.0
 * @param  string $pluck
 * @param  string $type
 * @param  string $plugin
 * @return array
 */
function acf_get_multilang_languages( $pluck = '', $type = '', $plugin = '' ) {
	return acf_get_instance( 'ACF_Multilang' )->get_languages( $pluck, $type, $plugin );
}

/**
 * Is PolyLang
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_polylang(){
	return acf_get_instance( 'ACF_Multilang' )->is_polylang;
}

/**
 * Is WPML
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_is_wpml(){
	return acf_get_instance( 'ACF_Multilang' )->is_wpml;
}

/**
 * Get post language
 *
 * @since  1.0.0
 * @param  integer  $post_id
 * @param  boolean $field
 * @return mixed
 */
function acf_get_post_lang( $post_id, $field = false ) {

	// Stop if not multilang.
	if ( ! acf_is_multilang() ) {
		return false;
	}

	// PolyLang.
	if ( acf_is_polylang() ) {

		// Default field.
		if ( ! $field ) {
			$field = 'locale';
		}
		return pll_get_post_language( $post_id, $field );

	// WPML
	} elseif ( acf_is_wpml() ) {

		$post_lang = apply_filters( 'wpml_post_language_details', null, $post_id );

		// Default field.
		if ( ! $field ) {
			$field = 'slug';
		}

		if ( 'locale' === $field ) {
			return $post_lang['locale'];
		} elseif ( 'slug' === $field ) {
			return $post_lang['language_code'];
		} elseif ( 'name' === $field ) {
			return $post_lang['display_name'];
		}
		return false;
	}
	return false;
}

/**
 * Get post translated
 *
 * @since  1.0.0
 * @param  integer  $post_id
 * @param  boolean $lang
 * @return integer
 */
function acf_get_post_translated( $post_id, $lang = false ) {

	// Stop if not multilang.
	if ( ! acf_is_multilang() ) {
		return $post_id;
	}

	// Default.
	$translated_post_id = $post_id;

	// PolyLang.
	if ( acf_is_polylang() ) {
		$translated_post_id = pll_get_post( $post_id, $lang );

	// WPML.
	} elseif ( acf_is_wpml() ) {
		$translated_post_id = apply_filters( 'wpml_object_id', $post_id, 'post', false, $lang );
	}
	return $translated_post_id;
}

/**
 * Get default post translated
 *
 * @since  1.0.0
 * @param  integer $post_id
 * @return integer
 */
function acf_get_post_translated_default( $post_id ) {

	// Get translated post ID.
	$translated_post_id = acf_get_post_translated( $post_id, acf_get_setting( 'default_language' ) );

	// Fallback to current.
	if ( empty( $translated_post_id ) ) {
		return $post_id;
	}
	return $translated_post_id;
}

/**
 * Translate string
 *
 * @since  1.0.0
 * @param  string  $string
 * @param  string $name
 * @param  string  $textdomain
 * @return string
 */
function acf_str_translate( $string, $name = false, $textdomain = 'acf' ) {

	if ( ! acf_is_multilang() || empty( $string ) ) {
		return __( $string, $textdomain );
	}

	// Name compatibility.
	if ( empty( $name ) ) {
		$name = $string;
	}

	// WPML.
	if ( acf_is_wpml() ) {

		// Translate (Register string during save).
		return apply_filters( 'wpml_translate_single_string', $string, $textdomain, $name );
	}

	// PolyLang.
	if ( acf_is_polylang() ) {

		// Register string.
		pll_register_string( $name, $string, $textdomain );

		// Translate.
		return pll__( $string );
	}

	// Default translation.
	return __( $string, $textdomain );
}
