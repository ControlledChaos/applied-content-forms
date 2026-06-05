<?php
/**
 * AJAX upgrade
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

class ACF_Ajax_Upgrade extends ACF_Ajax {
	
	/**
	 * AJAX action name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action = 'acf/ajax/upgrade';
	
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
		
		// Switch blog.
		if ( isset( $request['blog_id'] ) ) {
			switch_to_blog( $request['blog_id'] );
		}
		
		// Stop if no upgrade available.
		if ( ! acf_has_upgrade() ) {
			return new WP_Error( 'upgrade_error', __( 'No updates available.', 'acf' ) );
		}
		
		// Listen for output.
		ob_start();
		
		// Run upgrades.
		acf_upgrade_all();
		
		// Store output.
		$error = ob_get_clean();
		
		// Return error or success.
		if ( $error ) {
			return new WP_Error( 'upgrade_error', $error );
		}
		return true;
	}
}
acf_new_instance( 'ACF_Ajax_Upgrade' );
