<?php
/**
 * WordPress multi lang
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Functions
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_WPML_Compatibility {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $sitepress
	 * @return self
	 */
	public function __construct() {

		// Access global variables.
		global $sitepress;

		// Update settings.
		acf_update_setting( 'default_language', $sitepress->get_default_language() );
		acf_update_setting( 'current_language', $sitepress->get_current_language() );

		// Localize data.
		acf_localize_data( [
		   	'language' => $sitepress->get_current_language()
	   	] );

		// Switch lang during AJAX action.
		add_action( 'acf/verify_ajax', [ $this, 'verify_ajax' ] );

		// Prevent 'acf-field' from being translated.
		add_filter( 'get_translatable_documents', [ $this, 'get_translatable_documents' ] );

		// Check if 'acf-field-group' is translatable.
		if ( $this->is_translatable() ) {

			// Actions.
			add_action( 'acf/upgrade_500_field_group', [ $this, 'upgrade_500_field_group' ], 10, 2 );
			add_action( 'icl_make_duplicate', [ $this, 'icl_make_duplicate' ], 10, 4 );

			// Filters.
			add_filter( 'acf/settings/save_json', [ $this, 'settings_save_json' ] );
			add_filter( 'acf/settings/load_json', [ $this, 'settings_load_json' ] );
		}
	}

	/**
	 * Is translatable
	 *
	 * Returns true if the acf-field-group post type is translatable.
	 * Also adds compatibility with ACF4 settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $sitepress
	 * @return	boolean
	 */
	public function is_translatable() {

		// Access global variables.
		global $sitepress;

		$post_types = $sitepress->get_setting( 'custom_posts_sync_option' );

		// Return false if no post types.
		if ( ! acf_is_array( $post_types ) ) {
			return false;
		}

		// Prevent 'acf-field' from being translated.
		if ( ! empty( $post_types['acf-field'] ) ) {
			$post_types['acf-field'] = 0;
			$sitepress->set_setting( 'custom_posts_sync_option', $post_types );
		}

		/**
		 * When upgrading to version 5, review 'acf' setting,
		 * update 'acf-field-group' if 'acf' is translatable,
		 * and update 'acf-field-group' if 'acf' is translatable.
		 */
		if ( ! empty( $post_types['acf'] ) && ! isset( $post_types['acf-field-group'] ) ) {
			$post_types['acf-field-group'] = 1;
			$sitepress->set_setting( 'custom_posts_sync_option', $post_types );
		}

		// Return true if acf-field-group is translatable.
		if ( ! empty( $post_types['acf-field-group'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Upgrade 500 field group
	 *
	 * Update the icl_translations table data when creating the field groups.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field_group The new field group array.
	 * @param  object $ofg The old field group WP_Post object.
	 * @global array $wpdb
	 * @return void
	 */
	public function upgrade_500_field_group( $field_group, $ofg ) {

		// Access global variables.
		global $wpdb;

		// Get translation rows.
		$old_row = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type=%s AND element_id=%d",
			'post_acf', $ofg->ID
		), ARRAY_A );

		$new_row = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type=%s AND element_id=%d",
			'post_acf-field-group', $field_group['ID']
		), ARRAY_A );

		// Stop if no rows.
		if ( ! $old_row || ! $new_row ) {
			return;
		}

		/**
		 * Create reference of old trid to new trid.
		 * trid is a simple integer used to find associated objects.
		 */
		if ( empty( $this->trid_ref ) ) {
			$this->trid_ref = [];
		}

		// Update trid.
		if ( isset( $this->trid_ref[ $old_row['trid'] ] ) ) {

			/**
			 * This field group is a translation of another,
			 * update it's trid to match the previously inserted group.
			 */
			$new_row['trid'] = $this->trid_ref[ $old_row['trid'] ];
		} else {

			/**
			 * This field group is the first of it's translations,
			 * update the reference for future groups.
			 */
			$this->trid_ref[ $old_row['trid'] ] = $new_row['trid'];
		}

		/**
		 * Update icl_translations
		 *
		 * Row is created by WPML, and much easier to tweak it here due
		 * to the very complicated and nonsensical WPML logic.
		 */
		$table = "{$wpdb->prefix}icl_translations";
		$data  = [ 'trid' => $new_row['trid'], 'language_code' => $old_row['language_code'] ];
		$where = [ 'translation_id' => $new_row['translation_id'] ];
		$data_format  = [ '%d', '%s' ];
		$where_format = [ '%d' ];

		// Allow source_language_code to equal null.
		if ( $old_row['source_language_code'] ) {
			$data['source_language_code'] = $old_row['source_language_code'];
			$data_format[] = '%s';
		}

		// Update wpdb.
		$result = $wpdb->update( $table, $data, $where, $data_format, $where_format );
	}

	/**
	 * Settings save JSON
	 *
	 * Modifies the JSON path.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $path The JSON save path.
	 * @return string
	 */
	public function settings_save_json( $path ) {

		// Stop if directory does not exist.
		if ( ! is_writable( $path ) ) {
			return $path;
		}

		// Amend.
		$path = untrailingslashit( $path ) . '/' . acf_get_setting( 'current_language' );

		// Make directory if one does not exist.
		if ( ! file_exists( $path ) ) {
			mkdir( $path, 0777, true );
		}
		return $path;
	}

	/**
	 * Settings load JSON
	 *
	 * Modifies the JSON path.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $path The JSON save path.
	 * @return string
	 */
	public function settings_load_json( $paths ) {

		// Loop.
		if ( $paths ) {
			foreach ( $paths as $i => $path ) {
				$paths[ $i ] = untrailingslashit( $path ) . '/' . acf_get_setting( 'current_language' );
			}
		}
		return $paths;
	}

	/**
	 * ICL make duplicate
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  mixed $master_post_id
	 * @param  string $lang
	 * @param  array $postarr
	 * @param  mixed $id
	 * @global object $iclTranslationManagement;
	 * @return void
	 */
	public function icl_make_duplicate( $master_post_id, $lang, $postarr, $id ) {

		// Access global variables.
		global $iclTranslationManagement;

		// Stop if not acf-field-group.
		if ( $postarr['post_type'] != 'acf-field-group' ) {
			return;
		}

		// Update the language.
		acf_update_setting( 'current_language', $lang );

		// Duplicate field group specifying the $post_id.
		acf_duplicate_field_group( $master_post_id, $id );

		/**
		 * Always translate independently to avoid bugs.
		 * Translation post gets a new key (post_name) when
		 * original post is saved.
		 * Local json creates new files due to changed key.
		 */
		$iclTranslationManagement->reset_duplicate_flag( $id );
	}

	/**
	 * Verify AJAX
	 *
	 * @since 1.0.0
	 * @access public
	 * @global object $sitepress
	 * @return void
	 */
	public function verify_ajax() {

		// Access global variables.
		global $sitepress;

		/**
		 * Set the language for this AJAX request.
		 * This will allow get_posts to work as expected
		 * (load posts from the correct language).
		 */
		if ( isset( $_REQUEST['lang'] ) ) {
			$sitepress->switch_lang( $_REQUEST['lang'] );
		}
	}

	/**
	 * Get translatable documents
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  array $icl_post_types The array of post types.
	 * @return array
	 */
	public function get_translatable_documents( $icl_post_types ) {
		unset( $icl_post_types['acf-field'] );
		return $icl_post_types;
	}
}
acf_new_instance( 'ACF_WPML_Compatibility' );
