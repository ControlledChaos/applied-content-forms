<?php
/**
 * Taxonomies export tool
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

final class ACF_Taxonomies_Export extends ACF_Module_Export {

	public function initialize() {

		$this->name  = 'ACF_Taxonomies_Export';
		$this->title = __( 'Export Taxonomies', 'acf' );
		$this->description = __( 'Tool for dynamic custom taxonomies registered by this instance of Applied Content Forms. Export JSON to import into another site, generate PHP to add to a theme or plugin.', 'acf' );
		$this->select = __( 'Select Taxonomies', 'acf' );
		$this->default_action  = 'json';
		$this->allowed_actions = [ 'json', 'php' ];
		$this->instance = acf_get_instance( 'acfe_dynamic_taxonomies' );
		$this->file     = 'taxonomy';
		$this->files    = 'taxonomies';
		$this->messages = [
			'not_found'        => __( 'No taxonomy available.', 'acf' ),
			'not_selected'     => __( 'No taxonomies selected', 'acf' ),
			'success_single'   => __( '1 taxonomy exported', 'acf' ),
			'success_multiple' => __( '%s taxonomies exported', 'acf' )
		];
	}
}
acf_register_admin_tool( 'ACF_Taxonomies_Export' );
