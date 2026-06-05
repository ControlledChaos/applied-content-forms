<?php
/**
 * Upgrade functions
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

/**
 * Has upgrade
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_has_upgrade() {

	$db_version = acf_get_db_version();

	// Return true if DB version is < latest upgrade version.
	if ( $db_version && acf_version_compare( $db_version, '<', '5.5.0' ) ) {
		return true;
	}

	// Update DB version if needed.
	if ( $db_version !== ACF_VERSION ) {
		acf_update_db_version( ACF_VERSION );
	}
	return false;
}

/**
 * Upgrade all
 *
 * @since  1.0.0
 * @global array $wpdb
 * @return void
 */
function acf_upgrade_all() {

	// Access global variables.
	global $wpdb;

	@set_time_limit(600);
	timer_start();

	acf_dev_log( 'Begin ACF upgrade.' );

	$db_version = acf_get_db_version();

	if ( acf_version_compare( $db_version, '<', '5.0.0' ) ) {
		acf_upgrade_500();
	}
	if ( acf_version_compare( $db_version, '<', '5.5.0' ) ) {
		acf_upgrade_550();
	}

	// Upgrade DB version once all updates are complete.
	acf_update_db_version( ACF_VERSION );

	acf_dev_log( 'ACF Upgrade Complete.', $wpdb->num_queries, timer_stop(0) );
}

/**
 * Get ACF database version
 *
 * @since  1.0.0
 * @return string
 */
function acf_get_db_version() {
	return get_option( 'acf_version' );
}

/**
 * Update the ACF database version
 *
 * @since  1.0.0
 * @param  string $version
 * @return void
 */
function acf_update_db_version( $version = '' ) {
	update_option( 'acf_version', $version );
}

/**
 * Update the ACF database version for 5.0
 *
 * @since  1.0.0
 * @param  string $version
 * @return void
 */
function acf_upgrade_500() {
	acf_dev_log( 'ACF Upgrade 5.0.0.' );
	do_action( 'acf/upgrade_500' );
	acf_upgrade_500_field_groups();
	acf_update_db_version( '5.0.0' );
}

/**
 * Update field groups for 5.0
 *
 * @since  1.0.0
 * @return void
 */
function acf_upgrade_500_field_groups() {

	acf_dev_log( 'ACF Upgrade 5.0.0 Field Groups.' );

	// Get old field groups.
	$ofgs = get_posts( [
		'numberposts'      => -1,
		'post_type'        => 'acf',
		'orderby'          => 'menu_order title',
		'order'            => 'asc',
		'suppress_filters' => true,
	] );

	// Loop.
	if ( $ofgs ) {
		foreach ( $ofgs as $ofg ) {
			acf_upgrade_500_field_group( $ofg );
		}
	}
}

/**
 * Update field groups for 5.0
 *
 * @since  1.0.0
 * @param  object $ofg The old field group post object.
 * @return array The new field group array.
 */
function acf_upgrade_500_field_group( $ofg ) {

	acf_dev_log( 'ACF Upgrade 5.0.0 Field Group.', $ofg );

	$nfg = [
		'ID'         => 0,
		'title'      => $ofg->post_title,
		'menu_order' => $ofg->menu_order,
	];

	// Construct the location rules.
	$rules    = get_post_meta( $ofg->ID, 'rule', false );
	$anyorall = get_post_meta( $ofg->ID, 'allorany', true );

	if ( is_array( $rules ) ) {

		// If field group was duplicated, rules may be a serialized string.
		$rules = array_map( 'maybe_unserialize', $rules );

		// Convert rules to groups.
		$nfg['location'] = acf_convert_rules_to_groups( $rules, $anyorall );
	}

	// Settings.
	if ( $position = get_post_meta( $ofg->ID, 'position', true ) ) {
		$nfg['position'] = $position;
	}
	if ( $layout = get_post_meta( $ofg->ID, 'layout', true ) ) {
		$nfg['layout'] = $layout;
	}
	if ( $hide_on_screen = get_post_meta( $ofg->ID, 'hide_on_screen', true ) ) {
		$nfg['hide_on_screen'] = maybe_unserialize( $hide_on_screen );
	}

	/**
	 * Save field group
	 *
	 * acf_upgrade_field_group will call the acf_get_valid_field_group
	 * function and apply 'compatibility' changes.
	 */
	$nfg = acf_update_field_group( $nfg );

	acf_dev_log( '> Complete.', $nfg );

	do_action( 'acf/upgrade_500_field_group', $nfg, $ofg );

	acf_upgrade_500_fields( $ofg, $nfg );

	if ( 'trash' == $ofg->post_status ) {
		acf_trash_field_group( $nfg['ID'] );
	}
	return $nfg;
}

