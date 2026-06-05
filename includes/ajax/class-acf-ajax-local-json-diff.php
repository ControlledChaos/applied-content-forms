<?php
/**
 * AJAX local JSON diff
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   AJAX
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Ajax_Local_JSON_Diff extends ACF_Ajax {
	
	/**
	 * AJAX action name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action = 'acf/ajax/local_json_diff';
	
	/**
	 * Privacy
	 *
	 * Prevents access for non-logged in users.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $public = false;
	
	/**
	 * Get response
	 *
	 * Returns the response data to sent back.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return mixed The response data or WP_Error.
	 */
	public function get_response( $request ) {
		
		$json = [];
		
		// Extract props.
		$id = isset( $request['id'] ) ? intval( $request['id'] ) : 0;
		
		// Stop if missing props.
		if ( ! $id ) {
			return new WP_Error( 'acf_invalid_param', __( 'Invalid field group parameter(s).', 'acf' ), [ 'status' => 404 ] );
		}
		
		// Disable filters and load field group directly from database.
		acf_disable_filters();
		$field_group = acf_get_field_group( $id );
		if ( ! $field_group ) {
			return new WP_Error( 'acf_invalid_id', __( 'Invalid field group ID.', 'acf' ), [ 'status' => 404 ] );
		}
		$field_group['fields']   = acf_get_fields( $field_group );
		$field_group['modified'] = get_post_modified_time( 'U', true, $field_group['ID'] );
		$field_group = acf_prepare_field_group_for_export( $field_group );
		
		// Load local field group file.
		$files = acf_get_local_json_files();
		$key   = $field_group['key'];
		if ( ! isset( $files[ $key ] ) ) {
			return new WP_Error( 'acf_cannot_compare', __( 'Sorry, this field group is unavailable for diff comparison.', 'acf' ), [ 'status' => 404 ] );
		}
		$local_field_group = json_decode( file_get_contents( $files[ $key ] ), true );
		
		// Render diff HTML.
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$date_template = __( 'Last updated: %s', 'acf' );
		$json['html'] = '
		<div class="acf-diff">
			<div class="acf-diff-title">
				<div class="acf-diff-title-left">
					<strong>' . __( 'Original field group', 'acf' ) .  '</strong>
					<span>' . sprintf( $date_template, wp_date( $date_format, $field_group['modified'] ) ) . '</span>
				</div>
				<div class="acf-diff-title-right">
					<strong>' . __( 'JSON field group (newer)', 'acf' ) .  '</strong>
					<span>' . sprintf( $date_template, wp_date( $date_format, $local_field_group['modified'] ) ) . '</span>
				</div>
			</div>
			<div class="acf-diff-content">
				' . wp_text_diff( acf_json_encode( $field_group ), acf_json_encode( $local_field_group ) ) . '
			</div>
		</div>';
		return $json;
	}
}
acf_new_instance( 'ACF_Ajax_Local_JSON_Diff' );
