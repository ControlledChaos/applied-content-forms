<?php
/**
 * ACF location abstract
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Locations
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class ACF_Location {

	/**
	 * The location rule name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * The location rule label.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $label = '';

	/**
	 * The location rule category.
	 *
	 * Accepts "post", "page", "user", "forms" or a custom label.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $category = 'post';

	/**
	 * Whether or not the location rule is publicly accessible.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $public = true;

	/**
	 * The object type related to this location rule.
	 *
	 * Accepts an object type discoverable by `acf_get_object_type()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $object_type = '';

	/**
	 * The object subtype related to this location rule.
	 *
	 * Accepts a custom post type or custom taxonomy.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $object_subtype = '';

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		$this->initialize();

		// Legacy method filters.
		if ( method_exists( $this, 'rule_match' ) ) {
			add_filter( "acf/location/rule_match/{$this->name}", [ $this, 'rule_match' ], 5, 3 );
		}
		if ( method_exists( $this, 'rule_operators' ) ) {
			add_filter( "acf/location/rule_operators/{$this->name}", [ $this, 'rule_operators' ], 5, 2 );
		}
		if ( method_exists( $this, 'rule_values' ) ) {
			add_filter( "acf/location/rule_values/{$this->name}", [ $this, 'rule_values' ], 5, 2 );
		}
	}

	/**
	 * Magic __call method for backwards compatibility
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The method name.
	 * @param  array $arguments The array of arguments.
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {

		/**
		 * Add backwards compatibility for legacy methods.
		 *
		 * Combine 3x legacy filters cases together (remove first args).
		 */
		switch ( $name ) {
			case 'rule_match' :
				$method = isset( $method ) ? $method : 'match';
				$arguments[3] = isset( $arguments[3] ) ? $arguments[3] : false; // Add $field_group param.
			case 'rule_operators' :
				$method = isset( $method ) ? $method : 'get_operators';
			case 'rule_values' :
				$method = isset( $method ) ? $method : 'get_values';
				array_shift( $arguments );
				return call_user_func_array( [ $this, $method ], $arguments );
			case 'compare' :
				return call_user_func_array( [ $this, 'compare_to_rule' ], $arguments );
		}
    }

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function initialize() {
		// Hello.
	}

	/**
	 * Get operators
	 *
	 * Returns an array of operators for this location.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return array
	 */
	public static function get_operators( $rule ) {
		return [
			'==' => __( 'is equal to', 'acf' ),
			'!=' => __( 'is not equal to', 'acf' )
		];
	}

	/**
	 * Get values
	 *
	 * Returns an array of possible values for this location.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return array
	 */
	public function get_values( $rule ) {
		return [];
	}

	/**
	 * Get object type
	 *
	 * Returns the object_type connected to this location.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return string
	 */
	public function get_object_type( $rule ) {
		return $this->object_type;
	}

	/**
	 * Get object subtype
	 *
	 * Returns the object_subtype connected to this location.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule A location rule.
	 * @return mixed
	 */
	public function get_object_subtype( $rule ) {
		return $this->object_subtype;
	}

	/**
	 * Match
	 *
	 * Matches the provided rule against the screen args
	 * returning a boolean result.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule The location rule.
	 * @param  array $screen The screen args.
	 * @param  array $field_group The field group settings.
	 * @return boolean
	 */
	public function match( $rule, $screen, $field_group ) {
		return false;
	}

	/**
	 * Compare to rule
	 *
	 * Compares the given value and rule params returning
	 * true when they match.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $rule The location rule data.
	 * @param  mixed $value The value to compare against.
	 * @return boolean
	 */
	public function compare_to_rule( $value, $rule ) {

		$result = ( $value == $rule['value'] );

		// Allow "all" to match any value.
        if ( 'all' === $rule['value'] ) {
	        $result = true;
        }

		// Reverse result for "!=" operator.
        if ( '!=' === $rule['operator'] ) {
        	return ! $result;
        }
		return $result;
	}
}
