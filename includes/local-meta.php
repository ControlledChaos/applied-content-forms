<?php
/**
 * Local meta functions
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

class ACF_Local_Meta {

	/**
	 * Metadata
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $meta = [];

	/**
	 * Post ID
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    mixed
	 */
	public $post_id = 0;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Add filters.
		add_filter( 'acf/pre_load_post_id', [ $this, 'pre_load_post_id' ], 1, 2 );
		add_filter( 'acf/pre_load_meta', [ $this, 'pre_load_meta' ], 1, 2 );
		add_filter( 'acf/pre_load_metadata', [ $this, 'pre_load_metadata' ], 1, 4 );
	}

	/**
	 * Add postmeta to storage
	 *
	 * Accepts data in either raw or request format.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $meta An array of metdata to store.
	 * @param  mixed $post_id The post_id for this data.
	 * @param  bool $is_main Makes this postmeta visible to get_field() without a $post_id value.
	 * @return array
	 */
	public function add( $meta = [], $post_id = 0, $is_main = false ) {

		// Capture meta if supplied meta is from a REQUEST.
		if ( $this->is_request( $meta ) ) {
			$meta = $this->capture( $meta, $post_id );
		}

		// Add to storage.
		$this->meta[ $post_id ] = $meta;

		// Set $post_id reference when is the "main" postmeta.
		if ( $is_main ) {
			$this->post_id = $post_id;
		}
		return $meta;
	}

	/**
	 * Is a request
	 *
	 * Returns true if the supplied $meta is from a REQUEST (serialized <form> data).
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $meta An array of metdata to check.
	 * @return boolean
	 */
	public function is_request( $meta = [] ) {
		return acf_is_field_key( key( $meta ) );
	}

	/**
	 * Capture
	 *
	 * Returns a flattened array of meta for the given postdata.
	 * This is achieved by simulating a save whilst capturing all meta changes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $values An array of raw values.
	 * @param  mixed $post_id The post_id for this data.
	 * @return array
	 */
	public function capture( $values = [], $post_id = 0 ) {

		// Reset meta.
		$this->meta[ $post_id ] = [];

		// Listen for any added meta.
		add_filter( 'acf/pre_update_metadata', [ $this, 'capture_update_metadata' ], 1, 5 );

		// Simulate update.
		if ( $values ) {
			acf_update_values( $values, $post_id );
		}

		// Remove listener filter.
		remove_filter( 'acf/pre_update_metadata', [ $this, 'capture_update_metadata' ], 1, 5 );

		// Return meta.
		return $this->meta[ $post_id ];
	}

	/**
	 * Capture update metadata
	 *
	 * Records all meta activity and returns a non null
	 * value to bypass database updates.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $null
	 * @param  mixed $post_id The post id.
	 * @param  string $name The meta name.
	 * @param  mixed $value The meta value.
	 * @param  boolean $hidden If the meta is hidden (starts with an underscore).
	 * @return boolean
	 */
	public function capture_update_metadata( $null, $post_id, $name, $value, $hidden ) {
		$name = ($hidden ? '_' : '') . $name;
		$this->meta[ $post_id ][ $name ] = $value;

		// Return non null value to escape update process.
		return true;
	}

	/**
	 * Remove
	 *
	 * Removes postmeta from storage.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $post_id The post_id for this data.
	 * @return void
	 */
	public function remove( $post_id = 0 ) {

		// Unset meta.
		unset( $this->meta[ $post_id ] );

		// Reset post_id.
		if ( $post_id === $this->post_id ) {
			$this->post_id = 0;
		}
	}

	/**
	 * Preload meta
	 *
	 * Injects the local meta.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $null An empty parameter. Return a non null
	 *                    value to short-circuit the function.
	 * @param  mixed $post_id The post_id for this data.
	 * @return mixed
	 */
	public function pre_load_meta( $null, $post_id ) {
		if ( isset( $this->meta[ $post_id ] ) ) {
			return $this->meta[ $post_id ];
		}
		return $null;
	}

	/**
	 * Preload metadata
	 *
	 * Injects the local meta.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $null An empty parameter. Return a non null value to short-circuit the function.
	 * @param  mixed $post_id The post id.
	 * @param  string $name The meta name.
	 * @param  boolean $hidden If the meta is hidden (starts with an underscore).
	 * @return mixed
	 */
	public function pre_load_metadata( $null, $post_id, $name, $hidden ) {

		$name = ( $hidden ? '_' : '' ) . $name;
		if ( isset( $this->meta[ $post_id ] ) ) {
			if ( isset( $this->meta[ $post_id ][ $name ] ) ) {
				return $this->meta[ $post_id ][ $name ];
			}
			return '__return_null';
		}
		return $null;
	}

	/**
	 * Preload post_id
	 *
	 * Injects the local post_id.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $null An empty parameter. Return a non null
	 *                    value to short-circuit the function.
	 * @param  mixed $post_id The post_id for this data.
	 * @return mixed
	 */
	public function pre_load_post_id( $null, $post_id ) {
		if ( ! $post_id && $this->post_id ) {
			return $this->post_id;
		}
		return $null;
	}
}

/**
 * ACF setup meta
 *
 * Adds postmeta to storage.
 *
 * @see ACF_Local_Meta::add() for list of parameters.
 *
 * @since  1.0.0
 * @param  array $meta
 * @param  integer $post_id
 * @param  boolean $is_main
 * @return array
 */
function acf_setup_meta( $meta = [], $post_id = 0, $is_main = false ) {
	return acf_get_instance( 'ACF_Local_Meta' )->add( $meta, $post_id, $is_main );
}

