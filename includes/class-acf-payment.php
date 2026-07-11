<?php
/**
 * Payments class
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Classes
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Payment {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  mixed $data
	 * @return self
	 */
	public function __construct() {

		add_filter( 'acfe/fields/payment/create/gateway=stripe', [ $this, 'stripe_cart' ], 9, 4 );
		add_filter( 'acfe/fields/payment/create/gateway=paypal', [ $this, 'paypal_cart' ], 9, 4 );
		add_filter( 'acfe/fields/payment/object',  [ $this, 'payment_object' ], 9, 4 );

		add_action( 'wp_ajax_acfe/get_payment_field', [ $this, 'ajax_get_payment_field' ] );

	}

	/**
	 * Stripe cart
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @param  string $field
	 * @param  string $gateway
	 * @param  integer $post_id
	 * @return array
	 */
	public function stripe_cart( $args, $field, $gateway, $post_id ) {

		// Get cart.
		if ( ! $cart = acf_get_form_data( 'acfe/payment_cart' ) ) {
			return $args;
		}

		$items  = wp_list_pluck( $cart['items'], 'item' );
		$amount = $cart['amount'];

		// Set items in Stripe metadata.
		$args['metadata']['items'] = implode( ', ', $items );
		$args['amount'] = $amount;

		return $args;
	}

	/**
	 * PayPal cart
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @param  string $field
	 * @param  string $gateway
	 * @param  integer $post_id
	 * @return array
	 */
	public function paypal_cart( $args, $field, $gateway, $post_id ) {

		// Get cart.
		if ( ! $cart = acf_get_form_data( 'acfe/payment_cart' ) ) {
			return $args;
		}

		$items  = wp_list_pluck( $cart['items'], 'item' );
		$amount = $cart['amount'];

		// Set items in PayPal description.
		$args['description'] = implode( ', ', $items );
		$args['amount'] = $amount;

		return $args;
	}

	/**
	 * Payment object
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $response
	 * @param  string $field
	 * @param  string $gateway
	 * @param  integer $post_id
	 * @return string
	 */
	public function payment_object( $response, $field, $gateway, $post_id ) {

		// Get cart.
		if ( ! $cart = acf_get_form_data( 'acfe/payment_cart' ) ) {
			return $response;
		}

		// Add cart items.
		$response['items'] = acf_extract_var( $cart, 'items', [] );

		return $response;
	}

	/**
	 * AJAX get payment fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function ajax_get_payment_field() {

		if ( ! acf_verify_ajax() ) {
			die();
		}

		// Defaults.
		$options = acf_parse_args( $_POST, [
			'post_id'   => 0,
			's'         => '',
			'field_key' => '',
			'paged'     => 1
		] );

		$response = $this->ajax_get_payment_field_results( $options );
		acf_send_ajax_results( $response );
	}

	/**
	 * AJAX get payment field results
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $options
	 * @return array
	 */
	public function ajax_get_payment_field_results( $options ) {

		$hidden  = acf_get_setting( 'reserved_field_groups', [] );
		$choices = [];

		foreach ( acf_get_field_groups() as $field_group ) {

			// Stop if hidden.
			if ( in_array( $field_group['key'], $hidden ) ) {
				continue;
			}

			// Get fields.
			$fields  = acf_get_fields( $field_group['key'] );
			$choices = $this->ajax_get_payment_field_choices( $choices, $fields, $field_group );
		}

		$results = [];
		$s = null;

		if ( empty( $choices ) ) {
			return  [ 'results' => $results ];
		}

		// Search.
		if ( '' !== $options['s'] ) {

			// Strip slashes (search may be integer).
			$s = strval( $options['s'] );
			$s = wp_unslash( $s );

		}

		// Format results.
		foreach ( $choices as $title => $fields ) {

			$title    = strval( $title );
			$children = [];

			foreach ( $fields as $key => $label ) {

				$label = strval( $label );

				// Search.
				if ( is_string( $s ) && false === stripos( $label, $s ) && false === stripos( $title, $s ) ) {
					continue 2;
				}

				// Set children.
				$children[] = [
					'id'   => $key,
					'text' => $label
				];
			}

			$results[] = [
				'text'     => $title,
				'children' => $children
			];
		}
		return [ 'results' => $results ];
	}

	/**
	 * AJAX get payment field choices
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $choices
	 * @param  array $fields
	 * @param  string $field_group
	 * @return array
	 */
	public function ajax_get_payment_field_choices( $choices, $fields, $field_group ) {

		if ( empty( $fields ) ) {
			return $choices;
		}

		foreach ( $fields as $field ) {

			// Search for sub_fields (groups & clones).
			if ( acf_maybe_get( $field, 'sub_fields' ) ) {

				return $this->ajax_get_payment_field_choices( $choices, $field['sub_fields'], $field_group );
			}

			// Allow only a specific field.
			if ( 'acfe_payment' !=== $field['type'] ) {
				continue;
			}

			// Set choice.
			$choices[ $field_group['title'] ][ $field['key'] ] = $this->get_field_label( $field );
		}
		return $choices;
	}

	/**
	 * Get field label
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $field
	 * @return string
	 */
	public function get_field_label( $field ) {
		$label = acf_maybe_get( $field, 'label', $field['name'] );
		return "{$label} ({$field['key']})";
	}
}
acf_new_instance( 'ACF_Payment' );
