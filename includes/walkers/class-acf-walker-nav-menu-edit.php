<?php
/**
 * CMS functions
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Walkers
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

    /**
     * Start the element output
     *
     * Calls the Walker_Nav_Menu_Edit start_el function and then injects the custom field HTML
     *
	 * @since  1.0.0
	 * @access public
     * @param  string $output Used to append additional content
	 *                        (passed by reference).
     * @param  object $item Menu item data object.
     * @param  integer $depth Depth of menu item. Used for padding.
     * @param  object $args An object of wp_nav_menu() arguments.
     * @param  integer $id Current item ID.
	 * @return void
     */
	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {

		$item_output = '';

		// Call parent function.
		parent :: start_el( $item_output, $item, $depth, $args, $id );

		// Inject custom field HTML.
		$output .= preg_replace(
			// NOTE: Check this regex from time to time!
			'@/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/',
			$this->get_fields( $item, $depth, $args, $id ),
			$item_output
		);
	}

	/**
	 * Get custom fields HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $item Menu item data object.
	 * @param  integer $depth Depth of menu item. Used for padding.
	 * @param  array $args Menu item args.
	 * @param  integer $id Nav menu ID.
	 * @return string
	 */
	public function get_fields( $item, $depth, $args = [], $id = 0 ) {

		ob_start();

		/**
         * Get menu item custom fields from plugins/themes
         *
         * @param integer $item_id post ID of menu
         * @param object $item Menu item data object.
         * @param integer $depth Depth of menu item. Used for padding.
         * @param array $args Menu item args.
         * @param integer $id Nav menu ID.
         */
		do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args, $id );
		return ob_get_clean();
	}
}
