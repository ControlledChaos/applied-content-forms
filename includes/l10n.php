<?php
/**
 * Locale functions
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
 * Determine locale
 *
 * Determine the current locale desired for the request.
 *
 * @since 1.0.0
 * @return string The determined locale.
 */
if ( ! function_exists( 'determine_locale' ) ) :

function determine_locale() {

	$determined_locale = apply_filters( 'pre_determine_locale', null );
	if ( ! empty( $determined_locale ) && is_string( $determined_locale ) ) {
		return $determined_locale;
	}
	$determined_locale = get_locale();

	if ( function_exists( 'get_user_locale' ) && is_admin() ) {
		$determined_locale = get_user_locale();
	}

	if ( function_exists( 'get_user_locale' ) && isset( $_GET['_locale'] ) && 'user' === $_GET['_locale'] ) {
		$determined_locale = get_user_locale();
	}

	if ( ! empty( $_GET['wp_lang'] ) && ! empty( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
		$determined_locale = sanitize_text_field( $_GET['wp_lang'] );
	}

	return apply_filters( 'determine_locale', $determined_locale );
}
endif;

/*
 * Get locale
 *
 * Returns the current locale.
 *
 * @since  1.0.0
 * @return string
 */
function acf_get_locale() {

	// Determine local.
	$locale = determine_locale();

	// Fallback to parent language for regions without translation.
	// https://wpastra.com/docs/complete-list-wordpress-locale-codes/
	$langs = [
		'az_TR'	=> 'az',    // Azerbaijani (Turkey)
		'zh_HK'	=> 'zh_TW', // Chinese (Hong Kong)
		'nl_BE'	=> 'nl_NL', // Dutch (Belgium)
		'fr_BE'	=> 'fr_FR', // French (Belgium)
		'nn_NO'	=> 'nb_NO', // Norwegian (Nynorsk)
		'fa_AF'	=> 'fa_IR', // Persian (Afghanistan)
		'ru_UA'	=> 'ru_RU'  // Russian (Ukraine)
	];

	if ( isset( $langs[ $locale ] ) ) {
		$locale = $langs[ $locale ];
	}
	return apply_filters( 'acf/get_locale', $locale );
}

/**
 * Load textdomain
 *
 * Loads the plugin's translated strings similar to load_plugin_textdomain().
 *
 * @since  1.0.0
 * @param  string $locale The plugin's current locale.
 * @return void
 */
function acf_load_textdomain( $domain = 'acf' ) {

	$locale = apply_filters( 'plugin_locale', acf_get_locale(), $domain );
	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile ) ) {
		return true;
	}

	// Load from plugin lang folder.
	return load_textdomain( $domain, acf_get_path( 'lang/' . $mofile ) );
}

 /**
 * Apply language cache key
 *
 * Applies the current language to the cache key.
 *
 * @since  1.0.0
 * @param  string $key The cache key.
 * @return string
 */
function _acf_apply_language_cache_key( $key ) {

	// Get current language.
	$current_language = acf_get_setting( 'current_language' );
	if ( $current_language ) {
		$key = "{$key}:{$current_language}";
	}
	return $key;
}

// Hook into filter.
add_filter( 'acf/get_cache_key', '_acf_apply_language_cache_key' );
