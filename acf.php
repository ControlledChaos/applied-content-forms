<?php
/**
 * Applied Content Forms
 *
 * Content editing for ClassicPress and WordPress.
 *
 * @package  ACF
 * @category Core
 * @since    1.0.0
 * @link     https://github.com/ControlledChaos/applied-content-forms
 *
 * Plugin Name:       Applied Content Forms
 * Plugin URI:        https://github.com/ControlledChaos/applied-content-forms
 * Description:       Content editing for ClassicPress and WordPress.
 * Version:           1.0.0
 * UpdateURI:         https://github.com/ControlledChaos/releases/latest/download/applied-content-forms.zip
 * Author:            Controlled Chaos Design
 * Author URI:        https://github.com/ControlledChaos/
 * Text Domain:       acf
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 5.3
 * Tested up to:      6.9
 * Network:           false
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get core plugin functions file if necessary.
$get_plugin = ABSPATH . 'wp-admin/includes/plugin.php';
if ( file_exists( $get_plugin ) && ! function_exists( 'is_plugin_active' ) ) {
	include_once( $get_plugin );
}

if ( ! class_exists( 'ACF' ) ) :

final class ACF {

	/**
	 * This plugin version
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $plugin = '1.0.0';

	/**
	 * Original plugin version.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $version = '5.9.6';

	/**
	 * Fields class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_fields class.
	 */
	public $fields;

	/**
	 * Loop class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_loop class.
	 */
	public $loop;

	/**
	 * Revisions class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_revisions class.
	 */
	public $revisions;

	/**
	 * Validation class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_validation class.
	 */
	public $validation;

	/**
	 * Form front class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_form_front class.
	 */
	public $form_front;

	/**
	 * Admin tools class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_admin_tools class.
	 */
	public $admin_tools;

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = [];

	/**
	 * Plugin data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data = [];

	/**
	 * Instances
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $instances = [];

	/**
	 * Admin page slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $admin_slug = 'acf';

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {}

	/**
	 * Set constants
	 *
	 * @param  array $array
	 * @return void
	 */
    function constants( $array = [] ) {

        foreach ( $array as $name => $value ) {
            if ( defined( $name ) ) {
				continue;
			}
            define( $name, $value );
        }
    }

	/**
	 * Initialize the class
	 *
	 * Sets up the ACF functionality.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		// Define constants.
		$this->constants( [
            'ACF'          => true,
			'ACF_PRO'      => true,
			'ACFE'         => true,
			'ACF_BASENAME' => plugin_basename( __FILE__ ),
			'ACF_PATH'     => plugin_dir_path( __FILE__ ),
            'ACFE_FILE'    => __FILE__,
            'ACFE_PATH'    => plugin_dir_path( __FILE__ ) . 'extend/',
			'ACF_VERSION'  => $this->version,
            'ACFE_VERSION' => $this->plugin
        ] );

		// Define settings.
		$this->settings = [
			'name'                   => __( 'Applied Content Forms', 'acf' ),
			'desc'                   => __( 'A suite of tools for adding and managing custom content types and user forms.', 'acf' ),
			'website'                => 'https://github.com/ControlledChaos/applied-content-forms',
			'slug'                   => dirname( ACF_BASENAME ),
			'plugin'                 => $this->plugin,
			'version'                => $this->version,
			'pro'                    => true,
			'basename'               => ACF_BASENAME,
			'path'                   => ACF_PATH,
			'file'                   => __FILE__,
			'url'                    => plugin_dir_url( __FILE__ ),
			'show_admin'             => true,
			'dev_mode'               => false,
			'menu_position'          => '2',
			'show_updates'           => true,
			'stripslashes'           => false,
			'local'                  => true,
			'json'                   => true,
			'save_json'              => '',
			'load_json'              => [],
			'json_found'             => false,
			'default_language'       => '',
			'current_language'       => '',
			'capability'             => 'manage_options',
			'uploader'               => 'wp',
			'autoload'               => false,
			'l10n'                   => true,
			'l10n_textdomain'        => '',
			'multilang'              => false,
			'google_api_key'         => '',
			'google_api_client'      => '',
			'enqueue_google_maps'    => true,
			'enqueue_select2'        => true,
			'enqueue_datepicker'     => true,
			'enqueue_datetimepicker' => true,
			'select2_version'        => 4,
			'row_index_offset'       => 1,
			'remove_wp_meta_box'     => true,
			'post_types'             => true,
			'taxonomies'             => true,
			'block_types'            => true,
			'forms'                  => true,
			'templates'              => true,
			'options_pages'          => true,
			'author_ajax'            => true,
			'options_editor'         => false
		];

		include_once( ACF_PATH . 'includes/utility-functions.php' );

		acf_include( 'includes/api/api-helpers.php' );
		acf_include( 'includes/api/api-template.php' );
		acf_include( 'includes/api/api-term.php' );

		acf_include( 'includes/class-acf-data.php' );
		acf_include( 'includes/fields/class-acf-field.php' );
		acf_include( 'includes/locations/abstract-acf-legacy-location.php' );
		acf_include( 'includes/locations/abstract-acf-location.php' );

		acf_include( 'includes/acf-helper-functions.php' );
		acf_include( 'includes/acf-hook-functions.php' );
		acf_include( 'includes/acf-field-functions.php' );
		acf_include( 'includes/acf-field-group-functions.php' );
		acf_include( 'includes/acf-file-functions.php' );
		acf_include( 'includes/acf-form-functions.php' );
		acf_include( 'includes/acf-meta-functions.php' );
		acf_include( 'includes/acf-post-functions.php' );
		acf_include( 'includes/acf-user-functions.php' );
		acf_include( 'includes/acf-value-functions.php' );
		acf_include( 'includes/acf-input-functions.php' );
		acf_include( 'includes/acf-cms-functions.php' );

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
		acf_include( 'includes/updates.php' );
		acf_include( 'includes/upgrades.php' );
		acf_include( 'includes/validation.php' );

		acf_include( 'includes/ajax/class-acf-ajax.php' );
		acf_include( 'includes/ajax/class-acf-ajax-check-screen.php' );
		acf_include( 'includes/ajax/class-acf-ajax-user-setting.php' );
		acf_include( 'includes/ajax/class-acf-ajax-upgrade.php' );
		acf_include( 'includes/ajax/class-acf-ajax-query.php' );
		acf_include( 'includes/ajax/class-acf-ajax-query-users.php' );
		acf_include( 'includes/ajax/class-acf-ajax-local-json-diff.php' );

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

		if ( is_admin() ) {
			acf_include( 'includes/admin/admin.php' );
			acf_include( 'includes/admin/admin-field-group.php' );
			acf_include( 'includes/admin/admin-field-groups.php' );
			acf_include( 'includes/admin/admin-notices.php' );
			acf_include( 'includes/admin/admin-tools.php' );
			acf_include( 'includes/admin/admin-upgrade.php' );
		}

		acf_include( 'includes/legacy/legacy-locations.php' );
		acf_include( 'pro/acf-pro.php' );

		// Extend original ACF.
		acf_include( 'extend/extend.php' );
		if ( defined( 'ACF_DEV' ) && ACF_DEV ) {
			acf_include( 'tests/tests.php' );
		}

		add_action( 'init', [ $this, 'init' ], 5 );
		add_action( 'init', [ $this, 'register_post_types' ], 5 );
		add_action( 'init', [ $this, 'register_post_status' ], 5 );
		add_filter( 'posts_where', [ $this, 'posts_where' ], 10, 2 );
	}

	/**
	 * Initialize the plugin
	 *
	 * Completes the setup process on "init" of earlier.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {

		// Bail early if called directly from functions.php or plugin file.
		if ( ! did_action( 'plugins_loaded' ) ) {
			return;
		}

		/**
		 * May be called directly from template functions.
		 * Bail early if already did this.
		 */
		if ( acf_did( 'init' ) ) {
			return;
		}

		// Allow other plugins to modify the URL (force SSL).
		acf_update_setting( 'url', plugin_dir_url( __FILE__ ) );

		// Load textdomain file.
		acf_load_textdomain();

		// Include 3rd party compatibility.
		acf_include( 'includes/third-party.php' );

		// Include WPML support.
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
		acf_include( 'includes/fields/class-acf-field-separator.php' );
		acf_include( 'includes/fields/class-acf-field-horz-rule.php' );
		acf_include( 'includes/fields/class-acf-field-group.php' );

		/**
		 * Fires after field types have been included.
		 *
		 * @since 1.0.0
		 */
		do_action( 'acf/include_field_types' );

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

		// Settings update.
		acf_include( 'includes/settings-update.php' );

		/**
		 * Fires after location types have been included.
		 *
		 * @since 1.0.0
		 */
		do_action( 'acf/include_location_rules' );

		/**
		 * Fires during initialization. Used to add local fields.
		 *
		 * @since 1.0.0
		 */
		do_action( 'acf/include_fields' );

		/**
		 * Fires after ACF is completely "initialized".
		 *
		 * @since 1.0.0
		 */
		do_action( 'acf/init' );
	}

	/**
	 * Register post types
	 *
	 * Registers the ACF post types.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_post_types() {

		$cap = acf_get_setting( 'capability' );

		if ( acf_get_setting( 'post_types' ) ) {
			register_post_type( 'acf-post-type', [
				'label'       => __( 'Post Types', 'acf' ),
				'description' => __( 'Create custom post types without code.', 'acf' ),
				'labels'      => [
					'name'          => __( 'Post Types', 'acf' ),
					'singular_name' => __( 'Post Type', 'acf' ),
					'menu_name'     => __( 'Post Types', 'acf' ),
					'edit_item'     => __( 'Edit Post Type', 'acf' ),
					'add_new_item'  => __( 'New Post Type', 'acf' ),
				],
				'supports'            => [ 'title' ],
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => $this->admin_slug,
				'menu_icon'           => 'dashicons-sticky',
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capabilities'        => [
					'publish_posts'       => $cap,
					'edit_posts'          => $cap,
					'edit_others_posts'   => $cap,
					'delete_posts'        => $cap,
					'delete_others_posts' => $cap,
					'read_private_posts'  => $cap,
					'edit_post'           => $cap,
					'delete_post'         => $cap,
					'read_post'           => $cap,
				],
				'acfe_admin_orderby' => 'title',
				'acfe_admin_order'   => 'ASC',
				'acfe_admin_ppp'     => 999,
			] );
		}

		if ( acf_get_setting( 'taxonomies' ) ) {
			register_post_type( 'acf-taxonomy', [
				'label'       => __( 'Taxonomies', 'acf' ),
				'description' => __( 'Create custom taxonomies without code.', 'acf' ),
				'labels'      => [
					'name'          => __( 'Taxonomies', 'acf' ),
					'singular_name' => __( 'Taxonomy', 'acf' ),
					'menu_name'     => __( 'Taxonomies', 'acf' ),
					'edit_item'     => __( 'Edit Taxonomy', 'acf' ),
					'add_new_item'  => __( 'New Taxonomy', 'acf' ),
				],
				'supports'            => [ 'title' ],
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => $this->admin_slug,
				'menu_icon'           => 'dashicons-tag',
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capabilities'        => [
					'publish_posts'       => $cap,
					'edit_posts'          => $cap,
					'edit_others_posts'   => $cap,
					'delete_posts'        => $cap,
					'delete_others_posts' => $cap,
					'read_private_posts'  => $cap,
					'edit_post'           => $cap,
					'delete_post'         => $cap,
					'read_post'           => $cap,
				],
				'acfe_admin_orderby' => 'title',
				'acfe_admin_order'   => 'ASC',
				'acfe_admin_ppp'     => 999,
			] );
		}

		if ( acf_get_setting( 'block_types' ) ) {
			register_post_type( 'acf-block-type', [
				'label'       => __( 'Block Type', 'acf' ),
				'description' => __( 'Create custom block types without code.', 'acf' ),
				'labels'      => [
					'name'          => __( 'Block Types', 'acf' ),
					'singular_name' => __( 'Block Type', 'acf' ),
					'menu_name'     => __( 'Block Types', 'acf' ),
					'edit_item'     => __( 'Edit Block Type', 'acf' ),
					'add_new_item'  => __( 'New Block Type', 'acf' ),
				],
				'supports'            => [ 'title' ],
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => $this->admin_slug,
				'menu_icon'           => 'dashicons-block-default',
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capabilities'        => [
					'publish_posts'       => $cap,
					'edit_posts'          => $cap,
					'edit_others_posts'   => $cap,
					'delete_posts'        => $cap,
					'delete_others_posts' => $cap,
					'read_private_posts'  => $cap,
					'edit_post'           => $cap,
					'delete_post'         => $cap,
					'read_post'           => $cap,
				],
				'acfe_admin_orderby' => 'title',
				'acfe_admin_order'   => 'ASC',
				'acfe_admin_ppp'     => 999,
			] );
		}

		if ( acf_get_setting( 'forms' ) ) {
			register_post_type( 'acf-form', [
				'label'                 => __( 'Forms', 'acf' ),
				'description'           => __( 'Forms', 'acf' ),
				'labels'                => [
					'name'          => __( 'Forms', 'acf' ),
					'singular_name' => __( 'Form', 'acf' ),
					'menu_name'     => __( 'Forms', 'acf' ),
					'edit_item'     => __( 'Edit Form', 'acf' ),
					'add_new_item'  => __( 'New Form', 'acf' ),
				],
				'supports'            => [ 'title' ],
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => $this->admin_slug,
				'menu_icon'           => 'dashicons-editor-table',
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capabilities'        => [
					'publish_posts'       => $cap,
					'edit_posts'          => $cap,
					'edit_others_posts'   => $cap,
					'delete_posts'        => $cap,
					'delete_others_posts' => $cap,
					'read_private_posts'  => $cap,
					'edit_post'           => $cap,
					'delete_post'         => $cap,
					'read_post'           => $cap,
				],
				'acfe_admin_ppp'     => 999,
				'acfe_admin_orderby' => 'title',
				'acfe_admin_order'   => 'ASC',
			] );
		}

		if ( acf_get_setting( 'templates' ) ) {
			register_post_type( 'acf-template', [
				'label'       => __( 'Templates', 'acf' ),
				'description' => __( 'Templates', 'acf' ),
				'labels'      => [
					'name'          => __( 'Templates', 'acf' ),
					'singular_name' => __( 'Template', 'acf' ),
					'menu_name'     => __( 'Templates', 'acf' ),
					'edit_item'     => __( 'Edit Template', 'acf' ),
					'add_new_item'  => __( 'New Template', 'acf' ),
				],
				'supports'            => [ 'title' ],
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => $this->admin_slug,
				'menu_icon'           => 'dashicons-schedule',
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capabilities'        => [
					'publish_posts'       => $cap,
					'edit_posts'          => $cap,
					'edit_others_posts'   => $cap,
					'delete_posts'        => $cap,
					'delete_others_posts' => $cap,
					'read_private_posts'  => $cap,
					'edit_post'           => $cap,
					'delete_post'         => $cap,
					'read_post'           => $cap,
				],
				'acfe_admin_orderby' => 'title',
				'acfe_admin_order'   => 'ASC',
				'acfe_admin_ppp'     => 999,
			] );
		}

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
			'show_in_menu'    => $this->admin_slug,
			'menu_icon'       => 'dashicons-list-view',
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

		register_post_type( 'acf-field', [
			'labels'			=> [
			    'name'					=> __( 'Fields', 'acf' ),
				'singular_name'			=> __( 'Field', 'acf' ),
			    'add_new'				=> __( 'Add New' , 'acf' ),
			    'add_new_item'			=> __( 'Add New Field' , 'acf' ),
			    'edit_item'				=> __( 'Edit Field' , 'acf' ),
			    'new_item'				=> __( 'New Field' , 'acf' ),
			    'view_item'				=> __( 'View Field', 'acf' ),
			    'search_items'			=> __( 'Search Fields', 'acf' ),
			    'not_found'				=> __( 'No Fields found', 'acf' ),
			    'not_found_in_trash'	=> __( 'No Fields found in Trash', 'acf' ),
			],
			'public'          => false,
			'hierarchical'    => true,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'menu_icon'       => 'dashicons-list-view',
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

		register_taxonomy( 'acf-field-group-category',
			[ 'acf-field-group' ],
			[
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => 'ACFE',
				'show_admin_column' => true,
				'show_in_menu'      => 'acf',
				'show_in_nav_menus' => true,
				'show_tagcloud'     => false,
				'rewrite'           => false,
				'labels'            => [
					'name'              => _x( 'Field Group Categories', 'Field Group Category', 'acf' ),
					'singular_name'     => _x( 'Field Group Categories', 'Field Group Category', 'acf' ),
					'search_items'      => __( 'Search Categories', 'acf' ),
					'all_items'         => __( 'All Categories', 'acf' ),
					'parent_item'       => __( 'Parent Category', 'acf' ),
					'parent_item_colon' => __( 'Parent Category:', 'acf' ),
					'edit_item'         => __( 'Edit Category', 'acf' ),
					'update_item'       => __( 'Update Category', 'acf' ),
					'add_new_item'      => __( 'New Category', 'acf' ),
					'new_item_name'     => __( 'New Category Name', 'acf' ),
					'menu_name'         => __( 'Category', 'acf' ),
				],
			] );
	}

	/**
	 * Register post status
	 *
	 * Registers the ACF post statuses.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_post_status() {

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
	 * Posts where
	 *
	 * Filters the $where clause allowing for custom WP_Query args.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $where The WHERE clause.
	 * @global object $wpdb
	 * @return object $wp_query The query object.
	 */
	public function posts_where( $where, $wp_query ) {

		// Access global variables.
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
	 * Define
	 *
	 * Defines a constant if doesn't already exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The constant name.
	 * @param  mixed $value The constant value.
	 * @return void
	 */
	public function define( $name, $value = true ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Has setting
	 *
	 * Returns true if a setting exists for this name.
	 *
	 * @since  1.0.0
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
	 * @since  1.0.0
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
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The setting name.
	 * @param  mixed $value The setting value.
	 * @return boolean
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
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The data name.
	 * @return mixed
	 */
	public function get_data( $name ) {
		return isset( $this->data[ $name ] ) ? $this->data[ $name ] : null;
	}

	/**
	 * Set data
	 *
	 * Sets data for the given name and value.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The data name.
	 * @param  mixed $value The data value.
	 * @return void
	 */
	public function set_data( $name, $value ) {
		$this->data[ $name ] = $value;
	}

	/**
	 * Get instance
	 *
	 * Returns an instance or null if doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $class The instance class name.
	 * @return object
	 */
	public function get_instance( $class ) {
		$name = strtolower( $class );
		return isset( $this->instances[ $name ] ) ? $this->instances[ $name ] : null;
	}

	/**
	 * New instance
	 *
	 * Creates and stores an instance of the given class.
	 *
	 * @since  1.0.0
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
	 * This is for for backwards compatibility.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key Key name.
	 * @return boolean
	 */
	public function __isset( $key ) {
		return in_array( $key, [ 'locations', 'json' ] );
	}

	/**
	 * Magic __get method
	 *
	 * This is for for backwards compatibility.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'locations' :
				return acf_get_instance( 'ACF_Legacy_Locations' );
			case 'json' :
				return acf_get_instance( 'ACF_Local_JSON' );
		}
		return null;
	}
}

/*
 * Instantiate the ACF class.
 *
 * The main function responsible for returning the one true
 * ACF instance to functions everywhere.
 * Use this function like you would a global variable,
 * except without needing to declare the global.
 *
 * Example: <?php $acf = acf(); ?>
 *
 * @since  1.0.0
 * @global object $acf
 * @return object Returns an instance of the ACF class.
 */
function acf() {

	// Set a global variable.
	global $acf;

	// Instantiate only once.
	if ( ! isset( $acf ) ) {
		$acf = new ACF();
		$acf->initialize();
	}
	return $acf;
}
acf();

endif; // Check for ACF class.

/**
 * Add row notice
 *
 * Adds a notice to this plugin's row on the
 * Plugins screen if Advanced Custom Fields or
 * Advanced Custom Fields PRO are active.
 *
 * @since  1.0.0
 * @param  $plugin_file This plugin's file path.
 * @param  $plugin_data This plugin's header data.
 * @param  $status Tne plugin screen filtered list.
 * @return void
 */
if (
	is_plugin_active( 'advanced-custom-fields/acf.php' ) ||
	is_plugin_active( 'advanced-custom-fields-pro/acf.php' )
) {
	add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), function(  $plugin_file, $plugin_data, $status ) {
		?>
		<style>
			.plugins tr[data-plugin='<?php echo $plugin_file; ?>'] th,
			.plugins tr[data-plugin='<?php echo $plugin_file; ?>'] td {
				box-shadow: none;
			}
		</style>

		<tr id="acf-deactivate-notice" class="active">
			<th class="check-column"><span class="screen-reader-text"><?php _e( 'Notice', 'acf' ); ?></span></th>
			<td colspan="3" class="plugin-update colspanchange">
				<div class="notice inline notice-error notice-alt">
				<?php printf(
					__( '<p>Functionality of the %s plugin has been disabled. Please first deactivate Advanced Custom Fields.</p>', 'acf' ),
					$plugin_data['Name']
				); ?>
				</div>
			</td>
		</tr>
		<?php
	}, 5, 3 );
}
