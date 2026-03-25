<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ACF_Admin' ) ) :
class ACF_Admin {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_body_class', [ $this, 'admin_body_class' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );
	}

	/**
	 * Admin pages
	 *
	 * Adds the ACF pages & menu items.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		// Bail early if ACF is hidden.
		if ( ! acf_get_setting( 'show_admin' ) ) {
			return;
		}

		$slug = 'edit.php?post_type=acf-field-group';
		$cap  = acf_get_setting( 'capability' );

		// Add menu items.
		add_menu_page(
			__( 'Custom Fields','acf' ),
			__( 'Custom Fields','acf' ),
			$cap,
			$slug,
			false,
			'dashicons-welcome-widgets-menus',
			'80.025'
		);
		add_submenu_page(
			$slug,
			__( 'Field Groups','acf' ),
			__( 'Field Groups','acf' ),
			$cap,
			$slug
		);
		add_submenu_page(
			$slug, __( 'Add New','acf' ),
			__( 'Add New','acf' ),
			$cap,
			'post-new.php?post_type=acf-field-group'
		);
	}

	/**
	 * Enqueues global admin styling.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'acf-global' );
	}

	/**
	 * Appends custom admin body classes.
	 *
	 * @date	5/11/19
	 * @since	5.8.7
	 *
	 * @param	string $classes Space-separated list of CSS classes.
	 * @return	string
	 */
	public function admin_body_class( $classes ) {
		global $wp_version;

		// Determine body class version.
		$wp_minor_version = floatval( $wp_version );
		if( $wp_minor_version >= 5.3 ) {
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
	 * Current screen
	 *
	 * Adds custom functionality to "ACF" admin pages.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $screen
	 * @return void
	 */
	public function current_screen( $screen ) {

		// Determine if the current page being viewed is "ACF" related.
		if( isset( $screen->post_type ) && $screen->post_type === 'acf-field-group' ) {
			add_action( 'in_admin_header',		array( $this, 'in_admin_header' ) );
			add_filter( 'admin_footer_text',	array( $this, 'admin_footer_text' ) );
			$this->setup_help_tab();
		}
	}

	/**
	 * Sets up the admin help tab.
	 *
	 * @date	20/4/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function setup_help_tab() {
		$screen = get_current_screen();

		// Overview tab.
		$screen->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'acf' ),
				'content' =>
					'<p><strong>' . __( 'Overview', 'acf' ) . '</strong></p>' .
					'<p>' . __( 'The Applied Content Forms plugin provides a visual form builder to customize WordPress edit screens with extra fields, and an intuitive API to display custom field values in any theme template file.', 'acf' ) . '</p>' .
					'<p>' . sprintf(
						__( 'Before creating your first Field Group, we recommend first reading our <a href="%s" target="_blank">Getting started</a> guide to familiarize yourself with the plugin\'s philosophy and best practises.', 'acf' ),
						'https://www.advancedcustomfields.com/resources/getting-started-with-acf/'
					) . '</p>' .
					'<p>' . __( 'Please use the Help & Support tab to get in touch should you find yourself requiring assistance.', 'acf' ) . '</p>' .
					''
			)
		);

		// Help tab.
		$screen->add_help_tab(
			array(
				'id'      => 'help',
				'title'   => __( 'Help & Support', 'acf' ),
				'content' =>
					'<p><strong>' . __( 'Help & Support', 'acf' ) . '</strong></p>' .
					'<p>' . __( 'We are fanatical about support, and want you to get the best out of your website with ACF. If you run into any difficulties, there are several places you can find help:', 'acf' ) . '</p>' .
					'<ul>' .
						'<li>' . sprintf(
							__( '<a href="%s" target="_blank">Documentation</a>. Our extensive documentation contains references and guides for most situations you may encounter.', 'acf' ),
							'https://www.advancedcustomfields.com/resources/'
						) . '</li>' .
						'<li>' . sprintf(
							__( '<a href="%s" target="_blank">Discussions</a>. We have an active and friendly community on our Community Forums who may be able to help you figure out the ‘how-tos’ of the ACF world.', 'acf' ),
							'https://support.advancedcustomfields.com/'
						) . '</li>' .
						'<li>' . sprintf(
							__( '<a href="%s" target="_blank">Help Desk</a>. The support professionals on our Help Desk will assist with your more in depth, technical challenges.', 'acf' ),
							'https://www.advancedcustomfields.com/support/'
						) . '</li>' .
					'</ul>'
			)
		);

		// Sidebar.
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'Information', 'acf' ) . '</strong></p>' .
			'<p><span class="dashicons dashicons-admin-plugins"></span> ' . sprintf( __( 'Version %s', 'acf' ), ACF_VERSION ) . '</p>' .
			'<p><span class="dashicons dashicons-wordpress"></span> <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">' . __( 'View details', 'acf' ) . '</a></p>' .
			'<p><span class="dashicons dashicons-admin-home"></span> <a href="https://www.advancedcustomfields.com/" target="_blank" target="_blank">' . __( 'Visit website', 'acf' ) . '</a></p>' .
			''
		);
	}

	/**
	 * Renders the admin navigation element.
	 *
	 * @date	27/3/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function in_admin_header() {}

	/**
	 * Modifies the admin footer text.
	 *
	 * @date	7/4/20
	 * @since	5.9.0
	 *
	 * @param	string $text The admin footer text.
	 * @return	string
	 */
	public function admin_footer_text( $text ) {
		// Use RegExp to append "ACF" after the <a> element allowing translations to read correctly.
		return preg_replace( '/(<a[\S\s]+?\/a>)/', '$1 ' . __('and', 'acf') . ' <a href="https://www.advancedcustomfields.com" target="_blank">ACF</a>', $text, 1 );
	}
}

// Instantiate.
acf_new_instance( 'ACF_Admin' );

endif; // class_exists check.

/**
 * Menu entry
 *
 * Configures the top-level entry
 * in the admin menu.
 *
 * @since  1.0.0
 * @return array Returns an array of options.
 */
function acf_admin_menu() {

	$menu = [
		'slug'     => 'acf',
		'icon'     => 'dashicons-edit',
		'position' => acf_get_setting( 'menu_position' ),
		'title'    => __( 'Website Content', 'acf' ),
		'menu'     => __( 'Content', 'acf' )
	];
	return apply_filters( 'acf_admin_menu', $menu );
}

/**
 * Admin page
 *
 * @since  1.0.0
 * @return void
 */
function acf_admin_page() {

	// Get filtered menu options.
	$menu = acf_admin_menu();

	// Add primary menu entry.
	$page = add_menu_page(
		$menu['title'],
		$menu['menu'],
		acf_get_setting( 'capability' ),
		$menu['slug'],
		'acf_content_page',
		$menu['icon'],
		$menu['position'],
	);
	// add_action( "load-{$page}", 'acf_page_load' );
}
add_action( 'admin_menu', 'acf_admin_page', 9 );

/**
 * ACF content page
 *
 * @since  1.0.0
 * @return void
 */
function acf_content_page() {
	acf_get_view( 'acf-content-page' );
}

function acf_add_settings_page() {

	$menu = acf_admin_menu();
	acf_add_options_page( [
		'page_title'    => __( 'Content Settings', 'acf' ),
		'page_desc'     => __( 'Choose which content features to use and how to use them.', 'acf' ),
		'menu_title'    => __( 'Settings', 'acf' ),
		'menu_slug'     => 'content-settings',
		'parent'        => $menu['slug'],
		'capability'    => 'manage_options',
		'redirect'      => false
	] );
}
add_action( 'acf/init', 'acf_add_settings_page' );
