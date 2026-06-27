<?php
/**
 * Post types import tool
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

final class ACF_Post_Types_Import extends ACF_Module_Import {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->hook  = 'post_type';
		$this->name  = 'ACF_Post_Types_Import';
		$this->title = __( 'Import Post Types', 'acf' );
		$this->description = __( 'Tool for dynamic custom post types registered by another instance of Applied Content Forms. Upload an exported JSON file to import into the database.', 'acf' );
		$this->instance    = acf_get_instance( 'acf_dynamic_post_types' );
		$this->messages    = [
			'success_single'   => __( '1 post type imported', 'acf' ),
			'success_multiple' => __( '%s post types imported', 'acf' ),
		];
	}
}
acf_register_admin_tool( 'ACF_Post_Types_Import' );
