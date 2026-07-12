<?php
/**
 * ACF block functions
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register store.
acf_register_store( 'block-types' );
acf_register_store( 'block-cache' );

/**
 * Register block type
 *
 * Registers a custom block type.
 *
 * @since  1.0.0
 * @param  array $block The block settings.
 * @return fixed
 */
function acf_register_block_type( $block ) {

	// Validate block type settings.
	$block = acf_validate_block_type( $block );
    $block = apply_filters( 'acf/register_block_type_args', $block );

    // Require name.
    if ( ! $block['name'] ) {
	    $message = __( 'Block type name is required.', 'acf' );
	    _doing_it_wrong( __FUNCTION__, $message, '5.8.0' );
		return false;
    }

	// Stop if already exists.
	if ( acf_has_block_type( $block['name'] ) ) {
		$message = sprintf( __( 'Block type "%s" is already registered.' ), $block['name'] );
		_doing_it_wrong( __FUNCTION__, $message, '5.8.0' );
		return false;
	}

	// Add to storage.
	acf_get_store( 'block-types' )->set( $block['name'], $block );

	// Register block type in WP.
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type(
			$block['name'],
			[
				'attributes' => acf_get_block_type_default_attributes( $block ),
				'render_callback' => 'acf_render_block_callback',
			]
		);
	}

	// Register action.
	add_action( 'enqueue_block_editor_assets', 'acf_enqueue_block_assets' );

	// Return block.
	return $block;
}

/**
 * Register block
 *
 * @see acf_register_block_type().
 *
 * @since  1.0.0
 * @param  array $block The block settings.
 * @return mixed
 */
function acf_register_block( $block ) {
	return acf_register_block_type( $block );
}

/**
 * Has block type
 *
 * Returns true if a block type exists for the given name.
 *
 * @since  1.0.0
 * @param  string $name The block type name.
 * @return boolean
 */
function acf_has_block_type( $name ) {
	return acf_get_store( 'block-types' )->has( $name );
}

/**
 * Get block types
 *
 * Returns an array of all registered block types.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_block_types() {
	return acf_get_store( 'block-types' )->get();
}

/**
 * Get block type
 *
 * Returns a block type for the given name.
 *
 * @since  1.0.0
 * @param  string $name The block type name.
 * @return mixed
 */
function acf_get_block_type( $name ) {
	return acf_get_store( 'block-types' )->get( $name );
}

/**
 * Remove block type
 *
 * Removes a block type for the given name.
 *
 * @since  1.0.0
 * @param  string $name The block type name.
 * @return void
 */
function acf_remove_block_type( $name ) {
	acf_get_store( 'block-types' )->remove( $name );
}

/**
 * Get block type default attributes
 *
 * Returns an array of default attribute settings for a block type.
 *
 * @since  1.0.0
 * @param  string $block_type
 * @return array
 */
function acf_get_block_type_default_attributes( $block_type ) {

	$attributes = [
		'id'    => [
			'type'    => 'string',
			'default' => ''
		],
		'name'  => [
			'type'    => 'string',
			'default' => ''
		],
		'data'  => [
			'type'    => 'object',
			'default' => []
		],
		'align' => [
			'type'    => 'string',
			'default' => ''
		],
		'mode' => [
			'type'    => 'string',
			'default' => ''
		]
	];
	if ( ! empty( $block_type['supports']['align_text'] ) ) {
		$attributes['align_text'] = [
			'type'    => 'string',
			'default' => ''
		];
	}
	if ( ! empty( $block_type['supports']['align_content'] ) ) {
		$attributes['align_content'] = [
			'type'    => 'string',
			'default' => ''
		];
	}
	return $attributes;
}

/**
 * Validate block type
 *
 * Validates a block type ensuring all settings exist.
 *
 * @since  1.0.0
 * @param  array $block The block settings.
 * @return array
 */
