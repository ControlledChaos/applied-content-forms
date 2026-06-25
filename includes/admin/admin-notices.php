<?php
/**
 * ACF Admin Notices
 *
 * Functions and classes to manage admin notices.
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Admin
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register notices store.
acf_register_store( 'notices' );

class ACF_Admin_Notice extends ACF_Data {

	/**
	 * Storage for data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data = [
		'text'        => '',
		'type'        => 'info',
		'dismissible' => true
	];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {}

	/**
	 * Render the notice HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render() {

		$notice_text    = $this->get( 'text' );
		$notice_type    = $this->get( 'type' );
		$is_dismissible = $this->get( 'dismissible' );

		printf( '<div class="acf-admin-notice notice notice-%s %s">%s</div>',
			esc_attr( $notice_type ),
			$is_dismissible ? 'is-dismissible' : '',
			acf_esc_html( wpautop( acf_punctify( $notice_text ) ) )
		);
	}
}

/**
 * New admin notice
 *
 * @since  1.0.0
 * @param  mixed $data
 * @return object
 */
function acf_new_admin_notice( $data = false ) {

	$instance = new ACF_Admin_Notice( $data );
	acf_get_store( 'notices' )->set( $instance->cid, $instance );

	return $instance;
}

 /**
  * Render admin notices
  *
  * @since  1.0.0
  * @return void
  */
function acf_render_admin_notices() {

	$notices = acf_get_store( 'notices' )->get_data();
	if ( $notices ) {
		foreach ( $notices as $notice ) {
			$notice->render();
		}
	}
}
add_action( 'admin_notices', 'acf_render_admin_notices', 99 );

/**
 * Add admin notice
 *
 * @since  1.0.0
 * @param  string $text
 * @param  string $type
 * @return object
 */
function acf_add_admin_notice( $text = '', $type = 'info', $dismissible = true, $persisted = false ) {
	return acf_new_admin_notice(
		[
			'text'        => $text,
			'type'        => $type,
			'dismissible' => $dismissible,
			'persisted'   => $persisted
		]
	);
}
