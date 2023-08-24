<?php
/**
 * Content import/export
 *
 * Adds a metabox to the ACF content tools screen.
 *
 * @package    ACF
 * @subpackage Admin
 * @category   Tools
 * @since      1.0.0
 */

// Restrict direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Content_Import_Export extends ACF_Admin_Tool {

	/**
	 * Menu icon
	 *
	 * @since  1.0.0
	 * @access public
	 * @var string Admin menu icon class.
	 */
	public $icon = '';

	/**
	 * Initialize metabox
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {
		$this->name  = 'import-export-content-types';
		$this->title = __( 'Native Content Tools', 'acf' );
    	$this->icon  = 'dashicons-upload';
	}

	/**
	 * Metabox output
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {

	?>
	<p><?php _e( 'Import and export native content as well as custom post types.', 'acf' ); ?></p>
	<p>
		<a href="<?php echo admin_url( 'import.php' ); ?>" class="button button-primary">
			<?php _e( 'Import Content', 'acf' ); ?>
		</a>
		<a href="<?php echo admin_url( 'export.php' ); ?>" class="button button-primary">
			<?php _e( 'Export Content', 'acf' ); ?>
		</a>
	</p>
	<?php

	}
}

acf_register_admin_tool( 'Content_Import_Export' );
