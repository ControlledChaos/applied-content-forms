<?php
/**
 * AJAX query
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   AJAX
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Ajax_Query extends ACF_Ajax {
	
	/**
	 * Privacy
	 *
	 * Prevents access for non-logged in users.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $public = true;
	
	/**
	 * Page of results to return
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    integer
	 */
	public $page = 1;
	
	/**
	 * Results per page
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    integer
	 */
	public $per_page = 20;
	
	/**
	 * More pages
	 *
	 * Signifies whether or not this AJAX query
	 * has more pages to load.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $more = false;
	
	/**
	 * Searched term
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $search = '';
	
	/**
	 * Is search
	 *
	 * Signifies whether the current query is a search.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $is_search = false;
	
	/**
	 * Post ID being edited
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    integer
	 */
	public $post_id = 0;
	
	/**
	 * Field
	 *
	 * The ACF field related to this query.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $field = false;
	
	/**
	 * Get response
	 *
	 * Returns the response data to sent back.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return mixed The response data or WP_Error.
	 */
	public function get_response( $request ) {
		
		// Init request.
		$this->init_request( $request );
		
		// Get query args.
		$args = $this->get_args( $request );
		
		// Get query results.
		$results = $this->get_results( $args );
		if ( is_wp_error( $results ) ) {
			return $results;
		}
		
		// Return response.
		return [
			'results' => $results,
			'more'    => $this->more
		];
	}
	
	/**
	 * Init request
	 *
	 * Called at the beginning of a request to setup properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return void
	 */
	public function init_request( $request ) {
		
		// Get field for this query.
		if ( isset( $request['field_key'] ) ) {
			$this->field = acf_get_field( $request['field_key'] );
		}
		
		// Update query properties.
		if ( isset( $request['page'] ) ) {
			$this->page = intval( $request['page'] );
		}
		if ( isset( $request['per_page'] ) ) {
			$this->per_page = intval( $request['per_page'] );
		}
		if ( isset( $request['search'] ) && acf_not_empty( $request['search'] ) ) {
			$this->search    = sanitize_text_field( $request['search'] );
			$this->is_search = true;
		}
		if ( isset( $request['post_id'] ) ) {
			$this->post_id = $request['post_id'];
		}
	}
	
	/**
	 * Get args
	 *
	 * Returns an array of args for this query.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return array
	 */
	public function get_args( $request ) {
		
		// Allow for custom "query" arg.
		if ( isset( $request['query'] ) ) {
			return (array) $request['query'];
		}
		return [];
	}
	
	/**
	 * Get items
	 *
	 * Returns an array of results for the given args.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array args The query args.
	 * @return array
	 */
	public function get_results( $args ) {
		return [];
	}
	
	/**
	 * Get item
	 *
	 * Returns a single result for the given item object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $item A single item from the queried results.
	 * @return array An array containing "id" and "text".
	 */
	public function get_result( $item ) {
		return false;
	}
}
