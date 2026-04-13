<?php
/**
 * ACF options page
 *
 * @package    Applied Content Forms
 * @subpackage Pro
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class acf_options_page {

	/**
	 * Page settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $pages = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	function __construct() {}

	/**
	 * Validates an Options Page settings array.
	 *
	 * @since	1.0.0
	 * @access public
	 * @param  array|string $page The options page settings array or name.
	 * @return array
	 */
	public function validate_page( $page ) {

		// Allow empty arg to generate the default options page.
		if ( empty( $page ) ) {
			$page_title =  __( 'Options', 'acf' );
			$page_desc  = '';
			$page = [
				'page_title' => $page_title,
				'page_desc'  => $page_desc,
				'menu_title' => $page_title,
				'menu_slug'  => 'acf-options'
			];

		// Allow string to define options page name.
		} elseif ( is_string( $page ) ) {
			$page_title = $page;
			$page_desc  = '';
			$page = [
				'page_title' => $page_title,
				'page_desc'  => $page_desc,
				'menu_title' => $page_title
			];
		}

		// Apply defaults.
		$page = wp_parse_args( $page, [
			'page_title'      => '',
			'page_desc'       => '',
			'menu_title'      => '',
			'menu_slug'       => '',
			'capability'      => 'edit_posts',
			'parent_slug'     => '',
			'position'        => null,
			'icon_url'        => false,
			'redirect'        => true,
			'post_id'         => 'options',
			'autoload'        => false,
			'update_location' => 'side', // `side` or `bottom`
			'update_title'    => __( 'Publish','acf' ),
			'update_button'   => __( 'Update', 'acf' ),
			'update_message'  => __( 'Options Updated', 'acf' ),
			'before_form'     => false,
			'after_form'      => false
		] );
		$menu_slug       = $page['menu_slug'];
		$update_location = $page['update_location'];
		$update_button   = $page['update_button'];

		// Allow compatibility for changed settings.
		$migrate = [
			'title'  => 'page_title',
			'desc'   => 'page_desc',
			'menu'   => 'menu_title',
			'slug'   => 'menu_slug',
			'parent' => 'parent_slug'
		];
		foreach ( $migrate as $old => $new ) {
			if ( ! empty( $page[ $old ] ) ) {
				$page[ $new ] = $page[ $old ];
			}
		}

		// If no menu_title is set, use the page_title value.
		if ( empty( $page['menu_title'] ) ) {
			$page['menu_title'] = $page['page_title'];
		}

		// If no menu_slug is set, generate one using the menu_title value.
		if ( empty( $page['menu_slug'] ) ) {
			$page['menu_slug'] = 'acf-options-' . sanitize_title( $page['menu_title'] );
		}

		// Filters the $page array after it has been validated.
		return apply_filters( 'acf/validate_options_page', $page );
	}

	/**
	 * Add page
	 *
	 * @since	1.0.0
	 * @access public
	 * @param  array $page The page arguments array.
	 * @return array
	 */
	public function add_page( $page ) {

		// Validate.
		$page = $this->validate_page( $page );
		$slug = $page['menu_slug'];


		// Stop if the page already exists.
		if ( isset( $this->pages[$slug] ) ) {
			return false;
		}

		$this->pages[$slug] = $page;
		return $page;
	}

	/**
	 * Add subpage
	 *
	 * @since	1.0.0
	 * @access public
	 * @param  array $page The page arguments array.
	 * @return array
	 */
	public function add_sub_page( $page ) {

		// Validate.
		$page = $this->validate_page( $page );

		// Default parent.
		if ( ! $page['parent_slug'] ) {
			$page['parent_slug'] = 'acf-options';
		}

		// Create a default parent if it doesn't exist.
		if ( $page['parent_slug'] == 'acf-options' && ! $this->get_page( 'acf-options' ) ) {
			$this->add_page( '' );
		}
		return $this->add_page( $page );
	}

	/**
	 * Update page
	 *
	 * @since	1.0.0
	 * @access public
	 * @param  string $slug The page slug.
	 * @param  array $data The page data array.
	 * @return array
	 */
	public function update_page( $slug = '', $data = [] ) {

		$page = $this->get_page( $slug );

		// Stop if no page.
		if ( ! $page ) {
			return false;
		}

		$page = array_merge( $page, $data );

		$this->pages[ $slug ] = $page;
		return $page;
	}

	/**
	 * Get page
	 *
	 * @since	1.0.0
	 * @access public
	 * @param  string $slug The page slug.
	 * @return mixed
	 */
	public function get_page( $slug ) {
		return isset( $this->pages[$slug] ) ? $this->pages[$slug] : null;
	}

	/**
	 * Get pages
	 *
	 * @since	1.0.0
	 * @access public
	 * @return mixed
	 */
	public function get_pages() {
		return $this->pages;
	}
}

