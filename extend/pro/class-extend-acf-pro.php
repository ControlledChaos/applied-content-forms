<?php
/**
 * Extend ACF
 *
 * @package    Applied Content Forms
 * @subpackage Extend
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Extend_ACF_Pro {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	function __construct() {

		// Functions.
		acfe_include( 'pro/includes/acfe-helper-functions.php' );
		acfe_include( 'pro/includes/acfe-script-functions.php' );
		acfe_include( 'pro/includes/acfe-template-functions.php' );
		acfe_include( 'pro/includes/acfe-world-functions.php' );
		acfe_include( 'pro/includes/payment.php' );
		acfe_include( 'pro/includes/world.php' );

		// Compatibility.
		acfe_include( 'pro/includes/compatibility.php' );

		// Admin.
		acfe_include( 'pro/includes/admin/menu.php' );
		acfe_include( 'pro/includes/admin/settings.php' );

		// Modules.
		acfe_include( 'pro/includes/modules/block-types.php' );
		acfe_include( 'pro/includes/modules/forms.php' );
		acfe_include( 'pro/includes/modules/options-pages.php' );
		acfe_include( 'pro/includes/modules/post-types.php' );
		acfe_include( 'pro/includes/modules/taxonomies.php' );
		acfe_include( 'pro/includes/modules/scripts-class.php' );
	}
}
function acfe_pro() {

	// Set a global variable.
	global $acfe_pro;

	// Instantiate only once.
	if ( ! isset( $acfe_pro ) ) {
		$acfe_pro = new Extend_ACF_Pro();
	}
	return $acfe_pro;
}
acfe_pro();
