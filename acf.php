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
	 * Original plugin version
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $version = '5.9.6';

	/**
	 * Plugin basename
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $basename;

	/**
	 * Plugin path
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $dir_path;

	/**
	 * Plugin URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $dir_url;

	/**
	 * Fields class
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The ACF_Fields class.
	 */
	public $fields;

	/**
	 * Loop class
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The ACF_Loop class.
	 */
	public $loop;

	/**
	 * Revisions class
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_revisions class.
	 */
	public $revisions;

	/**
	 * Validation class
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_validation class.
	 */
	public $validation;

	/**
	 * Form front class
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object The acf_form_front class.
	 */
	public $form_front;

	/**
	 * Admin tools class
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
	 * Admin class
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object Instance of ACF_Admin()
	 */
	public $admin;

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
	 * Plugin name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function plugin_name() {
		$name = 'Applied Content Forms';
		return apply_filters( 'acf/plugin_name', $name );
	}

	/**
	 * Plugin description
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function plugin_description() {
		$desc = __( 'A suite of tools for adding and managing custom content types and user forms.', 'acf' );
		return apply_filters( 'acf/plugin_description', $desc );
	}

	/**
	 * Admin slug
	 *
	 * Apply a filter to the admin page slug.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function admin_slug() {
		$slug = $this->admin_slug;
		return apply_filters( 'acf/admin_slug', $slug );
	}

	/**
	 * Set constants
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $array
	 * @return void
	 */
    public function constants( $array = [] ) {

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

		// Define plugin utility properties.
		$this->basename = plugin_basename( __FILE__ );
		$this->dir_path = plugin_dir_path( __FILE__ );
		$this->dir_url  = plugin_dir_url( __FILE__ );

		// Define constants for compatibility.
		$this->constants( [
            'ACF'          => true,
			'ACF_PRO'      => true,
			'ACF_BASENAME' => $this->basename,
			'ACF_PATH'     => $this->dir_path,
			'ACF_VERSION'  => $this->version
        ] );

		// Active theme strings.
		$theme_path = get_stylesheet_directory();
		$theme_url  = get_stylesheet_directory_uri();

		// Define settings.
		$this->settings = [
			'name'       => $this->plugin_name(),
			'desc'       => $this->plugin_description(),
			'website'    => 'https://github.com/ControlledChaos/applied-content-forms',
			'slug'       => dirname( $this->basename ),
			'plugin'     => $this->plugin,
			'version'    => $this->version,
			'pro'        => true,
			'basename'   => $this->basename,
			'path'       => $this->dir_path,
			'file'       => __FILE__,
			'url'        => $this->dir_url,
			'show_admin' => true,
			'dev_mode'   => false,
			'local'      => true,
			'json'       => true,
			'save_json'  => '',
			'load_json'  => [],
			'json_found' => false,
			'menu_position'    => '2',
			'show_updates'     => true,
			'stripslashes'     => false,
			'default_language' => '',
			'current_language' => '',
			'capability'       => 'manage_options',
			'uploader'         => 'wp',
			'autoload'         => false,
			'l10n'             => true,
			'l10n_textdomain'  => '',
			'multilang'        => false,
			'google_api_key'   => '',
			'google_api_client'      => '',
			'enqueue_google_maps'    => true,
			'enqueue_select2'        => true,
			'enqueue_datepicker'     => true,
			'enqueue_datetimepicker' => true,
			'select2_version'        => 4,
			'row_index_offset'       => 1,
			'remove_wp_meta_box'     => true,
			'reserved_post_types'    => $this->reserved_post_types(),
			'reserved_taxonomies'    => $this->reserved_taxonomies(),
			'reserved_field_groups'  => $this->reserved_field_groups(),
			'post_types'             => true,
			'taxonomies'             => true,
			'block_types'            => true,
			'forms'                  => true,
			'templates'              => true,
			'options_pages'          => true,
			'author_ajax'            => true,
			'global_field_condition' => true,
			'field_group_ui'         => true,
			'screen_layouts'         => true,
			'options_editor'         => false,
			'single_meta'            => false,
			'meta_tools'             => true,
			'force_sync'             => false,
			'force_sync_delete'      => false,
			'form_shortcode_preview' => true,
			'rewrite_rules'          => true,

			'theme_path'             => $theme_path,
			'theme_url'              => $theme_url,
			'theme_folder'           => parse_url( $theme_url, PHP_URL_PATH ),

			'php'                    => true,
			'php_save'               => "{$theme_path}/acf-php",
			'php_load'               => [ "{$theme_path}/acf-php" ],
			'php_found'              => false,

			'recaptcha_site_key'     => null,
			'recaptcha_secret_key'   => null,
			'recaptcha_version'      => null,
			'recaptcha_v2_theme'     => null,
			'recaptcha_v2_size'      => null,
			'recaptcha_v3_hide_logo' => null
		];

		include_once( $this->dir_path . 'includes/utility-functions.php' );

		// Settings update.
		acf_include( 'includes/settings-update.php' );

		acf_include( 'includes/api/api-helpers.php' );
		acf_include( 'includes/api/api-template.php' );
		acf_include( 'includes/api/api-term.php' );

		acf_include( 'includes/locations/abstract-acf-location.php' );
		acf_include( 'includes/class-acf-data.php' );
		acf_include( 'includes/class-acf-field.php' );
		acf_include( 'includes/class-acf-fields.php' );
		acf_include( 'includes/class-acf-assets.php' );
		acf_include( 'includes/class-acf-compatibility.php' );
		acf_include( 'includes/class-acf-loop.php' );
		acf_include( 'includes/class-acf-media.php' );

		acf_include( 'includes/acf-helper-functions.php' );
		acf_include( 'includes/acf-hook-functions.php' );
		acf_include( 'includes/acf-field-functions.php' );
		acf_include( 'includes/acf-field-group-functions.php' );
		acf_include( 'includes/acf-file-functions.php' );
		acf_include( 'includes/acf-form-functions.php' );
		acf_include( 'includes/acf-meta-functions.php' );
		acf_include( 'includes/acf-post-functions.php' );
		acf_include( 'includes/acf-term-functions.php' );
		acf_include( 'includes/acf-user-functions.php' );
		acf_include( 'includes/acf-value-functions.php' );
		acf_include( 'includes/acf-input-functions.php' );
		acf_include( 'includes/acf-blocks-functions.php' );
		acf_include( 'includes/cms.php' );

		acf_include( 'includes/deprecated.php' );
		acf_include( 'includes/l10n.php' );
		acf_include( 'includes/local-fields.php' );
		acf_include( 'includes/local-meta.php' );
		acf_include( 'includes/local-json.php' );

		acf_include( 'includes/locations.php' );
		acf_include( 'includes/loop.php' );
		acf_include( 'includes/revisions.php' );
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
			acf_include( 'includes/admin/class-acf-admin.php' );
			acf_include( 'includes/admin/class-acf-admin-field-group.php' );
			acf_include( 'includes/admin/class-acf-admin-field-groups.php' );
			acf_include( 'includes/admin/class-acf-admin-notices.php' );
			acf_include( 'includes/admin/class-acf-admin-tools.php' );
			acf_include( 'includes/admin/class-acf-admin-options-page.php' );
			acf_include( 'includes/admin/class-acf-admin-upgrade.php' );
		}

		acf_include( 'includes/class-options-page.php' );
		acf_include( 'includes/acf-screen-functions.php' );

		if ( defined( 'ACF_DEV' ) && ACF_DEV ) {
			acf_include( 'tests/tests.php' );
		}

		add_action( 'init', [ $this, 'init' ], 5 );
		add_action( 'init', [ $this, 'register_post_types' ], 11 );
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

		// Stop if called directly from functions.php or plugin file.
		if ( ! did_action( 'plugins_loaded' ) ) {
			return;
		}

		/**
		 * May be called directly from template functions.
		 * Stop if already did this.
		 */
		if ( acf_did( 'init' ) ) {
			return;
		}

		do_action( 'acf/init_early' );

		// Allow other plugins to modify the URL (force SSL).
		acf_update_setting( 'url', plugin_dir_url( __FILE__ ) );

		// Load textdomain file.
		acf_load_textdomain();

		// Include 3rd party compatibility.
		acf_include( 'includes/third-party.php' );
		acf_include( 'extend/includes/hooks.php' );
		acf_include( 'extend/includes/admin/plugins.php' );
		acf_include( 'includes/class-extend-field.php' );

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
		acf_include( 'includes/fields/extend/field-checkbox.php' );
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
		acf_include( 'includes/fields/class-acf-field-repeater.php' );
		acf_include( 'includes/fields/class-acf-field-flexible-content.php' );
		acf_include( 'includes/fields/class-acf-field-gallery.php' );
		acf_include( 'includes/fields/class-acf-field-clone.php' );

		acf_include( 'includes/fields/class-acf-field-advanced-link.php' );
		acf_include( 'includes/fields/class-acf-field-block-types.php' );
		acf_include( 'includes/fields/class-acf-field-code-editor.php' );
		acf_include( 'includes/fields/class-acf-field-payment.php' );
		acf_include( 'includes/fields/class-acf-field-payment-cart.php' );
		acf_include( 'includes/fields/class-acf-field-payment-selector.php' );
		acf_include( 'includes/fields/class-acf-field-phone-number.php' );
		acf_include( 'includes/fields/class-acf-field-templates.php' );



		/**
		 * Fires after field types have been included.
		 *
		 * @since 1.0.0
		 */
		do_action( 'acf/include_field_types' );

		$this->load_local();

		acf_include( 'includes/fields/extend/field-button.php' );

		acf_include( 'includes/fields/extend/field-column.php' );
		acf_include( 'includes/fields/extend/field-dynamic-render.php' );
		acf_include( 'includes/fields/extend/field-forms.php' );
		acf_include( 'includes/fields/extend/field-hidden.php' );
		acf_include( 'includes/fields/extend/field-post-statuses.php' );
		acf_include( 'includes/fields/extend/field-post-types.php' );
		acf_include( 'includes/fields/extend/field-recaptcha.php' );
		acf_include( 'includes/fields/extend/field-slug.php' );
		acf_include( 'includes/fields/extend/field-taxonomies.php' );
		acf_include( 'includes/fields/extend/field-taxonomy-terms.php' );
		acf_include( 'includes/fields/extend/field-user-roles.php' );

		acf_include( 'includes/fields/extend/field-countries.php' );
		acf_include( 'includes/fields/extend/field-currencies.php' );
		acf_include( 'includes/fields/extend/field-date-range-picker.php' );
		acf_include( 'includes/fields/extend/field-field-groups.php' );
		acf_include( 'includes/fields/extend/field-field-types.php' );
		acf_include( 'includes/fields/extend/field-fields.php' );
		acf_include( 'includes/fields/extend/field-google-map.php' );
		acf_include( 'includes/fields/extend/field-image-selector.php' );
		acf_include( 'includes/fields/extend/field-image-sizes.php' );
		acf_include( 'includes/fields/extend/field-languages.php' );
		acf_include( 'includes/fields/extend/field-menus.php' );
		acf_include( 'includes/fields/extend/field-menu-locations.php' );
		acf_include( 'includes/fields/extend/field-options-pages.php' );
		acf_include( 'includes/fields/extend/field-post-field.php' );
		acf_include( 'includes/fields/extend/field-post-formats.php' );
		acf_include( 'includes/fields/extend/field-relationship.php' );

		acf_include( 'includes/multilang.php' );
		acf_include( 'includes/class-acf-module-settings.php' );

		acf_include( 'extend/includes/forms/form-attachment.php' );
		acf_include( 'extend/includes/forms/form-options-page.php' );
		acf_include( 'extend/includes/forms/form-post.php' );
		acf_include( 'extend/includes/forms/form-settings.php' );
		acf_include( 'extend/includes/forms/form-taxonomy.php' );
		acf_include( 'extend/includes/forms/form-user.php' );
		acf_include( 'includes/acf-script-functions.php' );
		acf_include( 'includes/acf-template-functions.php' );
		acf_include( 'includes/acf-world-functions.php' );
		acf_include( 'includes/payment.php' );
		acf_include( 'includes/world.php' );

		acf_include( 'includes/modules/class-acf-script.php' );

		// Include locations.
		acf_include( 'includes/locations/class-acf-location-post-type.php' );
		acf_include( 'includes/locations/class-acf-location-post-template. php' );
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
		acf_include( 'includes/locations/post-type-all.php' );
		acf_include( 'includes/locations/post-type-archive.php' );
		acf_include( 'includes/locations/post-type-list.php' );
		acf_include( 'includes/locations/taxonomy-list.php' );
		acf_include( 'includes/locations/class-attachment-list.php' );
		acf_include( 'includes/locations/class-acf-location-location.php' );
		acf_include( 'includes/locations/class-acf-location-menu-item-depth.php' );
		acf_include( 'includes/locations/class-acf-location-menu-item-type.php' );
		acf_include( 'includes/locations/class-acf-location-post-author.php' );
		acf_include( 'includes/locations/class-acf-location-post-author-role.php' );
		acf_include( 'includes/locations/class-acf-location-post-date.php' );
		acf_include( 'includes/locations/class-acf-location-post-date-time.php' );
		acf_include( 'includes/locations/class-acf-location-post-path.php' );
		acf_include( 'includes/locations/class-acf-location-post-screen.php' );
		acf_include( 'includes/locations/class-acf-location-post-slug.php' );
		acf_include( 'includes/locations/class-acf-location-post-time.php' );
		acf_include( 'includes/locations/class-acf-location-post-title.php' );
		acf_include( 'includes/locations/settings.php' );
		acf_include( 'includes/locations/class-acf-location-taxonomy-term.php' );
		acf_include( 'includes/locations/class-acf-location-taxonomy-term-name.php' );
		acf_include( 'includes/locations/class-acf-location-taxonomy-term-parent.php' );
		acf_include( 'includes/locations/class-acf-location-taxonomy-term-slug.php' );
		acf_include( 'includes/locations/class-acf-location-taxonomy-term-type.php' );
		acf_include( 'includes/locations/user-list.php' );

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

		acf_include( 'includes/modules/class-acf-autosync.php' );
		acf_include( 'includes/modules/class-acf-module.php' );
		acf_include( 'includes/modules/class-acf-author.php' );
		acf_include( 'includes/modules/class-acf-dev.php' );
		acf_include( 'includes/modules/class-acf-dev-pro.php' );
		acf_include( 'includes/modules/class-acf-post-types.php' );
		acf_include( 'includes/modules/class-acf-taxonomies.php' );
		acf_include( 'includes/modules/class-acf-block-types.php' );
		acf_include( 'includes/modules/class-acf-forms.php' );
		acf_include( 'includes/modules/class-acf-templates.php' );
		acf_include( 'includes/modules/class-acf-options-pages.php' );
		acf_include( 'includes/modules/class-acf-options-editor.php' );
		acf_include( 'includes/modules/class-acf-single-meta.php' );
		acf_include( 'includes/modules/class-acf-screen-layouts.php' );
		acf_include( 'includes/modules/class-acf-global-field-condition.php' );
		acf_include( 'includes/modules/class-acf-rewrite-rules.php' );
		acf_include( 'includes/modules/class-acf-force-sync.php' );
		acf_include( 'includes/modules/class-acf-scripts.php' );
		acf_include( 'includes/modules/class-acf-scripts-list.php' );

		/**
		 * Fires after ACF is completely "initialized".
		 *
		 * @since 1.0.0
		 */
		do_action( 'acf/init' );

		acf_include( 'includes/fields/extend/field-clone.php' );
		acf_include( 'includes/fields/extend/field-file.php' );
		acf_include( 'includes/fields/extend/field-flexible-content.php' );
		acf_include( 'includes/fields/extend/field-group.php' );
		acf_include( 'includes/fields/extend/field-image.php' );
		acf_include( 'includes/fields/extend/field-post-object.php' );
		acf_include( 'includes/fields/extend/field-repeater.php' );
		acf_include( 'includes/fields/extend/field-select.php' );
		acf_include( 'includes/fields/extend/field-textarea.php' );

		acf_include( 'extend/includes/fields-settings/bidirectional.php' );
		acf_include( 'extend/includes/fields-settings/data.php' );
		acf_include( 'extend/includes/fields-settings/instructions.php' );
		acf_include( 'extend/includes/fields-settings/permissions.php' );
		acf_include( 'extend/includes/fields-settings/settings.php' );
		acf_include( 'extend/includes/fields-settings/validation.php' );
		acf_include( 'extend/includes/fields-settings/min-max.php' );
		acf_include( 'extend/includes/fields-settings/required.php' );
		acf_include( 'extend/includes/fields-settings/visibility.php' );

		acf_include( 'extend/includes/field-groups/field-group.php' );
		acf_include( 'extend/includes/field-groups/field-group-advanced.php' );
		acf_include( 'extend/includes/field-groups/field-group-category.php' );
		acf_include( 'extend/includes/field-groups/field-group-display-title.php' );
		acf_include( 'extend/includes/field-groups/field-group-hide-on-screen.php' );
		acf_include( 'extend/includes/field-groups/field-group-instruction-placement.php' );
		acf_include( 'extend/includes/field-groups/field-group-meta.php' );
		acf_include( 'extend/includes/field-groups/field-group-permissions.php' );
		acf_include( 'extend/includes/field-groups/field-groups.php' );
		acf_include( 'extend/includes/field-groups/field-groups-local.php' );
		acf_include( 'extend/includes/field-groups/field-group-ui.php' );

		acf_include( 'includes/class-advanced-fields-hooks.php' );
		acf_include( 'includes/class-advanced-values-hooks.php' );

		acf_include( 'includes/fields/extend-pro/field-checkbox.php' );
		acf_include( 'includes/fields/extend-pro/field-column.php' );
		acf_include( 'includes/fields/extend/field-color-picker.php' );
		acf_include( 'includes/fields/extend/field-date-picker.php' );
		acf_include( 'includes/fields/extend-pro/field-flexible-content-grid.php' );
		acf_include( 'includes/fields/extend/field-flexible-content-locations.php' );
		acf_include( 'includes/fields/extend-pro/field-file.php' );
		acf_include( 'includes/fields/extend-pro/field-radio.php' );
		acf_include( 'includes/fields/extend-pro/field-select.php' );
		acf_include( 'includes/fields/extend-pro/field-tab.php' );

		do_action( 'acf/init_late' );
	}

	/**
	 * Set ACFE settings
	 *
	 * @todo Remove this or expand to ACF.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function settings( $array = [] ) {

		foreach ( $array as $name => $value ) {
			acf_update_setting( "acfe/{$name}", $value );

			add_filter( "acf/settings/acfe/{$name}", function( $value ) use( $name ) {
				return apply_filters( "acfe/settings/{$name}", $value );
			}, 5 );
		}
	}

	/**
	 * Load local ACF fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load_local() {

		$theme_path = acf_get_setting(
			'acfe/theme_path',
			get_stylesheet_directory()
		);
		$theme_url = acf_get_setting(
			'acfe/theme_url',
			get_stylesheet_directory_uri()
		);

		// Settings.
		$this->settings( [

			// General
			'theme_path'   => $theme_path,
			'theme_url'    => $theme_url,
			'theme_folder' => parse_url( $theme_url, PHP_URL_PATH ),

			// PHP
			'php'       => true,
			'php_save'  => "{$theme_path}/acfe-php",
			'php_load'  => [ "{$theme_path}/acfe-php" ],
			'php_found' => false
		] );
	}

	/**
	 * Reserved post types
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function reserved_post_types() {
		$reserved = [
			'acf-field',
			'acf-field-group',
			'acf-post-type',
			'acf-taxonomy',
			'acf-block-type',
			'acf-form',
			'acf-template',
			'acf-options-page'
		];
		return apply_filters( 'acf/reserved_post_types', $reserved );
	}

	/**
	 * Reserved taxonomies
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function reserved_taxonomies() {
		$reserved = [ 'acf-field-group-category' ];
		return apply_filters( 'acf/reserved_taxonomies', $reserved );
	}

	/**
	 * Reserved field groups
	 *
	 * @todo Uncomment settings group when hard coded.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function reserved_field_groups() {
		$reserved = [
			// 'group_acf_settings',
			'group_acf_post_type',
			'group_acf_post_type_side',
			'group_acf_taxonomy',
			'group_acf_taxonomy_side',
			'group_acf_block_type',
			'group_acf_block_type_side',
			'group_acf_form',
			'group_acf_form_side',
			'group_acf_template_side',
			'group_acf_options_page',
			'group_acf_options_page_side',
		];
		return apply_filters( 'acf/reserved_field_groups', $reserved );
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
			'show_in_menu'    => $this->admin_slug(),
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
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_menu'      => $this->admin_slug(),
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
