<?php
/**
 * Options pages import tool
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

final class ACF_Options_Pages_Import extends ACF_Module_Import {

    /**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
    public function initialize() {

        $this->hook  = 'options_page';
        $this->name  = 'ACF_Options_Pages_Import';
        $this->title = __( 'Import Options Pages', 'acf' );
        $this->description = __( 'Tool for dynamic options pages registered by another instance of Applied Content Forms. Upload an exported JSON file to import into the database.', 'acf' );
        $this->instance = acf_get_instance( 'acfe_dynamic_options_pages' );
        $this->messages = [
            'success_single'   => __( '1 options page imported', 'acf' ),
            'success_multiple' => __( '%s options pages imported', 'acf' )
        ];
    }
}
acf_register_admin_tool( 'ACF_Options_Pages_Import' );
