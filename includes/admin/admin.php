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

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
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

		add_submenu_page(
			'acf',
			__( 'Field Categories', 'acf' ),
			__( 'Field Categories', 'acf' ),
			acf_get_setting( 'capability' ),
			'edit-tags.php?taxonomy=acf-field-group-category'
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
	 * @since  1.0.0
	 * @access public
	 * @param  string $screen
	 * @return void
	 */
	public function current_screen( $screen ) {

		if ( isset( $screen->base ) && $screen->base === 'toplevel_page_acf' ) {
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
		'position' => get_field( 'acf_menu_position', 'option' ),
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
 * Post types grid
 *
 * The HTML markup for the grid of post types
 * on the first, default tab of the content screen.
 *
 * @since  1.0.0
 * @return void
 */
function post_types_grid() {

	// Array of data from relevant post types.
	$types = get_post_type_data();

	// Begin grid wrapping element and list.
	$html = '<div class="acf-tab-grid"><ul>';

	foreach ( $types as $type ) {

		// Post count bubble tooltip.
		$tooltip = $type['count'] . ' ' . __( 'published', 'acf' );
		if ( 'attachment' === $type['slug'] ) {
			$tooltip = $type['count'] . ' ' . __( 'uploads', 'acf' );
		}

		// Post count bubble in grid item heading.
		if ( $type['count'] > 0 ) {
			$count = sprintf(
				'<span class="acf-js-tooltip post-count post-count-has-published" role="tooltip" title="%s">%s</span>',
				$tooltip,
				$type['count']
			);

		// Print an add new link if count is zero.
		} else {
			$count = sprintf(
				'<span class="acf-js-tooltip post-count post-count-none-published" role="tooltip" title="%s"><a href="%s" class="add-new">&plus;</a></span>',
				__( 'Add New', 'acf' ),
				admin_url( 'post-new.php?post_type=' . $type['slug'] )
			);
		}

		// Add new post link, different for `post` and `attachment`.
		$add_new = admin_url( 'post-new.php?post_type=' . $type['slug'] );
		if ( 'post' === $type['slug'] ) {
			$add_new = admin_url( 'post-new.php' );
		} elseif ( 'attachment' === $type['slug'] ) {
			$add_new = admin_url( 'media-new.php' );
		}

		// Manage posts link, different for `post` and `attachment`.
		$manage = admin_url( 'edit.php?post_type=' . $type['slug'] );
		if ( 'post' === $type['slug'] ) {
			$manage = admin_url( 'edit.php' );
		} elseif ( 'attachment' === $type['slug'] ) {
			$manage = admin_url( 'upload.php' );
		}

		// List item markup.
		$html .= '<li>';
		$html .= sprintf(
			'<h3>%s %s</h3>',
			$type['name'],
			$count
		);
		$html .= '<figure>';
		$html .= sprintf(
			'<div class="acf-tab-grid-icon dashicons %s"></div>',
			$type['icon']
		);
		$html .= sprintf(
			'<figcaption><a href="%s">%s</a><a href="%s">%s</a></figcaption>',
			$add_new,
			__( 'Add New', 'acf' ),
			$manage,
			__( 'Manage', 'acf' )
		);
		$html .= '</figure>';
		$html .= '</li>';
	}

	// Field group categories.
	$html .= '<li>';
	$html .= sprintf(
		'<h3>%s</h3>',
		__( 'Field Categories', 'acf' )
	);
	$html .= '<figure>';
	$html .= sprintf(
		'<div class="acf-tab-grid-icon dashicons %s"></div>',
		'dashicons-category'
	);
	$html .= sprintf(
		'<figcaption><a href="%s">%s</a></figcaption>',
		admin_url( 'edit-tags.php?taxonomy=acf-field-group-category' ),
		__( 'Manage', 'acf' )
	);
	$html .= '</figure>';
	$html .= '</li>';

	// End list and wrapping element.
	$html .= '</ul></div>';

	// Print the markup.
	echo $html;
}
add_action( 'acf/post_types_grid', 'post_types_grid' );

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

/**
 * ACF post types
 *
 * Post types registered by ACF and
 * enabled by website options.
 *
 * @since  1.0.0
 * @return array
 */
function acf_builtin_post_types() {

	// Begin array of post types.
	$types = [];

	// If dynamic post types.
	if ( get_field( 'acf_post_types', 'option' ) ) {
		$types = array_merge( $types, [ 'acf-post-type' ] );
	}

	// If dynamic taxonomies.
	if ( get_field( 'acf_taxonomies', 'option' ) ) {
		$types = array_merge( $types, [ 'acf-taxonomy' ] );
	}

	// If dynamic block types.
	if ( ! function_exists( 'classicpress_version' ) ) {
		if ( get_field( 'acf_block_types', 'option' ) ) {
			$types = array_merge( $types, [ 'acf-block-type' ] );
		}
	}

	// If dynamic forms.
	if ( get_field( 'acf_forms', 'option' ) ) {
		$types = array_merge( $types, [ 'acf-form' ] );
	}

	// If dynamic templates.
	if ( get_field( 'acf_templates', 'option' ) ) {
		$types = array_merge( $types, [ 'acf-template' ] );
	}

	// If dynamic options pages.
	if ( get_field( 'acf_options_pages', 'option' ) ) {
		$types = array_merge( $types, [ 'acf-options-page' ] );
	}

	$types = array_merge( $types, [ 'acf-field-group' ] );

	return apply_filters( 'acf_builtin_post_types', $types );
}

/**
 * Custom post types query
 *
 * @since  1.0.0
 * @return array Returns an array of queried post types.
 */
function custom_post_types() {

	// Query public post types not built into the CMS.
	$query = [
		'public'   => true,
		'_builtin' => false
	];

	/**
	 * Return post types query.
	 * Escape namespace for native function.
	 */
	return \get_post_types( $query, 'names', 'and' );
}

/**
 * Get post type
 *
 * Gets registered post types for grid display.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_all_post_types() {

	// Get built-in and enabled ACF types
	$acf_types  = acf_builtin_post_types();

	/**
	 * Native post types
	 *
	 * This array has the opinion that users should
	 * upload and manage media to be published prior
	 * to creating the post or page where the media
	 * file will be used. Thus the `attachment` post
	 * type is first, also placing it first in the
	 * intro page grid.
	 *
	 * If you don't like this, a filter is applied.
	 */
	$native = [ 'attachment', 'post', 'page' ];

	// Get custom post types.
	$custom = custom_post_types();

	// Merge custom post with native types.
	$registered = array_merge( $native, $custom );

	// Merge plugin post types with native and custom types.
	$types = array_merge( $registered, $acf_types );

	return apply_filters( 'acf_get_post_types', $types );
}

/**
 * Get post type data
 *
 * Gets data from the registered post types for grid display.
 *
 * @since  1.0.0
 * @return array
 */
function get_post_type_data() {

	// Get queried post types and set up return fallback.
	$types = acf_get_all_post_types();
	$data  = null;

	// Loop through post types, if any found in custom query.
	if ( $types ) {
		foreach ( $types as $key => $type ) {

			/**
			 * Set up variables.
			 *
			 * $object  Get the post type object
			 * $count   Count the posts of the type.
			 * $publish Number of post with publish status.
			 * $cap     User capability for the post type.
			 * $icon    The default menu if not set.
			 */
			$object  = get_post_type_object( $type );
			$count   = wp_count_posts( $type, '' );
			$publish = $count->publish;
			$cap     = $object->cap->edit_posts;
			$icon    = 'dashicons-admin-post';

			// Override default icon if set for the type.
			if ( $object->menu_icon ) {
				$icon = $object->menu_icon;
			}

			// The attachment post type doesn't use `publish` status.
			if ( 'attachment' === $type ) {
				$publish = $count->inherit;
			}

			// Set return array keys and values.
			$data[$key]['slug']  = $object->name;
			$data[$key]['cap']   = $cap;
			$data[$key]['name']  = $object->labels->menu_name;
			$data[$key]['icon']  = $icon;
			$data[$key]['count'] = intval( $publish );
		}
	}
	return $data;
}
