<?php
/**
 * Extend ACF
 *
 * @package    Applied Content Forms
 * @subpackage Extend
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ACFE {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'acf/include_field_types', [ $this, 'load' ] );
	}

	/**
	 * Initialize the class
	 *
	 * Sets up the ACFE functionality.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		acfe_include( 'includes/acfe-field-functions.php' );
		acfe_include( 'includes/acfe-field-group-functions.php' );
		acfe_include( 'includes/acfe-file-functions.php' );
		acfe_include( 'includes/acfe-form-functions.php' );
		acfe_include( 'includes/acfe-helper-functions.php' );
		acfe_include( 'includes/acfe-meta-functions.php' );
		acfe_include( 'includes/acfe-post-functions.php' );
		acfe_include( 'includes/acfe-screen-functions.php' );
		acfe_include( 'includes/acfe-template-functions.php' );
		acfe_include( 'includes/acfe-term-functions.php' );
		acfe_include( 'includes/acfe-user-functions.php' );
		acfe_include( 'includes/acfe-wp-functions.php' );
		acfe_include( 'includes/compatibility.php' );
	}

	/*
	 * Load
	 */
	public function load() {

		$theme_path = acf_get_setting(
			'acfe/theme_path',
			get_stylesheet_directory()
		);
		$theme_url = acf_get_setting(
			'acfe/theme_url',
			get_stylesheet_directory_uri()
		);
		$reserved_post_types = [
			'acf-field',
			'acf-field-group',
			'acf-block-type',
			'acfe-form',
			'acf-options-page',
			'acf-post-type',
			'acf-taxonomy'
		];
		$reserved_taxonomies = [ 'acf-field-group-category' ];
		$reserved_field_groups = [
			'group_acfe_dynamic_post_type',
			'group_acfe_dynamic_taxonomy',
			'group_acfe_dynamic_block_type',
			'group_acfe_dynamic_form',
			'group_acfe_dynamic_options_page'
		];

		// Settings.
		$this->settings( [

			// General
			'url'                   => plugin_dir_url( __FILE__ ),
			'theme_path'            => $theme_path,
			'theme_url'             => $theme_url,
			'theme_folder'          => parse_url( $theme_url, PHP_URL_PATH ),
			'reserved_post_types'   => $reserved_post_types,
			'reserved_taxonomies'   => $reserved_taxonomies,
			'reserved_field_groups' => $reserved_field_groups,

			// PHP
			'php'       => true,
			'php_save'  => "{$theme_path}/acfe-php",
			'php_load'  => [ "{$theme_path}/acfe-php" ],
			'php_found' => false,

			// JSON
			'json'       => acf_get_setting( 'json' ),
			'json_save'  => acf_get_setting( 'save_json' ),
			'json_load'  => acf_get_setting( 'load_json' ),
			'json_found' => false,

			// Modules
			'dev'                   => false,
			'modules/author'        => true,
			'modules/categories'    => true,
			'modules/post_types'    => true,
			'modules/taxonomies'    => true,
			'modules/block_types'   => true,
			'modules/forms'         => true,
			'modules/options_pages' => true,
			'modules/multilang'     => true,
			'modules/options'       => true,
			'modules/single_meta'   => false,
			'modules/ui'            => true,

			// Fields
			'field/recaptcha/site_key'     => null,
			'field/recaptcha/secret_key'   => null,
			'field/recaptcha/version'      => null,
			'field/recaptcha/v2/theme'     => null,
			'field/recaptcha/v2/size'      => null,
			'field/recaptcha/v3/hide_logo' => null,

		] );

		// Load textdomain file.
		acfe_load_textdomain();

		add_action( 'acf/init', [ $this, 'init' ], 99 );
		add_action( 'acf/include_fields', [ $this, 'include_fields' ], 5 );
		add_action( 'acf/include_field_types', [ $this, 'include_field_types' ], 99 );
		add_action( 'acf/include_admin_tools', [ $this, 'include_admin_tools' ] );
		add_action( 'acf/include_admin_tools', [ $this, 'include_admin_tools_late' ], 20 );

		acfe_include( 'includes/admin/menu.php' );
		acfe_include( 'includes/admin/plugins.php' );
		acfe_include( 'includes/admin/settings.php' );

		acfe_include( 'includes/local-meta.php' );
		acfe_include( 'includes/multilang.php' );
		acfe_include( 'includes/settings.php' );
		acfe_include( 'includes/upgrades.php' );

		acfe_include( 'includes/forms/form-attachment.php' );
		acfe_include( 'includes/forms/form-options-page.php' );
		acfe_include( 'includes/forms/form-post.php' );
		acfe_include( 'includes/forms/form-settings.php' );
		acfe_include( 'includes/forms/form-taxonomy.php' );
		acfe_include( 'includes/forms/form-user.php' );

		acfe_include( 'pro/acf-extended-pro.php' );

	}

	/**
	 * Initialize extended
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {

		do_action( 'acfe/init' );

		acfe_include( 'includes/assets.php' );
		acfe_include( 'includes/hooks.php' );

		acfe_include( 'includes/admin/admin.php' );
		acfe_include( 'includes/admin/plugins.php' );

		acfe_include( 'includes/fields/field-checkbox.php' );
		acfe_include( 'includes/fields/field-clone.php' );
		acfe_include( 'includes/fields/field-file.php' );
		acfe_include( 'includes/fields/field-flexible-content.php' );
		acfe_include( 'includes/fields/field-group.php' );
		acfe_include( 'includes/fields/field-image.php' );
		acfe_include( 'includes/fields/field-post-object.php' );
		acfe_include( 'includes/fields/field-repeater.php' );
		acfe_include( 'includes/fields/field-select.php' );
		acfe_include( 'includes/fields/field-textarea.php' );

		acfe_include( 'includes/fields-settings/bidirectional.php' );
		acfe_include( 'includes/fields-settings/data.php' );
		acfe_include( 'includes/fields-settings/instructions.php' );
		acfe_include( 'includes/fields-settings/permissions.php' );
		acfe_include( 'includes/fields-settings/settings.php' );
		acfe_include( 'includes/fields-settings/validation.php' );

		acfe_include( 'includes/field-groups/field-group.php' );
		acfe_include( 'includes/field-groups/field-group-advanced.php' );
		acfe_include( 'includes/field-groups/field-group-category.php' );
		acfe_include( 'includes/field-groups/field-group-display-title.php' );
		acfe_include( 'includes/field-groups/field-group-hide-on-screen.php' );
		acfe_include( 'includes/field-groups/field-group-instruction-placement.php' );
		acfe_include( 'includes/field-groups/field-group-meta.php' );
		acfe_include( 'includes/field-groups/field-group-permissions.php' );
		acfe_include( 'includes/field-groups/field-groups.php' );
		acfe_include( 'includes/field-groups/field-groups-local.php' );

		acfe_include( 'includes/locations/post-type-all.php' );
		acfe_include( 'includes/locations/post-type-archive.php' );
		acfe_include( 'includes/locations/post-type-list.php' );

		acfe_include( 'includes/modules/module.php' );
		acfe_include( 'includes/modules/author.php' );
		acfe_include( 'includes/modules/dev.php' );
		acfe_include( 'includes/modules/post-types.php' );
		acfe_include( 'includes/modules/taxonomies.php' );
		acfe_include( 'includes/modules/block-types.php' );
		acfe_include( 'includes/modules/forms.php' );
		acfe_include( 'includes/modules/options.php' );
		acfe_include( 'includes/modules/options-pages.php' );
		acfe_include( 'includes/modules/single-meta.php' );
	}

	/**
	 * Include fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_fields() {
		acfe_include( 'includes/modules/autosync.php' );
	}

	/**
	 * Include field types
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_field_types(){

		acfe_include( 'includes/fields/field-advanced-link.php' );
		acfe_include( 'includes/fields/field-button.php' );
		acfe_include( 'includes/fields/field-code-editor.php' );
		acfe_include( 'includes/fields/field-column.php' );
		acfe_include( 'includes/fields/field-dynamic-render.php' );
		acfe_include( 'includes/fields/field-forms.php' );
		acfe_include( 'includes/fields/field-hidden.php' );
		acfe_include( 'includes/fields/field-post-statuses.php' );
		acfe_include( 'includes/fields/field-post-types.php' );
		acfe_include( 'includes/fields/field-recaptcha.php' );
		acfe_include( 'includes/fields/field-slug.php' );
		acfe_include( 'includes/fields/field-taxonomies.php' );
		acfe_include( 'includes/fields/field-taxonomy-terms.php' );
		acfe_include( 'includes/fields/field-user-roles.php' );
	}

	/**
	 * Include admin tools
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_admin_tools(){

		acfe_include( 'includes/admin/tools/module-export.php' );
		acfe_include( 'includes/admin/tools/module-import.php' );
		acfe_include( 'includes/admin/tools/post-types-export.php' );
		acfe_include( 'includes/admin/tools/post-types-import.php' );
		acfe_include( 'includes/admin/tools/taxonomies-export.php' );
		acfe_include( 'includes/admin/tools/taxonomies-import.php' );
		acfe_include( 'includes/admin/tools/options-pages-export.php' );
		acfe_include( 'includes/admin/tools/options-pages-import.php' );
		acfe_include( 'includes/admin/tools/block-types-export.php' );
		acfe_include( 'includes/admin/tools/block-types-import.php' );
		acfe_include( 'includes/admin/tools/forms-export.php' );
		acfe_include( 'includes/admin/tools/forms-import.php' );
	}

	/**
	 * Include admin tools late
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_admin_tools_late() {
		acfe_include( 'includes/admin/tools/field-groups-local.php' );
		acfe_include( 'includes/admin/tools/field-groups-export.php' );
	}

	/**
	 * Set settings
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
}

/*
 * Instantiate the ACFE class.
 *
 * The main function responsible for returning the one true
 * ACFE instance to functions everywhere.
 * Use this function like you would a global variable,
 * except without needing to declare the global.
 *
 * Example: <?php $acfe = acfe(); ?>
 *
 * @since  1.0.0
 * @global object $acfe
 * @return object Returns an instance of the ACFE class.
 */
function acfe() {

	// Set a global variable.
	global $acfe;

	// Instantiate only once.
	if ( ! isset( $acfe ) ) {
		$acfe = new ACFE();
		$acfe->initialize();
	}
	return $acfe;
}
acfe();
