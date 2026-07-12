<?php
/**
 * World data class
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

class ACF_World_Data {

	/**
	 * Countries
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $countries;

	/**
	 * Languages
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $languages;

	/**
	 * Currencies
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $currencies;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		$this->countries  = acf_include( 'includes/data/countries.php' );
		$this->languages  = acf_include( 'includes/data/languages.php' );
		$this->currencies = acf_include( 'includes/data/currencies.php' );

		// Localize names.
		if ( function_exists( 'locale_get_display_region' ) ) {

			$locale = acf_get_locale();

			foreach ( array_keys( $this->countries ) as $code ) {
				$this->countries[$code]['localized'] = locale_get_display_region( "-$code", $locale );
			}
		}
	}
}
