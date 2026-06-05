<?php
/**
 * AJAX handler
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   AJAX
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Ajax {
	
	/**
	 * AJAX action name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action = '';
	
	/**
	 * The $_REQUEST data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $request;
	
	/**
	 * Privacy
	 *
	 * Prevents access for non-logged in users.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $public = false;
	
	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		$this->initialize();
		$this->add_actions();
	}
	
	/**
	 * Has
	 *
	 * Returns true if the request has data for the given key.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The data key.
	 * @return boolean
	 */
	public function has( $key = '' ) {
		return isset( $this->request[$key] );
	}
	
	/**
	 * Get
	 *
	 * Returns request data for the given key.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The data key.
	 * @return mixed
	 */
	public function get( $key = '' ) {
		return isset( $this->request[$key] ) ? $this->request[$key] : null;
	}
	
	/**
	 * Set
	 *
	 * Sets request data for the given key.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The data key.
	 * @param  mixed $value The data value.
	 * @return object ACF_Ajax
	 */
	public function set( $key = '', $value = null ) {
		$this->request[$key] = $value;
		return $this;
	}
	
	/**
	 * Initialize
	 *
	 * Allows easy access to modifying properties without changing constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {
		// For child class.
	}
	
	/**
	 * Add actions
	 *
	 * Adds the AJAX actions for this response.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_actions() {
		
		// Add action for logged-in users.
		add_action( "wp_ajax_{$this->action}", [ $this, 'request' ] );
		
		// Add action for non logged-in users.
		if ( $this->public ) {
			add_action( "wp_ajax_nopriv_{$this->action}", [ $this, 'request' ] );
		}
	}
	
	/**
	 * Request
	 *
	 * Callback for AJAX action. Sets up properties and calls the get_response() function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function request() {
		
		// Store data for has() and get() functions.
		$this->request = wp_unslash( $_REQUEST );
		
		// Verify request and handle error.
		$error = $this->verify_request( $this->request );
		if ( is_wp_error( $error ) ) {
			$this->send( $error );
		}
		
		// Send response.
		$this->send( $this->get_response( $this->request ) );
	}
	
	/**
	 * Verify request
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return mixed True on success, WP_Error on fail.
	 */
	public function verify_request( $request ) {
		
		// Verify nonce.
		if ( ! acf_verify_ajax() ) {
			return new WP_Error( 'acf_invalid_nonce', __( 'Invalid nonce.', 'acf' ), [ 'status' => 404 ] );
		}
		return true;
	}
	
	/**
	 * Get response
	 *
	 * Returns the response data to sent back.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return mixed The response data or WP_Error.
	 */
	public function get_response( $request ) {
		return true;
	}
	
	/**
	 * Send
	 *
	 * Sends back JSON based on the $response as either success or failure.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $response The response to send back.
	 * @return void
	 */
	public function send( $response ) {
		
		// Return error.
		if ( is_wp_error( $response ) ) {
			$this->send_error( $response );
		
		// Return success.
		} else {
			wp_send_json( $response );
		}
	}
	
	/**
	 * Send error
	 *
	 * Sends a JSON response for the given WP_Error object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object error The error object.
	 * @return void
	 */
	public function send_error( $error ) {
		
		// Get error status.
		$error_data = $error->get_error_data();
		if ( is_array( $error_data ) && isset( $error_data['status'] ) ) {
			$status_code = $error_data['status'];
		} else {
			$status_code = 500;
		}
		
		wp_send_json( [
			'code'    => $error->get_error_code(),
			'message' => $error->get_error_message(),
			'data'    => $error->get_error_data()
		], $status_code );
	}
}
