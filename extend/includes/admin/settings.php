<?php
/**
 * Content settings fields
 *
 * Sets up the fields for the settings tab on the main content page.
 *
 * @package    ACF
 * @subpackage Admin
 * @category   Settings
 */

use function ACF\acf;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acfe_admin_settings' ) ) :

class acfe_admin_settings {

	/**
	 * Default settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = [];

	/**
	 * Updated settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $updated = [];

	/**
	 * Settings fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $fields = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'acf/init', [ $this, 'acf_pre_init' ], 1 );
		add_action( 'acf/init', [ $this, 'acf_init' ], 9 );
		add_action( 'acf/init', [ $this, 'acf_post_init' ], 100 );

		$this->register_fields();
	}

	/**
	 * Pre Init
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function acf_pre_init() {
		$this->defaults = acf()->settings;
	}

	/**
	 * ACF init
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function acf_init() {

		$settings = acfe_get_settings( 'settings' );

		if ( empty( $settings ) ) {
			return;
		}

		foreach ( $settings as $k => $v ) {
			acf_update_setting( $k, $v );
		}
	}

	/**
	 * Post Init
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function acf_post_init() {
		$this->updated = acf()->settings;
	}

	/*
	 * Register Fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_fields() {

		$this->fields = [

			// Features tab.
			'features' => [
				[
					'label'       => __( 'Classic Editor', 'acf' ),
					'name'        => 'acfe/modules/classic_editor',
					'description' => __( 'Enable classic editor module. Defaults to false.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'features',
				],
				[
					'label'       => __( 'Post Types', 'acf' ),
					'name'        => 'acfe/modules/post_types',
					'type'        => 'true_false',
					'description' => __( 'Enable custom post types creation. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Taxonomies', 'acf' ),
					'name'        => 'acfe/modules/taxonomies',
					'type'        => 'true_false',
					'description' => __( 'Enable custom taxonomies creation. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Block Types', 'acf' ),
					'name'        => 'acfe/modules/block_types',
					'type'        => 'true_false',
					'description' => __( 'Enable custom block types creation. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Templates', 'acf' ),
					'name'        => 'acfe/modules/templates',
					'description' => __( 'Enable post edit templates. Defaults to true.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'features',
				],
				[
					'label'       => __( 'Forms', 'acf' ),
					'name'        => 'acfe/modules/forms',
					'type'        => 'true_false',
					'description' => __( 'Enable dynamic forms creation. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Options Pages', 'acf' ),
					'name'        => 'acfe/modules/options_pages',
					'type'        => 'true_false',
					'description' => __( 'Enable dynamic options pages. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Field Categories', 'acf' ),
					'name'        => 'acfe/modules/categories',
					'type'        => 'true_false',
					'description' => __( 'Enable the Field Group Categories taxonomy. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Field Group UI', 'acf' ),
					'name'        => 'acfe/modules/field_group_ui',
					'description' => __( 'Enable the enhanced field group UI module. Defaults to true.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'features',
				],
				[
					'label'       => __( 'Author Metabox', 'acf' ),
					'name'        => 'acfe/modules/author',
					'type'        => 'true_false',
					'description' => __( 'Enhance the author metabox on the post edit screen. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Multilingual', 'acf' ),
					'name'        => 'acfe/modules/multilang',
					'type'        => 'true_false',
					'description' => __( 'Enable multi-language compatibility for WPML & Polylang. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Single Meta', 'acf' ),
					'name'        => 'acfe/modules/single_meta',
					'type'        => 'true_false',
					'description' => __( 'Enable single meta save option. Defaults to false.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'UI Enhancements', 'acf' ),
					'name'        => 'acfe/modules/ui',
					'type'        => 'true_false',
					'description' => __( 'Enable UI enhancements. Defaults to true.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Developer Mode', 'acf' ),
					'name'        => 'acfe/dev',
					'type'        => 'true_false',
					'description' => __( 'Show advanced data in the publish metabox on post edit screens. Defaults to false.', 'acf' ),
					'category'    => 'features',
				],
				[
					'label'       => __( 'Rewrite Rules', 'acf' ),
					'name'        => 'acfe/modules/rewrite_rules',
					'description' => __( 'Enable the rewrite rules UI. Defaults to true.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'features',
				],
				[
					'label'       => __( 'Screen Layouts', 'acf' ),
					'name'        => 'acfe/modules/screen_layouts',
					'description' => __( 'Enable screen layouts for post edit screens. Defaults to true.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'features',
				],
				[
					'label'       => __( 'Scripts', 'acf' ),
					'name'        => 'acfe/modules/scripts',
					'description' => __( 'Enable the Scripts UI. Defaults to true.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'features',
				]
			],

			// Options tab.
			'options' => [
				[
					'label'       => __( 'Plugin Path', 'acf' ),
					'name'        => 'path',
					'type'        => 'text',
					'description' => __( 'Absolute path to bundled ACF plugin folder including trailing slash.<br />Defaults to plugin_dir_path', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Plugin URL', 'acf' ),
					'name'        => 'url',
					'type'        => 'text',
					'description' => __( 'URL to bundled ACF plugin folder including trailing slash. Defaults to plugin_dir_url', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'User Capability', 'acf' ),
					'name'        => 'capability',
					'type'        => 'text',
					'description' => __( 'Capability used for ACF post types and if the current user can see the content submenu items.<br />Defaults to ‘manage_options’.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Show Admin', 'acf' ),
					'name'        => 'show_admin',
					'type'        => 'true_false',
					'description' => __( 'Show/hide content submenu items. Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Native Custom Fields', 'acf' ),
					'name'        => 'remove_wp_meta_box',
					'type'        => 'true_false',
					'description' => __( 'Remove the default custom fields metabox. Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Local', 'acf' ),
					'name'        => 'local',
					'type'        => 'true_false',
					'description' => __( 'Enable/Disable local (PHP/JSON) fields. Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'JSON', 'acf' ),
					'name'        => 'json',
					'type'        => 'true_false',
					'description' => __( 'Enable/Disable JSON fields. Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'JSON folder (save)', 'acf' ),
					'name'        => 'save_json',
					'type'        => 'text',
					'description' => __( 'Absolute path to folder where JSON files will be created when field groups are saved.<br />Defaults to ‘acf-json’ folder within current theme', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'JSON folder (load)', 'acf' ),
					'name'        => 'load_json',
					'type'        => 'text',
					'description' => __( 'Array of absolutes paths to folders where field group JSON files can be read.<br />Defaults to an array containing at index 0, the ‘acf-json’ folder within current theme', 'acf' ),
					'category'    => 'options',
					'format'      => 'array',
				],
				[
					'label'       => __( 'Force Sync', 'acf' ),
					'name'        => 'acfe/modules/force_sync',
					'description' => __( 'Enable the Force Sync module. Defaults to false.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'options',
				],
				[
					'label'       => __( 'Force Sync: Delete', 'acf' ),
					'name'        => 'acfe/modules/force_sync/delete',
					'description' => __( 'Sync deleted field groups files. Force Sync must be enabled. Defaults to false.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'options',
				],
				[
					'label'       => __( 'Default Language', 'acf' ),
					'name'        => 'default_language',
					'type'        => 'true_false',
					'description' => __( 'Language code of the default language. Defaults to ”.<br />If WPML is active, defaults to the WPML default language setting', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Current Language', 'acf' ),
					'name'        => 'current_language',
					'type'        => 'true_false',
					'description' => __( 'Language code of the current post’s language. Defaults to ”.<br />If WPML is active, ACF will default this to the WPML current language', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Auto Load', 'acf' ),
					'name'        => 'autoload',
					'type'        => 'true_false',
					'description' => __( 'Sets the text domain used when translating field and field group settings.<br />Defaults to ”. Strings will not be translated if this setting is empty', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'l10n', 'acf' ),
					'name'        => 'l10n',
					'type'        => 'true_false',
					'description' => __( 'Allows ACF to translate field and field group settings using the __() function.<br />Defaults to true. Useful to override translation without modifying the textdomain', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'l10n Textdomain', 'acf' ),
					'name'        => 'l10n_textdomain',
					'type'        => 'true_false',
					'description' => __( 'Sets the text domain used when translating field and field group settings.<br />Defaults to ”. Strings will not be translated if this setting is empty', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Forms: Shortcode Preview', 'acf' ),
					'name'        => 'acfe/modules/forms/shortcode_preview',
					'type'        => 'text',
					'description' => __( 'Display <code>[acfe_form]</code> shortcode preview in editors. Defaults to false.', 'acf' ),
					'category'    => 'options',
					'format'      => 'array',
				],
				[
					'label'       => __( 'Google API Key', 'acf' ),
					'name'        => 'google_api_key',
					'type'        => 'text',
					'description' => __( 'Specify a Google Maps API authentication key to prevent usage limits.<br />Defaults to ”', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Google API Client', 'acf' ),
					'name'        => 'google_api_client',
					'type'        => 'text',
					'description' => __( 'Specify a Google Maps API Client ID to prevent usage limits.<br />Not needed if using <code>google_api_key</code>. Defaults to ”', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Enqueue Google Maps', 'acf' ),
					'name'        => 'enqueue_google_maps',
					'type'        => 'true_false',
					'description' => __( 'Allows ACF to enqueue and load the Google Maps API JS library.<br />Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Enqueue Select2', 'acf' ),
					'name'        => 'enqueue_select2',
					'type'        => 'true_false',
					'description' => __( 'Allows ACF to enqueue and load the Select2 JS/CSS library.<br />Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Select2 Version', 'acf' ),
					'name'        => 'select2_version',
					'type'        => 'text',
					'description' => __( 'Defines which version of Select2 library to enqueue. Either 3 or 4.<br />Defaults to 4 since ACF 5.6.0', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Enqueue Date Picker', 'acf' ),
					'name'        => 'enqueue_datepicker',
					'type'        => 'true_false',
					'description' => __( 'Allows ACF to enqueue and load the WP datepicker JS/CSS library.<br />Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Enqueue Date Time Picker', 'acf' ),
					'name'        => 'enqueue_datetimepicker',
					'type'        => 'true_false',
					'description' => __( 'Allows ACF to enqueue and load the datetimepicker JS/CSS library.<br />Defaults to true.', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Global Field Condition', 'acf' ),
					'name'        => 'acfe/modules/global_field_condition',
					'description' => __( 'Enable global field condition. Defaults to true.', 'acf' ),
					'type'        => 'true_false',
					'category'    => 'options',
				],
				[
					'label'       => __( 'Row Index Offset', 'acf' ),
					'name'        => 'row_index_offset',
					'type'        => 'text',
					'description' => __( 'Defines the starting index used in all ‘loop’ and ‘row’ functions.<br />Defaults to 1 (1 is the first row), can be changed to 0 (0 is the first row)', 'acf' ),
					'category'    => 'options',
				],
				[
					'label'       => __( 'Strip Slashes', 'acf' ),
					'name'        => 'stripslashes',
					'type'        => 'true_false',
					'description' => __( 'Runs the function stripslashes on all $_POST data. Some servers/platform installs may require this extra functionality. Defaults to false.', 'acf' ),
					'category'    => 'options',
				]
			],

			// Theme tab.
			'theme' => [

				[
					'label'       => __( 'Theme Folder', 'acf' ),
					'name'        => 'acfe/theme_folder',
					'type'        => 'text',
					'description' => __( 'Detected Theme Folder', 'acf' ),
					'category'    => 'theme',
				],
				[
					'label'       => __( 'Theme Path', 'acf' ),
					'name'        => 'acfe/theme_path',
					'type'        => 'text',
					'description' => __( 'Detected Theme Path', 'acf' ),
					'category'    => 'theme',
				],
				[
					'label'       => __( 'Theme URL', 'acf' ),
					'name'        => 'acfe/theme_url',
					'type'        => 'text',
					'description' => __( 'Detected Theme URL', 'acf' ),
					'category'    => 'theme',
				]
			],

			// AutoSync tab.
			'autosync' => [
				[
					'label'       => __( 'JSON', 'acf' ),
					'name'        => 'acfe/json',
					'type'        => 'true_false',
					'description' => __( 'Whenever JSON AutoSync is enabled', 'acf' ),
					'category'    => 'autosync',
				],
				[
					'label'       => __( 'JSON: Load', 'acf' ),
					'name'        => 'acfe/json_load',
					'type'        => 'text',
					'description' => __( 'JSON AutoSync load paths (array)', 'acf' ),
					'category'    => 'autosync',
					'format'      => 'array',
				],
				[
					'label'       => __( 'JSON: Save', 'acf' ),
					'name'        => 'acfe/json_save',
					'type'        => 'text',
					'description' => __( 'JSON AutoSync saving path', 'acf' ),
					'category'    => 'autosync',
				],
				[
					'label'       => __( 'PHP', 'acf' ),
					'name'        => 'acfe/php',
					'type'        => 'true_false',
					'description' => __( 'Whenever PHP AutoSync is enabled', 'acf' ),
					'category'    => 'autosync',
				],
				[
					'label'       => __( 'PHP: Load', 'acf' ),
					'name'        => 'acfe/php_load',
					'type'        => 'text',
					'description' => __( 'PHP AutoSync load paths (array)', 'acf' ),
					'category'    => 'autosync',
					'format'      => 'array',
				],
				[
					'label'       => __( 'PHP: Save', 'acf' ),
					'name'        => 'acfe/php_save',
					'type'        => 'text',
					'description' => __( 'PHP AutoSync saving path', 'acf' ),
					'category'    => 'autosync',
				]
			],

			// reCaptcha tab.
			'recaptcha' => [
				[
					'label'       => __( 'reCaptcha: Secret Key', 'acf' ),
					'name'        => 'acfe/field/recaptcha/secret_key',
					'type'        => 'text',
					'description' => __( 'The default reCaptcha secret key', 'acf' ),
					'category'    => 'recaptcha',
				],
				[
					'label'       => __( 'reCaptcha: Site Key', 'acf' ),
					'name'        => 'acfe/field/recaptcha/site_key',
					'type'        => 'text',
					'description' => __( 'The default reCaptcha site key', 'acf' ),
					'category'    => 'recaptcha',
				],
				[
					'label'       => __( 'reCaptcha: Version', 'acf' ),
					'name'        => 'acfe/field/recaptcha/version',
					'type'        => 'text',
					'description' => __( 'The default reCaptcha version', 'acf' ),
					'category'    => 'recaptcha',
				],
				[
					'label'       => __( 'reCaptcha: V2 Size', 'acf' ),
					'name'        => 'acfe/field/recaptcha/v2/size',
					'type'        => 'text',
					'description' => __( 'The default reCaptcha v2 size', 'acf' ),
					'category'    => 'recaptcha',
				],
				[
					'label'       => __( 'reCaptcha: V2 Theme', 'acf' ),
					'name'        => 'acfe/field/recaptcha/v2/theme',
					'type'        => 'text',
					'description' => __( 'The default reCaptcha v2 theme', 'acf' ),
					'category'    => 'recaptcha',
				],
				[
					'label'       => __( 'reCaptcha: V3 Hide Logo', 'acf' ),
					'name'        => 'acfe/field/recaptcha/v3/hide_logo',
					'type'        => 'true_false',
					'description' => __( 'Show/hide reCaptcha v3 logo', 'acf' ),
					'category'    => 'recaptcha',
				]
			]
		];
	}
}

// Instantiate the `acfe_admin_settings` class.
acf_new_instance( 'acfe_admin_settings' );

endif; // End if `acfe_admin_settings` class.

if ( ! class_exists( 'acfe_admin_settings_ui' ) ) :

class acfe_admin_settings_ui{

	/**
	 * Default settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = [];

	/**
	 * Updated settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $updated = [];

	/**
	 * Settings fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $fields = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {}

	/*
	 * Load
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		$acfe_admin_settings = acf_get_instance( 'acfe_admin_settings' );

		$this->defaults = $acfe_admin_settings->defaults;
		$this->updated  = $acfe_admin_settings->updated;
		$this->fields   = $acfe_admin_settings->fields;

		// Enqueue scripts.
		acf_enqueue_scripts();

	}

	/*
	 * Prepare setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function prepare_setting( $setting) {

		$setting = wp_parse_args( $setting, [
			'label'       => '',
			'name'        => '',
			'type'        => '',
			'description' => '',
			'category'    => '',
			'format'      => '',
			'default'     => '',
			'updated'     => '',
			'diff'        => false
		] );

		$name    = $setting['name'];
		$type    = $setting['type'];
		$format  = $setting['format'];
		$default = $this->defaults[$name];
		$updated = $this->updated[$name];

		$vars = [
			'default' => $this->defaults[$name],
			'updated' => $this->updated[$name]
		];

		foreach ( $vars as $v => $var ) {

			$result = $var;

			if ( $type === 'true_false' ) {

				$result = $var ? '<span class="dashicons dashicons-saved"></span>' : '<span class="dashicons dashicons-no-alt"></span>';

			} elseif ( $type === 'text' ) {

				$result = '<span class="dashicons dashicons-no-alt"></span>';

				if ( $format === 'array' && empty( $var) && $v === 'updated' && $default !== $updated) {
					$var = [ '(empty)' ];
				}

				if ( ! empty( $var ) ) {

					if ( ! is_array( $var ) ) {
						$var = explode( ',', $var );
					}

					foreach ( $var as &$r ) {
						$r = '<div class="acf-js-tooltip acfe-settings-text" title="' . $r . '"><code>' . $r . '</code></div>';
					}
					$result = implode( '', $var );
				}
			}
			$setting[$v] = $result;
		}

		// Local Changes.
		if ( $default !== $updated ) {

			$setting['updated'] .= '<span style="color:#888; margin-left:7px;vertical-align: 6px;font-size:11px;">(Local code)</span>';

			$setting['diff'] = true;
		}

		return $setting;
	}

	/**
	 * Render fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_fields() {

		// Tabs for field categories.
		$tabs = [
			__( 'Features', 'acf' ),
			__( 'Options', 'acf' ),
			__( 'Theme', 'acf' ),
			__( 'AutoSync', 'acf' ),
			__( 'reCaptcha', 'acf' )
		];

		// Render tabs.
		foreach ( $tabs as $tab ) {

			// Field category.
			$category = sanitize_title( $tab );

			if ( isset( $this->fields[$category] ) ) {

				$fields = [];
				$count  = 0;

				foreach ( $this->fields[$category] as $field ) {
					$field    = $this->prepare_setting( $field);
					$fields[] = $field;
				}

				foreach ( $fields as $field ) {
					if ( ! $field['diff'] ) {
						continue;
					}
					$count++;
				}

				$class = $count > 0 ? 'acfe-tab-badge' : 'acfe-tab-badge acf-hidden';
				$tab  .= ' <span class="' . $class . '">' . $count . '</span>';

				// Tab.
				acf_render_field_wrap( [
					'type'  => 'tab',
					'label' => $tab,
					'key'   => 'field_acfe_settings_tabs',
					'wrapper' => [
						'data-no-preference' => true,
					]
				] );

				// Table head
				acf_render_field_wrap( [
					'type'    => 'acfe_dynamic_render',
					'label'   => '',
					'key'     => 'field_acfe_settings_thead_' . $category,
					'wrapper' => [
						'class' => 'acfe-settings-thead'
					],
					'render'  => function( $field ) {
						?>
						<div><?php _e( 'Default', 'acf' ); ?></div>
						<div><?php _e( 'Registered', 'acf' ); ?></div>
						<?php
					}
				] );

				foreach ( $fields as $field ) {

				?>
				<div class="acf-field">
					<div class="acf-label">
						<label><span class="acf-js-tooltip dashicons dashicons-info" title="<?php echo $field['name']; ?>"></span><?php echo $field['label']; ?></label>
						<?php if ( $field['description'] ) { ?>
							<p class="description"><?php echo $field['description']; ?></p>
						<?php } ?>
					</div>
					<div class="acf-input">

						<div><?php echo $field['default']; ?></div>
						<div><?php echo $field['updated']; ?></div>

					</div>
				</div>
				<?php
				}
			}
		}
	}
}

// Instantiate the `acfe_admin_settings_ui` class.
acf_new_instance( 'acfe_admin_settings_ui' );

endif; // End if `acfe_admin_settings_ui` class.
