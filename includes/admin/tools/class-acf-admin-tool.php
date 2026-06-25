<?php
/**
 * ACF tool parent class
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Tools
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Admin_Tool {


	/**
	 * Tool name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * Tool title
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $title = '';

	/**
	 * Menu icon
	 *
	 * @since  1.0.0
	 * @access public
	 * @var string
	 */
	public $icon = '';

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		$this->initialize();
	}

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {}

	/**
	 * Load
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {}

	/**
	 * Is active
	 *
	 * Returns true if the tool is active.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function is_active() {
		return acf_maybe_get_GET( 'tool' ) === $this->name;
	}

	/**
	 * Get name
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get title
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get URL
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function get_url() {
		return acf_get_admin_tool_url( $this->name );
	}

	/**
	 * Metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {}

	/**
	 * Submit
	 *
	 * This function will run when the tool's form has been submit.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function submit() {}
}
