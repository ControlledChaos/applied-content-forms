<?php
/**
 * Options page class
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

class ACF_Options_Page {

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
 * Instantiate the class
 *
 * This function will return the options page instance.
 *
 * @since	1.0.0
 * @global object $acf_options_page
 * @return object
 */
 function acf_options_page() {

	// Access global variables.
	global $acf_options_page;

	if ( ! isset( $acf_options_page ) ) {
		$acf_options_page = new ACF_Options_Page();
	}
	return $acf_options_page;
}

// Remove Options Page add-on conflict
unset( $GLOBALS['acf_options_page'] );

// Initialize.
acf_options_page();
