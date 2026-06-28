<?php

if(!defined('ABSPATH'))
	exit;

class ACF_Advanced_Values_Hooks {

	function __construct() {
		add_filter('acf/load_value',    array($this, 'load_value'), 15, 3);
		add_filter('acf/update_value',  array($this, 'update_value'), 15, 3);
		add_filter('acf/format_value',  array($this, 'format_value'), 15, 3);
		add_filter('acf/validate_value',array($this, 'validate_value'), 15, 4);
		add_action('acf/delete_value',  array($this, 'delete_value'), 15, 3);
	}

	function load_value($value, $post_id, $field){

		if(!$this->validate_hook($field, 'load_value'))
			return $value;

		$value = call_user_func_array($field['callback']['load_value'], array($value, $post_id, $field));

		return $value;
	}

	function update_value($value, $post_id, $field){

		if(!$this->validate_hook($field, 'update_value'))
			return $value;

		$value = call_user_func_array($field['callback']['update_value'], array($value, $post_id, $field));

		return $value;
	}

	function format_value($value, $post_id, $field){

		if(!$this->validate_hook($field, 'format_value'))
			return $value;

		$value = call_user_func_array($field['callback']['format_value'], array($value, $post_id, $field));

		return $value;
	}

	function validate_value($valid, $value, $field, $input){

		if(!$this->validate_hook($field, 'validate_value'))
			return $valid;

		$valid = call_user_func_array($field['callback']['validate_value'], array($valid, $value, $field, $input));

		return $valid;
	}

	function delete_value($post_id, $field_name, $field){

		if(!$this->validate_hook($field, 'delete_value'))
			return;

		call_user_func_array($field['callback']['delete_value'], array($post_id, $field_name, $field));
	}

	function validate_hook($field, $hook_name){

		if(!isset($field['callback'][$hook_name]) || !is_callable($field['callback'][$hook_name]))
			return false;

		return true;
	}
}
acf_new_instance( 'ACF_Advanced_Values_Hook' );
