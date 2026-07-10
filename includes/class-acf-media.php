<?php
/**
 * Media class
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

class ACF_Media {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Actions.
		add_action( 'acf/enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'acf/save_post', [ $this, 'save_files' ], 5, 1 );

		// Filters.
		add_filter( 'wp_handle_upload_prefilter', [ $this, 'handle_upload_prefilter' ], 10, 1 );

		// AJAX.
		add_action( 'wp_ajax_query-attachments', [ $this, 'wp_ajax_query_attachments' ], -1 );
	}

	/**
	 * Enqueue_scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {

		if ( wp_script_is( 'acf-input' ) ) {
			acf_localize_text( [
				'Select.verb'           => _x( 'Select', 'verb', 'acf' ),
				'Edit.verb'             => _x( 'Edit', 'verb', 'acf' ),
				'Update.verb'           => _x( 'Update', 'verb', 'acf' ),
				'Uploaded to this post' => __( 'Uploaded to this post', 'acf' ),
				'Expand Details'        => __( 'Expand Details', 'acf' ),
				'Collapse Details'      => __( 'Collapse Details', 'acf' ),
				'Restricted'            => __( 'Restricted', 'acf' ),
				'All images'            => __( 'All images', 'acf' )
			] );
			acf_localize_data( [
				'mimeTypeIcon' => wp_mime_type_icon(),
				'mimeTypes'    => get_allowed_mime_types()
			] );
		}
	}

	/**
	 * Handle upload pre-filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $file
	 * @return string
	 */
	public function handle_upload_prefilter( $file ) {

		// Stop if no acf field.
		if ( empty( $_POST['_acfuploader'] ) ) {
			return $file;
		}

		// Load field.
		$field = acf_get_field( $_POST['_acfuploader'] );
		if ( ! $field ) {
			return $file;
		}

		// Get errors.
		$errors = acf_validate_attachment( $file, $field, 'upload' );
		$errors = apply_filters( "acf/upload_prefilter/type={$field['type']}",	$errors, $file, $field );
		$errors = apply_filters( "acf/upload_prefilter/name={$field['_name']}",	$errors, $file, $field );
		$errors = apply_filters( "acf/upload_prefilter/key={$field['key']}", 	$errors, $file, $field );
		$errors = apply_filters( "acf/upload_prefilter", 						$errors, $file, $field );


		// Append error.
		if ( ! empty( $errors ) ) {
			$file['error'] = implode( "\n", $errors );
		}
		return $file;
	}

	/**
	 * Save files
	 *
	 * This function will save the $_FILES data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @return integer
	 */
	public function save_files( $post_id = 0 ) {

		// Stop if no $_FILES data.
		if ( empty( $_FILES['acf']['name'] ) ) {
			return;
		}
		acf_upload_files();
	}

	/**
	 * CMS AJAX query attachments
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function wp_ajax_query_attachments() {
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'wp_prepare_attachment_for_js' ], 10, 3 );
	}

	/**
	 * CMS prepare attachment for JavaScript
	 *
	 * @param  array $response
	 * @param  integer $attachment
	 * @param  array $meta
	 * @return array
	 */
	public function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {

		// Append attribute.
		$response['acf_errors'] = false;

		// Stop if no acf field.
		if ( empty( $_POST['query']['_acfuploader'] ) ) {
			return $response;
		}

		// Load field.
		$field = acf_get_field( $_POST['query']['_acfuploader'] );
		if ( ! $field ) {
			return $response;
		}

		// Get errors.
		$errors = acf_validate_attachment( $response, $field, 'prepare' );

		// Append errors.
		if ( ! empty( $errors ) ) {
			$response['acf_errors'] = implode( '<br />', $errors );
		}
		return $response;
	}
}
acf_new_instance( 'ACF_Media' );
