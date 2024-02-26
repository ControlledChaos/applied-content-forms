<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ACF_Location_Taxonomy' ) ) :

class ACF_Location_Taxonomy extends ACF_Location {

	/**
	 * Initializes props.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function initialize() {
		$this->name        = 'taxonomy';
		$this->label       = __( 'Taxonomy', 'acf' );
		$this->category    = 'forms';
		$this->object_type = 'term';
	}

	/**
	 * Matches the provided rule against the screen args returning a bool result.
	 *
	 * @since  5.9.0
	 * @access public
	 * @param  array $rule The location rule.
	 * @param  array $screen The screen args.
	 * @param  array $field_group The field group settings.
	 * @return boolean
	 */
	public function match( $rule, $screen, $field_group ) {

		// Check screen args.
		if ( isset( $screen['taxonomy'] ) ) {
			$taxonomy = $screen['taxonomy'];
		} else {
			return false;
		}

		// Compare rule against $taxonomy.
		return $this->compare_to_rule( $taxonomy, $rule );
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
		return array_merge(
			[
				'all' => __( 'All', 'acf' )
			],
			acf_get_taxonomy_labels()
		);
	}

	/**
	 * Returns the object_subtype connected to this location.
	 *
	 * @since  5.9.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return string|array
	 */
	function get_object_subtype( $rule ) {
		if ( $rule['operator'] === '==' && array_key_exists( 'value', $rule ) ) {
			return $rule['value'];
		}
		return '';
	}

}

// Initialize.
acf_register_location_type( 'ACF_Location_Taxonomy' );

endif; // Class check.
