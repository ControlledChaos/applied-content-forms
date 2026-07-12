<?php
/**
 * Template functions
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

/**
 * Get flexible
 *
 * Helper for the flexible content dynamic render.
 *
 * @since  1.0.0
 * @param  string $selector
 * @param  integer $post_id
 * @global boolean $is_preview
 * @return mixed
 */
if ( ! function_exists( 'get_flexible' ) ) {

	function get_flexible( $selector, $post_id = false ) {

		if ( ! have_rows( $selector, $post_id ) ) {
			return false;
		}

		$flexible = acf_get_field_type( 'flexible_content' );

		ob_start();

		while ( have_rows( $selector, $post_id ) ) : the_row();

			$loop  = acf_get_loop( 'active' );
			$field = $loop['field'];

			// Stop if not flexible content.
			if ( 'flexible_content' !== $field['type'] ) {
				break;
			}

			$loop_i = acf_get_loop( 'active', 'i' );
			$layout = $flexible->get_layout( get_row_layout(), $field );

			// First row.
			if ( 0 === $loop_i ) {

				// Access global variables.
				global $is_preview;

				if ( ! isset( $is_preview ) ) {
					$is_preview = false;
				}

				$name = $field['_name'];
				$key  = $field['key'];

				do_action( "acfe/flexible/enqueue", $field, $is_preview );
				do_action( "acfe/flexible/enqueue/name={$name}", $field, $is_preview );
				do_action( "acfe/flexible/enqueue/key={$key}", $field, $is_preview );
			}

			// Render HTML comment.
			echo "\n" . '<!-- ' . $layout['label'] . ' -->' . "\n";

			// Render enqueue.
			acf_flexible_render_layout_enqueue( $layout, $field );

			// Render template.
			acf_flexible_render_layout_template( $layout, $field );

		endwhile;
		return ob_get_clean();
	}
}

/**
 * The flexible
 *
 * Helper for the flexible content dynamic render.
 *
 * @since  1.0.0
 * @param  string $selector
 * @param  mixed $post_id
 * @return void
 */
if ( ! function_exists( 'the_flexible' ) ) {
	function the_flexible( $selector, $post_id = false ) {
		echo get_flexible( $selector, $post_id );
	}
}

/**
 * Has flexible
 *
 * Helper for the flexible content dynamic render.
 *
 * @since  1.0.0
 * @param  string $selector
 * @param  mixed $post_id
 * @return boolean
 */
if ( ! function_exists( 'has_flexible' ) ) {
	function has_flexible( $selector, $post_id = false ) {
		return have_rows( $selector, $post_id );
	}
}

/**
 * Flexible render layout template
 *
 * Find & include the flexible content layouts PHP files.
 *
 * @since  1.0.0
 * @param  string $layout
 * @param  string $field
 * @global boolean $col
 * @global boolean $is_preview
 * @return void
 */
