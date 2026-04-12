<?php
/**
 * Extend ACF assets class
 *
 * @package    Applied Content Forms
 * @subpackage Extend
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Extend_Assets {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'acf/input/admin_enqueue_scripts', [ $this, 'acf_admin_enqueue_scripts' ] );
	}

	/**
	 * Initiate
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$ver = acf_get_setting( 'version' );

		// Register scripts.
		wp_register_script( 'acf-extended', acf_get_url( "assets/js/acfe{$min}.js" ), [ 'acf-input' ], $ver );
		wp_register_script( 'acf-extended-input', acf_get_url( "assets/js/acfe-input{$min}.js" ), [ 'acf-extended' ], $ver );
		wp_register_script( 'acf-extended-admin', acf_get_url( "assets/js/acfe-admin{$min}.js" ), [ 'acf-extended' ], $ver );
		wp_register_script( 'acf-extended-field-group', acf_get_url( "assets/js/acfe-field-group{$min}.js" ), [ 'acf-field-group' ], $ver );
		wp_register_script( 'acf-extended-ui', acf_get_url( "assets/js/acfe-ui{$min}.js" ), [ 'acf-extended' ], $ver );
		wp_register_script( 'acf-extended-pro-input', acf_get_url( "assets/js/acfe-pro-input{$min}.js" ), [ 'acf-extended' ], $ver );
		wp_register_script( 'acf-extended-pro-admin', acf_get_url( "assets/js/acfe-pro-admin{$min}.js" ), [ 'acf-extended' ], $ver );
		wp_register_script( 'acf-extended-pro-field-group', acf_get_url( "assets/js/acfe-pro-field-group{$min}.js" ), [ 'acf-field-group' ], $ver );

		// Register styles.
		wp_register_style( 'acf-extended', acf_get_url( "assets/css/acfe{$min}.css" ), [], $ver );
		wp_register_style( 'acf-extended-input', acf_get_url( "assets/css/acfe-input{$min}.css" ), [], $ver );
		wp_register_style( 'acf-extended-admin', acf_get_url( "assets/css/acfe-admin{$min}.css" ), [], $ver );
		wp_register_style( 'acf-extended-field-group', acf_get_url( "assets/css/acfe-field-group{$min}.css" ), [], $ver );
		wp_register_style( 'acf-extended-pro-input', acf_get_url( "assets/css/acfe-pro-input{$min}.css" ), [ 'acf-extended-input' ], $ver );
		wp_register_style( 'acf-extended-pro-admin', acf_get_url( "assets/css/acfe-pro-admin{$min}.css" ), [ 'acf-extended-admin' ], $ver );
		wp_register_style( 'acf-extended-pro-field-group', acf_get_url( "assets/css/acfe-pro-field-group{$min}.css" ), [ 'acf-extended-field-group' ], $ver );
		wp_register_style( 'acf-extended-ui', acf_get_url( "assets/css/acfe-ui{$min}.css" ), [], $ver );
	}

	/**
	 * Enqueue CMS admin scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		wp_enqueue_style( 'acf-extended-admin' );
		wp_enqueue_style( 'acf-extended-pro-admin' );

		if ( acf_is_screen( [ 'edit-acf-field-group', 'acf-field-group' ] ) ) {
			wp_enqueue_style( 'acf-extended-field-group' );
		}
		if ( acf_is_screen( 'media' ) ) {
			acf_enqueue_scripts();
		}
	}

	/**
	 * Enqueue ACF admin scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function acf_admin_enqueue_scripts() {

		// Global.
		wp_enqueue_style( 'acf-extended' );
		wp_enqueue_script( 'acf-extended' );

		// Input.
		wp_enqueue_style( 'acf-extended-input' );
		wp_enqueue_script( 'acf-extended-input' );
		wp_enqueue_style( 'acf-extended-pro-input' );
		wp_enqueue_script( 'acf-extended-pro-input' );

		// Admin.
		if ( is_admin() ) {
			wp_enqueue_script( 'acf-extended-admin' );
			wp_enqueue_script( 'acf-extended-pro-admin' );
		}

		// Field group.
		if ( acf_is_screen( 'acf-field-group' ) ) {
			wp_enqueue_script( 'acf-extended-field-group' );
			wp_enqueue_style( 'acf-extended-pro-field-group' );
			wp_enqueue_script( 'acf-extended-pro-field-group' );
		}

		acf_localize_data( [
			'acfe' => [
				'version'           => ACF_VERSION,
				'home_url'          => home_url(),
				'is_admin'          => is_admin(),
				'is_user_logged_in' => is_user_logged_in(),
			]
		] );

		acf_localize_text( [
			'Close'     => __( 'Close', 'acf' ),
			'Read more' => __( 'Read more', 'acf' ),
			'Details'   => __( 'Details', 'acf' ),
			'Debug'     => __( 'Debug', 'acf' ),
		] );
	}
}
new ACF_Extend_Assets();
