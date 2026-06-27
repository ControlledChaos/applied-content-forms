<?php
/**
 * Block types import tool
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

final class ACF_Block_Types_Import extends ACF_Module_Import {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->hook  = 'block_type';
		$this->name  = 'ACF_Block_Types_Import';
		$this->title = __( 'Import Block Types', 'acf' );
		$this->description = __( 'Tool for dynamic block types registered by another instance of Applied Content Forms. Upload an exported JSON file to import into the database.', 'acf' );
		$this->instance    = acf_get_instance( 'acfe_dynamic_block_types' );
		$this->messages    = [
			'success_single'   => __( '1 block type imported', 'acf' ),
			'success_multiple' => __( '%s block types imported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Block_Types_Import' );
