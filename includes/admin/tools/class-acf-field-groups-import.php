<?php
/**
 * Field group import tool
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

final class ACF_Field_Groups_Import extends ACF_Admin_Tool {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		parent :: __construct();
	}

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->name  = 'import';
		$this->title = __( 'Import Field Groups', 'acf' );
    	$this->icon  = 'dashicons-upload';
	}

	/**
	 * Metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {

		?>
		<p><?php _e( 'Select the ACF JSON file that you would like to import. The field groups will be added to the database then can be edited using the field group interface.', 'acf' ); ?></p>
		<div class="acf-fields">
			<?php

			acf_render_field_wrap( [
				'label'    => __('Select File', 'acf'),
				'type'     => 'file',
				'name'     => 'acf_import_file',
				'value'    => false,
				'uploader' => 'basic'
			] );

			?>
		</div>
		<p class="acf-submit">
			<input type="submit" class="button button-primary" value="<?php _e( 'Import File', 'acf' ); ?>" />
		</p>
		<?php
	}

	/**
	 * Submit
	 *
	 * This function will run when the tool's form has been submit.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function submit() {

		// Check file size.
		if ( empty( $_FILES['acf_import_file']['size'] ) ) {
			return acf_add_admin_notice( __( 'No file selected', 'acf' ), 'warning' );
		}

		// Get file data.
		$file = $_FILES['acf_import_file'];

		// Check errors.
		if ( $file['error'] ) {
			return acf_add_admin_notice( __( 'Error uploading file. Please try again', 'acf' ), 'warning' );
		}

		// Check file type.
		if ( pathinfo( $file['name'], PATHINFO_EXTENSION) !== 'json' ) {
			return acf_add_admin_notice( __( 'Incorrect file type', 'acf' ), 'warning' );
		}

		// Read JSON.
		$json = file_get_contents( $file['tmp_name'] );
		$json = json_decode( $json, true );

		// Check if empty.
    	if ( ! $json || ! is_array( $json ) ) {
    		return acf_add_admin_notice( __( 'Import file empty', 'acf' ), 'warning' );
    	}

    	// Ensure $json is an array of groups.
    	if ( isset( $json['key'] ) ) {
	    	$json = [ $json ];
    	}

    	// Remeber imported field group IDs.
    	$ids = [];

    	// Loop over JSON.
    	foreach ( $json as $field_group ) {

	    	// Search database for existing field group.
	    	$post = acf_get_field_group_post( $field_group['key'] );
	    	if ( $post ) {
		    	$field_group['ID'] = $post->ID;
	    	}

	    	// Import field group.
	    	$field_group = acf_import_field_group( $field_group );

	    	// Append message.
	    	$ids[] = $field_group['ID'];
    	}

    	// Count number of imported field groups.
		$total = count( $ids );

		// Generate text.
		$text = sprintf( _n( 'Imported 1 field group', 'Imported %s field groups', $total, 'acf' ), $total );

		// Add links to text.
		$links = [];
		foreach ( $ids as $id ) {
			$links[] = '<a href="' . get_edit_post_link( $id ) . '">' . get_the_title( $id ) . '</a>';
		}
		$text .= ' ' . implode( ', ', $links );

		// Add notice.
		acf_add_admin_notice( $text, 'success' );
	}
}
acf_register_admin_tool( 'ACF_Field_Groups_Import' );
