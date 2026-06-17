<?php
/**
 * Fields functions
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

class acf_fields {

	/**
	 * Field type instances
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $types = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {}

	/**
	 * Register field type
	 *
	 * This function will register a field type instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $class
	 * @return void
	 */
	public function register_field_type( $class ) {

		// Allow instance.
		if  ( $class instanceOf acf_field ) {
			$this->types[ $class->name ] = $class;

		// Allow class name.
		} else {
			$instance = new $class();
			$this->types[ $instance->name ] = $instance;
		}
	}

	/**
	 * Get field type
	 *
	 * This function will return a field type instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function get_field_type( $name ) {
		return isset( $this->types[$name] ) ? $this->types[$name] : null;
	}

	/**
	 * Is field type
	 *
	 * This function will return true if a field type exists.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return boolean
	 */
	public function is_field_type( $name ) {
		return isset( $this->types[$name] );
	}

	/**
	 * Register field type info
	 *
	 * This function will store a basic array of info
	 * about the field type to later be overridden by
	 * the above register_field_type function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $info
	 * @return void
	 */
	public function register_field_type_info( $info ) {
		$instance = (object) $info;
		$this->types[ $instance->name ] = $instance;
	}

	/**
	 * Get field types
	 *
	 * This function will return an array of all field types.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_field_types() {
		return $this->types;
	}
}
acf()->fields = new acf_fields();

/**
 * Register field type
 *
 * Alias of acf()->fields->register_field_type()
 *
 * @since  1.0.0
 * @param  string $class
 * @return void
 */
function acf_register_field_type( $class ) {
	return acf()->fields->register_field_type( $class );
}

/**
 * Register field type info
 *
 * Alias of acf()->fields->register_field_type_info()
 *
 * @since  1.0.0
 * @param  array $info
 * @return void
 */
function acf_register_field_type_info( $info ) {
	return acf()->fields->register_field_type_info( $info );
}

/**
 * Get field type
 *
 * Alias of acf()->fields->get_field_type()
 *
 * @since  1.0.0
 * @param  string $name
 * @return void
 */
function acf_get_field_type( $name ) {
	return acf()->fields->get_field_type( $name );
}

/**
 * Get field types
 *
 * Alias of acf()->fields->get_field_types()
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_field_types( $args = [] ) {

	$args = wp_parse_args( $args, [ 'public' => true ] );
	$field_types = acf()->fields->get_field_types();

    return wp_filter_object_list( $field_types, $args );
}

/**
 * Get field types info
 *
 * Returns an array containing information about each field type.
 *
 * @since  1.0.0
 * @param  array $args
 * @return array
 */
function acf_get_field_types_info( $args = [] ) {

	$data = [];
	$field_types = acf_get_field_types();

	foreach ( $field_types as $type ) {
		$data[ $type->name ] = [
			'label'    => $type->label,
			'name'     => $type->name,
			'category' => $type->category,
			'public'   => $type->public
		];
	}
	return $data;
}

/**
 * Is field type
 *
 * Alias of acf()->fields->is_field_type()
 *
 * @since  1.0.0
 * @param  string $name
 * @return void
 */
function acf_is_field_type( $name = '' ) {
	return acf()->fields->is_field_type( $name );
}

/**
 * Get field type prop
 *
 * This function will return a field type's property.
 *
 * @since  1.0.0
 * @param  string $name
 * @param  string $prop
 * @return void
 */
function acf_get_field_type_prop( $name = '', $prop = '' ) {
	$type = acf_get_field_type( $name );
	return ( $type && isset( $type->$prop ) ) ? $type->$prop : null;
}

/**
 * Get field type label
 *
 * This function will return the label of a field type.
 *
 * @since  1.0.0
 * @param  string $name
 * @return string
 */
function acf_get_field_type_label( $name = '' ) {

	$prop = acf_get_field_type_prop( $name, 'label' );
	if ( $prop ) {
		$label = $prop;
	} else {
		$label = sprintf(
			'<span class="acf-tooltip-js" title="%s">%s</span>',
			__( 'Field type does not exist', 'acf' ),
			__( 'Unknown', 'acf' )
		);
	}
	return $label;
}

/**
 * Field type exists
 *
 * @deprecated
 *
 * @since  1.0.0
 * @param  string $type
 * @return string
 */
function acf_field_type_exists( $type = '' ) {
	return acf_is_field_type( $type );
}

/**
 * Get grouped field types
 *
 * Returns an multi-dimensional array of field types
 * "name => label" grouped by category.
 *
 * @since  1.0.0
 * @return array
 */
function acf_get_grouped_field_types() {

	$types  = acf_get_field_types();
	$groups = [];
	$l10n   = [
		'basic'      => __( 'Basic', 'acf' ),
		'content'    => __( 'Content', 'acf' ),
		'choice'     => __( 'Choice', 'acf' ),
		'relational' => __( 'Relational', 'acf' ),
		'jquery'     => __( 'jQuery', 'acf' ),
		'layout'     => __( 'Layout', 'acf' )
	];

	foreach ( $types as $type ) {

		$cat = $type->category;
		$cat = isset( $l10n[$cat] ) ? $l10n[$cat] : $cat;

		$groups[ $cat ][ $type->name ] = $type->label;
	}
	$groups = apply_filters( 'acf/get_field_types', $groups );
	return $groups;
}
