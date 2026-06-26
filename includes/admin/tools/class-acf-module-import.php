<?php
/**
 * ACF module import tool parent class
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

class ACF_Module_Import extends ACF_Admin_Tool {

	/**
	 * Tool instance
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object
	 */
	public $instance;

	/**
	 * Hook
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
    public $hook;

	/**
	 * Tool description
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
    public $description;

	/**
	 * Messages
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
    public $messages = [];

	/**
	 * Metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
    public function html() {

        ?>
        <p><?php echo $this->description; ?></p>

        <div class="acf-fields">
            <?php
            acf_render_field_wrap( [
                'label'     => __( 'Select File', 'acf' ),
                'type'      => 'file',
                'name'      => 'acf_import_file',
                'value'     => false,
                'uploader'  => 'basic'
            ] );
            ?>
        </div>
        <p class="acf-submit">
            <button type="submit" name="action" class="button button-primary"><?php _e( 'Import File', 'acf' ); ?></button>
        </p>
        <?php
    }

	/**
	 * Submit tool action
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
    public function submit() {

        $json = $this->validate_file();
        if ( ! $json ) {
            return;
        }
        $ids = [];
        foreach ( $json as $name => $args ) {

            $post_id = $this->instance->import( $name, $args );

            if ( is_wp_error( $post_id ) ) {
                acf_add_admin_notice( $post_id->get_error_message(), 'warning' );

                continue;
            }
            $ids[] = $post_id;
        }

        if ( empty( $ids ) ) {
			return;
		}
        $total = count( $ids );
        $text  = sprintf( _n( $this->messages['success_single'], $this->messages['success_multiple'], $total, 'acf' ), $total );

        // Add links to text.
        $links = [];
        foreach ( $ids as $id ) {
            $links[] = '<a href="' . get_edit_post_link( $id ) . '">' . get_the_title( $id ) . '</a>';
        }
        $text .= ': ' . implode( ', ', $links );

        acf_add_admin_notice( $text, 'success' );
        do_action( "acfe/{$this->hook}/import", $ids, $json );
    }

	/**
	 * Validate file
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed
	 */
    public function validate_file() {

        // Check file size.
        if ( empty( $_FILES['acf_import_file']['size'] ) ) {
            acf_add_admin_notice( __( 'No file selected.', 'acf' ), 'warning' );
            return false;
        }

        // Get file data.
        $file = $_FILES['acf_import_file'];
        if ( $file['error'] ) {
            acf_add_admin_notice( __( 'Error uploading file. Please try again.', 'acf' ), 'warning' );
            return false;
        }

        if ( pathinfo( $file['name'], PATHINFO_EXTENSION) !== 'json' ) {
            acf_add_admin_notice( __( 'Incorrect file type', 'acf' ), 'warning' );
            return false;
        }

        // Read JSON.
        $json = file_get_contents( $file['tmp_name'] );
        $json = json_decode( $json, true );

        // Check if empty.
        if ( ! $json || ! is_array( $json ) ) {
            acf_add_admin_notice( __( 'Import file empty', 'acf' ), 'warning' );
            return false;
        }
        return $json;
    }
}
