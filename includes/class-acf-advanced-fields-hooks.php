<?php

if(!defined('ABSPATH'))
	exit;

class ACF_Advanced_Fields_Hooks {

	function __construct() {
		add_filter('acf/load_field',    array($this, 'load_field'), 15);
		add_action('acf/render_field',  array($this, 'replace_render_field'), 9);
		add_action('acf/render_field',  array($this, 'render_field'), 15);
	}

	function load_field($field){

		if(!$this->validate_hook($field, 'load_field'))
			return $field;

		$field = call_user_func_array($field['callback']['load_field'], array($field));

		return $field;
	}

	function replace_render_field($field){

		if(!$this->validate_hook($field, 'replace_render_field'))
			return;

		call_user_func_array($field['callback']['replace_render_field'], array($field));

		$field_class = acf_get_field_type($field['type']);
		$field_key = $field['key'];

		if(method_exists($field_class, 'render_field')){

			add_action("acf/render_field/type={$field['type']}", function($field) use($field_class, $field_key){

				if(!has_action("acf/render_field/type={$field['type']}", array($field_class, 'render_field'))){

					add_action("acf/render_field/type={$field['type']}", array($field_class, 'render_field'), 9);

				}

				if($field['key'] !== $field_key)
					return;

				remove_action("acf/render_field/type={$field['type']}", array($field_class, 'render_field'), 9);
			}, 8);
		}
	}

	function render_field($field){

		if(!$this->validate_hook($field, 'render_field'))
			return;

		call_user_func_array($field['callback']['render_field'], array($field));
	}

	function validate_hook($field, $hook_name){

		if(!isset($field['callback'][$hook_name]) || !is_callable($field['callback'][$hook_name]))
			return false;

		return true;
	}
}
acf_new_instance( 'ACF_Advanced_Fields_Hook' );
