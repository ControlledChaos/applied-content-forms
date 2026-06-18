<?php
/**
 * Extend field
 *
 * Parent class for extending an ACF field.
 *
 * @package    Applied Content Forms
 * @subpackage Extend
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Extend_Field {

	/**
	 * Field name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * Arguments to replace
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $replace = [];

	/**
	 * Default arguments
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = [];

	/**
	 * Field instance
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $instance = '';

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Initialize.
		$this->initialize();

		// Field instance.
		$this->instance = $this->get_field_type();

		// Defaults.
		if ( $this->defaults ) {
			$this->instance->defaults = array_merge( $this->instance->defaults, $this->defaults );
		}

		// Field actions & filters.
		$add = [

			// Value.
			[ 'filter', 'acf/load_value', [ $this, 'load_value' ], 10, 3 ],
			[ 'filter', 'acf/update_value', [ $this, 'update_value' ], 10, 3 ],
			[ 'filter', 'acf/format_value', [ $this, 'format_value' ], 10, 3 ],
			[ 'filter', 'acf/validate_value', [ $this, 'validate_value' ], 10, 4 ],
			[ 'action', 'acf/delete_value', [ $this, 'delete_value' ], 10, 3 ],

			// Field.
			[ 'filter', 'acf/validate_rest_value', [ $this, 'validate_rest_value' ], 10, 3 ],
			[ 'filter', 'acf/validate_field', [ $this, 'validate_field' ], 10, 1 ],
			[ 'filter', 'acf/load_field', [ $this, 'load_field' ], 10, 1 ],
			[ 'filter', 'acf/update_field', [ $this, 'update_field' ], 10, 1 ],
			[ 'filter', 'acf/duplicate_field', [ $this, 'duplicate_field' ], 10, 1 ],
			[ 'action', 'acf/delete_field', [ $this, 'delete_field' ], 10, 1 ],
			[ 'action', 'acf/render_field', [ $this, 'render_field' ], 9, 1 ],
			[ 'action', 'acf/render_field_settings', [ $this, 'render_field_settings' ], 9, 1 ],
			[ 'filter', 'acf/prepare_field', [ $this, 'prepare_field' ], 10, 1 ],
			[ 'filter', 'acf/translate_field', [ $this, 'translate_field' ], 10, 1 ],

			// Extend.
			[ 'filter', 'acfe/form/format_value', [ $this, 'format_front_value' ], 10, 5 ],
			[ 'filter', 'acfe/form/validate_value', [ $this, 'validate_front_value' ], 10, 5 ],
			[ 'filter', 'acfe/field_wrapper_attributes', [ $this, 'field_wrapper_attributes' ], 10, 2 ],
			[ 'filter', 'acfe/load_fields', [ $this, 'load_fields' ], 10, 2 ],
		];

		// Loop actions & filters.
		foreach ( $add as $row ) {

			list( $type, $hook, $function, $priority, $args ) = $row;

			// Get method.
			if ( 'filter' === $type ) {
				$method = 'add_field_filter';
			} else {
				$method = 'add_field_action';
			}

			// Use replace method.
			if ( in_array( $function[1], $this->replace ) ) {
				if ( 'filter' === $type ) {
					$method = 'replace_field_filter';
				} else {
					$method = 'replace_field_action';
				}
			}

			// Call method.
			$this->{$method}( $hook, $function, $priority, $args );
		}

		// Input actions.
		$this->add_action( 'acf/input/admin_enqueue_scripts', [ $this, 'input_admin_enqueue_scripts' ], 10, 0 );
		$this->add_action( 'acf/input/admin_head', [ $this, 'input_admin_head' ], 10, 0 );
		$this->add_action( 'acf/input/form_data', [ $this, 'input_form_data' ], 10, 1 );
		$this->add_filter( 'acf/input/admin_l10n', [ $this, 'input_admin_l10n' ], 10, 1 );
		$this->add_action( 'acf/input/admin_footer', [ $this, 'input_admin_footer' ], 10, 1 );

		// Field group actions.
		$this->add_action( 'acf/field_group/admin_enqueue_scripts', [ $this, 'field_group_admin_enqueue_scripts' ], 10, 0 );
		$this->add_action( 'acf/field_group/admin_head', [ $this, 'field_group_admin_head' ], 10, 0 );
		$this->add_action( 'acf/field_group/admin_footer', [ $this, 'field_group_admin_footer' ], 10, 0 );
	}

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {
		// Do something.
	}

	/**
	 * Get field type
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed
	 */
	public function get_field_type() {
		return acf_get_field_type( $this->name );
	}


	/**
	 * Pre validate front value
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $valid
	 * @param  mixed $value
	 * @param  mixed $field
	 * @param  mixed $form
	 * @return mixed
	 */
	public function pre_validate_front_value( $valid, $value, $field, $form ) {

		// Already invalid.
		if ( ! $valid || ( is_string( $valid ) && ! empty( $valid ) ) ) {
			return false;
		}

		// Empty value.
		if ( empty( $value ) ) {
			return false;
		}

		// Default validation.
		$validate = true;

		// Variations
		$validate = apply_filters( "acfe/form/pre_validate_value/form={$form['name']}",   $validate, $field, $form );

		$validate = apply_filters( "acfe/form/pre_validate_value/type={$field['type']}",  $validate, $field, $form );

		$validate = apply_filters( "acfe/form/pre_validate_value/name={$field['_name']}", $validate, $field, $form );

		$validate = apply_filters( "acfe/form/pre_validate_value/key={$field['key']}",    $validate, $field, $form );

		return $validate;
	}

	/**
	 * Add filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_add
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function add_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		// Stop if not callable.
		if ( ! is_callable( $function_to_add ) ) {
			return;
		}
		add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * Remove filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_remove
	 * @param  integer $priority
	 * @return void
	 */
	public function remove_filter( $tag = '', $function_to_remove = '', $priority = 10 ) {

		// Stop if not callable.
		if ( ! is_callable( $function_to_remove ) ) {
			return;
		}
		remove_filter( $tag, $function_to_remove, $priority );
	}

	/**
	 * Replace filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  mixed $function_to_replace
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function replace_filter( $tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1 ) {

		// Check instance.
		if ( ! $this->instance ) {
			$this->instance = $this->get_field_type();
		}

		// Array.
		if ( is_array( $function_to_replace ) ) {
			$function_to_remove = [ $this->instance, $function_to_replace[1] ];
			$function_to_add = $function_to_replace;

		// String.
		} else {
			$function_to_remove = [ $this->instance, $function_to_replace ];
			$function_to_add    = [ $this, $function_to_replace ];
		}

		// Stop if not callable.
		if ( ! is_callable( $function_to_add ) ) {
			return;
		}

		// Replace.
		$this->remove_filter( $tag, $function_to_remove, $priority );
		$this->add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * Add field filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_add
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function add_field_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		$tag .= '/type=' . $this->name;
		$this->add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * Remove field filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_remove
	 * @param  integer $priority
	 * @return void
	 */
	public function remove_field_filter( $tag = '', $function_to_remove = '', $priority = 10 ) {

		$tag .= '/type=' . $this->name;
		$this->remove_filter( $tag, $function_to_remove, $priority );
	}

	/**
	 * Replace field filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_add
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function replace_field_filter( $tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1 ) {

		$tag .= '/type=' . $this->name;
		$this->replace_filter( $tag, $function_to_replace, $priority, $accepted_args );
	}

	/**
	 * add_action
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_add
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function add_action($tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1){

		// Stop if not callable.
		if ( ! is_callable( $function_to_add ) ) {
			return;
		}
		add_action( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * remove_action
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_remove
	 * @param  integer $priority
	 * @return void
	 */
	public function remove_action( $tag = '', $function_to_remove = '', $priority = 10 ) {

		// Stop if not callable.
		if ( ! is_callable( $function_to_aremove ) ) {
			return;
		}
		remove_action( $tag, $function_to_remove, $priority );
	}

	/**
	 * Replace action
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  mixed $function_to_replace
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function replace_action( $tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1 ) {

		// Check instance.
		if ( ! $this->instance ) {
			$this->instance = $this->get_field_type();
		}

		// Array.
		if ( is_array( $function_to_replace ) ) {
			$function_to_remove = [ $this->instance, $function_to_replace[1] ];
			$function_to_add    = $function_to_replace;

		// String.
		} else {
			$function_to_remove = [ $this->instance, $function_to_replace ];
			$function_to_add    = [ $this, $function_to_replace ];
		}

		// Stop if not callable.
		if ( ! is_callable( $function_to_add ) ) {
			return;
		}

		// Replace.
		$this->remove_action( $tag, $function_to_remove, $priority );
		$this->add_action( $tag, $function_to_add, $priority, $accepted_args );

	}

	/**
	 * Add field action
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_add
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function add_field_action( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		$tag .= '/type=' . $this->name;
		$this->add_action( $tag, $function_to_add, $priority, $accepted_args );
	}

	/**
	 * Remove field action
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_remove
	 * @param  integer $priority
	 * @return void
	 */
	public function remove_field_action( $tag = '', $function_to_remove = '', $priority = 10 ) {

		$tag .= '/type=' . $this->name;
		$this->remove_action( $tag, $function_to_remove, $priority );
	}

	/**
	 * Replace field action
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $tag
	 * @param  string $function_to_replace
	 * @param  integer $priority
	 * @param  integer $accepted_args
	 * @return void
	 */
	public function replace_field_action( $tag = '', $function_to_replace = '', $priority = 10, $accepted_args = 1 ) {

		$tag .= '/type=' . $this->name;
		$this->replace_action( $tag, $function_to_replace, $priority, $accepted_args );
	}
}
acf_new_instance( 'ACF_Extend_Field' );
