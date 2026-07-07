<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACF_Location_Page_Template') ) :

class ACF_Location_Page_Template extends ACF_Location {

	/**
	 * Initializes props.
	 *
	 * @date	5/03/2014
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function initialize() {
		$this->name = 'page_template';
		$this->label = __( "Page Template", 'acf' );
		$this->category = 'page';
    	$this->object_type = 'post';
    	$this->object_subtype = 'page';
	}

	/**
	 * Matches the provided rule against the screen args returning a bool result.
	 *
	 * @date	9/4/20
	 * @since	5.9.0
	 *
	 * @param	array $rule The location rule.
	 * @param	array $screen The screen args.
	 * @param	array $field_group The field group settings.
	 * @return	bool
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

		// Page templates were extended in WordPress version 4.7 for all post types.
		// Prevent this rule (which is scoped to the "page" post type) appearing on all post types without a template selected (default template).
		if ( $rule['value'] === 'default' && $post_type !== 'page' ) {
			return false;
		}

		// Check if this post type has templates.
		$post_templates = acf_get_post_templates();
		if ( ! isset( $post_templates[ $post_type ] ) ) {
			return false;
		}

		// Get page template allowing for screen or database value.
		if ( isset( $screen['page_template'] ) ) {
			$page_template = $screen['page_template'];
		} elseif ( isset( $screen['post_id'] ) ) {
			$page_template = get_post_meta( $screen['post_id'], '_wp_page_template', true );
		} else {
			$page_template = '';
		}

		// Treat empty value as default template.
		if ( $page_template === '' ) {
			$page_template = 'default';
		}

		// Compare rule against $page_template.
		return $this->compare_to_rule( $page_template, $rule );
	}

	/**
	 * Returns an array of possible values for this rule type.
	 *
	 * @date	9/4/20
	 * @since	5.9.0
	 *
	 * @param	array $rule A location rule.
	 * @return	array
	 */
	public function get_values( $rule ) {
		$post_templates = acf_get_post_templates();
		return array_merge(
			array(
				'default' => apply_filters( 'default_page_template_title',  __('Default Template', 'acf') )
			),
			$post_templates[ 'page' ]
		);
	}
}

// Register.
acf_register_location_type( 'ACF_Location_Page_Template' );

endif; // class_exists check.
