<?php

if(!defined('ABSPATH'))
	exit;

/**
 * acfe_is_post_type_reserved
 *
 * Check if the post type is reserved
 *
 * @param $post_type
 *
 * @return bool
 */
function acfe_is_post_type_reserved($post_type){

	// restricted post types
	$reserved = acf_get_setting('reserved_post_types', array());

	return in_array($post_type, $reserved);

}

/**
 * acfe_is_post_type_reserved_dev
 *
 * Check if the post type is reserved in dev mode
 *
 * @param $post_type
 *
 * @return bool
 */
function acfe_is_post_type_reserved_dev($post_type){

	// restricted post types
	$reserved = acf_get_setting('reserved_post_types', array());

	return !acf_is_super_dev() && in_array($post_type, $reserved);

}

/**
 * acfe_is_taxonomy_reserved
 *
 * Check if the taxonomy is reserved
 *
 * @param $taxonomy
 *
 * @return bool
 */
function acfe_is_taxonomy_reserved($taxonomy){

	// restricted post types
	$reserved = acf_get_setting('reserved_taxonomies', array());

	return in_array($taxonomy, $reserved);

}

/**
 * acfe_is_taxonomy_reserved_dev
 *
 * Check if the taxonomy is reserved in dev mode
 *
 * @param $taxonomy
 *
 * @return bool
 */
function acfe_is_taxonomy_reserved_dev($taxonomy){

	// restricted post types
	$reserved = acf_get_setting('reserved_taxonomies', array());

	return !acf_is_super_dev() && in_array($taxonomy, $reserved);

}

/**
 * acfe_update_setting
 *
 * Similar to acf_update_setting() but with the 'acfe' prefix
 *
 * @param $name
 * @param $value
 *
 * @return bool|true
 */
function acfe_update_setting($name, $value){

	return acf_update_setting("acfe/{$name}", $value);

}

/**
 * acfe_append_setting
 *
 * Similar to acf_append_setting() but with the 'acfe' prefix
 *
 * @param $name
 * @param $value
 *
 * @return bool|true
 */
function acfe_append_setting($name, $value){

	return acf_append_setting("acfe/{$name}", $value);

}

/**
 * acfe_get_setting
 *
 * Similar to acf_get_setting() but with the 'acfe' prefix
 *
 * @param      $name
 * @param null $value
 *
 * @return mixed|void
 */
function acfe_get_setting($name, $value = null){

	return acf_get_setting("acfe/{$name}", $value);

}

/**
 * acfe_unarray
 *
 * Retrieve and return only the first value of an array
 *
 * @param $val
 *
 * @return false|mixed
 */
function acfe_unarray( $val ) {
	if ( is_array( $val ) ) {
		return reset( $val );
	}
	return $val;
}

/**
 * acfe_get_ip
 * @return mixed
 */
function acfe_get_ip(){

	$ip = false;

	// http client
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){

		$ip = filter_var(wp_unslash($_SERVER['HTTP_CLIENT_IP']), FILTER_VALIDATE_IP);

	// proxy pass
	}elseif(!empty( $_SERVER['HTTP_X_FORWARDED_FOR'])){

		// can include more than 1 ip, first is the public one.
		$ips = explode(',', wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));

		if (is_array($ips)){
			$ip = filter_var( $ips[0], FILTER_VALIDATE_IP );
		}

	// remote addr
	}elseif(!empty($_SERVER['REMOTE_ADDR'])){

		$ip = filter_var(wp_unslash($_SERVER['REMOTE_ADDR']), FILTER_VALIDATE_IP);

	}

	// default
	$ip = $ip !== false ? $ip : '127.0.0.1';

	// fix potential csv return
	$ip_array = explode(',', $ip);
	$ip_array = array_map('trim', $ip_array);

	// return
	return $ip_array[0];

}

/*
 * Has Flexible Grid
 */
if(!function_exists('has_flexible_grid')){

function has_flexible_grid($name, $post_id = false){

	// get field
	$field = acf_maybe_get_field($name, $post_id);

	// bail early
	if(!$field)
		return false;

	// vars
	$flexible_grid = acf_maybe_get($field, 'acfe_flexible_grid');
	$flexible_grid_enabled = acf_maybe_get($flexible_grid, 'acfe_flexible_grid_enabled');

	// not enabled
	if(!$flexible_grid_enabled)
		return false;

	// return
	return true;

}

}

/*
 * Get Flexible Grid
 */
if(!function_exists('get_flexible_grid')){

function get_flexible_grid($name, $post_id = false){

	// bail early
	if(!has_flexible_grid($name, $post_id))
		return false;

	// vars
	$field = acf_maybe_get_field($name, $post_id);
	$flexible_grid = acf_maybe_get($field, 'acfe_flexible_grid');
	$flexible_grid_enabled = acf_maybe_get($flexible_grid, 'acfe_flexible_grid_enabled');

	// not enabled
	if(!$flexible_grid_enabled)
		return false;

	// return data
	return array(
		'align'     => $flexible_grid['acfe_flexible_grid_align'],
		'valign'    => $flexible_grid['acfe_flexible_grid_valign'],
		'wrap'      => $flexible_grid['acfe_flexible_grid_wrap'],
		'container' => $field['acfe_flexible_grid_container'],
	);

}

}

/*
 * Get Flexible Grid Class
 */
if(!function_exists('get_flexible_grid_class')){

function get_flexible_grid_class($name, $post_id = false){

	// get field
	$grid = get_flexible_grid($name, $post_id);

	// bail early
	if(!$grid)
		return false;

	// vars
	$class = "align-{$grid['align']} valign-{$grid['valign']}";
	$class .= $grid['wrap'] ? " wrap" : "";

	//return
	return $class;

}

}

/*
 * Get Layout Col
 */
if(!function_exists('get_layout_col')){

function get_layout_col(){
	return get_sub_field('acfe_layout_col');
}

}
