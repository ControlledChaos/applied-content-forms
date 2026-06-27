<?php
/**
 * Forms import tool
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

final class ACF_Forms_Import extends ACF_Module_Import {

    /**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
    public function initialize() {

        $this->hook  = 'form';
        $this->name  = 'ACF_Forms_Import';
        $this->title = __( 'Import Forms', 'acf' );
        $this->description = __( 'Tool for dynamic frontend forms registered by another instance of Applied Content Forms. Upload an exported JSON file to import into the database.', 'acf' );
        $this->instance = acf_get_instance( 'acfe_dynamic_forms' );
        $this->messages = [
            'success_single'   => __( '1 form imported', 'acf' ),
            'success_multiple' => __( '%s forms imported', 'acf' )
        ];
    }
}
acf_register_admin_tool( 'ACF_Forms_Import' );
