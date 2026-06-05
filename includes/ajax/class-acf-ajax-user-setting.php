<?php
/**
 * AJAX user settings
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

class ACF_Ajax_User_Setting extends ACF_Ajax {
	
	/**
	 * AJAX action name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action = 'acf/ajax/user_setting';
	
	/**
	 * Privacy
	 *
	 * Prevents access for non-logged in users.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $public = true;
	
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

		if ( $this->has( 'value' ) ) {
			return acf_update_user_setting( $this->get( 'name' ), $this->get( 'value' ) );
		} else {
			return acf_get_user_setting( $this->get( 'name' ) );
		}
	}
}
acf_new_instance( 'ACF_Ajax_User_Setting' );
