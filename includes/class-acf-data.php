<?php
/**
 * ACF data functions
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

class ACF_Data {

	/**
	 * CID
	 *
	 * A unique identifier.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $cid = '';

	/**
	 * Data
	 *
	 * Storage for data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data = [];

	/**
	 * Aliases
	 *
	 * Storage for data aliases.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $aliases = [];

	/**
	 * Multisite
	 *
	 * Enables unique data per site.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $multisite = false;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $data
	 * @return self
	 */
	public function __construct( $data = false ) {

		// Set cid.
		$this->cid = acf_uniqid();

		// Set data.
		if ( $data ) {
			$this->set( $data );
		}
		$this->initialize();
	}

	/**
	 * Initialize
	 *
	 * Called during constructor to setup class functionality.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function initialize() {
		// Use in child class.
	}

	/**
	 * Prop
	 *
	 * Sets a property for the given name and returns $this for chaining.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $name The data name or an array of data.
	 * @param  mixed $value The data value.
	 * @return self
	 */
	public function prop( $name = '', $value = null ) {

		// Update property.
		$this->{$name} = $value;

		// Return this for chaining.
		return $this;
	}

	/**
	 * Key
	 *
	 * Returns a key for the given name allowing alias to work.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return string
	 */
	public function _key( $name = '' ) {
		return isset( $this->aliases[ $name ] ) ? $this->aliases[ $name ] : $name;
	}

	/**
	 * Has
	 *
	 * Returns true if this has data for the given name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The data name.
	 * @return boolean
	 */
	public function has( $name = '' ) {
		$key = $this->_key( $name );
		return isset( $this->data[ $key ] );
	}

	/**
	 * Is
	 *
	 * Similar to has() but does not check aliases.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return boolean
	 */
	public function is( $key = '' ) {
		return isset( $this->data[ $key ] );
	}

	/**
	 * Get
	 *
	 * Returns data for the given name of null if doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $name The data name.
	 * @return mixed
	 */
	public function get( $name = false ) {

		// Get all.
		if ( false === $name ) {
			return $this->data;

		// Get specific.
		} else {
			$key = $this->_key( $name );
			return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
		}
	}

	/**
	 * Get data
	 *
	 * Returns an array of all data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set
	 *
	 * Sets data for the given name and returns $this for chaining.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $name The data name or an array of data.
	 * @param  mixed $value The data value.
	 * @return self
	 */
	public function set( $name = '', $value = null ) {

		// Set multiple.
		if ( is_array( $name ) ) {
			$this->data = array_merge( $this->data, $name );

		// Set single.
		} else {
			$this->data[ $name ] = $value;
		}

		// Return this for chaining.
		return $this;
	}

	/**
	 * Append
	 *
	 * Appends data for the given name and returns $this for chaining.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $value The data value.
	 * @return self
	 */
	public function append( $value = null ) {

		// Append.
		$this->data[] = $value;

		// Return this for chaining.
		return $this;
	}

	/**
	 * Remove
	 *
	 * Removes data for the given name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The data name.
	 * @return self
	 */
	public function remove( $name = '' ) {

		// Remove data.
		unset( $this->data[ $name ] );

		// Return this for chaining.
		return $this;
	}

	/**
	 * Reset
	 *
	 * Resets the data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function reset() {
		$this->data    = [];
		$this->aliases = [];
	}

	/**
	 * Count
	 *
	 * Returns the data count.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function count() {
		return count( $this->data );
	}

	/**
	 * Query
	 *
	 * Returns a filtered array of data based on the set of key => value arguments.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @param string $operator
	 * @return array
	 */
	public function query( $args, $operator = 'AND' ) {
		return wp_list_filter( $this->data, $args, $operator );
	}

	/**
	 * Alias
	 *
	 * Sets an alias for the given name allowing data to be found via multiple identifiers.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return self
	 */
	public function alias( $name = '' ) {

		// Get all aliases.
		$args = func_get_args();
		array_shift( $args );

		// Loop over aliases and add to data.
		foreach ( $args as $alias ) {
			$this->aliases[ $alias ] = $name;
		}

		// Return this for chaining.
		return $this;
	}

	/**
	 * Switch site
	 *
	 * Triggered when switching between sites on a multisite installation.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $site_id New blog ID.
	 * @param  integer prev_blog_id Prev blog ID.
	 * @return void
	 */
	public function switch_site( $site_id, $prev_site_id ) {

		// Stop if not multisite compatible.
		if ( ! $this->multisite ) {
			return;
		}

		// Stop if no change in blog ID.
		if ( $site_id === $prev_site_id ) {
			return;
		}

		// Create storage.
		if ( ! isset( $this->site_data ) ) {
			$this->site_data    = [];
			$this->site_aliases = [];
		}

		// Save state.
		$this->site_data[ $prev_site_id ]    = $this->data;
		$this->site_aliases[ $prev_site_id ] = $this->aliases;

		// Reset state.
		$this->data    = [];
		$this->aliases = [];

		// Load state.
		if ( isset( $this->site_data[ $site_id ] ) ) {

			$this->data    = $this->site_data[ $site_id ];
			$this->aliases = $this->site_aliases[ $site_id ];

			unset( $this->site_data[ $site_id ] );
			unset( $this->site_aliases[ $site_id ] );
		}
	}
}
