<?php
/**
 * Database upgrade
 *
 * UI to upgrade database if necessary.
 *
 * @package  ACF
 * @category Admin
 * @since    1.0.0
 */

namespace ACF\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Admin_Upgrade {

	/**
	* Constructor method
	*
	* @date   31/7/18
	* @since  5.7.2
	* @access public
	* @return self
	*/
	function __construct() {

		if ( acf_has_upgrade() ) {
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			add_action( 'admin_menu', [ $this, 'admin_page' ], 20 );

			if ( is_multisite() ) {
				add_action( 'network_admin_notices', [ $this, 'network_admin_notices' ] );
				add_action( 'network_admin_menu', [ $this, 'network_admin_page' ], 20 );
			}
		}
	}

	/**
	 * Admin page
	 *
	 * @since  5.7.4
	 * @access public
	 * @return void
	 */
	public function admin_page() {

		$page = add_submenu_page(
			'index.php',
			__( 'Upgrade Database', 'acf' ),
			__( 'Upgrade Database', 'acf' ),
			acf_get_setting( 'capability' ),
			'acf-upgrade',
			[ $this,'admin_html' ]
		);
		add_action( "load-$page", [ $this, 'admin_load' ] );
	}

	/**
	 * Network admin page
	 *
	 * @since  5.7.4
	 * @access public
	 * @return void
	 */
	public function network_admin_page() {

		$upgrade = false;

		// Loop over sites and check for upgrades.
		$sites = get_sites( [ 'number' => 0 ] );
		if ( $sites ) {

			// Unhook action to avoid memory issue (as seen in wp-includes/ms-site.php).
			remove_action( 'switch_blog', 'wp_switch_roles_and_user', 1 );
			foreach ( $sites as $site ) {

				// Switch site.
				switch_to_blog( $site->blog_id );

				// Check for upgrade.
				$site_upgrade = acf_has_upgrade();

				/**
				 * Restore site.
				 * Ideally, we would switch back to the
				 * original site at after looping,
				 * however the restore_current_blog()
				 * is needed to modify global vars.
				 */
				restore_current_blog();

				// Check if upgrade was found.
				if ( $site_upgrade ) {
					$upgrade = true;
					break;
				}
		    }
		    add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
		}

		// Bail early if no upgrade is needed.
		if ( ! $upgrade ) {
			return;
		}

		$page = add_submenu_page(
			'index.php',
			__( 'Upgrade Database', 'acf' ),
			__( 'Upgrade Database', 'acf' ),
			acf_get_setting( 'capability' ),
			'acf-upgrade-network',
			[ $this, 'network_admin_html' ]
		);
		add_action( "load-$page", [ $this, 'network_admin_load' ] );
	}

	/**
	 * Admin load
	 *
	 * Runs during the loading of the admin page.
	 *
	 * @since  5.7.4
	 * @access public
	 * @return void
	 */
	public function admin_load() {

		// Remove prompt.
		remove_action( 'admin_notices', [ $this, 'admin_notices' ] );

		// Enqueue core script.
		acf_enqueue_script( 'acf' );
	}

	/**
	 * Network admin load
	 *
	 * Runs during the loading of the network admin page.
	 *
	 * @since  5.7.4
	 * @access public
	 * @return void
	 */
	public function network_admin_load() {

		// Remove prompt.
		remove_action( 'network_admin_notices', [ $this, 'network_admin_notices' ] );

		// Enqueue core script.
		acf_enqueue_script( 'acf' );
	}

	/**
	 * Admin notices
	 *
	 * Displays the DB Upgrade prompt.
	 *
	 * @since	5.7.3
	 * @access public
	 * @return	void
	 */
	public function admin_notices() {

		$view = [
			'button_text' => __( 'Upgrade Database', 'acf' ),
			'button_url'  => admin_url( 'index.php?page=acf-upgrade' ),
			'confirm'     => true
		];
		acf_get_view( 'html-notice-upgrade', $view );
	}

	/**
	 * Network admin notices
	 *
	 * Displays the DB Upgrade prompt on a multi site.
	 *
	 * @since  5.7.3
	 * @access public
	 * @return void
	 */
	public function network_admin_notices() {

		$view = [
			'button_text' => __( 'Review sites & upgrade', 'acf' ),
			'button_url'  => network_admin_url( 'index.php?page=acf-upgrade-network' ),
			'confirm'     => false
		];
		acf_get_view( 'html-notice-upgrade', $view );
	}

	/**
	 * Admin page output
	 *
	 * Displays the HTML for the admin page.
	 *
	 * @since  5.7.4
	 * @access public
	 * @return	void
	 */
	public function admin_html() {
		acf_get_view( 'html-admin-page-upgrade' );
	}

	/**
	 * Network admin page output
	 *
	 * Displays the HTML for the network admin page.
	 *
	 * @since  5.7.4
	 * @access public
	 * @return void
	 */
	public function network_admin_html() {
		acf_get_view( 'html-admin-page-upgrade-network' );
	}
}

// Instantiate.
acf_new_instance( 'ACF\Admin\ACF_Admin_Upgrade' );
