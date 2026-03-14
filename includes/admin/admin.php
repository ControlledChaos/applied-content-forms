<?php
/**
 * Admin screens
 *
 * Adds pages, adds help tabs,
 * enqueues assets.
 *
 * @package  ACF
 * @category Admin
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin screens
 *
 * Adds custom functionality to this plugin's admin screens.
 *
 * @since  5.9.0
 * @global array $pagenow Array of admin screens.
 * @return void
 */
function acf_current_screen( $screen ) {

	global $pagenow;

	// Get filtered menu options.
	$menu = acf_admin_menu();

	// Add help tabs to the intro screen.
	if ( isset( $_GET['page'] ) ) {
		if ( in_array( $pagenow, array( 'admin.php' ) ) && ( $_GET['page'] == $menu['slug'] || $_GET['page'] == $menu['slug'] ) ) {
			setup_help_tab();
		}
	}

	// Add help tabs to field group screens.
	if ( isset( $screen->post_type ) && $screen->post_type === 'acf-field-group' ) {
		setup_help_tab();
	}
}
add_action( 'current_screen', 'acf_current_screen' );

/**
 * Enqueues global admin styling.
 *
 * @since  5.0.0
 * @return void
 */
function acf_admin_enqueue_scripts() {
	wp_enqueue_style( 'acf-global' );
}
add_action( 'admin_enqueue_scripts', 'acf_admin_enqueue_scripts' );

/**
 * Appends custom admin body classes.
 *
 * @since  5.8.7
 * @param  string $classes Space-separated list of CSS classes.
 * @return string
 */
function acf_admin_body_class( $classes ) {

	// Get the platform version.
	global $wp_version;

	// Determine body class version.
	$wp_minor_version = floatval( $wp_version );
	if ( $wp_minor_version >= 5.3 ) {
		$classes .= ' acf-admin-5-3';
	} else {
		$classes .= ' acf-admin-3-8';
	}

	// Add browser for specific CSS.
	$classes .= ' acf-browser-' . acf_get_browser();

	// Return classes.
	return $classes;
}
add_action( 'admin_body_class', 'acf_admin_body_class' );

/**
 * Admin page
 *
 * @since  5.0.0
 * @return void
 */
function acf_admin_page() {

	// Get filtered menu options.
	$menu = acf_admin_menu();

	// Add primary menu entry.
	$page = add_menu_page(
		$menu['page'],
		$menu['name'],
		acf_get_setting( 'capability' ),
		$menu['slug'],
		'acf_settings_page',
		$menu['icon'],
		$menu['position'],
	);
	add_action( "load-{$page}", 'acf_page_load' );
}
add_action( 'admin_menu', 'acf_admin_page', 9 );

/**
	* Settings page
	*
	* Gets the markup for the plugin's
	* intro/settings page; top level in
	* the admin menu.
	*
	* @since  6.0.0
	* @return void
	*/
function acf_settings_page() {
	acf_get_view( 'html-admin-page-intro' );
}

/**
 * Page Load
 *
 * @since  6.0.0
 * @return void
 */
function acf_page_load() {

	do_action( 'acfe/admin_settings/load' );

	if ( ! isset( $_GET['tab'] ) ) {
		add_action(
			'admin_enqueue_scripts', function() {

				$version = acf_get_setting( 'version' );
				$suffix  = '';
				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$suffix  = '.min';
				}

				wp_enqueue_style( 'acf-intro', acf_get_url( 'assets/css/intro-page' . $suffix . '.css' ), [], $version, 'screen' );
			}
		);
	}
}

/**
 * Sets up the admin help tab.
 *
 * @since  5.9.0
 * @return void
 */
function setup_help_tab() {

	$screen = get_current_screen();

	// Overview tab.
	$screen->add_help_tab(
		[
			'id'      => 'overview',
			'title'   => __( 'Overview', 'acf' ),
			'content' =>
				'<h4>' . __( 'Overview', 'acf' ) . '</h4>' .
				'<p>' . __( 'The Applied Content Forms plugin provides a visual form builder to customize edit screens with extra fields, and an intuitive API to display custom field values in any theme template file. This began as a fork of Advanced Custom Fields PRO version 5.9.6, the last version developed by Eliot Condon before selling the plugin to Delicious Brains.', 'acf' ) . '</p>' .
				'<p>' . sprintf(
					__( 'Before creating your first Field Group it is recommended to first read the <a href="%s" target="_blank" rel="noopener noreferrer">Getting Started</a> guide at Advanced Custom Fields to familiarize yourself with the plugin\'s philosophy and best practices.', 'acf' ),
					'https://www.advancedcustomfields.com/resources/getting-started-with-acf/'
				) . '</p>'
		]
	);

	// Help tab.
	$screen->add_help_tab(
		[
			'id'      => 'help',
			'title'   => __( 'Help & Support', 'acf' ),
			'content' =>
				'<h4>' . __( 'Help & Support', 'acf' ) . '</h4>' .
				'<p>' . __( 'There are several places you can find help at the Advanced Custom Fields website:', 'acf' ) . '</p>' .
				'<ul>' .
					'<li>' . sprintf(
						__( '<a href="%s" target="_blank">Documentation</a>. Extensive documentation contains references and guides for most situations you may encounter.', 'acf' ),
						'https://www.advancedcustomfields.com/resources/'
					) . '</li>' .
					'<li>' . sprintf(
						__( '<a href="%s" target="_blank">Discussions</a>. An active and friendly community on the Community Forums who may be able to help you figure out the how-tos of the ACF world.', 'acf' ),
						'https://support.advancedcustomfields.com/'
					) . '</li>' .
				'</ul>'
		]
	);
}

function acf_add_settings_page() {

	$menu = acf_admin_menu();
	acf_add_options_page( [
		'page_title'    => __( 'Content Settings', 'acf' ),
		'menu_title'    => __( 'Settings', 'acf' ),
		'menu_slug'     => 'content-settings',
		'parent'        => $menu['slug'],
		'capability'    => 'edit_posts',
		'redirect'      => false
	] );
}
add_action( 'acf/init', 'acf_add_settings_page' );
