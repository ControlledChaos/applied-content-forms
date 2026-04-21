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

final class Extend_ACF {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'acf/include_field_types', [ $this, 'load' ] );
		add_action( 'acfe/include_form_actions', [ $this, 'include_form_actions' ] );
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

		acf_include( 'extend/includes/acfe-helper-functions.php' );
		acf_include( 'extend/includes/acfe-meta-functions.php' );
		acf_include( 'extend/includes/acfe-post-functions.php' );
		acf_include( 'extend/includes/acfe-screen-functions.php' );
		acf_include( 'extend/includes/acfe-template-functions.php' );
		acf_include( 'extend/includes/acfe-term-functions.php' );
		acf_include( 'extend/includes/acfe-user-functions.php' );
		acf_include( 'extend/includes/acfe-wp-functions.php' );
		acf_include( 'extend/includes/compatibility.php' );
	}

	/**
	 * Load extended
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
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

		// Settings.
		$this->settings( [

			// General
			'theme_path'            => $theme_path,
			'theme_url'             => $theme_url,
			'theme_folder'          => parse_url( $theme_url, PHP_URL_PATH ),

			// PHP
			'php'       => true,
			'php_save'  => "{$theme_path}/acfe-php",
			'php_load'  => [ "{$theme_path}/acfe-php" ],
			'php_found' => false
		] );

		add_action( 'acf/init', [ $this, 'init' ], 99 );
		add_action( 'acf/include_fields', [ $this, 'include_fields' ], 5 );
		add_action( 'acf/include_field_types', [ $this, 'include_field_types' ], 99 );
		add_action( 'acf/include_admin_tools', [ $this, 'include_admin_tools' ] );
		add_action( 'acf/include_admin_tools', [ $this, 'include_admin_tools_late' ], 20 );

		acf_include( 'extend/includes/local-meta.php' );
		acf_include( 'extend/includes/multilang.php' );
		acf_include( 'extend/includes/settings.php' );
		acf_include( 'extend/includes/upgrades.php' );

		acf_include( 'extend/includes/forms/form-attachment.php' );
		acf_include( 'extend/includes/forms/form-options-page.php' );
		acf_include( 'extend/includes/forms/form-post.php' );
		acf_include( 'extend/includes/forms/form-settings.php' );
		acf_include( 'extend/includes/forms/form-taxonomy.php' );
		acf_include( 'extend/includes/forms/form-user.php' );

		// Functions.
		acf_include( 'extend/includes/acfe-script-functions.php' );
		acf_include( 'extend/includes/acfe-template-functions.php' );
		acf_include( 'extend/includes/acfe-world-functions.php' );
		acf_include( 'extend/includes/payment.php' );
		acf_include( 'extend/includes/world.php' );

		// Modules.
		acf_include( 'extend/includes/modules/scripts-class.php' );
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

		acf_include( 'extend/includes/class-extend-assets.php' );
		acf_include( 'extend/includes/hooks.php' );

		acf_include( 'extend/includes/admin/admin.php' );
		acf_include( 'extend/includes/admin/plugins.php' );

		acf_include( 'extend/includes/fields/field-checkbox.php' );
		acf_include( 'extend/includes/fields/field-clone.php' );
		acf_include( 'extend/includes/fields/field-file.php' );
		acf_include( 'extend/includes/fields/field-flexible-content.php' );
		acf_include( 'extend/includes/fields/field-group.php' );
		acf_include( 'extend/includes/fields/field-image.php' );
		acf_include( 'extend/includes/fields/field-post-object.php' );
		acf_include( 'extend/includes/fields/field-repeater.php' );
		acf_include( 'extend/includes/fields/field-select.php' );
		acf_include( 'extend/includes/fields/field-textarea.php' );

		acf_include( 'extend/includes/fields-settings/bidirectional.php' );
		acf_include( 'extend/includes/fields-settings/data.php' );
		acf_include( 'extend/includes/fields-settings/instructions.php' );
		acf_include( 'extend/includes/fields-settings/permissions.php' );
		acf_include( 'extend/includes/fields-settings/settings.php' );
		acf_include( 'extend/includes/fields-settings/validation.php' );

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

		acf_include( 'extend/includes/modules/module.php' );
		acf_include( 'extend/includes/modules/author.php' );
		acf_include( 'extend/includes/modules/dev.php' );
		acf_include( 'extend/includes/modules/post-types.php' );
		acf_include( 'extend/includes/modules/taxonomies.php' );
		acf_include( 'extend/includes/modules/block-types.php' );
		acf_include( 'extend/includes/modules/forms.php' );
		acf_include( 'extend/includes/modules/templates.php' );
		acf_include( 'extend/includes/modules/options-pages.php' );
		acf_include( 'extend/includes/modules/options.php' );
		acf_include( 'extend/includes/modules/single-meta.php' );
		acf_include( 'extend/includes/modules/screen-layouts.php' );
		acf_include( 'extend/includes/modules/global-field-condition.php' );
		acf_include( 'extend/includes/modules/rewrite-rules.php' );

		acf_include( 'extend/includes/pro-hooks.php' );

		acf_include( 'extend/pro/includes/fields/field-checkbox.php' );
		acf_include( 'extend/pro/includes/fields/field-column.php' );
		acf_include( 'extend/pro/includes/fields/field-color-picker.php' );
		acf_include( 'extend/pro/includes/fields/field-date-picker.php' );
		acf_include( 'extend/pro/includes/fields/field-flexible-content-grid.php' );
		acf_include( 'extend/pro/includes/fields/field-flexible-content-locations.php' );
		acf_include( 'extend/pro/includes/fields/field-file.php' );
		acf_include( 'extend/pro/includes/fields/field-radio.php' );
		acf_include( 'extend/pro/includes/fields/field-select.php' );
		acf_include( 'extend/pro/includes/fields/field-tab.php' );

		acf_include( 'extend/pro/includes/fields-settings/instructions.php' );
		acf_include( 'extend/pro/includes/fields-settings/min-max.php' );
		acf_include( 'extend/pro/includes/fields-settings/required.php' );
		acf_include( 'extend/pro/includes/fields-settings/visibility.php' );

		acf_include( 'extend/includes/field-groups/field-group-ui.php' );

		acf_include( 'extend/includes/modules/dev.php' );
		acf_include( 'extend/includes/modules/force-sync.php' );
		acf_include( 'extend/includes/modules/scripts.php' );
		acf_include( 'extend/includes/modules/scripts-list.php' );
	}

	/**
	 * Include fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_fields() {
		acf_include( 'extend/includes/modules/autosync.php' );
	}

	/**
	 * Include form actions
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function include_form_actions() {
		acf_include( 'extend/includes/modules/forms-action-option.php' );
	}

	/**
	 * Include field types
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_field_types(){

		acf_include( 'extend/includes/fields/field-advanced-link.php' );
		acf_include( 'extend/includes/fields/field-button.php' );
		acf_include( 'extend/includes/fields/field-code-editor.php' );
		acf_include( 'extend/includes/fields/field-column.php' );
		acf_include( 'extend/includes/fields/field-dynamic-render.php' );
		acf_include( 'extend/includes/fields/field-forms.php' );
		acf_include( 'extend/includes/fields/field-hidden.php' );
		acf_include( 'extend/includes/fields/field-post-statuses.php' );
		acf_include( 'extend/includes/fields/field-post-types.php' );
		acf_include( 'extend/includes/fields/field-recaptcha.php' );
		acf_include( 'extend/includes/fields/field-slug.php' );
		acf_include( 'extend/includes/fields/field-taxonomies.php' );
		acf_include( 'extend/includes/fields/field-taxonomy-terms.php' );
		acf_include( 'extend/includes/fields/field-user-roles.php' );

		acf_include( 'extend/pro/includes/fields/field-block-types.php' );
		acf_include( 'extend/pro/includes/fields/field-countries.php' );
		acf_include( 'extend/pro/includes/fields/field-currencies.php' );
		acf_include( 'extend/pro/includes/fields/field-date-range-picker.php' );
		acf_include( 'extend/pro/includes/fields/field-field-groups.php' );
		acf_include( 'extend/pro/includes/fields/field-field-types.php' );
		acf_include( 'extend/pro/includes/fields/field-fields.php' );
		acf_include( 'extend/pro/includes/fields/field-google-map.php' );
		acf_include( 'extend/pro/includes/fields/field-image-selector.php' );
		acf_include( 'extend/pro/includes/fields/field-image-sizes.php' );
		acf_include( 'extend/pro/includes/fields/field-languages.php' );
		acf_include( 'extend/pro/includes/fields/field-menus.php' );
		acf_include( 'extend/pro/includes/fields/field-menu-locations.php' );
		acf_include( 'extend/pro/includes/fields/field-options-pages.php' );
		acf_include( 'extend/pro/includes/fields/field-payment.php' );
		acf_include( 'extend/pro/includes/fields/field-payment-cart.php' );
		acf_include( 'extend/pro/includes/fields/field-payment-selector.php' );
		acf_include( 'extend/pro/includes/fields/field-phone-number.php' );
		acf_include( 'extend/pro/includes/fields/field-post-field.php' );
		acf_include( 'extend/pro/includes/fields/field-post-formats.php' );
		acf_include( 'extend/pro/includes/fields/field-relationship.php' );
		acf_include( 'extend/pro/includes/fields/field-templates.php' );
		acf_include( 'extend/pro/includes/fields/field-wysiwyg.php' );
	}

	/**
	 * Include admin tools
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_admin_tools(){

		acf_include( 'extend/includes/admin/tools/module-export.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/module-import.php' );
		acf_include( 'extend/includes/admin/tools/post-types-export.php' );
		acf_include( 'extend/includes/admin/tools/post-types-import.php' );
		acf_include( 'extend/includes/admin/tools/taxonomies-export.php' );
		acf_include( 'extend/includes/admin/tools/taxonomies-import.php' );
		acf_include( 'extend/includes/admin/tools/block-types-export.php' );
		acf_include( 'extend/includes/admin/tools/block-types-import.php' );
		acf_include( 'extend/includes/admin/tools/forms-export.php' );
		acf_include( 'extend/includes/admin/tools/forms-import.php' );
		acf_include( 'extend/includes/admin/tools/options-pages-export.php' );
		acf_include( 'extend/includes/admin/tools/options-pages-import.php' );
		acf_include( 'extend/includes/admin/tools/templates-export.php' );
		acf_include( 'extend/includes/admin/tools/templates-import.php' );
		acf_include( 'extend/includes/admin/tools/rewrite-rules-export.php' );
		acf_include( 'extend/includes/admin/tools/settings-export.php' );
		acf_include( 'extend/includes/admin/tools/settings-import.php' );
	}

	/**
	 * Include admin tools late
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_admin_tools_late() {
		acf_include( 'extend/includes/admin/tools/field-groups-local.php' );
		acf_include( 'extend/includes/admin/tools/field-groups-export.php' );
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
		$acfe = new Extend_ACF();
		$acfe->initialize();
	}
	return $acfe;
}
acfe();
