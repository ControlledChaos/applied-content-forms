<?php
/**
 * ACF third-party compatibility
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

class acf_third_party {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		/**
		 * Tabify Edit Screen
		 *
		 * @link http://wordpress.org/extend/plugins/tabify-edit-screen/
		 */
		if ( class_exists( 'Tabify_Edit_Screen' ) ) {
			add_filter( 'tabify_posttypes', [ $this, 'tabify_posttypes' ] );
			add_action( 'tabify_add_meta_boxes', [ $this, 'tabify_add_meta_boxes' ] );
		}

		/**
		 * Post Type Switcher
		 *
		 * @link http://wordpress.org/extend/plugins/post-type-switcher/
		 */
		if ( class_exists( 'Post_Type_Switcher' ) ) {
			add_filter( 'pts_allowed_pages', [ $this, 'pts_allowed_pages' ] );
		}

		/**
		 * Event Espresso
		 *
		 * @link https://wordpress.org/plugins/event-espresso-decaf/
		 */
		if ( function_exists( 'espresso_version' ) ) {
			add_filter( 'acf/get_post_types', [ $this, 'ee_get_post_types' ], 10, 2 );
		}

		// Dark Mode
		if ( class_exists( 'Dark_Mode' ) ) {
			add_action( 'doing_dark_mode', [ $this, 'doing_dark_mode' ] );
		}
	}

	/**
	 * Event Espresso get post types
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $post_types
	 * @param  array $args
	 * @return array
	 */
	public function ee_get_post_types( $post_types, $args ) {

		if ( ! empty( $args['show_ui'] ) ) {
			$ee_post_types = get_post_types( [ 'show_ee_ui' => 1 ] );
			$ee_post_types = array_keys( $ee_post_types );
			$post_types    = array_merge( $post_types, $ee_post_types );
			$post_types    = array_unique( $post_types );
		}
		return $post_types;
	}

	/**
	 * Tabify post types
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $posttypes
	 * @return integer
	 */
	public function tabify_posttypes( $posttypes ) {

		unset( $posttypes['acf-field-group'] );
		unset( $posttypes['acf-field'] );

		return $posttypes;
	}

	/**
	 * Tabify add meta boxes
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $post_type
	 * @return void
	 */
	public function tabify_add_meta_boxes( $post_type ) {

		// Get field groups.
		$field_groups = acf_get_field_groups();

		if ( ! empty( $field_groups ) ) {
			foreach ( $field_groups as $field_group ) {

				$id    = "acf-{$field_group['key']}";
				$title = 'ACF: ' . $field_group['title'];

				add_meta_box( $id, acf_esc_html( $title ), '__return_true', $post_type );
			}
		}
	}

	/**
	 * Post Type Switcher allowed pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $pages
	 * @return array
	 */
	public function pts_allowed_pages( $pages ) {

		$post_type = '';

		// Check $_GET because it is too early to use functions/global vars.
		if ( ! empty( $_GET['post_type'] ) ) {
			$post_type = $_GET['post_type'];
		} elseif ( ! empty( $_GET['post'] ) ) {
			$post_type = get_post_type( $_GET['post'] );
		}

		// Check post type.
		if ( 'acf-field-group' == $post_type ) {
			$pages = [];
		}
		return $pages;
	}

	/**
	 * Doing Dark Mode
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function doing_dark_mode() {
		wp_enqueue_style( 'acf-dark', acf_get_url( 'assets/css/acf-dark.css' ), [], ACF_VERSION );
	}
}
acf_new_instance( 'acf_third_party' );
