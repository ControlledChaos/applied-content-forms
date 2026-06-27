<?php
/**
 * Native content import/export
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Tools
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

final class ACF_Native_Export_Import extends ACF_Admin_Tool {

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
		$this->name  = 'import-export-content-types';
		$this->title = __( 'Native Content Tools', 'acf' );
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
	<p><?php _e( 'Import and export native CMS content, such as posts & pages, and media, as well as custom post types.', 'acf' ); ?></p>
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
acf_register_admin_tool( 'ACF_Native_Export_Import' );