function acf_validate_block_type( $block ) {

	// Add default settings.
	$block = wp_parse_args( $block, [
		'name'            => '',
		'title'           => '',
		'description'     => '',
		'category'        => 'common',
		'icon'            => '',
		'mode'            => 'preview',
		'align'           => '',
		'keywords'        => [],
		'supports'        => [],
		'post_types'      => [],
		'render_template' => false,
		'render_callback' => false,
		'enqueue_style'   => false,
		'enqueue_script'  => false,
		'enqueue_assets'  => false
	] );

	// Restrict keywords to 3 max to avoid JS error in older versions.
	if ( acf_version_compare( 'wp', '<', '5.2' ) ) {
		$block['keywords'] = array_slice( $block['keywords'], 0, 3 );
	}

	// Generate name with prefix.
	if ( $block['name'] ) {
		$block['name'] = 'acf/' . acf_slugify( $block['name'] );
	}

	// Add default 'supports' settings.
	$block['supports'] = wp_parse_args( $block['supports'], [
		'align' => true,
		'html'  => false,
		'mode'  => true
	] );

	// Correct "Experimental" flags.
	if ( isset( $block['supports']['__experimental_jsx'] ) ) {
		$block['supports']['jsx'] = $block['supports']['__experimental_jsx'];
	}

	// Return block.
	return $block;
}

/**
 * Prepare block
 *
 * Prepares a block for use in render_callback by merging in all settings and attributes.
 *
 * @since1.0.0
 * @param  array $block The block props.
 * @return array
 */
function acf_prepare_block( $block ) {

	// Stop if no name.
	if ( ! isset( $block['name'] ) ) {
		return false;
	}

	// Get block type and return false if doesn't exist.
	$block_type = acf_get_block_type( $block['name'] );
	if ( ! $block_type ) {
		return false;
	}

	// Generate default attributes.
	$attributes = [];
	foreach ( acf_get_block_type_default_attributes( $block_type ) as $k => $v ) {
		$attributes[ $k ] = $v['default'];
	}

	// Merge together arrays in order of least to most specific.
	$block = array_merge( $block_type, $attributes, $block );

	// Return block.
	return $block;
}

/**
 * Render block callback
 *
 * The render callback for all ACF blocks.
 *
 * @since  1.0.0
 * @param  array  $attributes The block attributes.
 * @param  string $content The block content.
 * @param  object $wp_block The block instance (since WP 5.5).
 * @return string The block HTML.
 */
function acf_render_block_callback( $attributes, $content = '', $wp_block = null ) {
	$is_preview = false;
	$post_id    = get_the_ID();

	// Set preview flag to true when rendering for the block editor.
	if ( is_admin() && acf_is_block_editor() ) {
		$is_preview = true;
	}

	// Return rendered block HTML.
	return acf_rendered_block( $attributes, $content, $is_preview, $post_id, $wp_block );
}

/**
 * Rendered block
 *
 * Returns the rendered block HTML.
 *
 * @since  1.0.0
 * @param  array  $attributes The block attributes.
 * @param  string $content The block content.
 * @param  boolean $is_preview Whether or not the block is being
 *                             rendered for editing preview.
 * @param  integer $post_id The current post being edited or viewed.
 * @param  object $wp_block The block instance (since WP 5.5).
 * @return string The block HTML.
 */
function acf_rendered_block( $attributes, $content = '', $is_preview = false, $post_id = 0, $wp_block = null ) {

	// Capture block render output.
	ob_start();
	acf_render_block( $attributes, $content, $is_preview, $post_id, $wp_block );
	$html = ob_get_clean();

	// Replace <InnerBlocks /> placeholder on front-end.
	if ( ! $is_preview ) {
		// Escape "$" character to avoid "capture group" interpretation.
		$content = str_replace( '$', '\$', $content );
		$html    = preg_replace( '/<InnerBlocks([\S\s]*?)\/>/', $content, $html );
	}

	// Store in cache for preloading.
	acf_get_store( 'block-cache' )->set( $attributes['id'], '<div class="acf-block-preview">' . $html . '</div>' );
	return $html;
}

/**
 * Render block
 *
 * Renders the block HTML.
 *
 * @since  1.0.0
 * @param  array  $attributes The block attributes.
 * @param  string $content The block content.
 * @param  boolean $is_preview Whether or not the block is being rendered for editing preview.
 * @param  integer $post_id The current post being edited or viewed.
 * @param  object $wp_block The block instance (since WP 5.5).
 * @return void
 */
function acf_render_block( $attributes, $content = '', $is_preview = false, $post_id = 0, $wp_block = null ) {

	// Prepare block ensuring all settings and attributes exist.
	$block = acf_prepare_block( $attributes );
	if ( ! $block ) {
		return '';
	}

	// Find post_id if not defined.
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Enqueue block type assets.
	acf_enqueue_block_type_assets( $block );

	// Setup postdata allowing get_field() to work.
	acf_setup_meta( $block['data'], $block['id'], true );

	// Call render_callback.
	if ( is_callable( $block['render_callback'] ) ) {
		call_user_func( $block['render_callback'], $block, $content, $is_preview, $post_id, $wp_block );

	// Or include template.
	} elseif ( $block['render_template'] ) {

		// Locate template.
		if ( file_exists( $block['render_template'] ) ) {
			$path = $block['render_template'];
	    } else {
		    $path = locate_template( $block['render_template'] );
	    }

	    // Include template.
	    if ( file_exists( $path ) ) {
		    include( $path );
	    }
	}

	// Reset postdata.
	acf_reset_meta( $block['id'] );
}

