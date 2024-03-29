<?php
/**
 * Applied Content Forms
 *
 * Content editing for WordPress and ClassicPress.
 *
 * @package  ACF
 * @category Core
 * @since    1.0.0
 * @link     https://github.com/ControlledChaos/applied-content-forms
 *
 * Plugin Name:       Applied Content Forms
 * Plugin URI:        https://github.com/ControlledChaos/applied-content-forms
 * Description:       Content editing for WordPress and ClassicPress.
 * Version:           5.9.6
 * Author:            Elliot Condon + Controlled Chaos Design
 * Author URI:        https://github.com/ControlledChaos/
 * Text Domain:       acf
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 4.9
 * Tested up to:      6.3
 * Network:           false
 */

namespace ACF;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$get_plugin = ABSPATH . 'wp-admin/includes/plugin.php';
if ( file_exists( $get_plugin ) && ! function_exists( 'is_plugin_active' ) ) {
	include_once( $get_plugin );
}

// Stop if Advanced Custom Fields/Extended are active.
if (
	is_plugin_active( 'advanced-custom-fields/acf.php' ) ||
	is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ||
	is_plugin_active( 'acf-extended/acf-extended.php' ) ||
	is_plugin_active( 'acf-extended-pro/acf-extended.php' )
) {
	return;
}

class ACF {

	/**
	 * Plugin version
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string The plugin version number.
	 */
	public $version = '5.9.6';

	/**
	 * Plugin settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array The plugin settings array.
	 */
	public $settings = [];

	/**
	 * Plugin data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array The plugin data array.
	 */
	public $data = [];

	/**
	 * Class instances
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array Storage for class instances.
	 */
	public $instances = [];

	/**
	 * Fields object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object ACF_Fields
	 */
	public $fields;

	/**
	 * Loop object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object ACF_Loop
	 */
	public $loop;

	/**
	 * Revisions object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object ACF_Revisions
	 */
	public $revisions;

	/**
	 * Validation object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object ACF_Validation
	 */
	public $validation;

	/**
	 * Frontend form object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object ACF_Form_Front
	 */
	public $form_front;

	/**
	 * Admin tools object
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object ACF_Admin_Tools
	 */
	public $admin_tools;

