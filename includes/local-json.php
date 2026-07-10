<?php
/**
 * Local JSON functions
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

class ACF_Local_JSON {

	/**
	 * The found JSON field group files.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var   array
	 */
	private $files = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Update settings.
		acf_update_setting( 'save_json', get_stylesheet_directory() . '/acf-json' );
		acf_append_setting( 'load_json', get_stylesheet_directory() . '/acf-json' );

		// Add listeners.
		add_action( 'acf/update_field_group', [ $this, 'update_field_group' ] );
		add_action( 'acf/untrash_field_group', [ $this, 'update_field_group' ] );
		add_action( 'acf/trash_field_group', [ $this, 'delete_field_group' ] );
		add_action( 'acf/delete_field_group', [ $this, 'delete_field_group' ] );

		// Include fields.
		add_action( 'acf/include_fields', [ $this, 'include_fields' ] );
	}

	/**
	 * Is enabled
	 *
	 * Returns true if this component is enabled.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function is_enabled() {
		return (bool) acf_get_setting( 'json' );
	}

	/**
	 * Update field group
	 *
	 * Writes field group data to JSON file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field_group The field group.
	 * @return void
	 */
	public function update_field_group( $field_group ) {

		// Stop if disabled.
		if ( ! $this->is_enabled() ) {
			return false;
		}

		// Append fields.
		$field_group['fields'] = acf_get_fields( $field_group );

		// Save to file.
		$this->save_file( $field_group['key'], $field_group );
	}

	/**
	 * Delete field group
	 *
	 * Deletes a field group JSON file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field_group The field group.
	 * @return void
	 */
	public function delete_field_group( $field_group ) {

		// Stop if disabled.
		if ( ! $this->is_enabled() ) {
			return false;
		}

		// CMS appends '__trashed' to end of 'key' (post_name).
		$key = str_replace( '__trashed', '', $field_group['key'] );

		// Delete file.
		$this->delete_file( $key );
	}

	/**
	 * Include all local JSON fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function include_fields() {

		// Stop if disabled.
		if ( ! $this->is_enabled() ) {
			return false;
		}

		// Get load paths.
		$files = $this->scan_field_groups();

		foreach ( $files as $key => $file ) {

			$json = json_decode( file_get_contents( $file ), true );
	    	$json['local']      = 'json';
	    	$json['local_file'] = $file;

	    	acf_add_local_field_group( $json );
		}
	}

	/**
	 * Scan for JSON field groups
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return array
	 */
	function scan_field_groups() {

		$json_files = [];

		// Loop over "local_json" paths and parse JSON files.
		$paths = (array) acf_get_setting( 'load_json' );
		foreach ( $paths as $path ) {
			if ( is_dir( $path ) ) {
				$files = scandir( $path );
				if ( $files ) {
					foreach ( $files as $filename ) {

						// Ignore hidden files.
						if ( $filename[0] === '.' ) {
							continue;
						}

						// Ignore sub directories.
						$file = untrailingslashit( $path ) . '/' . $filename;
						if ( is_dir( $file ) ) {
							continue;
						}

						// Ignore non JSON files.
						$ext = pathinfo( $filename, PATHINFO_EXTENSION );
						if ( $ext !== 'json' ) {
							continue;
						}

						// Read JSON data.
				    	$json = json_decode( file_get_contents( $file ), true );
				    	if ( ! is_array( $json ) || ! isset( $json['key'] ) ) {
					    	continue;
				    	}

				    	// Append data.
				    	$json_files[ $json['key'] ] = $file;
					}
				}
			}
		}

		// Store data and return.
		$this->files = $json_files;
		return $json_files;
	}

	/**
	 * Return JSON field group files
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return array
	 */
	public function get_files() {
		return $this->files;
	}

	/**
	 * Save a field group JSON file
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The field group key.
	 * @param  array $field_group The field group.
	 * @return boolean
	 */
	public function save_file( $key, $field_group ) {

		$path = acf_get_setting( 'save_json' );
		$file = untrailingslashit( $path ) . '/' . $key . '.json';

		if ( ! is_writable( $path ) ) {
			return false;
		}

		// Append modified time.
		if ( $field_group['ID'] ) {
			$field_group['modified'] = get_post_modified_time( 'U', true, $field_group['ID'] );
		} else {
			$field_group['modified'] = strtotime( 'now' );
		}

		// Prepare for export.
		$field_group = acf_prepare_field_group_for_export( $field_group );

		// Save and return true if bytes were written.
		$result = file_put_contents( $file, acf_json_encode( $field_group ) );
		return is_int( $result );
	}

	/**
	 * Delete a field group JSON file
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key The field group key.
	 * @return boolean True on success.
	 */
	public function delete_file( $key ) {

		$path = acf_get_setting( 'save_json' );
		$file = untrailingslashit( $path ) . '/' . $key . '.json';

		if ( is_readable( $file ) ) {
			unlink( $file );
			return true;
		}
		return false;
	}

	/**
	 * Include all local JSON files
	 * @deprecated
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function include_json_folders() {
		_deprecated_function( __METHOD__, '5.9.0', 'ACF_Local_JSON::include_fields()' );
		$this->include_fields();
	}

	/**
	 * Includes local JSON files within a specific folder.
	 * @deprecated
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $path The path to a specific JSON folder.
	 * @return void
	 */
	public function include_json_folder( $path = '' ) {
		_deprecated_function( __METHOD__, '5.9.0' );
		// Do nothing.
	}
}
acf_new_instance( 'ACF_Local_JSON' );

/**
 * Get local JSON
 *
 * Returns an array of found JSON field group files.
 *
 * @since  1.0.0
 * @return method
 */
function acf_get_local_json_files() {
	return acf_get_instance( 'ACF_Local_JSON' )->get_files();
}

/**
 * Write JSON
 *
 * Saves a field group JSON file.
 *
 * @since  1.0.0
 * @param  array $field_group The field group.
 * @return boolean
 */
function acf_write_json_field_group( $field_group ) {
	return acf_get_instance( 'ACF_Local_JSON' )->save_file( $field_group['key'], $field_group );
}

/**
 * Delete JSON
 *
 * Deletes a field group JSON file.
 *
 * @since  1.0.0
 * @param  string $key The field group key.
 * @return method
 */
function acf_delete_json_field_group( $key ) {
	return acf_get_instance( 'ACF_Local_JSON' )->delete_file( $key );
}
