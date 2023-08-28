<?php
/**
 * ACF field functions
 *
 * @package    ACF
 * @subpackage Includes
 * @category   Core
 * @since      1.0.0
 */

// Register store.
acf_register_store( 'fields' )->prop( 'multisite', true );

/**
 * Get field
 *
 * Retrieves a field for the given identifier.
 *
 * @since  5.7.10
 * @param  mixed $id The field ID, key or name.
 * @return mixed The field array or false.
 */
function acf_get_field( $id = 0 ) {

	// Allow WP_Post to be passed.
	if ( is_object( $id ) ) {
		$id = $id->ID;
	}

	// Check store.
	$store = acf_get_store( 'fields' );
	if ( $store->has( $id ) ) {
		return $store->get( $id );
	}

	// Check local fields first.
	if ( acf_is_local_field( $id ) ) {
		$field = acf_get_local_field( $id );

	// Then check database.
	} else {
		$field = acf_get_raw_field( $id );
	}

	// Bail early if no field.
	if ( ! $field ) {
		return false;
	}

	// Validate field.
	$field = acf_validate_field( $field );

	// Set input prefix.
	$field['prefix'] = 'acf';

	/**
	 * Filters the $field array after it has been loaded.
	 *
	 * @since 5.0.0
	 * @param array The field array.
	 */
	$field = apply_filters( "acf/load_field", $field );

	// Store field using aliases to also find via key, ID and name.
	$store->set( $field['key'], $field );
	$store->alias( $field['key'], $field['ID'], $field['name'] );

	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/load_field', [ 'type', 'name', 'key' ], 0 );

/**
 * Get raw field
 *
 * Retrieves raw field data for the given identifier.
 *
 * @since  5.7.10
 * @param  mixed $id The field ID, key or name.
 * @return mixed The field array or false.
 */
function acf_get_raw_field( $id = 0 ) {

	// Get raw field from database.
	$post = acf_get_field_post( $id );
	if ( ! $post ) {
		return false;
	}

	// Bail early if incorrect post type.
	if ( $post->post_type !== 'acf-field' ) {
		return false;
	}

	// Unserialize post_content.
	$field = (array) maybe_unserialize( $post->post_content );

	// Update attributes.
	$field['ID']         = $post->ID;
	$field['key']        = $post->post_name;
	$field['label']      = $post->post_title;
	$field['name']       = $post->post_excerpt;
	$field['menu_order'] = $post->menu_order;
	$field['parent']     = $post->post_parent;

	return $field;
}

/**
 * Get field post
 *
 * Retrieves the field's WP_Post object.
 *
 * @since  5.7.10
 * @param  mixed $id The field ID, key or name.
 * @return mixed The field array or false.
 */
function acf_get_field_post( $id = 0 ) {

	// Get post if numeric.
	if ( is_numeric( $id ) ) {
		return get_post( $id );

	// Search posts if is string.
	} elseif ( is_string( $id ) ) {

		// Determine id type.
		$type = acf_is_field_key( $id ) ? 'key' : 'name';

		// Try cache.
		$cache_key = acf_cache_key( "acf_get_field_post:$type:$id" );
		$post_id   = wp_cache_get( $cache_key, 'acf' );

		if ( $post_id === false ) {

			// Query posts.
			$posts = get_posts( [
				'posts_per_page'         => 1,
				'post_type'              => 'acf-field',
				'orderby'                => 'menu_order title',
				'order'                  => 'ASC',
				'suppress_filters'       => false,
				'cache_results'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				"acf_field_$type"        => $id
			] );

			// Update $post_id with a non false value.
			$post_id = $posts ? $posts[0]->ID : 0;

			// Update cache.
			wp_cache_set( $cache_key, $post_id, 'acf' );
		}

		// Check $post_id and return the post when possible.
		if ( $post_id ) {
			return get_post( $post_id );
		}
	}
	return false;
}

/**
 * Is field key
 *
 * Returns true if the given identifier is a field key.
 *
 * @since  5.0.0
 * @param  string $id The identifier.
 * @return boolean
 */
function acf_is_field_key( $id = '' ) {

	// Check if $id is a string starting with "field_".
	if ( is_string( $id ) && substr( $id, 0, 6 ) === 'field_' ) {
		return true;
	}

	/**
	 * Filters whether the $id is a field key.
	 *
	 * @since 5.7.10
	 * @param boolean The result.
	 * @param string $id The identifier.
	 */
	return apply_filters( 'acf/is_field_key', false, $id );
}

/**
 * Validate field
 *
 * Ensures the given field valid.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @return array
 */
function acf_validate_field( $field = [] ) {

	// Bail early if already valid.
	if ( is_array( $field ) && ! empty( $field['_valid'] ) ) {
		return $field;
	}

	// Apply defaults.
	$field = wp_parse_args( $field, [
		'ID'                => 0,
		'key'               => '',
		'label'             => '',
		'name'              => '',
		'prefix'            => '',
		'type'              => 'text',
		'value'             => null,
		'menu_order'        => 0,
		'instructions'      => '',
		'required'          => false,
		'id'                => '',
		'class'             => '',
		'conditional_logic'	=> false,
		'parent'            => 0,
		'wrapper'           => []
		// 'attributes'         => []
	] );

	// Convert types.
	$field['ID']         = (int) $field['ID'];
	$field['menu_order'] = (int) $field['menu_order'];

	// Add backwards compatibility for wrapper attributes.
	// Todo: Remove need for this.
	$field['wrapper'] = wp_parse_args( $field['wrapper'], [
		'width' => '',
		'class' => '',
		'id'    => ''
	] );

	// Store backups.
	$field['_name']  = $field['name'];
	$field['_valid'] = 1;

	/**
	 * Filters the $field array to validate settings.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	$field = apply_filters( "acf/validate_field", $field );

	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/validate_field', [ 'type' ], 0 );

/**
 * Get valid field
 *
 * Ensures the given field valid.
 *
 * @since  5.0.0
 * @param  array $field The field array.
 * @return array
 */
function acf_get_valid_field( $field = false ) {
	return acf_validate_field( $field );
}

/**
 * Translate field
 *
 * Translates a field's settings.
 *
 * @since  5.3.2
 * @param  array $field The field array.
 * @return array
 */
function acf_translate_field( $field = [] ) {

	// Get settings.
	$l10n = acf_get_setting( 'l10n' );
	$l10n_textdomain = acf_get_setting( 'l10n_textdomain' );

	// Translate field settings if textdomain is set.
	if ( $l10n && $l10n_textdomain ) {

		$field['label'] = acf_translate( $field['label'] );
		$field['instructions'] = acf_translate( $field['instructions'] );

		/**
		 * Filters the $field array to translate strings.
		 *
		 * @since 5.0.0
		 * @param array $field The field array.
		 */
		$field = apply_filters( 'acf/translate_field', $field );
	}
	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/translate_field', [ 'type' ], 0 );

// Translate fields passing through validation.
add_action( 'acf/validate_field', 'acf_translate_field' );

/**
 * Get fields
 *
 * Returns and array of fields for the given $parent.
 *
 * @since  5.0.0
 * @param  mixed $parent The field group or field settings.
 *                       Also accepts the field group ID or key.
 * @return array
 */
function acf_get_fields( $parent ) {

	// Allow field group selector as $parent.
	if ( ! is_array( $parent) ) {
		$parent = acf_get_field_group( $parent );
		if ( ! $parent ) {
			return [];
		}
	}

	$fields = [];

	// Check local fields first.
	if ( acf_have_local_fields( $parent['key'] ) ) {
		$raw_fields = acf_get_local_fields( $parent['key'] );
		foreach ( $raw_fields as $raw_field ) {
			$fields[] = acf_get_field( $raw_field['key'] );
		}

	// Then check database.
	} else {
		$raw_fields = acf_get_raw_fields( $parent['ID'] );
		foreach ( $raw_fields as $raw_field ) {
			$fields[] = acf_get_field( $raw_field['ID'] );
		}
	}

	/**
	 * Filters the $fields array.
	 *
	 * @since 5.0.0
	 * @param array $fields The array of fields.
	 */
	$fields = apply_filters( 'acf/load_fields', $fields, $parent );
	return $fields;
}

/**
 * Get raw fields
 *
 * Returns and array of raw field data for the given parent id.
 *
 * @since  5.7.10
 * @param  integer $id The field group or field id.
 * @return array
 */
function acf_get_raw_fields( $id = 0 ) {

	// Try cache.
	$cache_key = acf_cache_key( "acf_get_field_posts:$id" );
	$post_ids  = wp_cache_get( $cache_key, 'acf' );

	if ( $post_ids === false ) {

		// Query posts.
		$posts = get_posts( [
			'posts_per_page'			=> -1,
			'post_type'					=> 'acf-field',
			'orderby'					=> 'menu_order',
			'order'						=> 'ASC',
			'suppress_filters'			=> true, // DO NOT allow WPML to modify the query
			'cache_results'				=> true,
			'update_post_meta_cache'	=> false,
			'update_post_term_cache'	=> false,
			'post_parent'				=> $id,
			'post_status'				=> [ 'publish', 'trash' ],
		] );

		// Update $post_ids with a non false value.
		$post_ids = [];
		foreach ( $posts as $post ) {
			$post_ids[] = $post->ID;
		}

		// Update cache.
		wp_cache_set( $cache_key, $post_ids, 'acf' );
	}

	// Loop over ids and populate array of fields.
	$fields = [];
	foreach ( $post_ids as $post_id ) {
		$fields[] = acf_get_raw_field( $post_id );
	}

	return $fields;
}

/**
 * Get field count
 *
 * Return the number of fields for the given field group.
 *
 * @since  5.0.0
 * @param  array $parent The field group or field array.
 * @return integer
 */
function acf_get_field_count( $parent ) {

	// Check local fields first.
	if ( acf_have_local_fields( $parent['key'] ) ) {
		$raw_fields = acf_get_local_fields( $parent['key'] );

	// Then check database.
	} else {
		$raw_fields = acf_get_raw_fields( $parent['ID'] );
	}

	/**
	 * Filters the counted number of fields.
	 *
	 * @since 5.0.0
	 * @param integer $count The number of fields.
	 * @param array $parent The field group or field array.
	 */
	return apply_filters( 'acf/get_field_count', count( $raw_fields), $parent );
}

/**
 * Clone field
 *
 * Allows customization to a field when it is cloned.
 * Used by the clone field.
 *
 * @since  5.3.2
 * @param  array $field The field being cloned.
 * @param  array $clone_field The clone field.
 * @return array
 */
function acf_clone_field( $field, $clone_field ) {

	// Add reference to the clone field.
	$field['_clone'] = $clone_field['key'];

	/**
	 * Filters the $field array when it is being cloned.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 * @param array $clone_field The clone field array.
	 */
	$field = apply_filters( 'acf/clone_field', $field, $clone_field );

	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/clone_field', [ 'type' ], 0 );

/**
 * Prepare field
 *
 * Prepare a field for input.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @return array
 */
function acf_prepare_field( $field ) {

	// Stop if already prepared.
	if ( ! empty( $field['_prepare'] ) ) {
		return $field;
	}

	// Use field key to override input name.
	if ( $field['key'] ) {
		$field['name'] = $field['key'];
	}

	// Use field prefix to modify input name.
	if ( $field['prefix'] ) {
		$field['name'] = "{$field['prefix']}[ {$field['name']} ]";
	}

	// Generate id attribute from name.
	$field['id'] = acf_idify( $field['name'] );

	// Add state to field.
	$field['_prepare'] = true;

	/**
	 * Filters the $field array.
	 *
	 * Allows developers to modify field settings or return false to remove field.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	$field = apply_filters( 'acf/prepare_field', $field );

	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/prepare_field', [ 'type', 'name', 'key' ], 0 );

/**
 * Render fields
 *
 * Renders an array of fields. Also loads the field's value.
 *
 * @since  5.0.0
 * @since  5.6.9 Changed parameter order.
 * @param  array $fields An array of fields.
 * @param  mixed $post_id The post ID to load values from.
 * @param  string $element The wrapping element type.
 * @param  string $instruction The instruction render position (label|field ).
 * @return void
 */
function acf_render_fields( $fields, $post_id = 0, $el = 'div', $instruction = 'label' ) {

	// Parameter order changed in ACF 5.6.9.
	if ( is_array( $post_id ) ) {
		$args    = func_get_args();
		$fields  = $args[1];
		$post_id = $args[0];
	}

	/**
	 * Filters the $fields array before they are rendered.
	 *
	 * @since 5.0.0
	 * @param array $fields An array of fields.
	 * @param mixed $post_id The post ID to load values from.
	 */
	$fields = apply_filters( 'acf/pre_render_fields', $fields, $post_id );

	// Filter our false results.
	$fields = array_filter( $fields );

	// Loop over and render fields.
	if ( $fields ) {
		foreach ( $fields as $field ) {

			// Load value if not already loaded.
			if ( $field['value'] === null ) {
				$field['value'] = acf_get_value( $post_id, $field );
			}

			// Render wrap.
			acf_render_field_wrap( $field, $el, $instruction );
		}
	}
	/**
	 * Fires after fields have been rendered.
	 *
	 * @since 5.0.0
	 * @param array $fields An array of fields.
	 * @param mixed $post_id The post ID to load values from.
	 */
	do_action( 'acf/render_fields', $fields, $post_id );
}

/**
 * Render field wrap
 *
 * Render the wrapping element for a given field.
 *
 * @since  5.0.0
 * @param  array $field The field array.
 * @param  string $element The wrapping element type.
 * @param  string $instruction The instruction render position (label|field ).
 * @return void
 */
function acf_render_field_wrap( $field, $element = 'div', $instruction = 'label' ) {

	// Ensure field is complete (adds all settings).
	$field = acf_validate_field( $field );

	// Prepare field for input (modifies settings).
	$field = acf_prepare_field( $field );

	// Allow filters to cancel render.
	if ( ! $field ) {
		return;
	}

	// Determine wrapping element.
	$elements = [
		'div' => 'div',
		'tr'  => 'td',
		'td'  => 'div',
		'ul'  => 'li',
		'ol'  => 'li',
		'dl'  => 'dt',
	];

	if ( isset( $elements[$element] ) ) {
		$inner_element = $elements[$element];
	} else {
		$element = $inner_element = 'div';
	}

	// Generate wrapper attributes.
	$wrapper = [
		'id'        => '',
		'class'     => 'acf-field',
		'width'     => '',
		'style'     => '',
		'data-name' => $field['_name'],
		'data-type' => $field['type'],
		'data-key'  => $field['key'],
	];

	// Add field type attributes.
	$wrapper['class'] .= " acf-field-{$field['type']}";

	// Add field key attributes.
	if ( $field['key'] ) {
		$wrapper['class'] .= " acf-field-{$field['key']}";
	}

	// Add required attributes.
	// @todo Remove data-required.
	if ( $field['required'] ) {
		$wrapper['class']        .= ' is-required';
		$wrapper['data-required'] = 1;
	}

	// Clean up class attribute.
	$wrapper['class'] = str_replace( '_', '-', $wrapper['class'] );
	$wrapper['class'] = str_replace( 'field-field-', 'field-', $wrapper['class'] );

	// Merge in field 'wrapper' setting without destroying class and style.
	if ( $field['wrapper'] ) {
		$wrapper = acf_merge_attributes( $wrapper, $field['wrapper'] );
	}

	// Extract wrapper width and generate style.
	// Todo: Move from $wrapper out into $field.
	$width = acf_extract_var( $wrapper, 'width' );

	if ( $width ) {
		$width = acf_numval( $width );

		if ( $element !== 'tr' && $element !== 'td' ) {
			$wrapper['data-width'] = $width;
			$wrapper['style']     .= " width:{$width}%;";
		}
	}

	// Clean up all attributes.
	$wrapper = array_map( 'trim', $wrapper );
	$wrapper = array_filter( $wrapper );

	/**
	 * Filters the $wrapper array before rendering.
	 *
	 * @since 5.7.10
	 * @param array $wrapper The wrapper attributes array.
	 * @param array $field The field array.
	 */
	$wrapper = apply_filters( 'acf/field_wrapper_attributes', $wrapper, $field );

	// Append conditional logic attributes.
	if ( ! empty( $field['conditional_logic'] ) ) {
		$wrapper['data-conditions'] = $field['conditional_logic'];
	}
	if ( ! empty( $field['conditions'] ) ) {
		$wrapper['data-conditions'] = $field['conditions'];
	}

	// Vars for render.
	$attributes_html = acf_esc_attr( $wrapper );

	// Render HTML
	echo "<$element $attributes_html>" . "\n";
		if ( $element !== 'td' ) {
			echo "<$inner_element class=\"acf-label\">" . "\n";
				acf_render_field_label( $field );
				if ( $instruction == 'label' ) {
					acf_render_field_instructions( $field );
				}
			echo "</$inner_element>" . "\n";
		}
		echo "<$inner_element class=\"acf-input\">" . "\n";
			acf_render_field( $field );
			if ( $instruction == 'field' ) {
				acf_render_field_instructions( $field );
			}
		echo "</$inner_element>" . "\n";
	echo "</$element>" . "\n";
}

/**
 * Render field
 *
 * Render the input element for a given field.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @return void
 */
function acf_render_field( $field ) {

	// Ensure field is complete (adds all settings).
	$field = acf_validate_field( $field );

	// Prepare field for input (modifies settings).
	$field = acf_prepare_field( $field );

	// Allow filters to cancel render.
	if ( ! $field ) {
		return;
	}

	/**
	 * Fires when rendering a field.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	do_action( 'acf/render_field', $field );
}

// Register variation.
acf_add_action_variations( 'acf/render_field', [ 'type', 'name', 'key' ], 0 );

/**
 * Render field label
 *
 * Renders the field's label.
 *
 * @since  5.6.3
 * @param  array $field The field array.
 * @return void
 */
function acf_render_field_label( $field ) {

	// Get label.
	$label = acf_get_field_label( $field );

	// Output label.
	if ( $label ) {
		echo '<label' . ( $field['id'] ? ' for="' . esc_attr( $field['id'] ) . '"' : '' ) . '>' . acf_esc_html( $label) . '</label>';
	}
}

/**
 * Get field label
 *
 * Returns the field's label with appropriate required label.
 *
 * @since  5.0.0
 * @param  array $field The field array.
 * @param  string $context The output context (admin).
 * @return void
 */
function acf_get_field_label( $field, $context = '' ) {

	// Get label.
	$label = $field['label'];

	// Display empty text when editing field.
	if ( $context == 'admin' && $label === '' ) {
		$label = __( '(no label)', 'acf' );
	}

	// Add required HTML.
	if ( $field['required'] ) {
		$label .= ' <span class="acf-required">*</span>';
	}

	/**
	 * Filters the field's label HTML.
	 *
	 * @since 5.7.10
	 * @param string The label HTML.
	 * @param array $field The field array.
	 * @param string $context The output context (admin).
	 */
	$label = apply_filters( 'acf/get_field_label', $label, $field, $context );

	return $label;
}

/**
 * Render field instructions
 *
 * Renders the field's instructions.
 *
 * @since  5.6.3
 * @param  array $field The field array.
 * @return void
 */
function acf_render_field_instructions( $field ) {

	// Output instructions.
	if ( $field['instructions'] ) {
		echo '<p class="description">' . acf_esc_html( $field['instructions'] ) . '</p>';
	}
}

/**
 * Render field setting
 *
 * Renders a field setting used in the admin edit screen.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @param  array $setting The settings field array.
 * @param  boolean $global Whether this setting is a global or field type specific one.
 * @return void
 */
function acf_render_field_setting( $field, $setting, $global = false ) {

	// Validate field.
	$setting = acf_validate_field( $setting );

	// Add custom attributes to setting wrapper.
	$setting['wrapper']['data-key'] = $setting['name'];
	$setting['wrapper']['class']   .= ' acf-field-setting-' . $setting['name'];
	if ( ! $global ) {
		$setting['wrapper']['data-setting'] = $field['type'];
	}

	// Copy across prefix.
	$setting['prefix'] = $field['prefix'];

	// Find setting value from field.
	if ( $setting['value'] === null ) {

		// Setting name.
		if ( isset( $field[ $setting['name'] ] ) ) {
			$setting['value'] = $field[ $setting['name'] ];

		// Default value.
		} elseif ( isset( $setting['default_value'] ) ) {
			$setting['value'] = $setting['default_value'];
		}
	}

	// Add append attribute used by JS to join settings.
	if ( isset( $setting['_append'] ) ) {
		$setting['wrapper']['data-append'] = $setting['_append'];
	}

	// Render setting.
	acf_render_field_wrap( $setting, 'tr', 'label' );
}

/**
 * Update field
 *
 * Updates a field in the database.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @param  array $specific An array of specific field attributes to update.
 * @return void
 */
function acf_update_field( $field, $specific = [] ) {

	// Validate field.
	$field = acf_validate_field( $field );

	// May have been posted. Remove slashes.
	$field = wp_unslash ( $field );

	// Parse types (converts string '0' to integer 0).
	$field = acf_parse_types( $field );

	// Clean up conditional logic keys.
	if ( $field['conditional_logic'] ) {

		// Remove empty values and convert to associated array.
		$field['conditional_logic'] = array_filter( $field['conditional_logic'] );
		$field['conditional_logic'] = array_values( $field['conditional_logic'] );
		$field['conditional_logic'] = array_map( 'array_filter', $field['conditional_logic'] );
		$field['conditional_logic'] = array_map( 'array_values', $field['conditional_logic'] );
	}

	// Parent may be provided as a field key.
	if ( $field['parent'] && ! is_numeric( $field['parent'] ) ) {
		$parent = acf_get_field_post( $field['parent'] );
		$field['parent'] = $parent ? $parent->ID : 0;
	}

	/**
	 * Filters the $field array before it is updated.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	$field = apply_filters( 'acf/update_field', $field );

	// Make a backup of field data and remove some args.
	$_field = $field;
	acf_extract_vars(
		$_field,
		[
			'ID',
			'key',
			'label',
			'name',
			'prefix',
			'value',
			'menu_order',
			'id',
			'class',
			'parent',
			'_name',
			'_prepare',
			'_valid'
		]
	);

	// Create array of data to save.
	$save = [
		'ID'           => $field['ID'],
		'post_status'  => 'publish',
		'post_type'    => 'acf-field',
		'post_title'   => $field['label'],
		'post_name'    => $field['key'],
		'post_excerpt' => $field['name'],
		'post_content' => maybe_serialize( $_field ),
		'post_parent'  => $field['parent'],
		'menu_order'   => $field['menu_order'],
	];

	// Reduce save data if specific key list is provided.
	if ( $specific ) {
		$specific[] = 'ID';
		$save = acf_get_sub_array( $save, $specific );
	}

	// Unhook wp_targeted_link_rel() filter from WP 5.1 corrupting serialized data.
	remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );

	// Slash data.
	// WP expects all data to be slashed and will unslash it (fixes '\' character issues).
	$save = wp_slash ( $save );

	// Update or Insert.
	if ( $field['ID'] ) {
		wp_update_post( $save );
	} else	{
		$field['ID'] = wp_insert_post( $save );
	}

	// Flush field cache.
	acf_flush_field_cache( $field );

	/**
	 * Fires after a field has been updated, and the field cache has been cleaned.
	 *
	 * @since .7.10
	 * @param array $field The field array.
	 */
	do_action( 'acf/updated_field', $field );

	return $field;
}

// Register variation.
acf_add_filter_variations( 'acf/update_field', [ 'type', 'name', 'key' ], 0 );

/**
 * Apply unique field slug
 *
 * Allows full control over 'acf-field' slugs.
 *
 * @since  5.7.10
 * @param  string $slug The post slug.
 * @param  integer $post_ID Post ID.
 * @param  string $post_status The post status.
 * @param  string $post_type Post type.
 * @param  integer $post_parent Post parent ID
 * @param  string $original_slug The original post slug.
 * @return string
 */
function _acf_apply_unique_field_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {

	// Check post type and reset to original value.
	if ( $post_type === 'acf-field' ) {
		return $original_slug;
	}

	return $slug;
}
add_filter( 'wp_unique_post_slug', '_acf_apply_unique_field_slug', 999, 6 );

/**
 * Flush field cache
 *
 * Deletes all caches for this field.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @return void
 */
function acf_flush_field_cache( $field ) {

	// Delete stored data.
	acf_get_store( 'fields' )->remove( $field['key'] );

	// Flush cached post_id for this field's name and key.
	wp_cache_delete( acf_cache_key( "acf_get_field_post:name:{$field['name']}" ), 'acf' );
	wp_cache_delete( acf_cache_key( "acf_get_field_post:key:{$field['key']}" ), 'acf' );

	// Flush cached array of post_ids for this field's parent.
	wp_cache_delete( acf_cache_key( "acf_get_field_posts:{$field['parent']}" ), 'acf' );
}

/**
 * Delete field
 *
 * Deletes a field from the database.
 *
 * @since  5.7.10
 * @param  mixed $id The field ID, key or name.
 * @return boolean True if field was deleted.
 */
function acf_delete_field( $id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field || ! $field['ID'] ) {
		return false;
	}

	// Delete post.
	wp_delete_post( $field['ID'], true );

	// Flush field cache.
	acf_flush_field_cache( $field );

	/**
	 * Fires immediately after a field has been deleted.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	do_action( 'acf/delete_field', $field );

	return true;
}

// Register variation.
acf_add_action_variations( 'acf/delete_field', [ 'type', 'name', 'key' ], 0 );

/**
 * Trash field
 *
 * Trashes a field from the database.
 *
 * @since  5.0.0
 * @param  mixed $id The field ID, key or name.
 * @return boolean True if field was trashed.
 */
function acf_trash_field( $id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field || ! $field['ID'] ) {
		return false;
	}

	// Trash post.
	wp_trash_post( $field['ID'], true );

	/**
	 * Fires immediately after a field has been trashed.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	do_action( 'acf/trash_field', $field );

	return true;
}

/**
 * Untrash field
 *
 * Restores a field from the trash.
 *
 * @since  5.0.0
 * @param  mixed $id The field ID, key or name.
 * @return boolean True if field was trashed.
 */
function acf_untrash_field( $id = 0 ) {

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field || ! $field['ID'] ) {
		return false;
	}

	// Untrash post.
	wp_untrash_post( $field['ID'], true );

	// Flush field cache.
	acf_flush_field_cache( $field );

	/**
	 * Fires immediately after a field has been trashed.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	do_action( 'acf/untrash_field', $field );

	return true;
}

/**
 * Untrash field post status
 *
 * Filter callback which returns the previous post_status instead of "draft" for the "acf-field" post type.
 *
 * Prior to WordPress 5.6.0, this filter was not needed as restored posts were always assigned their original status.
 *
 * @since  5.9.5
 * @param  string $new_status The new status of the post being restored.
 * @param  integer $post_id The ID of the post being restored.
 * @param  string $previous_status The status of the post at the point where it was trashed.
 * @return string
 */
function _acf_untrash_field_post_status( $new_status, $post_id, $previous_status ) {
	return ( get_post_type( $post_id ) === 'acf-field' ) ? $previous_status : $new_status;
}
add_action( 'wp_untrash_post_status', '_acf_untrash_field_post_status', 10, 3 );

/**
 * Prefix fields
 *
 * Changes the prefix for an array of fields by reference.
 *
 * @since  5.6.0
 * @param  array $fields An array of fields.
 * @param  string $prefix The new prefix.
 * @return void
 */
function acf_prefix_fields( &$fields, $prefix = 'acf' ) {

	// Loop over fields.
	foreach ( $fields as &$field ) {

		// Replace 'acf' with $prefix.
		$field['prefix'] = $prefix . substr( $field['prefix'], 3);
	}
}

/**
 * Get sub field
 *
 * Searches a field for sub fields matching the given selector.
 *
 * @since  5.7.10
 * @param  mixed $id The field ID, key or name.
 * @param  array $field The parent field array.
 * @return mixed
 */
function acf_get_sub_field( $id, $field ) {

	$sub_field = false;

	// Search sub fields.
	if ( isset( $field['sub_fields'] ) ) {
		$sub_field = acf_search_fields( $id, $field['sub_fields'] );
	}

	/**
	 * Filters the $sub_field found.
	 *
	 * @since 5.0.0
	 * @param array $sub_field The found sub field array.
	 * @param string $selector The selector used to search.
	 * @param array $field The parent field array.
	 */
	$sub_field = apply_filters( 'acf/get_sub_field', $sub_field, $id, $field );

	return $sub_field;
}

// Register variation.
acf_add_filter_variations( 'acf/get_sub_field', [ 'type' ], 2 );

/**
 * Search fields
 *
 * Searches an array of fields for one that matches the given identifier.
 *
 * @since  5.7.11
 * @param  mixed $id The field ID, key or name.
 * @param  array $haystack The array of fields.
 * @return mixed
 */
function acf_search_fields( $id, $fields ) {

	/**
	 * Loop over searchable keys in order of priority.
	 * Important to search "name" on all fields before "_name" backup.
	 */
	foreach ( [ 'key', 'name', '_name', '__name' ] as $key ) {

		// Loop over fields and compare.
		foreach ( $fields as $field ) {
			if ( isset( $field[$key] ) && $field[$key] === $id ) {
				return $field;
			}
		}
	}
	return false;
}

/**
 * Is field
 *
 * Returns true if the given params match a field.
 *
 * @since  5.7.10
 * @param  array $field The field array.
 * @param  mixed $id An optional identifier to search for.
 * @return boolean
 */
function acf_is_field( $field = false, $id = '' ) {
	return (
		is_array( $field )
		&& isset( $field['key'] )
		&& isset( $field['name'] )
	);
}

/**
 * Get field ancestors
 *
 * Returns an array of ancestor field ID's or keys.
 *
 * @since  5.3.8
 * @param  array $field The field array.
 * @return array
 */
function acf_get_field_ancestors( $field ) {

	$ancestors = [];

	// Loop over parents.
	while( $field['parent'] && $field = acf_get_field( $field['parent'] ) ) {
		$ancestors[] = $field['ID'] ? $field['ID'] : $field['key'];
	}
	return $ancestors;
}

/**
 * Duplicate fields
 *
 * Duplicate an array of fields.
 *
 * @since  5.0.0
 * @param  array $fields An array of fields.
 * @param  integer $parent_id The new parent ID.
 * @return array
 */
function acf_duplicate_fields( $fields = [], $parent_id = 0 ) {

	// Generate keys for all new fields.
	// - Needed to alter conditional logic rules.
	// - Use usleep() to ensure unique keys.
	$keys = [];
	foreach ( $fields as $field ) {
		usleep( 1 );
		$keys[ $field['key'] ] = uniqid( 'field_' );
	}
	acf_append_data( 'generated_keys', $keys );

	// Duplicate fields.
	foreach ( $fields as $field ) {
		$field_id = $field['ID'] ? $field['ID'] : $field['key'];
		$duplicates[] = acf_duplicate_field( $field_id, $parent_id );
	}

	// Return.
	return $duplicates;
}

/**
 * Duplicate field
 *
 * Duplicates a field.
 *
 * @since  5.0.0
 * @param  mixed $id The field ID, key or name.
 * @param  integer $parent_id The new parent ID.
 * @return boolean True if field was duplicated.
 */
function acf_duplicate_field( $id = 0, $parent_id = 0 ){

	// Get the field.
	$field = acf_get_field( $id );

	// Bail early if field was not found.
	if ( ! $field ) {
		return false;
	}

	// Remove ID to avoid update.
	$field['ID'] = 0;

	// Generate key.
	$keys = acf_get_data( 'generated_keys' );
	if ( isset( $keys[ $field['key'] ] ) ) {
		$field['key'] = $keys[ $field['key'] ];
	} else {
		$field['key'] = uniqid( 'field_' );
	}

	// Set parent.
	if ( $parent_id ) {
		$field['parent'] = $parent_id;
	}

	// Update conditional logic references because field keys have changed.
	if ( $field['conditional_logic'] ) {

		// Loop over groups.
		foreach ( $field['conditional_logic'] as $group_i => $group ) {

			// Loop over rules.
			foreach ( $group as $rule_i => $rule ) {
				$field['conditional_logic'][ $group_i ][ $rule_i ]['field'] = isset( $keys[ $rule['field'] ] ) ? $keys[ $rule['field'] ] : $rule['field'];
			}
		}
	}

	/**
	 * Filters the $field array after it has been duplicated.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	$field = apply_filters( 'acf/duplicate_field', $field );

	return acf_update_field( $field );
}

// Register variation.
acf_add_filter_variations( 'acf/duplicate_field', [ 'type' ], 0 );

/**
 * Prepare fields for export
 *
 * Returns a modified array of fields ready for export.
 *
 * @since  5.0.0
 * @param  array $fields An array of fields.
 * @return array
 */
function acf_prepare_fields_for_export( $fields = [] ) {

	// Map function and return.
	return array_map( 'acf_prepare_field_for_export', $fields );
}

/**
 * Prepare field for export
 *
 * Returns a modified field ready for export.
 *
 * @since  5.0.0
 * @param  array $field The field array.
 * @return array
 */
function acf_prepare_field_for_export( $field ) {

	// Remove args.
	acf_extract_vars(
		$field,
		[
			'ID',
			'prefix',
			'value',
			'menu_order',
			'id',
			'class',
			'parent',
			'_name',
			'_prepare',
			'_valid'
		]
	);

	/**
	 * Filters the $field array before being returned to the export tool.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	return apply_filters( 'acf/prepare_field_for_export', $field );
}

// Register variation.
acf_add_filter_variations( 'acf/prepare_field_for_export', [ 'type' ], 0 );

/**
 * Prepare field for import
 *
 * Returns a modified array of fields ready for import.
 *
 * @since  5.0.0
 * @param  array $fields An array of fields.
 * @return array
 */
function acf_prepare_fields_for_import( $fields = [] ) {

	// Ensure array is sequential.
	$fields = array_values( $fields);

	// Prepare each field for import making sure to detect additional sub fields.
	$i = 0;
	while( $i < count( $fields) ) {

		// Prepare field.
		$field = acf_prepare_field_for_import( $fields[ $i ] );

		// Update single field.
		if ( isset( $field['key'] ) ) {
			$fields[ $i ] = $field;

		// Insert multiple fields.
		} else {
			array_splice( $fields, $i, 1, $field );
		}

		// Iterate.
		$i++;
	}

	/**
	 * Filters the $fields array before being returned to the import tool.
	 *
	 * @since 5.0.0
	 * @param array $fields The array of fields.
	 */
	return apply_filters( 'acf/prepare_fields_for_import', $fields );
}

/**
 * Prepare field for import
 *
 * Returns a modified field ready for import.
 * Allows parent fields to modify themselves and also return sub fields.
 *
 * @since  5.0.0
 * @param  array $field The field array.
 * @return array
 */
function acf_prepare_field_for_import( $field ) {

	/**
	 * Filters the $field array before being returned to the import tool.
	 *
	 * @since 5.0.0
	 * @param array $field The field array.
	 */
	return apply_filters( 'acf/prepare_field_for_import', $field );
}

// Register variation.
acf_add_filter_variations( 'acf/prepare_field_for_import', [ 'type' ], 0 );