	/**
	 * Constructor method
	 *
	 * @since  5.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'acf/input/admin_enqueue_scripts', [ $this, 'input_admin_enqueue_scripts' ] );
		add_action( 'acf/field_group/admin_enqueue_scripts', [ $this, 'field_group_admin_enqueue_scripts' ] );
	}

	/**
	 * Initialize the plugin
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$define = function( $array = [] ) {
			foreach ( $array as $name => $value ) {
				if ( defined( $name ) ) {
					continue;
				}
				define( $name, $value );
			}
		};

		// Define constants.
		$define( [
			'ACF'               => true,
			'ACF_PRO'           => true,
			'ACF_PATH'          => plugin_dir_path( __FILE__ ),
			'ACF_BASENAME'      => plugin_basename( __FILE__ ),
			'ACF_VERSION'       => $this->version,
			'ACF_MAJOR_VERSION' => 5,
			'ACFE_PATH'         => plugin_dir_path( __FILE__ ) . 'extend/'
		] );

		// Define settings.
		$this->settings = apply_filters(
			'acf_settings',
			[
				'name'                   => __( 'Applied Content Forms', 'acf' ),
				'site'                   => 'https://github.com/ControlledChaos/applied-content-forms',
				'slug'                   => dirname( ACF_BASENAME ),
				'version'                => ACF_VERSION,
				'basename'               => ACF_BASENAME,
				'path'                   => ACF_PATH,
				'file'                   => __FILE__,
				'url'                    => plugin_dir_url( __FILE__ ),
				'pro'                    => true,
				'fork'                   => true,
				'show_admin'             => true,
				'menu_position'          => 2,
				'stripslashes'           => false,
				'local'                  => true,
				'json'                   => true,
				'save_json'              => '',
				'load_json'              => [],
				'default_language'       => '',
				'current_language'       => '',
				'capability'             => 'manage_options',
				'uploader'               => 'wp',
				'autoload'               => false,
				'l10n'                   => true,
				'l10n_textdomain'        => '',
				'google_api_key'         => '',
				'google_api_client'      => '',
				'enqueue_google_maps'    => true,
				'enqueue_select2'        => true,
				'enqueue_datepicker'     => true,
				'enqueue_datetimepicker' => true,
				'select2_version'        => 4,
				'row_index_offset'       => 1,
				'remove_wp_meta_box'     => true
			]
		);

		// Include utility functions.
		include_once( ACF_PATH . 'includes/acf-utility-functions.php' );

		// Include previous API functions.
		acf_include( 'includes/api/api-helpers.php' );
		acf_include( 'includes/api/api-template.php' );
		acf_include( 'includes/api/api-term.php' );

		// Include classes.
		acf_include( 'includes/class-acf-data.php' );
		acf_include( 'includes/fields/class-acf-field.php' );
		acf_include( 'includes/locations/abstract-acf-legacy-location.php' );
		acf_include( 'includes/locations/abstract-acf-location.php' );

		// Include functions.
		acf_include( 'includes/acf-helper-functions.php' );
		acf_include( 'includes/acf-hook-functions.php' );
		acf_include( 'includes/acf-field-functions.php' );
		acf_include( 'includes/acf-field-group-functions.php' );
		acf_include( 'includes/acf-form-functions.php' );
		acf_include( 'includes/acf-meta-functions.php' );
		acf_include( 'includes/acf-post-functions.php' );
		acf_include( 'includes/acf-user-functions.php' );
		acf_include( 'includes/acf-value-functions.php' );
		acf_include( 'includes/acf-input-functions.php' );
		acf_include( 'includes/acf-wp-functions.php' );

		// Include core.
		acf_include( 'includes/fields.php' );
		acf_include( 'includes/locations.php' );
		acf_include( 'includes/assets.php' );
		acf_include( 'includes/compatibility.php' );
		acf_include( 'includes/deprecated.php' );
		acf_include( 'includes/l10n.php' );
		acf_include( 'includes/local-fields.php' );
		acf_include( 'includes/local-meta.php' );
		acf_include( 'includes/local-json.php' );
		acf_include( 'includes/loop.php' );
		acf_include( 'includes/media.php' );
		acf_include( 'includes/revisions.php' );
		acf_include( 'includes/upgrades.php' );
		acf_include( 'includes/validation.php' );
		acf_include( 'includes/blocks.php' );
		acf_include( 'includes/options-page.php' );

		// Include AJAX.
		acf_include( 'includes/ajax/class-acf-ajax.php' );
		acf_include( 'includes/ajax/class-acf-ajax-check-screen.php' );
		acf_include( 'includes/ajax/class-acf-ajax-user-setting.php' );
		acf_include( 'includes/ajax/class-acf-ajax-upgrade.php' );
		acf_include( 'includes/ajax/class-acf-ajax-query.php' );
		acf_include( 'includes/ajax/class-acf-ajax-query-users.php' );
		acf_include( 'includes/ajax/class-acf-ajax-local-json-diff.php' );

		// Include forms.
		acf_include( 'includes/forms/form-attachment.php' );
		acf_include( 'includes/forms/form-comment.php' );
		acf_include( 'includes/forms/form-customizer.php' );
		acf_include( 'includes/forms/form-front.php' );
		acf_include( 'includes/forms/form-nav-menu.php' );
		acf_include( 'includes/forms/form-post.php' );
		acf_include( 'includes/forms/form-gutenberg.php' );
		acf_include( 'includes/forms/form-taxonomy.php' );
		acf_include( 'includes/forms/form-user.php' );
		acf_include( 'includes/forms/form-widget.php' );

		// Include admin.
		if ( is_admin() ) {
			acf_include( 'includes/admin/admin.php' );
			acf_include( 'includes/admin/admin-field-group.php' );
			acf_include( 'includes/admin/admin-field-groups.php' );
			acf_include( 'includes/admin/admin-notices.php' );
			acf_include( 'includes/admin/admin-intro-functions.php' );
			acf_include( 'includes/admin/admin-tools.php' );
			acf_include( 'includes/admin/admin-upgrade.php' );
			acf_include( 'includes/admin/admin-options-page.php' );
		}

		// Include legacy.
		acf_include( 'includes/legacy/legacy-locations.php' );

		// Extend original ACF.
		acf_include( 'extend/includes/init.php' );
		acf_include( 'extend/extend.php' );

		// Include tests.
		if ( defined( 'ACF_DEV' ) && ACF_DEV ) {
			acf_include( 'tests/tests.php' );
		}

		// Add actions & filters.
		add_action( 'init', [ $this, 'setup' ], 5 );
		add_action( 'init', [ $this, 'register_post_types' ], 5 );
		add_action( 'init', [ $this, 'register_post_status' ], 5 );
		add_filter( 'posts_where', [ $this, 'posts_where' ], 10, 2 );
	}

	/**
	 * Plugin setup
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function setup() {

		// Bail early if called directly from functions.php or plugin file.
		if ( ! did_action( 'plugins_loaded' ) ) {
			return;
		}

		// This function may be called directly from template functions. Bail early if already did this.
		if ( acf_did( 'init' ) ) {
			return;
		}

		// Update url setting. Allows other plugins to modify the URL (force SSL).
		acf_update_setting( 'url', plugin_dir_url( __FILE__ ) );

		// Load textdomain file.
		acf_load_textdomain();

		// Include 3rd party compatibility.
		acf_include( 'includes/third-party.php' );

		// Include wpml support.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			acf_include( 'includes/wpml.php' );
		}

		// Include fields.
		acf_include( 'includes/fields/class-acf-field-text.php' );
		acf_include( 'includes/fields/class-acf-field-textarea.php' );
		acf_include( 'includes/fields/class-acf-field-number.php' );
		acf_include( 'includes/fields/class-acf-field-range.php' );
		acf_include( 'includes/fields/class-acf-field-email.php' );
		acf_include( 'includes/fields/class-acf-field-url.php' );
		acf_include( 'includes/fields/class-acf-field-password.php' );
		acf_include( 'includes/fields/class-acf-field-image.php' );
		acf_include( 'includes/fields/class-acf-field-file.php' );
		acf_include( 'includes/fields/class-acf-field-wysiwyg.php' );
		acf_include( 'includes/fields/class-acf-field-oembed.php' );
		acf_include( 'includes/fields/class-acf-field-select.php' );
		acf_include( 'includes/fields/class-acf-field-checkbox.php' );
		acf_include( 'includes/fields/class-acf-field-radio.php' );
		acf_include( 'includes/fields/class-acf-field-button-group.php' );
		acf_include( 'includes/fields/class-acf-field-true_false.php' );
		acf_include( 'includes/fields/class-acf-field-link.php' );
		acf_include( 'includes/fields/class-acf-field-post_object.php' );
		acf_include( 'includes/fields/class-acf-field-page_link.php' );
		acf_include( 'includes/fields/class-acf-field-relationship.php' );
		acf_include( 'includes/fields/class-acf-field-taxonomy.php' );
		acf_include( 'includes/fields/class-acf-field-user.php' );
		acf_include( 'includes/fields/class-acf-field-google-map.php' );
		acf_include( 'includes/fields/class-acf-field-date_picker.php' );
		acf_include( 'includes/fields/class-acf-field-date_time_picker.php' );
		acf_include( 'includes/fields/class-acf-field-time_picker.php' );
		acf_include( 'includes/fields/class-acf-field-color_picker.php' );
		acf_include( 'includes/fields/class-acf-field-message.php' );
		acf_include( 'includes/fields/class-acf-field-accordion.php' );
		acf_include( 'includes/fields/class-acf-field-tab.php' );
		acf_include( 'includes/fields/class-acf-field-group.php' );
		acf_include( 'includes/fields/class-acf-field-repeater.php' );
		acf_include( 'includes/fields/class-acf-field-flexible-content.php' );
		acf_include( 'includes/fields/class-acf-field-gallery.php' );
		acf_include( 'includes/fields/class-acf-field-clone.php' );

		/**
		 * Fires after field types have been included.
		 *
		 * @since 5.0.0
		 * @param int $major_version The major version of ACF.
		 */
		do_action( 'acf/include_field_types', ACF_MAJOR_VERSION );

