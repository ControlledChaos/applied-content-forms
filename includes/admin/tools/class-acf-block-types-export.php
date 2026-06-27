<?php
/**
 * Block types export tool
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

// Stop if block types are not active.
if ( ! acf_get_setting( 'block_types' ) ) {
	return;
}

final class ACF_Block_Types_Export extends ACF_Module_Export {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'ACF_Block_Types_Export';
		$this->title = __( 'Export Block Types', 'acf' );
		if ( $this->is_active() ) {
			$this->title = __( 'Generated PHP: Block Types', 'acf' );
		}
		$this->description = __( 'Tool for dynamic block types registered by this instance of Applied Content Forms. Export JSON to import into another site, generate PHP to add to a theme or plugin.', 'acf' );
		$this->select      = __( 'Select Block Types', 'acf' );
		$this->default_action  = 'json';
		$this->allowed_actions = [ 'json', 'php' ];
		$this->instance = acf_get_instance( 'acfe_dynamic_block_types' );
		$this->file     = 'block-type';
		$this->files    = 'block-types';
		$this->messages = [
			'not_found'        => __( 'No block type available.', 'acf' ),
			'not_selected'     => __( 'No block types selected', 'acf' ),
			'success_single'   => __( '1 block type exported', 'acf' ),
			'success_multiple' => __( '%s block types exported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Block_Types_Export' );
