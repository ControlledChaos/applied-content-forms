<?php
/**
 * Options pages export tool
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

// Stop if options pages are not active.
if ( ! acf_get_setting( 'options_pages' ) ) {
	return;
}

final class ACF_Options_Pages_Export extends ACF_Module_Export {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'ACF_Options_Pages_Export';
		$this->title = __( 'Export Options Pages', 'acf' );
		$this->description = __( 'Tool for dynamic options pages registered by this instance of Applied Content Forms. Export JSON to import into another site, generate PHP to add to a theme or plugin.', 'acf' );
		$this->select = __( 'Select Options Pages', 'acf' );
		$this->default_action  = 'json';
		$this->allowed_actions = [ 'json', 'php' ];
		$this->instance = acf_get_instance( 'acfe_dynamic_options_pages' );
		$this->file     = 'options-page';
		$this->files    = 'options-pages';
		$this->messages = [
			'not_found'        => __( 'No options page available.', 'acf' ),
			'not_selected'     => __( 'No options pages selected', 'acf' ),
			'success_single'   => __( '1 options page exported', 'acf' ),
			'success_multiple' => __( '%s options pages exported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Options_Pages_Export' );
