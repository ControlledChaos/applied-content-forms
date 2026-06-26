<?php
/**
 * Post type export tool
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Tools
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Stop if post types are not active.
if ( ! acf_get_setting( 'post_types' ) ) {
	return;
}

class ACF_Post_Types_Export extends ACF_Module_Export {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'ACF_Post_Types_Export';
		$this->title = __( 'Export Post Types', 'acf' );
		if ( $this->is_active() ) {
			$this->title = __( 'Generated PHP: Post Types' );
		}
		$this->description = __( 'Export Post Types', 'acf' );
		$this->select      = __( 'Select Post Types', 'acf' );
		$this->default_action  = 'json';
		$this->allowed_actions = [ 'json', 'php' ];
		$this->instance = acf_get_instance( 'acf_dynamic_post_types' );
		$this->file     = 'post-type';
		$this->files    = 'post-types';
		$this->messages = [
			'not_found'        => __( 'No post type available.', 'acf' ),
			'not_selected'     => __( 'No post types selected', 'acf' ),
			'success_single'   => __( '1 post type exported', 'acf' ),
			'success_multiple' => __( '%s post types exported', 'acf' ),
		];
	}
}
acf_register_admin_tool( 'ACF_Post_Types_Export' );