		// Include locations.
		acf_include( 'includes/locations/class-acf-location-post-type.php' );
		acf_include( 'includes/locations/class-acf-location-post-template.php' );
		acf_include( 'includes/locations/class-acf-location-post-status.php' );
		acf_include( 'includes/locations/class-acf-location-post-format.php' );
		acf_include( 'includes/locations/class-acf-location-post-category.php' );
		acf_include( 'includes/locations/class-acf-location-post-taxonomy.php' );
		acf_include( 'includes/locations/class-acf-location-post.php' );
		acf_include( 'includes/locations/class-acf-location-page-template.php' );
		acf_include( 'includes/locations/class-acf-location-page-type.php' );
		acf_include( 'includes/locations/class-acf-location-page-parent.php' );
		acf_include( 'includes/locations/class-acf-location-page.php' );
		acf_include( 'includes/locations/class-acf-location-current-user.php' );
		acf_include( 'includes/locations/class-acf-location-current-user-role.php' );
		acf_include( 'includes/locations/class-acf-location-user-form.php' );
		acf_include( 'includes/locations/class-acf-location-user-role.php' );
		acf_include( 'includes/locations/class-acf-location-taxonomy.php' );
		acf_include( 'includes/locations/class-acf-location-attachment.php' );
		acf_include( 'includes/locations/class-acf-location-comment.php' );
		acf_include( 'includes/locations/class-acf-location-widget.php' );
		acf_include( 'includes/locations/class-acf-location-nav-menu.php' );
		acf_include( 'includes/locations/class-acf-location-nav-menu-item.php' );
		acf_include( 'includes/locations/class-acf-location-block.php' );
		acf_include( 'includes/locations/class-acf-location-options-page.php' );

