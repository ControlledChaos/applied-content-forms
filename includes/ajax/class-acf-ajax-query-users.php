<?php
/**
 * AJAX query users
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

class ACF_Ajax_Query_Users extends ACF_Ajax_Query {
	
	/**
	 * AJAX action name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action = 'acf/ajax/query_users';
	
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

		parent :: init_request( $request );
		
		// Customize query.
		add_filter( 'user_search_columns', [ $this, 'filter_search_columns' ], 10, 3 );
		
		// Fires when a request is made.
		do_action( 'acf/ajax/query_users/init', $request, $this );
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

		$args = parent::get_args( $request );
		$args['number'] = $this->per_page;
		$args['paged']  = $this->page;
		if ( $this->is_search ) {
			$args['search'] = "*{$this->search}*";
		}

		return apply_filters( 'acf/ajax/query_users/args', $args, $request, $this );
	}
	
	/**
	 * Prepare args
	 *
	 * Prepares args for the get_results() method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array args The query args.
	 * @return array
	 */
	public function prepare_args( $args ) {
		
		// Parse pagination args that may have been modified.
		if ( isset( $args['users_per_page'] ) ) {
			$this->per_page = intval( $args['users_per_page'] );
			unset( $args['users_per_page'] );
			
		} elseif ( isset( $args['number'] ) ) {
			$this->per_page = intval( $args['number'] );
		}
		
		if ( isset( $args['paged'] ) ) {
			$this->page = intval( $args['paged'] );
			unset( $args['paged'] );
		}
		
		// Set pagination args for fine control.
		$args['number'] = $this->per_page;
		$args['offset'] = $this->per_page * ( $this->page - 1 );
		$args['count_total'] = true;
		return $args;
	}
	
	/**
	 * Get results
	 *
	 * Returns an array of results for the given args.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array args The query args.
	 * @return array
	 */
	public function get_results( $args ) {

		$results = [];
		
		// Prepare args for query.
		$args = $this->prepare_args( $args );
		
		// Get result groups.
		if ( ! empty( $args['role__in'] ) ) {
			$roles = acf_get_user_role_labels( $args['role__in'] );
		} else {
			$roles = acf_get_user_role_labels();
		}
		
		/**
		 * Return a flat array of results when searching
		 * or when queriying one group only.
		 */
		if ( $this->is_search || count( $roles ) === 1 ) {
		
			// Query users and append to results.
			$wp_user_query = new WP_User_Query( $args );
			$users = (array) $wp_user_query->get_results();
			$total_users = $wp_user_query->get_total();
			
			foreach ( $users as $user ) {
				$results[] = $this->get_result( $user );
			}
			
			/**
			 * Determine if more results exist.
			 * As this query does not return grouped results,
			 * the calculation can be exact (">").
			 */
			$this->more = ( $total_users > count( $users ) + $args['offset'] );
			
		// Otherwise, group results via role.
		} else {
			
			// Unset args that will interfere with query results.
			unset( $args['role__in'], $args['role__not_in'] );
			
			// Loop over each role.
			foreach ( $roles as $role => $role_label ) {
				
				// Query users (for this role only).
				$args['role']  = $role;
				$wp_user_query = new WP_User_Query( $args );
				$users = (array) $wp_user_query->get_results();
				$total_users = $wp_user_query->get_total();
				
				// acf_log( $args );
				// acf_log( '- ', count($users) );
				// acf_log( '- ', $total_users );
				
				// If users were found for this query.
				if ( $users ) {
					
					// Append optgroup of results.
					$role_results = [];
					foreach ( $users as $user ) {
						$role_results[] = $this->get_result( $user );
					}
					$results[] = [
						'text'     => $role_label,
						'children' => $role_results
					];
					
					// End loop when enough results have been found.
					if ( count( $users ) === $args['number'] ) {

						/**
						 * Determine if more results exist.
						 *
						 * As this query does return grouped results,
						 * the calculation is best left fuzzy to avoid
						 * querying the next group (">=").
						 */
						$this->more = ( $total_users >= count( $users ) + $args['offset'] );
						break;
					
					// Modify the args so that the next query can continue on correctly.
					} else {
						$args['offset'] = 0;
						$args['number'] -= count( $users );
					}

				/**
				 * If no users were found (for the current pagination args)
				 * but there were users found for previous pages.
				 * Modify the args so that the next query is offset less
				 * (number of total users) and can continue on correctly.
				 */
				} elseif ( $total_users ) {
					$args['offset'] -= $total_users;
					continue;
					
				// Ignore roles that will never return a result.
				} else {
					continue;
				}				
			}
		}
		return apply_filters( 'acf/ajax/query_users/results', $results, $args, $this );
	}
	
	/**
	 * Get result
	 *
	 * Returns a single result for the given item object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $item A single item from the queried results.
	 * @return string
	 */
	public function get_result( $user ) {
		$item = acf_get_user_result( $user );
		return apply_filters( 'acf/ajax/query_users/result', $item, $user, $this );
	}
	
	/**
	 * Filter search columns
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $columns An array of column names to be searched.
	 * @param  string $search The search term.
	 * @param  object $WP_User_Query The WP_User_Query instance.
	 * @return array
	 */
	public function filter_search_columns( $columns, $search, $WP_User_Query ) {
		return apply_filters( 'acf/ajax/query_users/search_columns', $columns, $search, $WP_User_Query, $this );
	}
}
acf_new_instance( 'ACF_Ajax_Query_Users' );