/**
 * Update fields for 5.0
 *
 * Upgrades all ACF4 fields to ACF5 from a specific field group.
 *
 * @since  1.0.0
 * @param  object $ofgThe old field group post object.
 * @param  array $nfg The new field group array.
 * @global array $wpdb
 * @return void
 */
function acf_upgrade_500_fields( $ofg, $nfg ) {

	// Access global variables.
	global $wpdb;

	acf_dev_log( 'ACF Upgrade 5.0.0 Fields.' );

	// Get field from postmeta.
	$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s", $ofg->ID, 'field_%' ), ARRAY_A );

	if ( $rows ) {
		$checked = [];

		foreach ( $rows as $row ) {

			$field = $row['meta_value'];
			$field = maybe_unserialize( $field );
			$field = maybe_unserialize( $field ); // Run again for WPML.

			// Stop if key already migrated (potential duplicates in DB).
			if ( isset( $checked[ $field['key'] ] ) ) {
				continue;
			}
			$checked[ $field['key'] ] = 1;
			$field['parent'] = $nfg['ID'];

			// Migrate field.
			$field = acf_upgrade_500_field( $field );
		}
 	}
}

/**
 * Update field for 5.0
 *
 * Upgrades a ACF4 field to ACF5.
 *
 * @since  1.0.0
 * @param  array $field
 * @return array
 */
function acf_upgrade_500_field( $field ) {

	acf_dev_log( 'ACF Upgrade 5.0.0 Field.', $field );

	$field['menu_order'] = acf_extract_var( $field, 'order_no', 0 );

	// Correct very old field keys (field2 => field_2).
	if ( substr( $field['key'], 0, 6 ) !== 'field_' ) {
		$field['key'] = 'field_' . str_replace( 'field', '', $field['key'] );
	}

	// Extract sub fields.
	$sub_fields = [];
	if ( 'repeater' == $field['type'] ) {

		// Loop over sub fields.
		if ( ! empty( $field['sub_fields'] ) ) {
			foreach ( $field['sub_fields'] as $sub_field ) {
				$sub_fields[] = $sub_field;
			}
		}

		// Remove sub fields from field.
		unset( $field['sub_fields'] );

	} elseif ( 'flexible_content' == $field['type'] ) {

		// Loop over layouts.
		if ( is_array( $field['layouts'] ) ) {
			foreach ( $field['layouts'] as $i => $layout ) {

				// Generate key.
				$layout['key'] = uniqid( 'layout_' );

				// Loop over sub fields.
				if ( ! empty( $layout['sub_fields'] ) ) {
					foreach ( $layout['sub_fields'] as $sub_field ) {
						$sub_field['parent_layout'] = $layout['key'];
						$sub_fields[] = $sub_field;
					}
				}

				// Remove sub fields from layout.
				unset( $layout['sub_fields'] );

				// Update.
				$field['layouts'][ $i ] = $layout;
			}
		}
	}

	// Save field.
	$field = acf_update_field( $field );
	acf_dev_log( '> Complete.', $field );

	// Sub fields.
	if ( $sub_fields ) {
		foreach ( $sub_fields as $sub_field ) {
			$sub_field['parent'] = $field['ID'];
			acf_upgrade_500_field( $sub_field );
		}
	}

	// Action for third party.
	do_action( 'acf/update_500_field', $field );

	return $field;
}

/**
 * Upgrade for ACF 5.5
 *
 * Version 5.5 adds support for the wp_termmeta table added in WP 4.4.
 *
 * @since  1.0.0
 * @return void
 */