		/**
		 * Fires after location types have been included.
		 *
		 * @since 5.0.0
		 * @param int $major_version The major version of ACF.
		 */
		do_action( 'acf/include_location_rules', ACF_MAJOR_VERSION );

		/**
		 * Fires during initialization. Used to add local fields.
		 *
		 * @since 5.0.0
		 * @param int $major_version The major version of ACF.
		 */
		do_action( 'acf/include_fields', ACF_MAJOR_VERSION );

		/**
		 * Fires after ACF is completely "initialized".
		 *
		 * @since .0.0
		 * @param int $major_version The major version of ACF.
		 */
		do_action( 'acf/init', ACF_MAJOR_VERSION );
	}

	/**
	 * Register post types
	 *
	 * @since  5.3.2
	 * @access public
	 * @return void
	 */
	public function register_post_types() {

		$cap = acf_get_setting( 'capability' );

		// Get filtered menu options.
		$menu = acf_admin_menu();

		// Show in admin menu or not.
		$show_in_menu = $menu['slug'];
		if (
			! acf_get_setting( 'show_admin' ) ||
			! acf_get_setting( 'acfe/modules/field_groups' )
		) {
			$show_in_menu = false;
		}

		// Register the Field Group post type.
		register_post_type( 'acf-field-group', [
			'labels' => [
			    'name'               => __( 'Field Groups', 'acf' ),
				'singular_name'      => __( 'Field Group', 'acf' ),
			    'add_new'            => __( 'Add New' , 'acf' ),
			    'add_new_item'       => __( 'Add New Field Group' , 'acf' ),
			    'edit_item'          => __( 'Edit Field Group' , 'acf' ),
			    'new_item'           => __( 'New Field Group' , 'acf' ),
			    'view_item'          => __( 'View Field Group', 'acf' ),
			    'search_items'       => __( 'Search Field Groups', 'acf' ),
			    'not_found'          => __( 'No Field Groups found', 'acf' ),
			    'not_found_in_trash' => __( 'No Field Groups found in Trash', 'acf' ),
			],
			'public'          => false,
			'hierarchical'    => true,
			'show_ui'         => true,
			'show_in_menu'    => $show_in_menu,
			'menu_icon'       => 'dashicons-feedback',
			'_builtin'        => false,
			'capability_type' => 'post',
			'capabilities'    => [
				'edit_post'    => $cap,
				'delete_post'  => $cap,
				'edit_posts'   => $cap,
				'delete_posts' => $cap,
			],
			'supports'  => [ 'title' ],
			'rewrite'   => false,
			'query_var' => false,
		] );

		// Register the Field post type.
		register_post_type( 'acf-field', [
			'labels' => [
			    'name'               => __( 'Fields', 'acf' ),
				'singular_name'      => __( 'Field', 'acf' ),
			    'add_new'            => __( 'Add New' , 'acf' ),
			    'add_new_item'       => __( 'Add New Field' , 'acf' ),
			    'edit_item'          => __( 'Edit Field' , 'acf' ),
			    'new_item'           => __( 'New Field' , 'acf' ),
			    'view_item'          => __( 'View Field', 'acf' ),
			    'search_items'       => __( 'Search Fields', 'acf' ),
			    'not_found'          => __( 'No Fields found', 'acf' ),
			    'not_found_in_trash' => __( 'No Fields found in Trash', 'acf' ),
			],
			'public'          => false,
			'hierarchical'    => true,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'_builtin'        => false,
			'capability_type' => 'post',
			'capabilities'    => [
				'edit_post'    => $cap,
				'delete_post'  => $cap,
				'edit_posts'   => $cap,
				'delete_posts' => $cap,
			],
			'supports'  => [ 'title' ],
			'rewrite'   => false,
			'query_var' => false,
		] );
	}

	/**
	 * Register post statuses
	 *
	 * @since  5.3.2
	 * @access public
	 * @return void
	 */
	public function register_post_status() {

		// Register the Disabled post status.
		register_post_status( 'acf-disabled', [
			'label'                     => _x( 'Disabled', 'post status', 'acf' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Disabled <span class="count">(%s)</span>', 'Disabled <span class="count">(%s)</span>', 'acf' ),
		] );
	}

	/**
	 * Enqueue input scripts
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function input_admin_enqueue_scripts() {
		wp_enqueue_script( 'acf-pro-input' );
		wp_enqueue_style( 'acf-pro-input' );
	}

	/**
	 *  Enqueue field group admin scripts
	 *
	 * @since  5.0.0
	 * @access public
	 * @return void
	 */
	public function field_group_admin_enqueue_scripts() {
		wp_enqueue_script( 'acf-pro-field-group' );
		wp_enqueue_style( 'acf-pro-field-group' );
	}

	/**
	 * Posts where
	 *
	 * Filters the $where clause allowing for custom WP_Query args.
	 *
	 * @since	5.8.1
	 * @access public
	 * @param  string $where The WHERE clause.
	 * @param  $wp_query The query object.
	 * @return object WP_Query instance.
	 */
	public function posts_where( $where, $wp_query ) {

		global $wpdb;

		// Add custom "acf_field_key" arg.
		if ( $field_key = $wp_query->get( 'acf_field_key' ) ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key );
	    }

	    // Add custom "acf_field_name" arg.
	    if ( $field_name = $wp_query->get( 'acf_field_name' ) ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name );
	    }

	    // Add custom "acf_group_key" arg.
		if ( $group_key = $wp_query->get( 'acf_group_key' ) ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key );
	    }
	    return $where;
	}

	/**
	 * Has setting
	 *
	 * Returns true if a setting exists for this name.
	 *
	 * @since  5.6.5
	 * @access public
	 * @param  string $name The setting name.
	 * @return boolean
	 */
	public function has_setting( $name ) {
		return isset( $this->settings[ $name ] );
	}

	/**
	 * Get setting
	 *
	 * Returns a setting or null if doesn't exist.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string $name The setting name.
	 * @return mixed
	 */
	public function get_setting( $name ) {
		return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;
	}

	/**
	 * Update setting
	 *
	 * Updates a setting for the given name and value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string $name The setting name.
	 * @param  mixed $value The setting value.
	 * @return true
	 */
	public function update_setting( $name, $value ) {
		$this->settings[ $name ] = $value;
		return true;
	}

	/**
	 * Get data
	 *
	 * Returns data or null if doesn't exist.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string $name The data name.
	 * @return mixed
	 */
	public function get_data( $name ) {

		if ( isset( $this->data[ $name ] ) ) {
			return isset( $this->data[ $name ] );
		} else {
			return null;
		}
	}

	/**
	 * Set data
	 *
	 * Sets data for the given name and value.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string $name The data name.
	 * @param  mixed $value The data value.
	 * @return void
	 */
	public function set_data( $name, $value ) {
		$this->data[ $name ] = $value;
	}

	/**
	 * Get class instance
	 *
	 * Returns an instance or null if doesn't exist.
	 *
	 * @since  5.6.9
	 * @access public
	 * @param  string $class The instance class name.
	 * @return object
	 */
	public function get_instance( $class ) {

		$name = strtolower( $class );

		if ( isset( $this->instances[ $name ] ) ) {
			return $this->instances[ $name ];
		} else {
			return null;
		}
	}

	/**
	 * New class instance
	 *
	 * Creates and stores an instance of the given class.
	 *
	 * @since  5.6.9
	 * @access public
	 * @param  string $class The instance class name.
	 * @return object
	 */
	public function new_instance( $class ) {

		$instance = new $class();
		$name     = strtolower( $class );

		$this->instances[ $name ] = $instance;

		return $instance;
	}

	/**
	 * Magic __isset method
	 *
	 * This is for backwards compatibility.
	 *
	 * @since  5.9.0
	 * @access public
	 * @param  string $key Key name.
	 * @return bool
	 */
	public function __isset( $key ) {
		return in_array( $key, [ 'locations', 'json' ] );
	}

	/**
	 * Magic __get method
	 *
	 * This is for backwards compatibility.
	 *
	 * @since  5.9.0
	 * @access public
	 * @param  string $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {

		switch ( $key ) {
			case 'locations':
				return acf_get_instance( 'ACF_Legacy_Locations' );
			case 'json':
				return acf_get_instance( 'ACF_Local_JSON' );
		}
		return null;
	}
}

/**
 * Core class instance
 *
 * The main function responsible for returning the one true acf Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $acf = ACF\acf(); ?>
 *
 * @since  4.3.0
 * @return object ACF
 */
function acf() {

	global $acf;

	// Instantiate only once.
	if ( ! isset( $acf ) ) {
		$acf = new ACF();
		$acf->initialize();
	}
	return $acf;
}

// Instantiate the core class.
acf();

// Dummy classes.
acf_include( 'includes/class-dummy.php' );
