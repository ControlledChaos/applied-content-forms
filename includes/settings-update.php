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

	// Features.
	acf_update_setting( 'post_types', get_field( 'acf_post_types', 'option' ) );
	acf_update_setting( 'taxonomies', get_field( 'acf_taxonomies', 'option' ) );
	acf_update_setting( 'block_types', get_field( 'acf_block_types', 'option' ) );
	acf_update_setting( 'forms', get_field( 'acf_forms', 'option' ) );
	acf_update_setting( 'templates', get_field( 'acf_templates', 'option' ) );
	acf_update_setting( 'options_pages', get_field( 'acf_options_pages', 'option' ) );

	// Tools.
	acf_update_setting( 'menu_position', get_field( 'acf_menu_position', 'option' ) );
	acf_update_setting( 'field_group_ui', get_field( 'acf_group_settings_tabbed', 'option' ) );
	acf_update_setting( 'options_editor', get_field( 'acf_options_editor', 'option' ) );
	acf_update_setting( 'multilang', get_field( 'acf_multilang_compat', 'option' ) );
	acf_update_setting( 'dev_mode', get_field( 'acf_dev_mode', 'option' ) );
}
add_action( 'acf/init', 'acf_settings_update' );
