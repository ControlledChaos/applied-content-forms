<?php
/**
 * Templates export tool
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

// Stop if templates are not active.
if ( ! acf_get_setting( 'templates' ) ) {
	return;
}

final class ACF_Templates_Export extends ACF_Module_Export {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'ACF_Templates_Export';
		$this->title = __( 'Export Templates', 'acf' );
		$this->description = __( 'Tool for field group templates registered by this instance of Applied Content Forms. Export JSON to import into another site, generate PHP to add to a theme or plugin.', 'acf' );
		$this->select = __( 'Select Templates', 'acf' );
		$this->default_action  = 'json';
		$this->allowed_actions = [ 'json', 'php' ];
		$this->instance = acf_get_instance( 'acfe_dynamic_templates' );
		$this->file     = 'template';
		$this->files    = 'templates';
		$this->messages = [
			'not_found'        => __( 'No template available.', 'acf' ),
			'not_selected'     => __( 'No templates selected', 'acf' ),
			'success_single'   => __( '1 template exported', 'acf' ),
			'success_multiple' => __( '%s templates exported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Templates_Export' );
