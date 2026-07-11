<?php
/**
 * Taxonomies import tool
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

// Stop if taxonomies are not active.
if ( ! acf_get_setting( 'taxonomies' ) ) {
	return;
}

final class ACF_Taxonomies_Import extends ACF_Module_Import {

	public function initialize() {

		$this->hook  = 'taxonomy';
		$this->name  = 'ACF_Taxonomies_Import';
		$this->title = __( 'Import Taxonomies', 'acf' );
		$this->description = __( 'Tool for dynamic taxonomies registered by another instance of Applied Content Forms. Upload an exported JSON file to import into the database.', 'acf' );
		$this->instance = acf_get_instance( 'acfe_dynamic_taxonomies' );
		$this->messages = [
			'success_single'   => __( '1 taxonomy imported', 'acf' ),
			'success_multiple' => __( '%s taxonomies imported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Taxonomies_Import' );
