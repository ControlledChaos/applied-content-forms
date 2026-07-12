<?php
/**
 * World query class
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

class ACF_World_Query {

	/**
	 * Type
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type;

	/**
	 * Arguments
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $args;

	/**
	 * Data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return self
	 */
	public function __construct( $args ) {

		$this->type = acf_extract_var( $args, 'type', 'countries' );
		$this->data = acf_get_instance( 'ACF_World_Data' )->{$this->type};
		$this->args = $args;
		$this->validate();
		$this->filter();
		$this->order();
	}

	/**
	 * Validate
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function validate() {

		if ( ! $this->args['orderby'] && $this->args['field'] ) {
			$this->args['orderby'] = $this->args['field'];
		} else {
			$this->args['orderby'] = $this->args['orderby'];
		}

		if ( $this->args['orderby'] ) {
			$this->args['orderby'] = $this->args['orderby'];
		} else {
			$this->args['orderby'] = 'code';
		}
	}

	/**
	 * Filter
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function filter() {

		$args = $this->args;
		$data = $this->data;

		if ( ! is_array( $data ) ) {
			return;
		}

		// Generate rules.
		$_args = array_keys( $args );
		$rules = [];

		foreach ( $_args as $rule ) {

			$split = explode( '__', $rule );
			$key   = $split[0];

			if ( acf_maybe_get( $split, 1 ) !== 'in' ) {
				continue;
			}

			if ( 'language' === $key ) {
				$key = 'languages';
			}
			if ( 'country' === $key ) {
				$key = 'countries';
			}
			if ( 'currency' === $key ) {
				$key = 'currencies';
			}

			// name == name__in
			$rules[ $key ] = $rule;
		}

		foreach ( array_keys( $data ) as $key ) {

			$row   = $data[$key];
			$valid = true;

			foreach ( $rules as $r_key => $rule ) {

				if ( ! $args[$rule] ) {
					continue;
				}

				// array( 'us', 'fr', 'de' )
				$args[$rule] = acf_get_array( $args[$rule] );

				// $data['fr_FR']['locale']
				$is_string = isset( $row[$r_key] ) && ! is_array( $row[$r_key] );

				if ( $is_string ) {
					if ( in_array( $row[$r_key], $args[$rule] ) ) {
						continue;
					}
					$valid = false;
				} else {

					$found = false;
					foreach ( $row[$r_key] as $sub_row ) {

						if ( ! in_array( $sub_row, $args[$rule] ) ) {
							continue;
						}
						$found = true;
					}
					if ( ! $found ) {
						$valid = false;
					}
				}
			}

			if ( $valid ) {
				continue;
			}
			unset( $data[$key] );
		}
		$this->data = $data;
	}

	/**
	 * Order
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function order() {

		$args    = $this->args;
		$data    = $this->data;
		$orderby = $args['orderby'];
		$order   = $args['order'];
		$columns = explode( '__', $orderby );

		// Order by key.
		if ( acf_maybe_get( $columns, 1 ) !== 'in' ) {

			$data = wp_list_sort( $data, $orderby, $order, true );

		// Order by name__in.
		} else {

			// Name.
			$key = $columns[0];

			// array( 'fr', 'us', 'de' )
			$array = acf_get_array( $args[$orderby] );

			uasort( $data, function( $a, $b ) use( $key, $array, $order ) {

				// ASC.
				$value_a = $a[$key];
				$value_b = $b[$key];

				// DESC.
				if ( 'DESC' === $order ) {
					$value_a = $b[$key];
					$value_b = $a[$key];
				}

				// Position.
				$pos_a = array_search( $value_a, $array );
				$pos_b = array_search( $value_b, $array );

				// Calculate.
				return $pos_a - $pos_b;
			} );
		}

		if ( $args['offset'] > 0 ) {
			$data = array_slice( $data, $args['offset'] );
		}
		if ( $args['limit'] > 0 ) {
			$data = array_slice( $data, 0, $args['limit'] );
		}

		// Clone.
		$_data = $data;

		if ( $args['field'] ) {

			$data = wp_list_pluck( $data, $args['field'] );

			if ( false !== $args['display'] ) {

				foreach ( array_keys( $data ) as $code ) {

					$display = $args['display'];
					if ( preg_match_all('/{(.*?)}/', $display, $matches ) ) {

						foreach ( $matches[1] as $i => $tag ) {
							$value   = acf_maybe_get( $_data[$code], $tag );
							$display = str_replace( '{' . $tag . '}', $value, $display );
						}
						$display = str_replace( '{' . $tag . '}', '', $display );
					}
					$data[$code] = $display;
				}
			}

			if ( false !== $args['prepend'] ) {

				foreach ( array_keys( $data ) as $code ) {

					$prepend = $args['prepend'];

					if ( preg_match_all( '/{(.*?)}/', $prepend, $matches ) ) {

						foreach ( $matches[1] as $i => $tag ) {
							$value   = acf_maybe_get( $_data[$code], $tag );
							$prepend = str_replace( '{' . $tag . '}', $value, $prepend );
						}
						$prepend = str_replace( '{' . $tag . '}', '', $prepend );
					}
					$data[$code] = $prepend . $data[$code];
				}
			}

			if ( false !== $args['append'] ) {

				foreach ( array_keys( $data ) as $code ) {

					$append = $args['append'];

					if ( preg_match_all( '/{(.*?)}/', $append, $matches ) ) {

						foreach ( $matches[1] as $i => $tag ) {

							$value  = acf_maybe_get( $_data[$code], $tag );
							$append = str_replace( '{' . $tag . '}', $value, $append );
						}
						$append = str_replace( '{' . $tag . '}', '', $append );
					}
					$data[$code] = $data[$code] . $append;
				}
			}
		}

		if ( $args['groupby'] ) {

			$groups = [];

			foreach ( $_data as $code => $row ) {

				if ( ! isset( $row[ $args['groupby'] ] ) ) {
					break;
				}
				$groups[ $row[ $args['groupby'] ] ][ $code ] = $data[$code];

			}

			if ( $groups ) {
				ksort( $groups );
				$data = $groups;
			}
		}
		$this->data = $data;
	}

	/**
	 * Get
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get() {
		return $this->data;
	}
}
