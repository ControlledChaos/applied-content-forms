<?php
/**
 * Forms export tool
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

// Stop if forms are not active.
if ( ! acf_get_setting( 'forms' ) ) {
	return;
}

final class ACF_Forms_Export extends ACF_Module_Export {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'ACF_Forms_Export';
		$this->title = __( 'Export Forms', 'acf' );
		$this->description = __( 'Tool for dynamic frontend forms registered by this instance of Applied Content Forms. Export JSON to import into another site, generate PHP to add to a theme or plugin.', 'acf' );
		$this->select = __( 'Select Forms', 'acf' );
		$this->default_action  = 'json';
		$this->allowed_actions = [ 'json' ];
		$this->instance = acf_get_instance( 'acfe_dynamic_forms' );
		$this->file     = 'form';
		$this->files    = 'forms';
		$this->messages = [
			'not_found'        => __( 'No form available.', 'acf' ),
			'not_selected'     => __( 'No forms selected.', 'acf' ),
			'success_single'   => __( '1 form exported', 'acf' ),
			'success_multiple' => __( '%s forms exported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Forms_Export' );
