<?php
/**
 * Templates import tool
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

final class ACF_Templates_Import extends ACF_Module_Import {

	public function initialize() {

		$this->hook = 'template';
		$this->name = 'ACF_Templates_Import';
		$this->title = __( 'Import Templates', 'acf' );
		$this->description = __( 'Tool for field group templates registered by another instance of Applied Content Forms. Upload an exported JSON file to import into the database.', 'acf' );
		$this->instance = acf_get_instance( 'acfe_dynamic_templates' );
		$this->messages = [
			'success_single'   => __( '1 template imported', 'acf' ),
			'success_multiple' => __( '%s templates imported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Templates_Import' );
