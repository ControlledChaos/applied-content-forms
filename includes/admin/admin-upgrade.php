<?php
/**
 * ACF admin upgrade screen
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

class ACF_Admin_Upgrade {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'admin_menu', [ $this,'admin_menu' ], 20 );
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', [ $this,'network_admin_menu' ], 20 );
		}
	}

	/**
	 * Admin menu
	 *
	 * Sets up logic if DB Upgrade is needed on a single site.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		// Check if upgrade is avaialble.
		if ( acf_has_upgrade() ) {

			add_action( 'admin_notices', [ $this, 'admin_notices' ] );

			$page = add_submenu_page(
				'index.php',
				__( 'Upgrade Database', 'acf' ),
				__( 'Upgrade Database', 'acf' ),
				acf_get_setting( 'capability' ),
				'acf-upgrade',
				[ $this, 'admin_html' ]
			);
			add_action( 'load-' . $page, [ $this, 'admin_load' ] );
		}
	}

	/**
	 * Network admin menu
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function network_admin_menu() {

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
				 * Restore site
				 *
				 * Ideally this would switch back to the original site at after
				 * looping however the restore_current_blog() is needed to
				 * modify global variables.
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

		// Stop if no upgrade is needed.
		if ( ! $upgrade ) {
			return;
		}

		// Add notice.
		add_action( 'network_admin_notices', [ $this, 'network_admin_notices' ] );

		// Add page.
		$page = add_submenu_page(
			'index.php',
			__( 'Upgrade Database','acf' ),
			__( 'Upgrade Database','acf' ),
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
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_load() {
		remove_action( 'admin_notices', [ $this, 'admin_notices' ] );
		acf_enqueue_script( 'acf' );
	}

	/**
	 * Network admin load
	 *
	 * Runs during the loading of the network admin page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function network_admin_load() {
		remove_action( 'network_admin_notices', [ $this, 'network_admin_notices' ] );
		acf_enqueue_script( 'acf' );
	}

	/**
	 * Admin notices
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_notices() {

		// vars
		$view = array(
			'button_text' => __( 'Upgrade Database', 'acf' ),
			'button_url'  => admin_url( 'index.php?page=acf-upgrade' ),
			'confirm'     => true
		);
		acf_get_view( 'html-notice-upgrade', $view );
	}

	/**
	 * Network admin notices
	 *
	 * @since  1.0.0
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
	 * Admin HTML
	 *
	 * Displays the HTML for the upgrade page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_html() {
		acf_get_view( 'html-admin-page-upgrade' );
	}

	/**
	 * Network admin HTML
	 *
	 * Displays the HTML for the network upgrade page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function network_admin_html() {
		acf_get_view( 'html-admin-page-upgrade-network' );
	}
}
acf_new_instance( 'ACF_Admin_Upgrade' );
