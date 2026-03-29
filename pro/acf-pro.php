<?php
/**
 * ACF admin screen
 *
 * @package    Applied Content Forms
 * @subpackage Pro
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ACF_PRO {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		acf_update_setting( 'pro', true );

		acf_include( 'pro/blocks.php' );
		acf_include( 'pro/options-page.php' );

		if ( is_admin() ) {
			acf_include( 'pro/admin/admin-options-page.php' );
		}

		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'acf/include_field_types', [ $this, 'include_field_types' ], 5 );
		add_action( 'acf/include_location_rules', [ $this, 'include_location_rules' ], 5 );
		add_action( 'acf/input/admin_enqueue_scripts', [ $this, 'input_admin_enqueue_scripts' ] );
		add_action( 'acf/field_group/admin_enqueue_scripts', [ $this, 'field_group_admin_enqueue_scripts' ] );
	}

	/**
	 * Include field types
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_field_types() {

		acf_include( 'pro/fields/class-acf-field-repeater.php' );
		acf_include( 'pro/fields/class-acf-field-flexible-content.php' );
		acf_include( 'pro/fields/class-acf-field-gallery.php' );
		acf_include( 'pro/fields/class-acf-field-clone.php' );
	}

	/**
	 * Include location rules
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_location_rules() {
		acf_include( 'pro/locations/class-acf-location-block.php' );
		acf_include( 'pro/locations/class-acf-location-options-page.php' );
	}

	/**
	 * Register assets
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_assets() {

		$version = acf_get_setting( 'version' );
		$min     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts.
		wp_register_script( 'acf-pro-input', acf_get_url( "assets/js/acf-pro-input{$min}.js" ), [ 'acf-input' ], $version );
		wp_register_script( 'acf-pro-field-group', acf_get_url( "assets/js/acf-pro-field-group{$min}.js" ), [ 'acf-field-group' ], $version );

		// Register styles.
		wp_register_style( 'acf-pro-input', acf_get_url( 'assets/css/acf-pro-input.css' ), [ 'acf-input' ], $version );
		wp_register_style( 'acf-pro-field-group', acf_get_url( 'assets/css/acf-pro-field-group.css' ), [ 'acf-input' ], $version );
	}

	/**
	 * Enqueue admin input scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function input_admin_enqueue_scripts() {
		wp_enqueue_script( 'acf-pro-input' );
		wp_enqueue_style( 'acf-pro-input' );
	}

	/**
	 * Enqueue admin group scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function field_group_admin_enqueue_scripts() {
		wp_enqueue_script( 'acf-pro-field-group' );
		wp_enqueue_style( 'acf-pro-field-group' );
	}
}

/*
 * Instantiate the ACF_PRO class.
 *
 * The main function responsible for returning the one true
 * ACF_PRO instance to functions everywhere.
 * Use this function like you would a global variable,
 * except without needing to declare the global.
 *
 * Example: <?php $acf_pro = acf_pro(); ?>
 *
 * @since  1.0.0
 * @global object $acf_pro
 * @return object Returns an instance of the ACF_PRO class.
 */
function acf_pro() {

	// Set a global variable.
	global $acf_pro;

	// Instantiate only once.
	if ( ! isset( $acf_pro ) ) {
		$acf_pro = new ACF_PRO();
	}
	return $acf_pro;
}
acf_pro();
