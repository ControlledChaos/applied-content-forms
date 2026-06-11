<?php
/**
 * Compatibility functions
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

class ACF_Compatibility {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Filters.
		add_filter( 'acf/validate_field', [ $this, 'validate_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=textarea', [ $this, 'validate_textarea_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=relationship', [ $this, 'validate_relationship_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=post_object', [ $this, 'validate_relationship_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=page_link', [ $this, 'validate_relationship_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=image', [ $this, 'validate_image_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=file', [ $this, 'validate_image_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=wysiwyg', [ $this, 'validate_wysiwyg_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=date_picker', [ $this, 'validate_date_picker_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=taxonomy', [ $this, 'validate_taxonomy_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=date_time_picker', [ $this, 'validate_date_time_picker_field' ], 20, 1 );
		add_filter( 'acf/validate_field/type=user', [ $this, 'validate_user_field' ], 20, 1 );
		add_filter( 'acf/validate_field_group', [ $this, 'validate_field_group' ], 20, 1 );

		// Modify field wrapper attributes.
		add_filter( 'acf/field_wrapper_attributes', [ $this, 'field_wrapper_attributes' ], 20, 2);

		// Location.
		add_filter( 'acf/location/validate_rule/type=post_taxonomy', [ $this, 'validate_post_taxonomy_location_rule' ], 20, 1 );
		add_filter( 'acf/location/validate_rule/type=post_category', [ $this, 'validate_post_taxonomy_location_rule' ], 20, 1 );

		// Update settings.
		add_action( 'acf/init', [ $this, 'init' ] );
	}

	/**
	 * Init
	 *
	 * Adds compatibility for deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {

		// Update "show_admin" setting based on defined constant.
		if ( defined( 'ACF_LITE' ) && ACF_LITE ) {
			acf_update_setting( 'show_admin', false );
		}
	}

	/**
	 * Field wrapper attributes
	 *
	 * Adds compatibility with deprecated field wrap attributes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $wrapper The wrapper attributes array.
	 * @param  array $field The field array.
	 * @return array
	 */
	public function field_wrapper_attributes( $wrapper, $field ) {

		// Check compatibility setting.
		if ( acf_get_compatibility( 'field_wrapper_class' ) ) {
			$wrapper['class'] .= " field_type-{$field['type']}";
			if ( $field['key'] ) {
				$wrapper['class'] .= " field_key-{$field['key']}";
			}
		}
		return $wrapper;
	}

	/**
	 * Validate field
	 *
	 * Adds compatibility with deprecated settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field The field array.
	 * @return array $field
	 */
	public function validate_field( $field ) {

		/**
		 * Conditional logic data structure changed to groups
		 * in version 5.0.0. Convert previous data
		 * (status, rules, all, or any) into groups.
		 */
		if ( isset( $field['conditional_logic']['status'] ) ) {

			// Check status.
			if ( $field['conditional_logic']['status'] ) {
				$field['conditional_logic'] = acf_convert_rules_to_groups( $field['conditional_logic']['rules'], $field['conditional_logic']['allorany']);
			} else {
				$field['conditional_logic'] = 0;
			}
		}
		return $field;
	}

	/**
	 * Validate textarea field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_textarea_field( $field ) {

		// Formatting has been removed.
		$formatting = acf_extract_var( $field, 'formatting' );
		if ( 'br' === $formatting ) {
			$field['new_lines'] = 'br';
		}
		return $field;
	}

	/**
	 *
	 * Validate relationship field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_relationship_field( $field ) {

		// Remove 'all' from post_type.
		if ( acf_in_array( 'all', $field['post_type'] ) ) {
			$field['post_type'] = [];
		}

		// Remove 'all' from taxonomy.
		if ( acf_in_array( 'all', $field['taxonomy'] ) ) {
			$field['taxonomy'] = [];
		}

		// Result_elements is now elements.
		if ( isset( $field['result_elements'] ) ) {
			$field['elements'] = acf_extract_var( $field, 'result_elements' );
		}

		return $field;
	}

	/**
	 * Validate image field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_image_field( $field ) {

		// save_format is now return_format.
		if ( isset( $field['save_format'] ) ) {
			$field['return_format'] = acf_extract_var( $field, 'save_format' );
		}

		// object is now array.
		if ( 'object' == $field['return_format'] ) {
			$field['return_format'] = 'array';
		}

		return $field;
	}

	/**
	 * Validate WYSIWYG field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_wysiwyg_field( $field ) {

		// Media_upload is now numeric.
		if ( 'yes' === $field['media_upload'] ) {
			$field['media_upload'] = 1;
		} elseif ( 'no' === $field['media_upload'] ) {
			$field['media_upload'] = 0;
		}
		return $field;
	}

	/**
	 * Validate date picker field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_date_picker_field( $field ) {

		// date_format has changed to display_format.
		if ( isset( $field['date_format'] ) ) {

			$date_format    = $field['date_format'];
			$display_format = $field['display_format'];
			$display_format = acf_convert_date_to_php( $display_format );

			// Append settings.
			$field['display_format'] = $display_format;
			$field['save_format']    = $date_format;

			unset( $field['date_format'] );
		}
		return $field;
	}

	/**
	 * Validate taxonomy field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_taxonomy_field( $field ) {

		// Load_save_terms deprecated in favor of separate save_terms.
		if ( isset( $field['load_save_terms'] ) ) {
			$field['save_terms'] = acf_extract_var( $field, 'load_save_terms' );
		}
		return $field;
	}

	/**
	 * Validate date time picker field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_date_time_picker_field( $field ) {

		/**
		 * Third party date time picker
		 *
		 * @link https://github.com/soderlind/acf-field-date-time-picker
		 */
		if ( ! empty( $field['time_format'] ) ) {

			$time_format      = acf_extract_var( $field, 'time_format' );
			$date_format      = acf_extract_var( $field, 'date_format' );
			$get_as_timestamp = acf_extract_var( $field, 'get_as_timestamp' );
			$time_format      = acf_convert_time_to_php( $time_format );
			$date_format      = acf_convert_date_to_php( $date_format );

			// Append settings.
			$field['return_format']  = $date_format . ' ' . $time_format;
			$field['display_format'] = $date_format . ' ' . $time_format;

			if ( 'true' === $get_as_timestamp ) {
				$field['return_format'] = 'U';
			}
		}
		return $field;
	}

	/**
	 * Validate user field
	 *
	 * Adds compatibility with deprecated settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function validate_user_field( $field ) {

		// Remove 'all' from roles.
		if ( acf_in_array( 'all', $field['role'] ) ) {
			$field['role'] = '';
		}

		// field_type removed in favor of multiple.
		if ( isset( $field['field_type'] ) ) {

			$field_type = acf_extract_var( $field, 'field_type' );
			if ( 'multi_select' === $field_type ) {
				$field['multiple'] = true;
			}
		}
		return $field;
	}

	/**
	 * Validate field group
	 *
	 * This function will provide compatibility with ACF4 field groups.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field_group
	 * @return array
	 */
	public function validate_field_group( $field_group ) {

		$version = 5;
		if ( ! $field_group['key'] ) {
			$version = 4;
			$field_group['key'] = isset( $field_group['id'] ) ? "group_{$field_group['id']}" : uniqid( 'group_' );
		}

		if ( isset( $field_group['options'] ) ) {
			$options = acf_extract_var( $field_group, 'options' );
			$field_group = array_merge( $field_group, $options );
		}

		if ( isset( $field_group['location']['rules'] ) ) {
			$field_group['location'] = acf_convert_rules_to_groups( $field_group['location']['rules'], $field_group['location']['allorany'] );
		}

		$replace = [
	 		'taxonomy'    => 'post_taxonomy',
	 		'ef_media'    => 'attachment',
	 		'ef_taxonomy' => 'taxonomy',
	 		'ef_user'     => 'user_role',
	 		'user_type'   => 'current_user_role'
	 	];

	 	// Only replace 'taxonomy' rule if is an ACF4 field group.
	 	if ( $version > 4 ) {
		 	unset( $replace['taxonomy'] );
	 	}

	 	// Loop over location groups.
		if ( $field_group['location'] ) {
			foreach ( $field_group['location'] as $i => $group ) {

				// Loop over group rules.
				if ( $group ) {
					foreach ( $group as $j => $rule ) {

						// Migrate parameter.
						if ( isset( $replace[ $rule['param'] ] ) ) {
							$field_group['location'][ $i ][ $j ]['param'] = $replace[ $rule['param'] ];
						}
					}
				}
			}
		}

		// Change layout to style (v5.0.0)
		if ( isset( $field_group['layout'] ) ) {
			$field_group['style'] = acf_extract_var( $field_group, 'layout' );
		}

		// Change no_box to seamless (v5.0.0)
		if ( 'no_box' === $field_group['style'] ) {
			$field_group['style'] = 'seamless';
		}
		return $field_group;
	}

	/**
	 * Validate post taxonomy location rule
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule
	 * @return array
	 */
	public function validate_post_taxonomy_location_rule( $rule ) {

		if ( is_numeric( $rule['value'] ) ) {
			$term = acf_get_term( $rule['value'] );
			if ( $term ) {
				$rule['value'] = acf_encode_term( $term );
			}
		}
		return $rule;
	}
}
acf_new_instance( 'ACF_Compatibility' );

/**
 * Get compatibility
 *
 * Returns true if compatibility is enabled for the given component.
 *
 * @since  1.0.0
 * @param  string $name The name of the component to check.
 * @return boolean
 */
function acf_get_compatibility( $name ) {
	return apply_filters( "acf/compatibility/{$name}", false );
}