function acf_flexible_render_layout_template( $layout, $field ) {

	// Access global variables.
	global $col, $is_preview;

	$col    = false;
	$name   = $field['_name'];
	$key    = $field['key'];
	$l_name = $layout['name'];

	$file = acf_maybe_get( $layout, 'acfe_flexible_render_template' );
	$file = apply_filters( "acfe/flexible/render/template", $file, $field, $layout, $is_preview );
	$file = apply_filters( "acfe/flexible/render/template/name={$name}", $file, $field, $layout, $is_preview );
	$file = apply_filters( "acfe/flexible/render/template/key={$key}", $file, $field, $layout, $is_preview );
	$file = apply_filters( "acfe/flexible/render/template/layout={$l_name}", $file, $field, $layout, $is_preview );
	$file = apply_filters( "acfe/flexible/render/template/name={$name}&layout={$l_name}", $file, $field, $layout, $is_preview );
	$file = apply_filters( "acfe/flexible/render/template/key={$key}&layout={$l_name}", $file, $field, $layout, $is_preview );

	// Before template.
	do_action( "acfe/flexible/render/before_template", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/before_template/name={$name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/before_template/key={$key}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/before_template/layout={$l_name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/before_template/name={$name}&layout={$l_name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/before_template/key={$key}&layout={$l_name}", $field, $layout, $is_preview );

	// Check file.
	if ( ! empty( $file ) ) {

		$file_found = acfe_locate_file_path( $file );
		if ( ! empty( $file_found ) ) {

			// Front end
			if ( ! $is_preview ) {
				include( $file_found );
			} else {

				$path = pathinfo( $file );
				$extension = $path['extension'];

				$file_preview  = substr( $file, 0, -strlen( $extension ) -1 );
				$file_preview .= '-preview.' . $extension;
				$file_preview  = acfe_locate_file_path( $file_preview );

				if ( ! empty( $file_preview ) ) {
					include( $file_preview );
				} else {
					include( $file_found );
				}
			}
		}
	}

	// After template.
	do_action( "acfe/flexible/render/after_template", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/after_template/name={$name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/after_template/key={$key}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/after_template/layout={$l_name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/after_template/name={$name}&layout={$l_name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/render/after_template/key={$key}&layout={$l_name}", $field, $layout, $is_preview );
}

/**
 * Flexible render layout enqueue
 *
 * Find and enqueue assets files for the flexible content.
 *
 * @since  1.0.0
 * @param  $layout
 * @param  $field
 * @global boolean $is_preview
 * @return void
 */
function acf_flexible_render_layout_enqueue( $layout, $field ) {

	// Access global variables.
	global $is_preview;

	$name   = $field['_name'];
	$key    = $field['key'];
	$l_name = $layout['name'];
	$handle = acf_slugify( $name ) . '-layout-' . acf_slugify( $l_name );
	$style  = acf_maybe_get( $layout, 'acfe_flexible_render_style' );
	$script = acf_maybe_get( $layout, 'acfe_flexible_render_script' );

	do_action( "acfe/flexible/enqueue/layout={$l_name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/enqueue/name={$name}&layout={$l_name}", $field, $layout, $is_preview );
	do_action( "acfe/flexible/enqueue/key={$key}&layout={$l_name}", $field, $layout, $is_preview );

	$style = apply_filters( "acfe/flexible/render/style",                                        $style, $field, $layout, $is_preview);
	$style = apply_filters( "acfe/flexible/render/style/name={$name}", $style, $field, $layout, $is_preview );
	$style = apply_filters( "acfe/flexible/render/style/key={$key}", $style, $field, $layout, $is_preview );
	$style = apply_filters( "acfe/flexible/render/style/layout={$l_name}", $style, $field, $layout, $is_preview );
	$style = apply_filters( "acfe/flexible/render/style/name={$name}&layout={$l_name}", $style, $field, $layout, $is_preview );
	$style = apply_filters( "acfe/flexible/render/style/key={$key}&layout={$l_name}", $style, $field, $layout, $is_preview );

	if ( ! empty( $style ) ) {

		// URL starting with current domain.
		if ( 0 === stripos( $style, home_url() ) ) {
			$style = str_replace( home_url(), '', $style );
		}

		$style_file = acfe_locate_file_url( $style );
		if ( ! empty( $style_file ) ) {
			wp_enqueue_style( $handle, $style_file, [], false, 'all' );
		}

		if ( $is_preview && stripos( $style, 'http://' ) !== 0 && stripos( $style, 'https://' ) !== 0 && stripos( $style, '//' ) !== 0 ) {

			$path = pathinfo( $style );
			$extension = $path['extension'];

			$style_preview  = substr( $style, 0, -strlen( $extension ) -1 );
			$style_preview .= '-preview.' . $extension;
			$style_preview  = acfe_locate_file_url( $style_preview );

			if ( ! empty( $style_preview ) ) {
				wp_enqueue_style( $handle . '-preview', $style_preview, [], false, 'all' );
			}
		}
	}

	$script = apply_filters( "acfe/flexible/render/script", $script, $field, $layout, $is_preview );
	$script = apply_filters( "acfe/flexible/render/script/name={$name}", $script, $field, $layout, $is_preview );
	$script = apply_filters( "acfe/flexible/render/script/key={$key}", $script, $field, $layout, $is_preview );
	$script = apply_filters( "acfe/flexible/render/script/layout={$l_name}", $script, $field, $layout, $is_preview );
	$script = apply_filters( "acfe/flexible/render/script/name={$name}&layout={$l_name}", $script, $field, $layout, $is_preview );
	$script = apply_filters( "acfe/flexible/render/script/key={$key}&layout={$l_name}", $script, $field, $layout, $is_preview );

	if ( ! empty( $script ) ) {

		// URL starting with current domain.
		if ( 0 === stripos( $script, home_url() ) ) {
			$script = str_replace( home_url(), '', $script );
		}

		$script_file = acfe_locate_file_url( $script );

		if ( ! $is_preview || ( stripos( $script, 'http://' ) === 0 || stripos( $script, 'https://' ) === 0 || stripos( $script, '//' ) === 0 ) ) {
			if ( ! empty( $script_file ) ) {
				wp_enqueue_script( $handle, $script_file, [], false, true );
			}
		} else {

			$path = pathinfo( $script );
			$extension = $path['extension'];

			$script_preview  = substr( $script, 0, -strlen( $extension ) -1 );
			$script_preview .= '-preview.' . $extension;
			$script_preview  = acfe_locate_file_url( $script_preview );

			if ( ! empty( $script_preview ) ) {
				wp_enqueue_script( $handle . '-preview', $script_preview, [], false, true );

			} elseif ( ! empty( $script_file ) ) {
				wp_enqueue_script( $handle, $script_file, [], false, true );
			}
		}
	}
}

/**
 * Has flexible grid
 *
 * @since  1.0.0
 * @param  string $name
 * @param  mixed $post_id
 * @return boolean
 */
if ( ! function_exists( 'has_flexible_grid' ) ) {
	function has_flexible_grid( $name, $post_id = false ) {

		$field = acf_maybe_get_field( $name, $post_id );
		if ( ! $field ) {
			return false;
		}

		$flexible_grid = acf_maybe_get( $field, 'acfe_flexible_grid' );
		$flexible_grid_enabled = acf_maybe_get( $flexible_grid, 'acfe_flexible_grid_enabled' );

		if ( ! $flexible_grid_enabled ) {
			return false;
		}
		return true;
	}
}

/**
 * Get flexible grid
 *
 * @since  1.0.0
 * @param  string $name
 * @param  mixed $post_id
 * @return mixed
 */
if ( ! function_exists( 'get_flexible_grid' ) ) {
	function get_flexible_grid( $name, $post_id = false ) {

		if ( ! has_flexible_grid( $name, $post_id ) ) {
			return false;
		}

		$field = acf_maybe_get_field( $name, $post_id );
		$flexible_grid = acf_maybe_get( $field, 'acfe_flexible_grid' );
		$flexible_grid_enabled = acf_maybe_get( $flexible_grid, 'acfe_flexible_grid_enabled' );

		if ( ! $flexible_grid_enabled ) {
			return false;
		}
		return [
			'align'     => $flexible_grid['acfe_flexible_grid_align'],
			'valign'    => $flexible_grid['acfe_flexible_grid_valign'],
			'wrap'      => $flexible_grid['acfe_flexible_grid_wrap'],
			'container' => $field['acfe_flexible_grid_container']
		];
	}
}

/**
 * Get flexible grid class
 *
 * @since  1.0.0
 * @param  string $name
 * @param  mixed $post_id
 * @return mixed
 */
if ( ! function_exists( 'get_flexible_grid_class' ) ) {
	function get_flexible_grid_class( $name, $post_id = false ) {

		$grid = get_flexible_grid( $name, $post_id );

		if ( ! $grid ) {
			return false;
		}

		$class  = "align-{$grid['align']} valign-{$grid['valign']}";
		if ( $grid['wrap'] ) {
			$class .= ' wrap';
		}
		return $class;
	}
}

/**
 * Get layout column
 *
 * @since  1.0.0
 * @return string
 */
if ( ! function_exists( 'get_layout_col' ) ) {
	function get_layout_col() {
		return get_sub_field( 'acfe_layout_col' );
	}
}

/**
 * Have settings
 *
 * While loop function for the flexible content settings modal.
 *
 * @since  1.0.0
 * @return boolean
 */
if ( ! function_exists( 'have_settings' ) ) {
	function have_settings() {
		return have_rows( 'layout_settings' );
	}
}

/**
 * The setting
 *
 * Setup data for the flexible content settings modal loop.
 *
 * @since  1.0.0
 * @return mixed
 */
if ( ! function_exists( 'the_setting' ) ) {
	function the_setting() {
		return the_row();
	}
}

/**
 * Have archive
 *
 * While loop function for the post type archive page.
 *
 * @since  1.0.0
 * @param  boolean $_post_type
 * @global integer $acfe_archive_i
 * @global string $acfe_archive_post_type
 * @return boolean
 */
if ( ! function_exists( 'have_archive' ) ) {

	function have_archive( $_post_type = false ) {

		// Access global variables.
		global $acfe_archive_i, $acfe_archive_post_type;

		$acfe_archive_post_type = false;
		if ( ! isset( $acfe_archive_i ) || 0 === $acfe_archive_i ) {

			$acfe_archive_i = 0;
			$post_type = get_post_type();

			if ( ! empty( $_post_type ) ) {
				$post_type = $_post_type;
			}

			if ( ! post_type_exists( $post_type ) ) {
				return false;
			}

			$post_type_object = get_post_type_object( $post_type );
			if ( empty( $post_type_object ) ) {
				return false;
			}

			if ( ! isset( $post_type_object->acfe_admin_archive ) || empty( $post_type_object->acfe_admin_archive ) ) {
				return false;
			}
			$acfe_archive_post_type = $post_type;

			return true;

		}

		remove_filter( 'acf/pre_load_post_id', 'acf_the_archive_post_id' );
		return false;
	}
}

/**
 * The archive
 *
 * Set up data for the post type archive page.
 *
 * @since  1.0.0
 * @global integer $acfe_archive_i
 * @return mixed
 */
if ( ! function_exists( 'the_archive' ) ) {
	function the_archive() {

		// Access global variables.
		global $acfe_archive_i;

		add_filter( 'acf/pre_load_post_id', 'acf_the_archive_post_id', 10, 2 );
		$acfe_archive_i++;
	}
}

/**
 * The archive post ID
 *
 * @since  1.0.0
 * @param  $null
 * @param  $post_id
 * @global string $acfe_archive_post_type
 * @return mixed
 */
function acf_the_archive_post_id( $null, $post_id ) {

	// Access global variables.
	global $acfe_archive_post_type;

	if ( false !== $post_id ) {
		return $null;
	}

	if ( empty( $acfe_archive_post_type ) ) {
		return $null;
	}

	$return = acf_get_valid_post_id( $acfe_archive_post_type . '_archive' );
	return $return;
}

/**
 * Get post id
 *
 * Universal way to always retrieve the correct ACF post ID
 * on the front end and the back end.
 *
 * Returns ACF formatted post ID.
 * @example 12|term_24|user_56|my-options
 *
 * @param  boolean $format
 * @global string $pagenow
 * @global integer $user_ID
 * @return mixed
 */
function acf_get_post_id( $format = true ) {

	// Access global variables.
	global $pagenow, $user_ID;

	if ( acf_is_admin() ) {

		// Legacy method.
		$post_id = acf_get_valid_post_id();

		// Exclude local meta post IDs.
		if ( function_exists( 'acfe_get_local_post_ids' ) ) {

			$exclude_post_ids = acfe_get_local_post_ids();
			if ( in_array( $post_id, $exclude_post_ids ) ) {
				$post_id = false;
			}
		}

		if ( $post_id ) {
			return $post_id;
		}
		$post_id = acf_get_form_data( 'post_id' );

		if ( ! $post_id ) {
			$post_id = acf_maybe_get_POST( '_acf_post_id' );
		}
		if ( ! $post_id ) {
			$post_id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : 0;
		}
		if ( ! $post_id ) {
			$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
		}
		if ( ! $post_id ) {
			$post_id = isset( $_REQUEST['user_id'] ) ? 'user_' . absint( $_REQUEST['user_id'] ) : 0;
		}
		if ( ! $post_id ) {
			$post_id = $pagenow === 'profile.php' && $user_ID !== null ? 'user_' . absint( $user_ID ) : 0;
		}
		if ( ! $post_id ) {
			$post_id = isset( $_REQUEST['tag_ID'] ) ? 'term_' . absint( $_REQUEST['tag_ID'] ) : 0;
		}
		if ( ! $post_id ) {
			$post_id = 0;
		}
	} else {

		$object  = get_queried_object();
		$post_id = 0;

		if ( is_object( $object ) ) {

			if ( isset( $object->post_type, $object->ID ) ) {
				$post_id = $object->ID;
			} elseif ( isset( $object->hierarchical, $object->name, $object->acfe_admin_archive ) ) {
				$post_id = $object->name . '_archive';
			} elseif ( isset( $object->roles, $object->ID ) ) {
				$post_id = 'user_' . $object->ID;
			} elseif ( isset( $object->taxonomy, $object->term_id ) ) {
				$post_id = 'term_' . $object->term_id;
			} elseif ( isset( $object->comment_ID ) ) {
				$post_id = 'comment_' . $object->comment_ID;
			}
		}
	}

	// Validate with filters.
	$post_id = acf_get_valid_post_id( $post_id );

	if ( ! $format ) {

		$info    = acf_decode_post_id( $post_id );
		$post_id = $info['id'];
	}
	return $post_id;
}

// Register Local Templates store.
acf_register_store( 'local-templates' );

/**
 * Get local templates
 *
 * @since  1.0.0
 * @return method
 */
function acf_get_local_templates() {
	return acf_get_local_store( 'templates' )->get();
}

/**
 * Get local template
 *
 * @since  1.0.0
 * @param  string $name
 * @return method
 */
function acf_get_local_template( $name = '' ) {
	return acf_get_local_store( 'templates' )->get( $name );
}

/**
 * Remove local template
 *
 * @since  1.0.0
 * @param  string $name
 * @return method
 */
function acf_remove_local_template( $name = '' ) {
	return acf_get_local_store( 'templates' )->remove( $name );
}

/**
 * Have local templates
 *
 * @since  1.0.0
 * @return boolean
 */
function acf_have_local_templates() {
	if ( acf_get_local_store( 'templates' )->count() ) {
		return true;
	}
	return false;
}

/**
 * Is local template
 *
 * @since  1.0.0
 * @param  string $name
 * @return method
 */
function acf_is_local_template( $name = '' ) {
	return acf_get_local_store( 'templates' )->has( $name );
}

/**
 * Count local template
 *
 * @since  1.0.0
 * @return method
 */
function acf_count_local_template() {
	return acf_get_local_store( 'templates' )->count();
}

/**
 * Add local template
 *
 * @since  1.0.0
 * @param  array $args
 * @return boolean
 */
function acf_add_local_template( $args = [] ) {

	$args = wp_parse_args( $args, [
		'title'    => '',
		'name'     => '',
		'active'   => true,
		'values'   => [],
		'location' => []
	] );

	acf_get_local_store( 'templates' )->set( $args['name'], $args );
	return true;
}
