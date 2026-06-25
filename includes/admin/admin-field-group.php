<?php
/**
 * ACF Admin Field Group
 *
 * All the logic for editing a field group.
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

class acf_admin_field_group {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Actions.
		add_action( 'current_screen', [ $this, 'current_screen' ] );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );


		// AJAX.
		add_action( 'wp_ajax_acf/field_group/render_field_settings', [ $this, 'ajax_render_field_settings' ] );
		add_action( 'wp_ajax_acf/field_group/render_location_rule', [ $this, 'ajax_render_location_rule' ] );
		add_action( 'wp_ajax_acf/field_group/move_field', [ $this, 'ajax_move_field' ] );


		// Filters.
		add_filter( 'post_updated_messages', [ $this, 'post_updated_messages' ] );
		add_filter( 'use_block_editor_for_post_type', [ $this, 'use_block_editor_for_post_type' ], 10, 2 );
	}

	/**
	 * Use block editor for post type
	 *
	 * Prevents the block editor from loading when editing an ACF field group.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $use_block_editor
	 * @param  string $post_type
	 * @return boolean
	 */
	public function use_block_editor_for_post_type( $use_block_editor, $post_type ) {
		if ( 'acf-field-group' === $post_type ) {
			return false;
		}
		return $use_block_editor;
	}

	/**
	 * Post updated messages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {

		// Append to messages.
		$messages['acf-field-group'] = [
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Field group updated.', 'acf' ),
			2  => __( 'Field group updated.', 'acf' ),
			3  => __( 'Field group deleted.', 'acf' ),
			4  => __( 'Field group updated.', 'acf' ),
			5  => false, // Field group does not support revisions.
			6  => __( 'Field group published.', 'acf' ),
			7  => __( 'Field group saved.', 'acf' ),
			8  => __( 'Field group submitted.', 'acf' ),
			9  => __( 'Field group scheduled for.', 'acf' ),
			10 => __( 'Field group draft updated.', 'acf' )
		];
		return $messages;
	}

	/**
	 * Current screen
	 *
	 *
	 * This is fired when loading the admin page before HTML has been rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function current_screen() {

		// Validate screen.
		if ( ! acf_is_screen( 'acf-field-group' ) ) {
			return;
		}

		// Disable filters to ensure ACF loads raw data from DB.
		acf_disable_filters();

		// Enqueue scripts.
		acf_enqueue_scripts();

		// Actions.
		add_action( 'acf/input/admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'acf/input/admin_head', [ $this, 'admin_head' ] );
		add_action( 'acf/input/form_data', [ $this, 'form_data' ] );
		add_action( 'acf/input/admin_footer', [ $this, 'admin_footer' ] );

		// Filters.
		add_filter( 'acf/input/admin_l10n', [ $this, 'admin_l10n' ] );
	}

	/**
	 * Admin enqueue scripts
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		wp_dequeue_script( 'autosave' );

		// Custom scripts.
		wp_enqueue_style( 'acf-field-group' );
		wp_enqueue_script( 'acf-field-group' );
		wp_enqueue_script( 'acf-pro-field-group' );
		wp_enqueue_style( 'acf-pro-field-group' );

		// Localize text.
		acf_localize_text( [
			'The string "field_" may not be used at the start of a field name' => __( 'The string "field_" may not be used at the start of a field name', 'acf' ),
			'This field cannot be moved until its changes have been saved' => __( 'This field cannot be moved until its changes have been saved', 'acf' ),
			'Field group title is required' => __( 'Field group title is required', 'acf' ),
			'Move to trash. Are you sure?'  => __( 'Move to trash. Are you sure?', 'acf' ),
			'No toggle fields available' => __( 'No toggle fields available', 'acf' ),
			'Move Custom Field' => __( 'Move Custom Field', 'acf' ),
			'Checked'      => __( 'Checked', 'acf' ),
			'(no label)'   => __( '(no label)', 'acf' ),
			'(this field)' => __( '(this field)', 'acf' ),
			'copy'         => __( 'copy', 'acf' ),
			'or'           => __( 'or', 'acf' ),
			'Null'         => __( 'Null', 'acf' ),

			// Conditions
			'Has any value'				=> __( 'Has any value', 'acf' ),
			'Has no value'				=> __( 'Has no value', 'acf' ),
			'Value is equal to'			=> __( 'Value is equal to', 'acf' ),
			'Value is not equal to'		=> __( 'Value is not equal to', 'acf' ),
			'Value matches pattern'		=> __( 'Value matches pattern', 'acf' ),
			'Value contains'			=> __( 'Value contains', 'acf' ),
			'Value is greater than'		=> __( 'Value is greater than', 'acf' ),
			'Value is less than'		=> __( 'Value is less than', 'acf' ),
			'Selection is greater than'	=> __( 'Selection is greater than', 'acf' ),
			'Selection is less than'	=> __( 'Selection is less than', 'acf' ),
		] );

		// Localize data.
		acf_localize_data( [
		   	'fieldTypes' => acf_get_field_types_info()
	   	] );

		// Thirrd party hook.
		do_action( 'acf/field_group/admin_enqueue_scripts' );
	}

	/**
	 * Admin head
	 *
	 * This function will setup all functionality for the
	 * field group edit page to work.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global integer $field_group
	 * @global object $post
	 * @return void
	 */
	public function admin_head() {

		// Access global variables.
		global $post, $field_group;

		// Set global variable.
		$field_group = acf_get_field_group( $post->ID );

		// Metaboxes.
		add_meta_box( 'acf-field-group-fields', __( 'Fields', 'acf' ), [ $this, 'mb_fields' ], 'acf-field-group', 'normal', 'high' );
		add_meta_box( 'acf-field-group-locations', __( 'Location', 'acf' ), [ $this, 'mb_locations' ], 'acf-field-group', 'normal', 'high' );
		add_meta_box( 'acf-field-group-options', __( 'Settings', 'acf' ), [ $this, 'mb_options' ], 'acf-field-group', 'normal', 'high' );

		// Actions.
		add_action( 'post_submitbox_misc_actions', [ $this, 'post_submitbox_misc_actions' ], 10, 0 );
		add_action( 'edit_form_after_title', [ $this, 'edit_form_after_title' ], 10, 0 );

		// Filters.
		add_filter( 'screen_settings', [ $this, 'screen_settings' ], 10, 1 );

		// Third party hook.
		do_action( 'acf/field_group/admin_head' );
	}

	/**
	 * Edit form - after title
	 *
	 * This action will allow ACF to render metaboxes after the title.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $post
	 * @return void
	 */
	public function edit_form_after_title() {

		// Access global variables.
		global $post;

		// Render post data.
		acf_form_data( [
			'screen'        => 'field_group',
			'post_id'       => $post->ID,
			'delete_fields' => 0,
			'validation'    => 0
		] );
	}

	/**
	 * Form data
	 *
	 * This will add extra HTML to the acf form data element.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @return void
	 */
	public function form_data( $args ) {
		do_action( 'acf/field_group/form_data', $args );
	}

	/**
	 * Admin l10n
	 *
	 * This function will append extra l10n strings to the acf JS object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $l10n
	 * @return void
	 */
	public function admin_l10n( $l10n ) {
		return apply_filters( 'acf/field_group/admin_l10n', $l10n );
	}

	/**
	 * Admin footer
	 *
	 * Third party hook.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_footer() {
		do_action( 'acf/field_group/admin_footer' );
	}

	/**
	 * Screen settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $html
	 * @return string
	 */
	public function screen_settings( $html ) {

		if ( acf_get_user_setting( 'show_field_keys' ) ) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		$html .= '<div id="acf-append-show-on-screen" class="acf-hidden">';
		$html .= sprintf(
			'<label for="acf-field-key-hide"><input id="acf-field-key-hide" type="checkbox" value="1" name="show_field_keys" %s /> %s</label>',
			$checked,
			__( 'Field Keys','acf' )
		);
		$html .= '</div>';

		return $html;
	}

	/**
	 * Submit box misc. actions
	 *
	 * This function will customize the publish metabox
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $field_group
	 * @return void
	 */
	public function post_submitbox_misc_actions() {

		// Access global variables.
		global $field_group;

		if ( $field_group['active'] ) {
			$status_label = _x( 'Active', 'post status', 'acf' );
		} else {
			$status_label = _x( 'Inactive', 'post status', 'acf' );
		}

		?>
		<script type="text/javascript">(function($) { $( '#post-status-display' ).html( '<?php echo esc_html( $status_label ); ?>' ); } )(jQuery);</script>
		<?php
	}

	/**
	 * Save post
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @param  object $post
	 * @return integer
	 */
	public function save_post( $post_id, $post ) {

		// Do not save if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Stop if not acf-field-group.
		if ( 'acf-field-group' !== $post->post_type ) {
			return $post_id;
		}

		// Only save once. The CMS saves a revision as well.
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		// Verify nonce.
		if ( ! acf_verify_nonce( 'field_group' ) ) {
			return $post_id;
		}

		// Stop if request came from an unauthorised user.
		if ( ! current_user_can( acf_get_setting( 'capability' ) ) ) {
			return $post_id;
		}

		// Disable filters to ensure ACF loads raw data from DB.
		acf_disable_filters();

		// Save fields.
		if ( ! empty( $_POST['acf_fields'] ) ) {

			foreach ( $_POST['acf_fields'] as $field ) {

				$specific = false;
				$save     = acf_extract_var( $field, 'save' );


				// Only save field if it has changed.
				if ( 'meta' == $save ) {
					$specific = [
						'menu_order',
						'post_parent'
					];
				}

				// Set parent.
				if ( ! $field['parent'] ) {
					$field['parent'] = $post_id;
				}

				// Save field.
				acf_update_field( $field, $specific );
			}
		}

		// Delete fields.
		if ( $_POST['_acf_delete_fields'] ) {

			$ids = explode( '|', $_POST['_acf_delete_fields'] );
			$ids = array_map( 'intval', $ids );

			foreach ( $ids as $id ) {

				if ( ! $id ) {
					continue;
				}
				acf_delete_field( $id );
			}
		}

		$_POST['acf_field_group']['ID']    = $post_id;
		$_POST['acf_field_group']['title'] = $_POST['post_title'];

		acf_update_field_group( $_POST['acf_field_group'] );
		return $post_id;
	}

	/**
	 * Metabox fields
	 *
	 * This function will render the HTML for the metabox 'acf-field-group-fields'
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $field_group
	 * @return void
	 */
	public function mb_fields() {

		// Access global variables.
		global $field_group;

		// Get fields.
		$view = [
			'fields' => acf_get_fields( $field_group ),
			'parent' => 0
		];
		acf_get_view( 'field-group-fields', $view );
	}

	/**
	 * Metabox options
	 *
	 * This function will render the HTML for the
	 * metabox 'acf-field-group-options'
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $field_group
	 * @return void
	 */
	public function mb_options() {

		// Access global variables.
		global $field_group;

		// Field key (leave in for compatibility).
		if ( ! acf_is_field_group_key( $field_group['key'] ) ) {
			$field_group['key'] = uniqid( 'group_' );
		}
		acf_get_view( 'field-group-options' );
	}

	/**
	 * Metabox locations
	 *
	 * This function will render the HTML for the
	 * metabox 'acf-field-group-locations'
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $field_group
	 * @return void
	 */
	public function mb_locations() {

		// Access global variables.
		global $field_group;

		// UI needs at lease one location rule.
		if ( empty( $field_group['location'] ) ) {

			$field_group['location'] = [
				// Group 0
				[
					// Rule 0
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post'
					]
				]
			];
		}
		acf_get_view( 'field-group-locations' );
	}

	/**
	 * AJAX render location rule
	 *
	 * This function can be accessed via an AJAX action and will return the result from the render_location_value function
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function ajax_render_location_rule() {

		if ( ! acf_verify_ajax() ) {
			die();
		}

		$rule = acf_validate_location_rule( $_POST['rule'] );

		acf_get_view( 'html-location-rule', [
			'rule' => $rule
		] );
		die();
	}

	/**
	 * AJAX render field settings
	 *
	 * This function will return HTML containing the field's
	 * settings based on its new type.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function ajax_render_field_settings() {

		if ( ! acf_verify_ajax() ) {
			die();
		}

		$field = acf_maybe_get_POST( 'field' );
		if ( ! $field ) {
			die();
		}

		// Set prefix.
		$field['prefix'] = acf_maybe_get_POST( 'prefix' );
		$field = acf_get_valid_field( $field );

		do_action( "acf/render_field_settings/type={$field['type']}", $field );
		die();
	}

	/**
	 * AJAX move field
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function ajax_move_field() {

		// Disable filters to ensure ACF loads raw data from DB.
		acf_disable_filters();

		$args = acf_parse_args( $_POST, [
			'nonce'          => '',
			'post_id'        => 0,
			'field_id'       => 0,
			'field_group_id' => 0
		] );

		if ( ! wp_verify_nonce( $args['nonce'], 'acf_nonce' ) ) {
			die();
		}

		if ( $args['field_id'] && $args['field_group_id'] ) {

			$field = acf_get_field( $args['field_id'] );
			$field_group     = acf_get_field_group( $args['field_group_id'] );
			$field['parent'] = $field_group['ID'];


			// Remove conditional logic.
			$field['conditional_logic'] = 0;

			// Update field.
			acf_update_field( $field );

			// Output HTML.
			$link = '<a href="' . admin_url( 'post.php?post=' . $field_group['ID'] . '&action=edit' ) . '" target="_blank">' . esc_html( $field_group['title'] ) . '</a>';

			echo '' .
				'<p><strong>' . __( 'Move Complete.', 'acf' ) . '</strong></p>' .
				'<p>' . sprintf(
					acf_punctify( __( 'The %s field can now be found in the %s field group', 'acf' ) ),
					esc_html( $field['label'] ),
					$link
				). '</p>' .
				'<a href="#" class="button button-primary acf-close-popup">' . __( 'Close Window', 'acf' ) . '</a>';
			die();
		}

		// Get all field groups.
		$field_groups = acf_get_field_groups();
		$choices      = [];

		if ( ! empty( $field_groups ) ) {

			foreach ( $field_groups as $field_group ) {

				// Stop if no ID.
				if ( ! $field_group['ID'] ) {
					continue;
				}

				// Stop if is current.
				if ( $field_group['ID'] == $args['post_id'] ) {
					continue;
				}

				$choices[ $field_group['ID'] ] = $field_group['title'];
			}
		}

		$field = acf_get_valid_field( [
			'type'    => 'select',
			'name'    => 'acf_field_group',
			'choices' => $choices
		] );

		printf(
			'<p>%s</p>',
			__( 'Please select the destination for this field.', 'acf' )
		);
		echo '<form id="acf-move-field-form">';
			acf_render_field_wrap( $field );
			printf(
				'<button type="submit" class="button button-primary">%s</button>',
				__( 'Move Field' ,'acf' )
			);
		echo '</form>';

		die();
	}
}
acf_new_instance( 'acf_admin_field_group' );
