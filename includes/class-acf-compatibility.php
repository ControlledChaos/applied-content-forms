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

		// Deactivate the megalomattic fork of ACF.
		deactivate_plugins( 'secure-custom-fields/secure-custom-fields.php' );

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

		add_filter( 'acf/validate_field_group', [ $this, 'field_group_location_list' ], 20 );
        add_filter( 'acf/validate_field/type=group', [ $this, 'field_seamless_style' ], 20 );
        add_filter( 'acf/validate_field/type=clone', [ $this, 'field_seamless_style' ], 20 );
        add_filter( 'acf/validate_field/type=acfe_dynamic_message', [ $this, 'field_dynamic_message' ], 20 );

        add_filter( 'acfe/load_fields/type=flexible_content', [ $this, 'field_flexible_settings_title' ], 20, 2 );

        add_filter( 'acf/prepare_field/name=acfe_flexible_category', [ $this, 'field_flexible_layout_categories' ], 10, 2 );

		// Third party.
		add_filter( 'pto/posts_orderby/ignore', [ $this, 'pto_acf_field_group' ], 10, 3 );
		add_filter( 'pto/get_options', [ $this, 'pto_options_acf_field_group' ] );

		add_filter( 'rank_math/metabox/priority', [ $this, 'rankmath_metaboxes_priority' ] );
		add_filter( 'wpseo_metabox_prio', [ $this, 'yoast_metaboxes_priority' ] );
		add_filter( 'pll_get_post_types', [ $this, 'polylang' ], 10, 2 );
		add_action( 'elementor/documents/register_controls', [ $this, 'elementor' ] );
		add_filter( 'wpgraphql_acf_supported_fields', [ $this, 'wpgraphql_supported_fields' ] );
		add_filter( 'wpgraphql_acf_register_graphql_field', [ $this, 'wpgraphql_register_field' ], 10, 4 );
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
		if ( isset( $field['taxonomy'] ) && acf_in_array( 'all', $field['taxonomy'] ) ) {
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

	/**
	 * Field group location list
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field_group
	 * @return array
	 */
	public function field_group_location_list( $field_group ) {

		if ( ! acf_maybe_get( $field_group, 'location' ) ) {
			return $field_group;
		}

		foreach ( $field_group['location'] as &$or ) {
			foreach ( $or as &$and ) {

				if ( ! isset( $and['value'] ) ) {
					continue;
				}

				// Post type list.
				if ( 'post_type' === $and['param'] && acf_ends_with( $and['value'], '_archive' ) ) {
					$and['param'] = 'post_type_list';
					$and['value'] = substr_replace( $and['value'], '', -8 );

				// Taxonomy list.
				} elseif ( 'taxonomy' === $and['param'] && acf_ends_with( $and['value'], '_archive' ) ) {
					$and['param'] = 'taxonomy_list';
					$and['value'] = substr_replace( $and['value'], '', -8 );
				}
			}
		}
		return $field_group;
	}

	/**
	 * Seamless field group style
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function field_seamless_style( $field ) {

		if ( $seamless = acf_maybe_get( $field, 'acfe_seamless_style', false ) ) {
			$field['acfe_seamless_style'] = $seamless;
		}
		return $field;
	}

	/**
	 * Dynamic message (render)
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function field_dynamic_message( $field ) {
		$field['type'] = 'acfe_dynamic_render';
		return $field;
	}

	/**
	 * Flexible content settings title
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $fields
	 * @param  string $parent
	 * @return array
	 */
	public function field_flexible_settings_title( $fields, $parent ) {

		// Check if is tool screen.
		if ( ! acf_is_screen( acf_get_acf_screen_id( 'acf-tools' ) ) ) {
			return $fields;
		}

		foreach ( $fields as $_k => $_field ) {

			// Field name.
			$_field_name = acf_maybe_get( $_field, 'name' );

			// Check 'acfe_flexible_layout_title' & 'layout_settings'.
			if (
				'acfe_flexible_layout_title' !== $_field_name &&
				'layout_settings' !== $_field_name
			) {
				continue;
			}
			unset( $fields[$_k] );
		}
		return $fields;
	}

	/**
	 * Flexible layout categories
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field
	 * @return array
	 */
	public function field_flexible_layout_categories( $field ) {

		$value = acf_maybe_get( $field, 'value' );
		if ( empty( $value ) ) {
			return $field;
		}

		if ( is_string( $value ) ) {

			$explode = explode( '|', $value );
			$choices = [];

			foreach ( $explode as $v ) {
				$v = trim( $v );
				$choices[$v] = $v;
			}
			$field['choices'] = $choices;
			$field['value']   = $choices;
		}
		return $field;
	}

	/**
	 * Plugin: Post Types Order
	 *
	 * @link https://wordpress.org/plugins/post-types-order/
	 *
	 * Fix plugin applying custom order to the Field Group post type.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $ignore
	 * @param  string $orderby
	 * @param  object $query
	 * @return boolean
	 */
	public function pto_acf_field_group( $ignore = false, $orderby, $query ) {

		if ( is_admin() && $query->is_main_query() && 'acf-field-group' === $query->get( 'post_type' ) ) {
			$ignore = true;
		}
		return $ignore;
	}

	/**
	 * Plugin: Post Types Order
	 *
	 * @link https://wordpress.org/plugins/post-types-order/
	 *
	 * Fix plugin applying a drag & drop UI on field group UI.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $options
	 * @return array
	 */
	public function pto_options_acf_field_group( $options ) {
		$options['show_reorder_interfaces']['acf-field-group'] = 'hide';
		return $options;
	}

	/**
	 * Plugin: Rank Math SEO
	 *
	 * @link https://wordpress.org/plugins/seo-by-rank-math/
	 *
	 * Fix the plugin post metabox which is always above field metaboxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function rankmath_metaboxes_priority() {
		return 'default';
	}

	/**
	 * Plugin: YOAST SEO
	 *
	 * @link https://wordpress.org/plugins/wordpress-seo/
	 *
	 * Fix the plugin post metabox which is always above field metaboxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function yoast_metaboxes_priority() {
		return 'default';
	}

	/**
	 * Plugin: PolyLang
	 *
	 * @link https://polylang.pro/doc/filter-reference/
	 *
	 * Enable translation for form module.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $post_types
	 * @param  boolean $is_settings
	 * @return array
	 */
	public function polylang( $post_types, $is_settings ) {

		if ( $is_settings ) {
			unset( $post_types['acf-form'] );
			unset( $post_types['acf-template'] );
		} else {
			$post_types['acf-form']     = 'acf-form';
			$post_types['acf-template'] = 'acf-template';
		}
		return $post_types;
	}

	/**
	 * Plugin: Elementor Pro
	 *
	 * @link https://elementor.com/pro/
	 *
	 * Fix Elementor listing all private field groups
	 * in the dynamic tags options list.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function elementor() {

		add_filter( 'acf/load_field_groups', function( $field_groups ) {

			// Hidden local field groups.
			$hidden = acf_get_setting( 'reserved_field_groups', [] );

			foreach ( $field_groups as $i => $field_group ) {
				if( ! in_array( $field_group['key'], $hidden ) ) {
					continue;
				}
				unset( $field_groups[$i] );
			}
			$field_groups = array_values( $field_groups );

			return $field_groups;
		}, 25 );
	}

	/**
	 * Plugin: WP GraphQL
	 *
	 * Supported custom fields for WP GraphQL.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $fields
	 * @return array
	 */
	public function wpgraphql_supported_fields( $fields ) {

		$acfe_fields = [
			'acfe_advanced_link',
			'acfe_code_editor',
			'acfe_forms',
			'acfe_hidden',
			'acfe_post_statuses',
			'acfe_post_types',
			'acfe_slug',
			'acfe_taxonomies',
			'acfe_taxonomy_terms',
			'acfe_user_roles',
			'acfe_block_types',
			'acfe_countries',
			'acfe_currencies',
			'acfe_date_range_picker',
			'acfe_field_groups',
			'acfe_field_types',
			'acfe_fields',
			'acfe_languages',
			'acfe_menu_locations',
			'acfe_menus',
			'acfe_options_pages',
			'acfe_phone_number',
			'acfe_post_formats',
			'acfe_templates',
		];
		return array_merge( $fields, $acfe_fields );
	}

	/**
	 * Plugin: WP GraphQL
	 *
	 * Configure custom fields for WP GraphQL.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field_config
	 * @param  string $type_name
	 * @param  string $field_name
	 * @param  array $config
	 * @return array
	 */
	public function wpgraphql_register_field( $field_config, $type_name, $field_name, $config ) {

		$acf_field = null;
		if ( isset( $config['acf_field'] ) ) {
			$acf_field = $config['acf_field'];
		}

		$acf_type = null;
		if ( isset( $acf_field['type'] ) ) {
			$acf_type = $acf_field['type'];
		}

		if ( $acf_type === 'acfe_advanced_link' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_code_editor' ) {
			$field_config['type'] = 'String';
		} elseif ( $acf_type === 'acfe_forms' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_hidden' ) {
			$field_config['type'] = 'String';
		} elseif ( $acf_type === 'acfe_post_statuses' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_post_types' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_slug' ) {
			$field_config['type'] = 'String';
		} elseif ( $acf_type === 'acfe_taxonomies' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_taxonomy_terms' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_user_roles' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_block_types' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_countries' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_currencies' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_date_range_picker' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_field_groups' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_field_types' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_fields' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_languages' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_menu_locations' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_menus' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_options_pages' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_phone_number ' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_post_formats' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		} elseif ( $acf_type === 'acfe_templates' ) {
			$field_config['type'] = [ 'list_of' => 'String' ];
		}
		return $field_config;
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