function acf_upgrade_550() {

	acf_dev_log( 'ACF Upgrade 5.5.0.' );

	do_action( 'acf/upgrade_550' );

	acf_upgrade_550_termmeta();

	acf_update_db_version( '5.5.0' );
}

/**
 * Upgrade 5.5 term meta
 *
 * Upgrades all ACF4 termmeta saved in wp_options to the wp_termmeta table.
 *
 * @since  1.0.0
 * @return void
 */
function acf_upgrade_550_termmeta() {

	acf_dev_log( 'ACF Upgrade 5.5.0 Termmeta.' );

	// Stop if no wp_termmeta table.
	if ( get_option('db_version') < 34370 ) {
		return;
	}

	// Get all taxonomies.
	$taxonomies = get_taxonomies( false, 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy ) {
			acf_upgrade_550_taxonomy( $taxonomy->name );
		}
	}

	// Action for third party.
	do_action( 'acf/upgrade_550_termmeta' );
}

/*
*  acf_wp_upgrade_550_termmeta
*
*  When the database is updated to support term meta, migrate ACF term meta data across.
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	string $wp_db_version The new $wp_db_version.
*  @param	string $wp_current_db_version The old (current) $wp_db_version.
*  @return	void
*/
function acf_wp_upgrade_550_termmeta( $wp_db_version, $wp_current_db_version ) {
	if ( $wp_db_version >= 34370 && $wp_current_db_version < 34370 ) {
		if ( acf_version_compare( acf_get_db_version(), '>', '5.5.0' ) ) {
			acf_upgrade_550_termmeta();
		}
	}
}
add_action( 'wp_upgrade', 'acf_wp_upgrade_550_termmeta', 10, 2 );

/**
*  acf_upgrade_550_taxonomy
*
*  Upgrades all ACF4 termmeta for a specific taxonomy.
*
*  @date	24/8/18
*  @since	5.7.4
*
*  @param	string $taxonomy The taxonomy name.
* @global array $wpdb
*  @return	void
*/
function acf_upgrade_550_taxonomy( $taxonomy ) {

	// Access global variables.
	global $wpdb;

	acf_dev_log( 'ACF Upgrade 5.5.0 Taxonomy.', $taxonomy );

	$search  = $taxonomy . '_%';
	$_search = '_' . $search;

	// escape '_'
	// http://stackoverflow.com/questions/2300285/how-do-i-escape-in-sql-server
	$search  = str_replace( '_', '\_', $search );
	$_search = str_replace( '_', '\_', $_search );

	// Search results show faster query times using 2 LIKE vs 2 wildcards
	$rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT *
		FROM $wpdb->options
		WHERE option_name LIKE %s
		OR option_name LIKE %s",
		$search,
		$_search
	), ARRAY_A );

	// loop
	if ( $rows ) {
		foreach ( $rows as $row ) {

			/*
			Use regex to find "(_)taxonomy_(term_id)_(field_name)" and populate $matches:
			[
				[0] => _category_3_color
				[1] => _
				[2] => 3
				[3] => color
			]
			*/
			if ( ! preg_match( "/^(_?){$taxonomy}_(\d+)_(.+)/", $row['option_name'], $matches ) ) {
				continue;
			}

			$term_id    = $matches[2];
			$meta_key   = $matches[1] . $matches[3];
			$meta_value = $row['option_value'];

			/**
			 * Memory usage reduced by 50% by using a manual insert vs update_metadata() function.
			 * Update_metadata( 'term', $term_id, $meta_name, $meta_value );
			 */
			$wpdb->insert( $wpdb->termmeta, [
				'term_id'    => $term_id,
				'meta_key'   => $meta_key,
				'meta_value' => $meta_value
			] );
			acf_dev_log( 'ACF Upgrade 5.5.0 Term.', $term_id, $meta_key );
			do_action( 'acf/upgrade_550_taxonomy_term', $term_id );
		}
	}

	// Action for third party.
	do_action( 'acf/upgrade_550_taxonomy', $taxonomy );
}
