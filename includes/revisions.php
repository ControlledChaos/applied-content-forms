<?php
/**
 * Revisions
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

class acf_revisions {

	/**
	 * Cache
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $cache = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Actions.
		add_action( 'wp_restore_post_revision', [ $this, 'wp_restore_post_revision' ], 10, 2 );

		// Filters.
		add_filter( 'wp_save_post_revision_check_for_changes', [ $this, 'wp_save_post_revision_check_for_changes' ], 10, 3 );
		add_filter( '_wp_post_revision_fields', [ $this, 'wp_preview_post_fields' ], 10, 2 );
		add_filter( '_wp_post_revision_fields', [ $this, 'wp_post_revision_fields' ], 10, 2 );
		add_filter( 'acf/validate_post_id', [ $this, 'acf_validate_post_id' ], 10, 2 );
	}

	/**
	 * CMS preview post fields
	 *
	 * This function is used to trick the CMS into thinking that
	 * one of the $post's fields has changed and will allow an
	 * autosave to be updated.
	 * Fixes an odd bug causing the preview page to render the
	 * non autosave post data on every odd attempt.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $fields
	 * @return $fields
	 */
	public function wp_preview_post_fields( $fields ) {

		// Stop if not previewing a post.
		if ( acf_maybe_get_POST( 'wp-preview' ) !== 'dopreview' ) {
			return $fields;
		}

		// Add to fields if ACF has changed.
		if ( acf_maybe_get_POST( '_acf_changed' ) ) {
			$fields['_acf_changed'] = 'different than 1';
		}
		return $fields;
	}

	/**
	 * CMS save post revision check for changes
	 *
	 * This filter will return false and force CMS to save a revision.
	 * This is required due to the CMS checking only post_title,
	 * post_excerpt and post_content values, not custom fields.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $return defaults to true
	 * @param  object $last_revision the last revision that CMS will compare against
	 * @param  object $post the $post that CMS will compare against
	 * @return boolean
	 */
	public function wp_save_post_revision_check_for_changes( $return, $last_revision, $post ) {

		// If ACF has changed, return false and prevent CMS from performing 'compare' logic.
		if ( acf_maybe_get_POST( '_acf_changed' ) ) {
			return false;
		}
		return $return;
	}

	/**
	 * CMS post_revision_fields
	 *
	 * This filter will add the ACF fields to the returned array
	 * Versions 3.5 and 3.6 of CMS feature different uses of the
	 * revisions filters, so there are some hacks to allow both
	 * versions to work correctly.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $fields
	 * @param  integer $post
	 * @global object $post
	 * @return array
	 */
	public function wp_post_revision_fields( $fields, $post = null ) {

		// Validate page.
		if ( acf_is_screen( 'revision' ) || acf_is_ajax( 'get-revision-diffs' ) ) {

			// Stop if is restoring.
			if ( acf_maybe_get_GET( 'action' ) === 'restore' ) {
				return $fields;
			}
		} else {
			return $fields;
		}

		$append  = [];
		$order   = [];
		$post_id = acf_maybe_get( $post, 'ID' );

		// Compatibility with CMS < 4.5 (test).
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		// Get all postmeta.
		$meta = get_post_meta( $post_id );

		// Stop if no meta.
		if ( ! $meta ) {
			return $fields;
		}

		foreach ( $meta as $name => $value ) {

			// Attempt to find key value.
			$key = acf_maybe_get( $meta, '_' . $name );


			// Move to next if no key.
			if ( ! $key ) {
				continue;
			}

			$value = $value[0];
			$key   = $key[0];
			$field = acf_get_field( $key );
			if ( ! $field ) {
				continue;
			}

			// Get field.
			$field_title = $field['label'] . ' (' . $name . ')';
			$field_order = $field['menu_order'];
			$ancestors   = acf_get_field_ancestors( $field );

			// Ancestors.
			if ( ! empty( $ancestors ) ) {

				$count  = count( $ancestors );
				$oldest = acf_get_field( $ancestors[$count-1] );
				$field_title = str_repeat( '- ', $count ) . $field_title;
				$field_order = $oldest['menu_order'] . '.1';
			}

			$append[ $name ] = $field_title;
			$order[ $name ]  = $field_order;

			// Hook into specific revision field filter and return local value.
			add_filter( "_wp_post_revision_field_{$name}", [ $this, 'wp_post_revision_field' ], 10, 4 );
		}

		// Append.
		if ( ! empty( $append ) ) {
			$prefix = '_';
			$append = acf_add_array_key_prefix( $append, $prefix );
			$order  = acf_add_array_key_prefix( $order, $prefix );

			// Sort by name (orders sub field values correctly).
			array_multisort( $order, $append );

			// Remove prefix.
			$append = acf_remove_array_key_prefix( $append, $prefix );

			// Append.
			$fields = $fields + $append;
		}
		return $fields;
	}

	/**
	 * CMS post revision field
	 *
	 * This filter will load the value for the given field
	 * and return it for rendering.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $value
	 * @param  string $field_name
	 * @param  mixed $post
	 * @param  boolean $direction
	 * @return string
	 */
	public function wp_post_revision_field( $value, $field_name, $post = null, $direction = false ) {

		// Stop if is empty.
		if ( empty( $value ) ) {
			return $value;
		}

		// Value has not yet been 'maybe_unserialize'.
		$value = maybe_unserialize( $value );

		$post_id = $post->ID;
		$field   = acf_maybe_get_field( $field_name, $post_id );

		// Default formatting.
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		} elseif ( is_object( $value ) ) {
			$value = serialize( $value );
		}

		// Image and file fields.
		if ( 'image' == $field['type'] || 'file' == $field['type'] ) {
			$url   = wp_get_attachment_url( $value );
			$value = $value . ' (' . $url . ')';
		}
		return $value;
	}

	/**
	 * CMS restore post revision
	 *
	 * This action will copy and paste the metadata from a revision to the post.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @param  integer $revision_id
	 * @return integer
	 */
	public function wp_restore_post_revision( $post_id, $revision_id ) {

		// Copy postmeta from revision to post (restore from revision).
		acf_copy_postmeta( $revision_id, $post_id );

		// Make sure the latest revision is also updated to match the new $post data.
		$revision = acf_get_post_latest_revision( $post_id );
		if ( $revision ) {

			/**
			 * Copy postmeta from revision to latest revision.
			 * Potentially the same but most likely different.
			 */
			acf_copy_postmeta( $revision_id, $revision->ID );
		}
	}

	/**
	 * Validate post ID
	 *
	 * This function will modify the $post_id and allow
	 * loading values from a revision.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @param  integer $_post_id
	 * @return integer
	 */
	public function acf_validate_post_id( $post_id, $_post_id ) {

		// Stop if no preview in URL.
		if ( ! isset( $_GET['preview'] ) ) {
			return $post_id;
		}

		// Stop if $post_id is not numeric.
		if ( ! is_numeric( $post_id ) ) {
			return $post_id;
		}

		$k = $post_id;
		$preview_id = 0;

		// Check cache.
		if ( isset( $this->cache[$k] ) ) {
			return $this->cache[$k];
		}

		// Validate.
		if ( isset( $_GET['preview_id'] ) ) {
			$preview_id = (int) $_GET['preview_id'];
		} elseif ( isset( $_GET['p'] ) ) {
			$preview_id = (int) $_GET['p'];
		} elseif ( isset( $_GET['page_id'] ) ) {
			$preview_id = (int) $_GET['page_id'];
		}

		// Stop if $preview_id does not match $post_id.
		if ( $preview_id != $post_id ) {
			return $post_id;
		}

		// Attempt find revision.
		$revision = acf_get_post_latest_revision( $post_id );

		// Save.
		if ( $revision && $revision->post_parent == $post_id ) {
			$post_id = (int) $revision->ID;
		}

		// Set cache.
		$this->cache[$k] = $post_id;

		return $post_id;
	}
}
acf()->revisions = new acf_revisions();

/*
*  acf_save_post_revision
*
*  This function will copy meta from a post to it's latest revision
*
*  @type	function
*  @date	26/09/2016
*  @since	5.4.0
*
*  @param	$post_id (int)
*  @return	n/a
*/
/**
 * Save post revision
 *
 * This function will copy meta from a post to its latest revision.
 *
 * @since  1.0.0
 * @param  integer $post_id
 * @return void
 */
function acf_save_post_revision( $post_id = 0 ) {

	// Get latest revision.
	$revision = acf_get_post_latest_revision( $post_id );
	if ( $revision ) {
		acf_copy_postmeta( $post_id, $revision->ID );
	}
}

/**
 * Get post latest revision
 *
 * This function will return the latest revision for a given post
 *
 * @since  1.0.0
 * @param  integer $post_id
 * @return integer
 */
function acf_get_post_latest_revision( $post_id ) {

	$revisions = wp_get_post_revisions( $post_id );

	// Shift off and return first revision (will return null if no revisions).
	$revision = array_shift( $revisions );

	return $revision;
}
