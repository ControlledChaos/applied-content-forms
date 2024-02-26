<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ACF_Location_Post_Type' ) ) :

class ACF_Location_Post_Type extends ACF_Location {

	/**
	 * Initializes props
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function initialize() {
		$this->name        = 'post_type';
		$this->label       = __( 'Post Type', 'acf' );
		$this->category    = 'post';
    	$this->object_type = 'post';
	}

	/**
	 * Matches the provided rule against the screen args returning a bool result.
	 *
	 * @since  5.9.0\
	 * @access public
	 * @param  array $rule The location rule.
	 * @param  array $screen The screen args.
	 * @param  array $field_group The field group settings.
	 * @return boolean
	 */
	public function match( $rule, $screen, $field_group ) {

		// Check screen args.
		if ( isset( $screen['post_type'] ) ) {
			$post_type = $screen['post_type'];
		} elseif ( isset( $screen['post_id'] ) ) {
			$post_type = get_post_type( $screen['post_id'] );
		} else {
			return false;
		}

		// Compare rule against $post_type.
		return $this->compare_to_rule( $post_type, $rule );
	}

	/**
	 * Returns an array of possible values for this rule type.
	 *
	 * @since  5.9.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return array
	 */
	public function get_values( $rule ) {

		// Get post types.
		$post_types = acf_get_post_types( [
			'show_ui' => 1,
			'exclude' => [ 'attachment' ]
		] );

		// Return array of [type => label].
		return acf_get_pretty_post_types( $post_types );
	}

	/**
	 * Returns the object_subtype connected to this location.
	 *
	 * @since  5.9.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return string|array
	 */
	public function get_object_subtype( $rule ) {
		if ( $rule['operator'] === '==' && array_key_exists( 'value', $rule ) ) {
			return $rule['value'];
		}
		return '';
	}
}

// Initialize.
acf_register_location_type( 'ACF_Location_Post_Type' );

endif; // Class check.
