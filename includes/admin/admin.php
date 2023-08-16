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

namespace ACF\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Screens {

	/**
	 * Constructor method
	 *
	 * @date   23/06/12
	 * @since  5.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'admin_menu', [ $this, 'admin_menu' ], 9 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_body_class', [ $this, 'admin_body_class' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );
	}

	/**
	 * Adds the ACF menu item.
	 *
	 * @date   28/09/13
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		// Stop if ACF is hidden.
		if ( ! acf_get_setting( 'show_admin' ) ) {
			return;
		}

		// Get filtered menu options.
		$menu = acf_admin_menu();

		// Add primary menu entry.
		add_menu_page(
			$menu['page'],
			$menu['name'],
			acf_get_setting( 'capability' ),
			$menu['slug'],
			[ $this, 'settings_page' ],
			$menu['icon'],
			$menu['position'],
		);
	}

	/**
	 * Settings page
	 *
	 * Gets the markup for the plugin's
	 * intro/settings page; top level in
	 * the admin menu.
	 *
	 * @since  6.0.0
	 * @access public
	 * @return void
	 */
	public function settings_page() {
		acf_get_view( 'content-settings-page' );
	}

	/**
	 * Enqueues global admin styling.
	 *
	 * @date   28/09/13
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'acf-global' );
	}

	/**
	 * Appends custom admin body classes.
	 *
	 * @date   5/11/19
	 * @since  5.8.7
	 * @access public
	 * @param  string $classes Space-separated list of CSS classes.
	 * @return string
	 */
	public function admin_body_class( $classes ) {

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

	/**
	 * Admin screens
	 *
	 * Adds custom functionality to this plugin's admin screens.
	 *
	 * @date   7/4/20
	 * @since  5.9.0
	 * @access public
	 * @global array $pagenow Array of admin screens.
	 * @return void
	 */
	public function current_screen( $screen ) {

		global $pagenow;

		// Add help tabs to the intro screen.
		if ( isset( $_GET['page'] ) ) {
			if ( in_array( $pagenow, array( 'admin.php' ) ) && ( $_GET['page'] == 'acf' || $_GET['page'] == 'acf' ) ) {
				$this->setup_help_tab();
			}
		}

		// Add help tabs to field group screens.
		if ( isset( $screen->post_type ) && $screen->post_type === 'acf-field-group' ) {
			$this->setup_help_tab();
		}
	}

	/**
	 * Sets up the admin help tab.
	 *
	 * @date   20/4/20
	 * @since  5.9.0
	 * @access public
	 * @return void
	 */
	public function setup_help_tab() {

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
						__( 'Before creating your first Field Group it is recommended to first read the <a href="%s" target="_blank" rel="noopener nofollow">Getting Started</a> guide at Advanced Custom Fields to familiarize yourself with the plugin\'s philosophy and best practices.', 'acf' ),
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
}

// Instantiate.
acf_new_instance( 'ACF\Admin\Admin_Screens' );