/**
 * ACF reset meta
 *
 * Removes postmeta to storage.
 *
 * @see ACF_Local_Meta::remove() for list of parameters.
 *
 * @since  1.0.0
 * @param  integer $post_id
 * @return void
 */
function acf_reset_meta( $post_id = 0 ) {
	return acf_get_instance( 'ACF_Local_Meta' )->remove( $post_id );
}

if(!class_exists('ACFE_Local_Meta')):

class ACFE_Local_Meta {

    // Vars
    var $meta = array();
    var $curr_id = array();
    var $main_id = array();

    /*
     * Construct
     */
    function __construct(){

        // Filters
        add_filter('acf/pre_load_post_id',  array($this, 'pre_load_post_id'),   1, 2);
        add_filter('acf/pre_load_meta',     array($this, 'pre_load_meta'),      1, 2);
        add_filter('acf/pre_load_metadata', array($this, 'pre_load_metadata'),  1, 4);

    }

    /*
     * Add
     */
    function add($meta = array(), $post_id = 0, $is_main = false){

        // Capture meta
        if($this->is_request($meta)){
            $meta = $this->capture($meta, $post_id);
        }

        // Add to storage
        $this->meta[$post_id] = $meta;

        // Add to current ID
        $this->curr_id[] = $post_id;

        // Add to main ID
        if($is_main){
            $this->main_id[] = $post_id;
        }

        // Return meta.
        return $meta;

    }

    /*
     * Remove
     */
    function remove() {

        // unset meta
        unset($this->meta[end($this->curr_id)]);

        // reset main id
        if(end($this->curr_id) === end($this->main_id)){

            // remove last value of main id
            array_pop($this->main_id);

        }

        // remove last value of current id
        array_pop($this->curr_id);

    }

    /*
     * Preload Post ID
     */
    function pre_load_post_id($null, $post_id){

        if(!$post_id && $this->main_id && end($this->curr_id) === end($this->main_id)){
            return end($this->main_id);
        }

        return $null;

    }

    /*
     * Is Request
     */
    function is_request($meta = array()){
        return acf_is_field_key(key($meta));
    }

    /*
     * Capture
     */
    function capture($values = array(), $post_id = 0){

        // Reset meta.
        $this->meta[ $post_id ] = array();

        // Listen for any added meta.
        add_filter('acf/pre_update_metadata', array($this, 'capture_update_metadata'), 1, 5);

        // Simulate update.
        if($values){

            // Get hook variations
            $hook = acf_get_store('hook-variations')->get('acf/update_value');

            // Clone Hook
            $_hook = $hook;
            unset($_hook['variations'][1]); // unset name
            unset($_hook['variations'][2]); // unset key

            // Update hook variations
            acf_get_store('hook-variations')->set('acf/update_value', $_hook);

            // update values
            acf_update_values($values, $post_id);

            // Reset hook variations back to default
            acf_get_store('hook-variations')->set('acf/update_value', $hook);

        }

        // Remove listener filter.
        remove_filter('acf/pre_update_metadata', array($this, 'capture_update_metadata'), 1);

        // Return meta.
        return $this->meta[ $post_id ];

    }

    /*
     * Capture Update Metadata
     */
    function capture_update_metadata($null, $post_id, $name, $value, $hidden){

        $name = ($hidden ? '_' : '') . $name;
        $this->meta[ $post_id ][ $name ] = $value;

        // Return non null value to escape update process.
        return true;

    }

    /*
     * Preload Meta
     */
    function pre_load_meta($null, $post_id){

        if(isset($this->meta[ $post_id ])){
            return $this->meta[ $post_id ];
        }

        return $null;

    }

    /*
     * Preload Metadata
     */
    function pre_load_metadata($null, $post_id, $name, $hidden){

        $name = ($hidden ? '_' : '') . $name;

        if(isset($this->meta[ $post_id ])){

            if(isset($this->meta[ $post_id ][ $name ])){
                return $this->meta[ $post_id ][ $name ];
            }
            return '__return_null';

        }

        return $null;

    }

}

endif;

/*
 * acfe_setup_meta
 */
function acfe_setup_meta( $meta = array(), $post_id = 0, $is_main = false){
    return acf_get_instance('ACFE_Local_Meta')->add($meta, $post_id, $is_main);
}

/*
 * acfe_reset_meta
 */
function acfe_reset_meta($post_id = null){
    return acf_get_instance('ACFE_Local_Meta')->remove();
}

/*
 * acfe_get_local_post_ids
 */
function acfe_get_local_post_ids(){

    $post_ids = array();

    // ACF Local Meta
    $acf_meta = acf_get_instance('ACF_Local_Meta')->meta;
    $post_ids = array_merge($post_ids, array_keys($acf_meta));

    // ACFE Local Meta
    $acfe_meta = acf_get_instance('ACFE_Local_Meta')->meta;
    $post_ids = array_merge($post_ids, array_keys($acfe_meta));

    return array_unique($post_ids);

}

/*
 * acfe_get_local_post_id
 */
function acfe_get_local_post_id(){

    $post_ids = acfe_get_local_post_ids();

    return end($post_ids);

}

/*
 * acfe_is_local_post_id
 */
function acfe_is_local_post_id($post_id){

    $local_post_ids = acfe_get_local_post_ids();

    return in_array($post_id, $local_post_ids);

}

/*
 * acfe_is_local_meta
 */
function acfe_is_local_meta(){

    return !empty(acfe_get_local_post_ids());

}