/**
 * Get block fields
 *
 * Returns an array of all fields for the given block.
 *
 * @since  1.0.0
 * @param  array $block The block props.
 * @return array
 */
function acf_get_block_fields( $block ) {

	$fields = [];

	// Get field groups for this block.
	$field_groups = acf_get_field_groups( [
		'block'	=> $block['name']
	] );

	// Loop over results and append fields.
	if ( $field_groups ) {
		foreach ( $field_groups as $field_group ) {
			$fields = array_merge( $fields, acf_get_fields( $field_group ) );
		}
	}

	// Return fields.
	return $fields;
}

/**
 * Enqueue block assets
 *
 * Enqueues and localizes block scripts and styles.
 *
 * @since  1.0.0
 * @return void
 */
function acf_enqueue_block_assets() {

	// Localize text.
	acf_localize_text( [
		'Switch to Edit'    => __( 'Switch to Edit', 'acf' ),
		'Switch to Preview' => __( 'Switch to Preview', 'acf' ),
		'Change content alignment' => __( 'Change content alignment', 'acf' ),
		'%s settings'       => __( '%s settings', 'acf' )
	] );

	// Get block types.
	$block_types = acf_get_block_types();

	// Localize data.
	acf_localize_data( [
		'blockTypes' => array_values( $block_types ),

		// List of attributes to replace for HTML to JSX compatibility.
		// https://github.com/facebook/react/blob/master/packages/react-dom/src/shared/possibleStandardNames.js
		'jsxAttributes'	=> [
			'cellpadding'   => 'cellPadding',
			'cellspacing'   => 'cellSpacing',
			'class'         => 'className',
			'colspan'       => 'colSpan',
			'datetime'      => 'dateTime',
			'for'           => 'htmlFor',
			'hreflang'      => 'hrefLang',
			'readonly'      => 'readOnly',
			'rowspan'       => 'rowSpan',
			'srclang'       => 'srcLang',
			'srcset'        => 'srcSet',
			'allowedblocks' => 'allowedBlocks',
			'templatelock'  => 'templateLock'
		],
		'postType' => get_post_type()
	] );

	// Enqueue script.
	wp_enqueue_script( 'acf-blocks', acf_get_url( 'assets/js/acf-pro-blocks.min.js' ), [ 'acf-input', 'wp-blocks' ], ACF_VERSION, true );

	// Enqueue block assets.
	array_map( 'acf_enqueue_block_type_assets', $block_types );

	// During the edit screen loading, WordPress renders all blocks in its own attempt to preload data.
	// Retrieve any cached block HTML and include this in the localized data.
	if ( defined( 'ACF_EXPERIMENTAL_PRELOAD_BLOCKS' ) && ACF_EXPERIMENTAL_PRELOAD_BLOCKS ) {
		$preloaded_blocks = acf_get_store( 'block-cache' )->get_data();
		acf_localize_data( [
			'preloadedBlocks' => $preloaded_blocks
		] );
	}
}

/**
 * Enqueue block type assets
 *
 * Enqueues scripts and styles for a specific block type.
 *
 * @since  1.0.0
 * @param  array $block_type The block type settings.
 * @return void
 */
function acf_enqueue_block_type_assets( $block_type ) {

	// Generate handle from name.
	$handle = 'block-' . acf_slugify( $block_type['name'] );

	// Enqueue style.
	if ( $block_type['enqueue_style'] ) {
		wp_enqueue_style( $handle, $block_type['enqueue_style'], [], false, 'all' );
	}

	// Enqueue script.
	if ( $block_type['enqueue_script'] ) {
		wp_enqueue_script( $handle, $block_type['enqueue_script'], [], false, true );
	}

	// Enqueue assets callback.
	if ( $block_type['enqueue_assets'] && is_callable( $block_type['enqueue_assets'] ) ) {
		call_user_func( $block_type['enqueue_assets'], $block_type );
	}
}

/**
 * AJAX fetch block
 *
 * Handles the AJAX request for block data.
 *
 * @since  1.0.0
 * @return void
 */
