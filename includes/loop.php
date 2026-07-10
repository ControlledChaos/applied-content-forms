<?php
/**
 * Loop functions
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

/**
 * Add loop alias
 *
 * An alias of acf()->loop->add_loop()
 *
 * @since  1.0.0
 * @param  array $loop
 * @return array
 */
function acf_add_loop( $loop = [] ) {
	return acf()->loop->add_loop( $loop );
}

/**
 * Update loop alias
 *
 * An alias of acf()->loop->update_loop()
 *
 * @since  1.0.0
 * @param  mixed $i
 * @param  string $key
 * @param  mixed $value
 * @return boolean
 */
function acf_update_loop( $i = 'active', $key = null, $value = null ) {
	return acf()->loop->update_loop( $i, $key, $value );
}

/**
 * Get loop alias
 *
 * An alias of acf()->loop->get_loop()
 *
 * @param  mixed $i
 * @param  string $key
 * @return mixed
 */
function acf_get_loop( $i = 'active', $key = null ) {
	return acf()->loop->get_loop( $i, $key );
}

/**
 * Remove loop alias
 *
 * An alias of acf()->loop->remove_loop()
 *
 * @since  1.0.0
 * @param  mixed $i
 * @return boolean
 */
function acf_remove_loop( $i = 'active' ) {
	return acf()->loop->remove_loop( $i );
}
