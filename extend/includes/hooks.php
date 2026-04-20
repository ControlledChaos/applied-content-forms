<?php
/**
 * Hooks
 *
 * @package    Applied Content Forms
 * @subpackage Extend
 * @category   Functions
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class acfe_hooks{

	/**
	 * Field group
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $field_group;

	/**
	 * Upload field
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $upload_field = false;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct(){

		// Prepare.
		add_action( 'acf/validate_save_post', [ $this, 'set_form_data' ], 0 );
		add_action( 'acf/save_post', [ $this, 'set_form_data' ], 0 );

		// Hooks.
		add_action( 'acf/save_post', [ $this, 'pre_save_post' ], 9 );
		add_action( 'acf/save_post', [ $this, 'save_post' ], 15 );
		add_action( 'acf/validate_save_post', [ $this, 'validate_save_post' ] );

		// Field Groups.
		add_filter( 'acf/load_field_groups', [ $this, 'load_field_groups' ], 100 );
		add_filter( 'acf/pre_render_fields', [ $this, 'pre_render_fields' ], 10, 2 );
		add_action( 'acf/render_fields', [ $this, 'render_fields' ], 10, 2 );

		// Fields.
		add_filter( 'acf/field_wrapper_attributes', [ $this, 'field_wrapper_attributes' ], 10, 2 );
		add_filter( 'acf/load_fields', [ $this, 'load_fields' ], 10, 2 );
		add_filter( 'acf/load_field', [ $this, 'load_field' ], 15  );
		add_filter( 'acf/prepare_field', [ $this, 'prepare_field' ], 15 );
		add_action( 'acf/render_field',  [ $this, 'pre_render_field' ], 8 );

		// Form Data.
		add_filter( 'acf/location/screen', [ $this, 'location_screen' ], 99 );
		add_action( 'acf/input/form_data', [ $this, 'form_data' ] );

		// Upload.
		add_filter( 'acf/upload_prefilter', [ $this, 'attachment_upload' ], 10, 3 );
	}

	/**
	 * Validate hook
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @param  string $hook_name
	 * @return void
	 */
	public function validate_hook( $field, $hook_name ) {

		if ( ! isset( $field['callback'][$hook_name] ) || ! is_callable( $field['callback'][$hook_name] ) ) {
			return false;
		}
		return true;
	}

	public function prepare_field( $field ) {

		if ( ! $this->validate_hook( $field, 'prepare_field' ) ) {
			return $field;
		}
		$field = call_user_func_array( $field['callback']['prepare_field'], [ $field ] );

		return $field;

	}

	public function pre_render_field($field){

		if(!$this->validate_hook($field, 'pre_render_field'))
			return;

		call_user_func_array($field['callback']['pre_render_field'], array($field));

	}

	public function set_form_data(){

		$screen   = acf_maybe_get_POST( '_acf_screen', 'post' );
		$post_id  = acf_maybe_get_POST( '_acf_post_id', 0 );
		$location = acf_maybe_get_POST( '_acf_location', [] );

		// set form data
		acf_set_form_data( [
			'screen'   => $screen,
			'post_id'  => $post_id,
			'location' => $location,
		] );
	}

	public function pre_save_post( $post_id = 0 ) {
		$this->do_save_post( $post_id, true );
	}

	public function save_post( $post_id = 0 ) {
		$this->do_save_post( $post_id );
	}

	public function do_save_post( $post_id = 0, $pre = false ) {

		// Validate ACF.
		if ( ! acf_maybe_get_POST( 'acf' ) ) {
			return;
		}

		// Check data.
		$data = $this->decode_object( $post_id );
		if ( ! $data ) {
			return;
		}

		$id     = $data['id'];
		$type   = $data['type'];
		$object = $data['object'];
		$hooks  = $data['hooks'];
		if ( $pre ) {
			$suffix = 'pre_';
		} else {
			$suffix = false;
		}

		// All hooks.
		$all_hooks   = [];
		$all_hooks[] = "acfe/{$suffix}save";
		$all_hooks[] = "acfe/{$suffix}save/id={$post_id}";
		$all_hooks[] = "acfe/{$suffix}save_{$type}";

		foreach ( $hooks as $hook ) {
			$all_hooks[] = "acfe/{$suffix}save_{$type}/{$hook}";
		}
		$all_hooks[] = "acfe/{$suffix}save_{$type}/id={$post_id}";

		// Check if hooked.
		$do_action = false;

		foreach ( $all_hooks as $all_hook ) {

			if ( ! has_action( $all_hook ) ) {
				continue;
			}

			$do_action = true;
			break;
		}

		if ( ! $do_action ) {
			return;
		}

		// Setup meta.
		acfe_setup_meta( $_POST['acf'], 'acfe/save', true );

		foreach ( $all_hooks as $all_hook ) {
			do_action( $all_hook, $post_id, $object );
		}
		acfe_reset_meta();
	}

	public function validate_save_post(){

		$rows = [];
		$acf  = acf_maybe_get_POST( 'acf' );

		if ( ! empty( $acf ) ) {
			$post_id = acf_maybe_get_POST( '_acf_post_id' );
			if ( $post_id ) {
				$rows[$post_id] = $acf;
			}
		}

		// Menu Items
		$menu_items = acf_maybe_get_POST( 'menu-item-acf' );

		if ( ! empty( $menu_items ) ) {
			foreach ( $menu_items as $post_id => $fields ) {
				$rows[$post_id] = $fields;
			}
		}

		foreach ( $rows as $post_id => $acf ) {

			// Check data.
			$data = $this->decode_object( $post_id );
			if ( ! $data ) {
				continue;
			}

			$id          = $data['id'];
			$type        = $data['type'];
			$object      = $data['object'];
			$hooks       = $data['hooks'];
			$all_hooks   = [];
			$all_hooks[] = "acfe/validate_save";
			$all_hooks[] = "acfe/validate_save/id={$post_id}";
			$all_hooks[] = "acfe/validate_save_{$type}";

			foreach ( $hooks as $hook ) {
				$all_hooks[] = "acfe/validate_save_{$type}/{$hook}";
			}
			$all_hooks[] = "acfe/validate_save_{$type}/id={$post_id}";

			// Check if hooked.
			$do_action = false;

			foreach ( $all_hooks as $all_hook ) {

				if ( ! has_action( $all_hook ) ) {
					continue;
				}

				$do_action = true;
				break;
			}

			if ( ! $do_action ) {
				continue;
			}

			// Setup Meta
			acfe_setup_meta( $acf, 'acfe/validate_save', true );

			foreach ( $all_hooks as $all_hook ) {
				do_action( $all_hook, $post_id, $object );
			}
			acfe_reset_meta();
		}
	}

	public function decode_object( $post_id ) {

		$id   = false;
		$type = false;
		$data = [
			'id'     => false,
			'type'   => false,
			'object' => false,
			'hooks'  => [],
		];

		/*
		 * @string  $post_id  12   | term_46 | user_22 | my-option | comment_89 | widget_56 | menu_74 | menu_item_96 | block_my-block | blog_55 | site_36 | attachment_24
		 * @string  $id       12   | 46      | 22      | my-option | 89         | widget_56 | 74      | 96           | block_my-block | 55      | 36      | 24
		 * @string  $type     post | term    | user    | option    | comment    | option    | term    | post         | block          | blog    | blog    | post
		 */
		extract(acf_decode_post_id( $post_id));

		// Validate ID
		if(!$id)
			return false;

		$data['id'] = $id;
		$data['type'] = $type;

		// Post
		if( $type === 'post' ){

			$post = get_post( $id);

			if( $post && !is_wp_error( $post)){

				$data['object'] = $post;

				if(isset( $post->post_type) && post_type_exists( $post->post_type)){
					$data['hooks'][] = "post_type={$post->post_type}";
				}

			}

		// Term
		}elseif( $type === 'term' ){

			$term = get_term( $id);

			if( $term && !is_wp_error( $term)){

				$data['object'] = $term;

				if(isset( $term->taxonomy) && taxonomy_exists( $term->taxonomy)){
					$data['hooks'][] = "taxonomy={$term->taxonomy}";
				}

			}

		// User
		}elseif( $type === 'user' ){

			$user = get_user_by( 'id', $id);

			if( $user && !is_wp_error( $user)){

				$data['object'] = $user;

				if(isset( $user->roles) && !empty( $user->roles)){

					foreach( $user->roles as $role){
						$data['hooks'][] = "role={$role}";
					}

				}

			}

		// Option
		}elseif( $type === 'option' ){

			$location = acf_get_form_data( 'location' );
			$options_page = acf_maybe_get( $location, 'options_page' );

			if( $options_page){

				$data['object'] = acf_get_options_page( $options_page);

				$data['hooks'][] = "slug={$options_page}";

			}

		// Comment
		}elseif( $type === 'comment' ){

			$comment = get_comment( $id);

			if( $comment && !is_wp_error( $comment)){

				$data['object'] = $comment;

			}

		// Block
		}elseif( $type === 'block' ){

			$block = acf_get_block_type("acf/$id");

			if( $block){

				$data['object'] = $block;

			}

		// Blog
		}elseif( $type === 'blog' ){

			if(function_exists( 'get_blog_details' )){

				$blog = get_blog_details( $id);

				if( $blog){

					$data['object'] = $blog;

				}

			}

		}

		return $data;

	}

	/*
	 * Load Field Groups
	 */
	public function load_field_groups( $field_groups){

		// Do not execute in ACF Field Group UI
		if(acfe_is_admin_screen()){
			return $field_groups;
		}

		foreach( $field_groups as $i => &$field_group){

			$field_group = apply_filters("acfe/prepare_field_group", $field_group);

			if(isset( $field_group['ID'])){
				$field_group = apply_filters("acfe/prepare_field_group/ID={$field_group['ID']}", $field_group);
			}

			if(isset( $field_group['key'])){
				$field_group = apply_filters("acfe/prepare_field_group/key={$field_group['key']}", $field_group);
			}

			// Do not render if false
			if( $field_group === false){
				unset( $field_groups[$i]);
			}

		}

		return $field_groups;

	}

	/*
	 * Pre Render Fields
	 */
	public function pre_render_fields( $fields, $post_id){

		$this->field_group = array();

		if(!isset( $fields[0])) return $fields;

		if(!acf_maybe_get( $fields[0], 'parent' )) return $fields;

		$field_group = acf_get_field_group( $fields[0]['parent']);

		if(!$field_group) return $fields;

		$this->field_group = $field_group;

		do_action("acfe/pre_render_field_group",                            $field_group, $fields, $post_id);
		do_action("acfe/pre_render_field_group/ID={$field_group['ID']}",    $field_group, $fields, $post_id);
		do_action("acfe/pre_render_field_group/key={$field_group['key']}",  $field_group, $fields, $post_id);

		return $fields;

	}

	/*
	 * Pre Render Fields
	 */
	public function render_fields( $fields, $post_id){

		if(empty( $this->field_group)) return;

		$field_group = $this->field_group;

		do_action("acfe/render_field_group",                            $field_group, $fields, $post_id);
		do_action("acfe/render_field_group/ID={$field_group['ID']}",    $field_group, $fields, $post_id);
		do_action("acfe/render_field_group/key={$field_group['key']}",  $field_group, $fields, $post_id);

	}

	/*
	 *  Field Wrapper Attributes
	 */
	public function field_wrapper_attributes( $wrapper, $field){

		$wrapper = apply_filters("acfe/field_wrapper_attributes",                       $wrapper, $field);
		$wrapper = apply_filters("acfe/field_wrapper_attributes/type={$field['type']}", $wrapper, $field);
		$wrapper = apply_filters("acfe/field_wrapper_attributes/name={$field['name']}", $wrapper, $field);
		$wrapper = apply_filters("acfe/field_wrapper_attributes/key={$field['key']}",   $wrapper, $field);

		return $wrapper;

	}

	/*
	 *  Load Fields
	 */
	public function load_fields( $fields, $parent){

		// check if field (fitler is also called on field groups)
		if(!acf_maybe_get( $parent, 'type' )) return $fields;

		$fields = apply_filters("acfe/load_fields",                         $fields, $parent);
		$fields = apply_filters("acfe/load_fields/type={$parent['type']}",  $fields, $parent);
		$fields = apply_filters("acfe/load_fields/name={$parent['name']}",  $fields, $parent);
		$fields = apply_filters("acfe/load_fields/key={$parent['key']}",    $fields, $parent);

		return $fields;

	}

	/*
	 *  Load Field
	 */
	public function load_field( $field){

		if(!$this->validate_hook($field, 'load_field'))
			return $field;

		// Do not execute in ACF Field Group UI
		if(acfe_is_admin_screen()) return $field;

		// Hooks
		$field = apply_filters("acfe/load_field",                       $field);
		$field = apply_filters("acfe/load_field/type={$field['type']}", $field);
		$field = apply_filters("acfe/load_field/name={$field['name']}", $field);
		$field = apply_filters("acfe/load_field/key={$field['key']}",   $field);


		// Deprecated: Admin
		if(acfe_is_admin()){

			$field = apply_filters_deprecated("acfe/load_field_admin",                          array( $field), '0.8.8', "acfe/load_field");
			$field = apply_filters_deprecated("acfe/load_field_admin/type={$field['type']}",    array( $field), '0.8.8', "acfe/load_field/type={$field['type']}");
			$field = apply_filters_deprecated("acfe/load_field_admin/name={$field['name']}",    array( $field), '0.8.8', "acfe/load_field/name={$field['name']}");
			$field = apply_filters_deprecated("acfe/load_field_admin/key={$field['key']}",      array( $field), '0.8.8', "acfe/load_field/key={$field['key']}");

		// Deprecated: Front
		}else{

			$field = apply_filters_deprecated("acfe/load_field_front",                          array( $field), '0.8.8', "acfe/load_field");
			$field = apply_filters_deprecated("acfe/load_field_front/type={$field['type']}",    array( $field), '0.8.8', "acfe/load_field/type={$field['type']}");
			$field = apply_filters_deprecated("acfe/load_field_front/name={$field['name']}",    array( $field), '0.8.8', "acfe/load_field/name={$field['name']}");
			$field = apply_filters_deprecated("acfe/load_field_front/key={$field['key']}",      array( $field), '0.8.8', "acfe/load_field/key={$field['key']}");

		}

		$field = call_user_func_array($field['callback']['load_field'], array($field));
		return $field;

	}

	public function location_screen( $screen){

		// add taxonomy term id
		if(acf_maybe_get( $screen, 'taxonomy' )){

			// check term_id already set
			if(!acf_maybe_get( $screen, 'term_id' )){

				global $tag;

				$screen['term_id'] = acfe_maybe_get( $tag, 'term_id' );

			}

		}

		// get location form data
		/*$location = acf_get_form_data( 'location' );

		// location already set
		if( $location === $screen){
			return $screen;
		}*/

		// clone var
		$location = $screen;
		unset( $location['lang'], $location['ajax']);

		// set form data
		acf_set_form_data( 'location', $location);

		return $screen;

	}

	public function form_data(){

		// get location form data
		$location = acf_get_form_data( 'location' );

		// generate hidden input
		if( $location){

			foreach( $location as $name => $value){

				acf_hidden_input(array(
					'id'	=> "_acf_location[$name]",
					'name'	=> "_acf_location[$name]",
					'value'	=> $value
				));

			}

		}

	}

	/*
	 * Attachment Upload
	 */
	public function attachment_upload( $errors, $file, $field){

		// vars
		$this->upload_field = $field;

		// filters
		add_filter( 'upload_dir',                    array( $this, 'handle_upload_dir' ), 20);
		add_filter( 'wp_handle_upload_prefilter',    array( $this, 'handle_upload_file' ), 20);

		// return
		return $errors;

	}

	public function handle_upload_dir( $uploads){

		// vars
		$field = $this->upload_field;

		// filters
		$uploads = apply_filters("acfe/upload_dir",                         $uploads, $field);
		$uploads = apply_filters("acfe/upload_dir/type={$field['type']}",   $uploads, $field);
		$uploads = apply_filters("acfe/upload_dir/name={$field['name']}",   $uploads, $field);
		$uploads = apply_filters("acfe/upload_dir/key={$field['key']}",     $uploads, $field);

		// return
		return $uploads;

	}

	public function handle_upload_file( $file){

		// vars
		$field = $this->upload_field;

		// filters
		$file = apply_filters("acfe/upload_file",                           $file, $field);
		$file = apply_filters("acfe/upload_file/type={$field['type']}",     $file, $field);
		$file = apply_filters("acfe/upload_file/name={$field['name']}",     $file, $field);
		$file = apply_filters("acfe/upload_file/key={$field['key']}",       $file, $field);

		// return
		return $file;
	}
}
new acfe_hooks();