function acf_ajax_fetch_block() {

	// Validate AJAX request.
	if ( ! acf_verify_ajax() ) {
		 wp_send_json_error();
	}

	// Get request args.
	extract( acf_request_args( [
		'block'   => false,
		'post_id' => 0,
		'query'   => []
	] ) );

	// Stop if no block.
	if ( ! $block ) {
		wp_send_json_error();
	}

	// Unslash and decode $_POST data.
	$block = wp_unslash( $block );
	$block = json_decode( $block, true );

	// Prepare block ensuring all settings and attributes exist.
	if ( ! $block = acf_prepare_block( $block ) ) {
		wp_send_json_error();
	}

	// Load field defaults when first previewing a block.
	if ( ! empty( $query['preview'] ) && ! $block['data'] ) {
		$fields = acf_get_block_fields( $block );
		foreach ( $fields as $field ) {
		   	$block['data'][ "_{$field['name']}" ] = $field['key'];
   		}
	}

	// Setup postdata allowing form to load meta.
	acf_setup_meta( $block['data'], $block['id'], true );

	$response = [];

	// Query form.
	if ( ! empty( $query['form'] ) ) {

		// Load fields for form.
		$fields = acf_get_block_fields( $block );

		// Prefix field inputs to avoid multiple blocks using the same name/id attributes.
		acf_prefix_fields( $fields, "acf-{$block['id']}" );

		// Start Capture.
		ob_start();

		// Render.
		echo '<div class="acf-block-fields acf-fields">';
   			acf_render_fields( $fields, $block['id'], 'div', 'field' );
		echo '</div>';

		// Store Capture.
		$response['form'] = ob_get_contents();
		ob_end_clean();
	}

	// Query preview.
	if ( ! empty( $query['preview'] ) ) {

		// Render_callback vars.
   		$content    = '';
   		$is_preview = true;

		// Render.
		$html  = '';
		$html .= '<div class="acf-block-preview">';
   		$html .= acf_rendered_block( $block, $content, $is_preview, $post_id );
		$html .= '</div>';

		// Store HTML.
		$response['preview'] = $html;
	}

	// Send response.
	wp_send_json_success( $response );
}

// Register AJAX action.
acf_register_ajax( 'fetch-block', 'acf_ajax_fetch_block' );

/**
 * Parse save blocks
 *
 * Parse content that may contain HTML block comments and saves ACF block meta.
 *
 * @since  1.0.0
 * @param  string $text Content that may contain HTML block comments.
 * @return string
 */
function acf_parse_save_blocks( $text = '' ) {

	// Search text for dynamic blocks and modify attrs.
	return addslashes(
		preg_replace_callback(
			'/<!--\s+wp:(?P<name>[\S]+)\s+(?P<attrs>{[\S\s]+?})\s+(?P<void>\/)?-->/',
			'acf_parse_save_blocks_callback',
			stripslashes( $text )
		)
	);
}

// Hook into saving process.
add_filter( 'content_save_pre', 'acf_parse_save_blocks', 5, 1 );

/**
 * Parse save blocks callback
 *
 * Callback used in preg_replace to modify ACF Block comment.
 *
 * @since  1.0.0
 * @param  array $matches The preg matches.
 * @return string
 */
function acf_parse_save_blocks_callback( $matches ) {

	// Defaults.
	$name  = isset( $matches['name'] ) ? $matches['name'] : '';
	$attrs = isset( $matches['attrs'] ) ? json_decode( $matches['attrs'], true ) : '';
	$void  = isset( $matches['void'] ) ? $matches['void'] : '';

	// Stop if missing data or not an ACF Block.
	if ( ! $name || ! $attrs || ! acf_has_block_type( $name ) ) {
		return $matches[0];
	}

	/**
	 * Convert "data" to "meta".
	 * No need to check if already in meta format.
	 * Local Meta will do this for us.
	 */
	if ( isset( $attrs['data'] ) ) {
		$attrs['data'] = acf_setup_meta( $attrs['data'], $attrs['id'] );
	}

	// Prevent wp_targeted_link_rel from corrupting JSON.
	remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
	remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );
	remove_filter( 'content_save_pre', 'balanceTags', 50 );

	$attrs = apply_filters( 'acf/pre_save_block', $attrs );

	// Return new comment.
	return '<!-- wp:' . $name . ' ' . acf_json_encode( $attrs ) . ' ' . $void . '-->';
}
