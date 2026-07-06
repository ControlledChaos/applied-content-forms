<?php
/**
 * Options page template
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

class ACF_Admin_Options_Page {

	/**
	 * Options page
	 *
	 * Contains the current options page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $page;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this,'admin_menu' ], 99, 0 );
	}

	/**
	 * Admin menu
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function admin_menu() {

		$pages = acf_get_options_pages();

		if ( empty( $pages ) ) {
			return;
		}

		foreach ( $pages as $page ) {
			$slug = '';

			if ( empty( $page['parent_slug'] ) ) {
				$slug = add_menu_page(
					$page['page_title'],
					$page['menu_title'],
					$page['capability'],
					$page['menu_slug'],
					[ $this, 'html' ],
					$page['icon_url'],
					$page['position']
				);
			} else {
				$slug = add_submenu_page(
					$page['parent_slug'],
					$page['page_title'],
					$page['menu_title'],
					$page['capability'],
					$page['menu_slug'],
					[ $this, 'html' ],
					$page['position']
				);
			}
			add_action( "load-{$slug}", [ $this,'admin_load' ] );
		}
	}

	/**
	 * Admin load
	 *
	 * @since  1.0.0
	 * @access public
	 * @global string $plugin_page
	 * @return void
	 */
	public function admin_load() {

		// Access global variables.
		global $plugin_page;

		$this->page = acf_get_options_page( $plugin_page );

		// Get post_id (allow lang modification).
		$this->page['post_id'] = acf_get_valid_post_id( $this->page['post_id'] );

		// Verify and remove nonce.
		if ( acf_verify_nonce( 'options' ) ) {

		    if ( acf_validate_save_post( true ) ) {
		    	acf_update_setting( 'autoload', $this->page['autoload'] );
				acf_save_post( $this->page['post_id'] );
				wp_redirect( add_query_arg( [ 'message' => '1' ] ) );

				exit;
			}
		}

		acf_enqueue_scripts();

		add_action( 'acf/input/admin_enqueue_scripts', [ $this,'admin_enqueue_scripts' ] );
		add_action( 'acf/input/admin_head', [ $this, 'admin_head' ] );
		add_screen_option( 'layout_columns', [ 'max' => 2, 'default' => 2 ] );
	}

	/**
	 * Admin enqueue scripts
	 *
	 * This function will enqueue the 'post.js' script which adds
	 * support for 'Screen Options' column toggle.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'post' );
	}

	/*
	*  admin_head
	*
	*  This action will find and add field groups to the current edit page
	*
	*  @type	action (admin_head)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	/**
	 * Admin head
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_head() {

		// Get field groups.
		$field_groups = acf_get_field_groups( [
			'options_page' => $this->page['menu_slug']
		] );

		// Notices.
		if ( ! empty( $_GET['message'] ) && $_GET['message'] == '1' ) {
			acf_add_admin_notice( $this->page['update_message'], 'success' );
		}

		// Add submit div.
		if ( 'side' === $this->page['update_location'] ) :
		add_meta_box(
			'submitdiv',
			$this->page['update_title'],
			[ $this, 'postbox_submitdiv' ],
			'acf_options_page',
			'side',
			'high'
		);
		endif;

		if ( empty( $field_groups ) ) {

			acf_add_admin_notice(
				sprintf( __( 'No Custom Field Groups found for this options page. <a href="%s">Create a Custom Field Group</a>', 'acf' ), admin_url( 'post-new.php?post_type=acf-field-group' ) ),
				'warning'
			);

		} else {

			foreach ( $field_groups as $i => $field_group ) {

				$id       = "acf-{$field_group['key']}";
				$title    = $field_group['title'];
				$context  = $field_group['position'];
				$priority = 'high';
				$args     = [ 'field_group' => $field_group ];


				if ( 'acf_after_title' == $context ) {
					$context = 'normal';
				} elseif ( 'side' == $context ) {
					$priority = 'core';
				}

				// Filter for third party customization.
				$priority = apply_filters( 'acf/input/meta_box_priority', $priority, $field_group );

				add_meta_box( $id, acf_esc_html( $title ), [ $this, 'postbox_acf' ], 'acf_options_page', $context, $priority, $args );
			}
		}
	}

	/**
	 * Postbox submit div
	 *
	 * This function will render the submitdiv metabox
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $post
	 * @param  array $args
	 * @return void
	 */
	public function postbox_submitdiv( $post, $args ) {

		// Fires before the major-publishing-actions div.
		do_action( 'acf/options_page/submitbox_before_major_actions', $this->page );
		?>
		<div id="major-publishing-actions">
			<div id="publishing-action">
				<span class="spinner"></span>
				<input type="submit" accesskey="p" value="<?php echo $this->page['update_button']; ?>" class="button button-primary button-large" id="publish" name="publish">
			</div>

			<?php

			// Fires before the major-publishing-actions div.
			do_action( 'acf/options_page/submitbox_major_actions', $this->page );
			?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Postbox ACF
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $post
	 * @param  array $args
	 * @return void
	 */
	public function postbox_acf( $post, $args ) {

		// All variables from the add_meta_box function.
		extract( $args );

		// All variables from the args argument.
		extract( $args );

		$o = [
			'id'         => $id,
			'key'        => $field_group['key'],
			'style'      => $field_group['style'],
			'label'      => $field_group['label_placement'],
			'editLink'   => '',
			'editTitle'  => __( 'Edit field group', 'acf' ),
			'visibility' => true
		];

		if ( $field_group['ID'] && acf_current_user_can_admin() ) {
			$o['editLink'] = admin_url( 'post.php?post=' . $field_group['ID'] . '&action=edit' );
		}

		$fields = acf_get_fields( $field_group );
		acf_render_fields( $fields, $this->page['post_id'], 'div', $field_group['instruction_placement'] );

		?>
		<script type="text/javascript">
		if( typeof acf !== 'undefined' ) {
			acf.newPostbox(<?php echo json_encode($o); ?>);
		}
		</script>
		<?php
	}

	/**
	 * HTML
	 *
	 * The markup output of the page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {
		acf_get_view( dirname(__FILE__) . '/views/html-options-page.php', $this->page );
	}
}
acf_new_instance( 'ACF_Admin_Options_Page' );
