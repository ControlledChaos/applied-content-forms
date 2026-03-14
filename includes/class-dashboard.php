<?php
/**
 * Admin dashboard
 */

namespace ACF;

class Dashboard {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', [ $this, 'add_widget' ] );
	}

	/**
	 * Add the Website Summary widget
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function add_widget() {

		$title = apply_filters( 'acf_dashboard_widget_title', __( 'Site Content', 'acf' ) );

		wp_add_dashboard_widget( 'dashboard_summary', $title, [ $this, 'output' ] );
	}

	public function output() {
		echo __( 'Widget under development.', 'acf' );
	}
}

if ( acfe_get_setting( 'acf/dashboard_widget' ) ) {
	new Dashboard();
}