/**
 *  Instantiate the class
 *
 *  This function will return the options page instance.
 *
 *  @since	1.0.0
 *  @return object
 */
 function acf_options_page() {

	global $acf_options_page;
	if ( ! isset( $acf_options_page ) ) {
		$acf_options_page = new acf_options_page();
	}
	return $acf_options_page;
}

// Remove Options Page add-on conflict
unset( $GLOBALS['acf_options_page'] );

// Initialize.
acf_options_page();

/**
 *  An alias of acf_options_page()->add_page()
 *
 * @since  1.0.0
 * @param  $page mixed
 * @return array
 */
function acf_add_options_page( $page = '' ) {
	return acf_options_page()->add_page( $page );
}

/**
 * An alias of acf_options_page()->add_sub_page()
 *
 * @since  1.0.0
 * @param  string $page
 * @return array
 */
function acf_add_options_sub_page( $page = '' ) {
	return acf_options_page()->add_sub_page( $page );
}

/**
 * An alias of acf_options_page()->update_page()
 *
 * @since  1.0.0
 * @param  string $slug
 * @param  array  $data
 * @return array
 */
function acf_update_options_page( $slug = '', $data = [] ) {
	return acf_options_page()->update_page( $slug, $data );
}

/**
 * Get options page
 *
 * Returns an options page's settings
 *
 * @since  1.0.0
 * @param  string $slug
 * @return array
 */
function acf_get_options_page( $slug ) {

	$page = acf_options_page()->get_page( $slug );

	// Stop if no page.
	if ( ! $page ) {
		return false;
	}
	return apply_filters( 'acf/get_options_page', $page, $slug );
}

/**
 * Get options pages
 *
 * Returns all options page settings.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_options_pages() {

	// Access global variables.
	global $_wp_last_utility_menu;

	$pages = acf_options_page()->get_pages();
	if ( empty( $pages ) ) {
		return false;
	}

	// Apply a filter to each page.
	foreach( $pages as $slug => &$page ) {
		$page = acf_get_options_page( $slug );
	}

	// Calculate parent => child redirects.
	foreach( $pages as $slug => &$page ) {

		// Stop if is a child.
		if ( $page['parent_slug'] ) {
			continue;
		}

		// Add missing position.
		if ( ! $page['position'] ) {
			$_wp_last_utility_menu++;
			$page['position'] = $_wp_last_utility_menu;
		}

		// Stop if no redirect.
		if ( ! $page['redirect'] ) {
			continue;
		}

		$parent = $page['menu_slug'];
		$child  = '';

		// Update children.
		foreach( $pages as &$sub_page ) {

			// Stop if not child of this parent.
			if ( $sub_page['parent_slug'] !== $parent ) {
				continue;
			}

			// Set child (only once).
			if ( ! $child ) {
				$child = $sub_page['menu_slug'];
			}

			// Update parent_slug to the first child.
			$sub_page['parent_slug'] = $child;
		}

		// Finally, update parent menu_slug.
		if ( $child ) {
			$page['menu_slug'] = $child;
		}
	}
	return apply_filters( 'acf/get_options_pages', $pages );
}

/**
 * Set options page title
 *
 * @since  1.0.0
 * @param  string $title
 * @return void
 */
function acf_set_options_page_title( $title = '' ) {

	$title = __( 'Options' );
	acf_update_options_page( 'acf-options', [
		'page_title' => $title,
		'menu_title' => $title
	] );
}

/**
 * Options page menu name
 *
 * @since  1.0.0
 * @param  string $title
 * @return void
 */
function acf_set_options_page_menu( $title = '' ) {

	$title = __( 'Options' );
	acf_update_options_page( 'acf-options', [
		'menu_title' => $title
	] );
}

/**
 * Set options page capability
 *
 * Used to customize the options page capability.
 * Defaults to 'edit_posts'.
 *
 * @since  1.0.0
 * @param  string $capability
 * @return void
 */
function acf_set_options_page_capability( $capability = 'edit_posts' ) {
	acf_update_options_page( 'acf-options', [
		'capability' => $capability
	] );
}

/**
 * Register options page
 *
 * This is an old function which is now referencing
 * the 'acf_add_options_sub_page' function.
 *
 * @param  string $page
 * @return void
 */
function register_options_page( $page = '' ) {
	acf_add_options_sub_page( $page );
}
