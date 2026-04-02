<?php
/**
 * Update ACF settings
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF settings update
 *
 * Updates core settings per the field group
 * on the Content Settings screen.
 *
 * @since  1.0.0
 * @return void
 */
function acf_settings_update() {
	acf_update_setting( 'post_types', get_field( 'acf_post_types', 'option' ) );
	acf_update_setting( 'taxonomies', get_field( 'acf_taxonomies', 'option' ) );
	acf_update_setting( 'block_types', get_field( 'acf_block_types', 'option' ) );
	acf_update_setting( 'forms', get_field( 'acf_forms', 'option' ) );
	acf_update_setting( 'templates', get_field( 'acf_templates', 'option' ) );
	acf_update_setting( 'options_pages', get_field( 'acf_options_pages', 'option' ) );
}
add_action( 'acf/init', 'acf_settings_update' );
