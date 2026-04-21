<?php

if(!defined('ABSPATH'))
	exit;

if(!class_exists('acfe_instructions')):

class acfe_instructions{

	function __construct(){

		add_action('acf/field_group/admin_head', array($this, 'admin_head'));
		add_action('acfe/pre_render_field_group', array($this, 'pre_render_field_group'), 10, 3);
		add_filter('acf/field_wrapper_attributes', array($this, 'field_wrapper_attributes'), 10, 2);

	}

	/*
	 * Admin Head
	 */
	function admin_head(){

		global $field_group;

		if(acf_maybe_get($field_group, 'acfe_form')){
			add_action('acf/render_field_settings', array($this, 'render_field_instructions_settings'), 11);
		}

	}

	/*
	 * Render Field Instructions Settings
	 */
	function render_field_instructions_settings($field){

		// Hide Field
		acf_render_field_setting($field, array(
			'label'         => __('Instructions Placement', 'acfe'),
			'instructions'  => '',
			'name'          => 'instruction_placement',
			'type'          => 'select',
			'placeholder'   => 'Default',
			'allow_null'    => true,
			'choices'       => array(
				'label'         => 'Below labels',
				'field'         => 'Below fields',
				'above_field'   => 'Above fields',
				'tooltip'       => 'Tooltip',
			),
			'wrapper' => array(
				'data-after' => 'instructions',
			)
		), true);

	}

	function pre_render_field_group($field_group, $fields, $post_id){

		acf_disable_filter('acfe/instruction_tooltip');
		acf_disable_filter('acfe/instruction_above_field');

		if(acf_maybe_get($field_group, 'instruction_placement') === 'tooltip'){

			acf_enable_filter('acfe/instruction_tooltip');

		}elseif(acf_maybe_get($field_group, 'instruction_placement') === 'above_field'){

			acf_enable_filter('acfe/instruction_above_field');

		}

	}

	function field_wrapper_attributes($wrapper, $field){

		if(!acf_maybe_get($field, 'label')){
			$wrapper['class'] .= ' acfe-no-label';
		}

		if(acf_maybe_get($field, 'instructions')){

			if(acf_is_filter_enabled('acfe/instruction_tooltip')){

				$wrapper['data-instruction-tooltip'] = acf_esc_html($field['instructions']);

			}elseif(acf_is_filter_enabled('acfe/instruction_above_field')){

				$wrapper['data-instruction-above-field'] = acf_esc_html($field['instructions']);

			}

			if(acf_maybe_get($field, 'instruction_placement')){
				$wrapper['data-instruction-placement'] = acf_maybe_get($field, 'instruction_placement');
			}

			if(strpos($field['instructions'], '---') !== false){
				$wrapper['data-instruction-more'] = true;
			}

		}

		return $wrapper;

	}

}

new acfe_instructions();

endif;