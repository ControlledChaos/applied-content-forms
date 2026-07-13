<?php
/**
 * Validation class
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

class ACF_Validation {

	/**
	 * Errors
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $errors;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		$this->errors = [];

		// AJAX.
		add_action( 'wp_ajax_acf/validate_save_post', [ $this, 'ajax_validate_save_post' ] );
		add_action( 'wp_ajax_nopriv_acf/validate_save_post', [ $this, 'ajax_validate_save_post' ] );
		add_action( 'acf/validate_save_post', [ $this, 'acf_validate_save_post' ], 5 );
	}

	/**
	 * Add error
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  string $input
	 * @param  string $message
	 * @return integer
	 */
	public function add_error( $input, $message ) {

		// Add to array.
		$this->errors[] = [
			'input'   => $input,
			'message' => $message
		];
	}

	/**
	 * Get error
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  string $input
	 * @return mixed
	 */
	public function get_error( $input ) {

		// Stop if no errors.
		if ( empty( $this->errors ) ) {
			return false;
		}

		// Loop.
		foreach ( $this->errors as $error ) {
			if ( $error['input'] === $input ) {
				return $error;
			}
		}
		return false;
	}

	/**
	 * Get errors
	 *
	 * @since 1.0.0
	 * @access public
	 * @return mixed
	 */
	public function get_errors() {

		// Stop if no errors.
		if ( empty( $this->errors ) ) {
			return false;
		}
		return $this->errors;
	}

	/**
	 * Reset errors
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function reset_errors() {
		$this->errors = [];
	}

	/**
	 * AJAX validate save post
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function ajax_validate_save_post() {

		if ( ! acf_verify_ajax() ) {
			die( 'AJAX post save not validated.' );
		}

		$json = [
			'valid'  => 1,
			'errors' => 0
		];

		// Success.
		if ( acf_validate_save_post() ) {
			wp_send_json_success( $json );
		}

		// Update vars.
		$json['valid']  = 0;
		$json['errors'] = acf_get_validation_errors();

		wp_send_json_success( $json );
	}

	/**
	 * AJAX validate save post
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function acf_validate_save_post() {

		// Stop if no $_POST.
		if ( empty( $_POST['acf'] ) ) {
			return;
		}
		acf_validate_values( $_POST['acf'], 'acf' );
	}
}

// Initialize.
acf()->validation = new ACF_Validation();
