<?php
/**
 * Content settings fields
 *
 * Sets up the fields for the settings tab on the main content page.
 *
 * @package    ACF
 * @subpackage Admin
 * @category   Settings
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acfe_pro_admin_settings' ) ) :

class acfe_pro_admin_settings{

	/**
	 * Default settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $defaults = [];

	/**
	 * Updated settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $updated = [];

	/**
	 * Settings fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $fields = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'acfe/admin_settings/load', [ $this, 'load' ] );
		add_action( 'acfe/admin_settings/html', [ $this, 'html' ] );
	}

	/*
	 * Load
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		$acfe_admin_settings = acf_get_instance( 'acfe_admin_settings' );

		$this->defaults = $acfe_admin_settings->defaults;
		$this->updated  = $acfe_admin_settings->updated;
		$this->fields   = $acfe_admin_settings->fields;

		// Enqueue scripts.
		acf_enqueue_scripts();

		// Submit.
		if ( acf_verify_nonce( 'acfe_settings' ) ) {

			// Validate
			if ( acf_validate_save_post( true ) ) {

				$this->save_post();

				// Redirect.
				wp_redirect( add_query_arg( [ 'message' => 'acfe_settings' ] ) );
				exit;
			}
		}

		// Success.
		if ( acf_maybe_get_GET( 'message' ) === 'acfe_settings' ) {
			acf_add_admin_notice( 'Settings Saved.', 'success' );
		}
	}

	/*
	 * Save post
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function save_post() {

		$values = acf_maybe_get_POST( 'acfe_settings', [] );

		foreach ( $values as $name => &$value ) {

			$data = $this->get_setting( $name );

			if ( $data['format'] === 'array' ) {
				if ( empty( $value ) ) {
					$value = [];
				} else {
					$value = explode( ',', $value );
				}
			}
			$value = wp_unslash ( $value );
		}

		// Update settings.
		acfe_update_settings( 'settings', $values );
	}

	/*
	 * Get setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_setting( $name ) {

		foreach ( $this->fields as $category => $rows ) {
			foreach ( $rows as $row) {

				if ( $row['name'] !== $name ) {
					continue;
				}

				$setting = $row;
				break;
			}
		}
		return $this->validate_setting( $setting );
	}

	/**
	 * Validate setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function validate_setting( $setting ) {

		$setting = wp_parse_args( $setting, [
			'label'       => '',
			'name'        => '',
			'type'        => '',
			'description' => '',
			'category'    => '',
			'format'      => '',
			'default'     => '',
			'updated'     => '',
			'value'       => '',
			'class'       => '',
			'buttons'     => '',
			'diff'        => false
		] );

		return $setting;
	}

	/**
	 * Prepare setting
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function prepare_setting( $setting ) {

		$settings = acfe_get_settings( 'settings' );
		$name     = $setting['name'];
		$type     = $setting['type'];
		$format   = $setting['format'];
		$default  = $this->defaults[$name];
		$updated  = $this->updated[$name];
		$vars     = [
			'default' => $default,
			'updated' => $updated
		];

		foreach ( $vars as $v => $var ) {

			$result = $var;

			if ( $type === 'true_false' ) {

				$result = $var ? '<span class="dashicons dashicons-saved"></span>' : '<span class="dashicons dashicons-no-alt"></span>';

			} elseif ( $type === 'text' ) {

				$result = '<span class="dashicons dashicons-no-alt"></span>';

				if ( $format === 'array' && empty( $var ) && $v === 'updated' && $default !== $updated ) {
					$var = [ '( empty)' ];
				}

				if ( ! empty( $var ) ) {

					if ( ! is_array( $var ) ) {
						$var = explode( ',', $var );
					}

					foreach ( $var as &$r ) {
						$r = '<div class="acf-js-tooltip acfe-settings-text" title="' . $r . '"><code>' . $r . '</code></div>';
					}
					$result = implode( '', $var );
				}
			}
			$setting[$v] = $result;
		}

		// Local changes.
		if ( $default !== $updated && $updated !== acfe_get_settings( "settings.{$name}" ) ) {

			$setting['updated'] .= '<span style="color:#888; margin-left:7px;vertical-align: 6px;font-size:11px;">(Local code )</span>';
			$setting['diff'] = true;

		}

		// Value.
		$button_edit = $button_default = $class = '';
		$value       = acf_maybe_get( $settings, $name );

		// Value exists.
		if ( $value !== null ) {
			$button_edit     = 'acf-hidden';
			$setting['diff'] = true;
		} else {
			$button_default = 'acf-hidden';
			$class = 'acf-hidden acfe-disabled';
			$value = $this->defaults[$name];
		}

		if ( is_array( $value ) ) {
			$value = implode( ',', $value );
		}

		$setting['value']   = $value;
		$setting['class']   = $class;
		$setting['buttons'] = '<a href="#" class="' . $button_default . '" data-acfe-settings-action="default" data-acfe-settings-field="' . $name . '" style="margin-left:2px; padding:6px 0;display:block;">Default</a><a href="#" class="acf-button button ' . $button_edit . '" data-acfe-settings-action="edit" data-acfe-settings-field="' . $name . '">Edit</a>';

		return $setting;
	}

	/**
	 * Tab markup
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {

	?>
	<div id="acfe-admin-settings">
		<form id="post" method="post" name="post">
			<?php

			// Render post data
			acf_form_data( [
				'screen' => 'acfe_settings',
			] );

			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<!-- Sidebar -->
					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">
							<div id="submitdiv" class="postbox">
								<div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e( 'Publish', 'acf' ); ?></h2></div>
								<div class="inside">

									<div id="minor-publishing">

										<div id="misc-publishing-actions">
											<?php if ( acf_get_setting( 'show_admin' ) ) : ?>
											<div class="misc-pub-section acfe-misc-export">
												<span class="dashicons dashicons-editor-code"></span>
												<?php _e( 'Export:', 'acf' ); ?>
												<a href="<?php echo admin_url("admin.php?page=acf-tools&tool=acfe_settings_export&action=php"); ?>"><?php _e( 'PHP', 'acf' ); ?></a>
												<a href="<?php echo admin_url("admin.php?page=acf-tools&tool=acfe_settings_export&action=json"); ?>"><?php _e( 'JSON', 'acf' ); ?></a>
											</div>
											<?php endif; ?>
										</div>
									</div>
									<div id="major-publishing-actions">
										<div id="publishing-action">
											<span class="spinner"></span>
											<input type="submit" accesskey="p" value="<?php _e( 'Update', 'acf' ); ?>" class="button button-primary button-large" id="publish" name="publish">
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Metabox -->
					<div id="postbox-container-2" class="postbox-container">

						<div class="postbox acf-postbox">
							<div class="inside acf-fields -left">

								<?php $this->render_fields(); ?>

								<script type="text/javascript">
									if ( typeof acf !== 'undefined' ) {
										acf.newPostbox( {
											'id'    : 'acfe-settings',
											'label' : 'left'
										} );
									}
								</script>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
	}

	/**
	 * Render fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_fields() {

		// Tabs for field categories.
		$tabs = [
			__( 'Features', 'acf' ),
			__( 'Options', 'acf' ),
			__( 'Theme', 'acf' ),
			__( 'AutoSync', 'acf' ),
			__( 'reCaptcha', 'acf' )
		];

		// Render tabs.
		foreach ( $tabs as $tab ) {

			// Category.
			$category = sanitize_title( $tab );

			if ( isset( $this->fields[$category] ) ) {

				$fields = [];
				$count  = 0;

				foreach ( $this->fields[$category] as $field ) {

					// Prepare.
					$field    = $this->validate_setting( $field );
					$field    = $this->prepare_setting( $field );
					$fields[] = $field;
				}

				foreach ( $fields as $field ) {
					if ( ! $field['diff'] ) {
						continue;
					}
					$count++;

				}

				$class = $count > 0 ? 'acfe-tab-badge' : 'acfe-tab-badge acf-hidden';
				$tab  .= ' <span class="' . $class . '">' . $count . '</span>';

				// Tab.
				acf_render_field_wrap( [
					'type'    => 'tab',
					'label'   => $tab,
					'key'     => 'field_acfe_settings_tabs',
					'wrapper' => [
						'data-no-preference' => true
					]
				] );

				// Table head.
				acf_render_field_wrap( [
					'type'    => 'acfe_dynamic_render',
					'label'   => '',
					'key'     => 'field_acfe_settings_thead_' . $category,
					'wrapper' => [
						'class' => 'acfe-settings-thead'
					],
					'render' => function( $field ) {
						?>
						<div><?php _e( 'Default', 'acf' ); ?></div>
						<div><?php _e( 'Registered', 'acf' ); ?></div>
						<div><?php _e( 'Edit', 'acf' ); ?></div>
						<?php
					}
				] );

				foreach ( $fields as $field ) { ?>

					<div class="acf-field">
						<div class="acf-label">
							<label><span class="acf-js-tooltip dashicons dashicons-info" title="<?php echo $field['name']; ?>"></span><?php echo $field['label']; ?></label>
							<?php if ( $field['description'] ) { ?>
								<p class="description"><?php echo $field['description']; ?></p>
							<?php } ?>
						</div>
						<div class="acf-input">

							<div><?php echo $field['default']; ?></div>
							<div><?php echo $field['updated']; ?></div>
							<div>
								<div><?php echo $field['buttons']; ?></div>
								<div>
									<?php
									acf_render_field_wrap( [
										'instructions' => '',
										'type'         => $field['type'],
										'ui'           => true,
										'key'          => $field['name'],
										'name'         => $field['name'],
										'prefix'       => 'acfe_settings',
										'value'        => $field['value'],
										'wrapper'      => [
											'class' => $field['class'],
											'style' => 'margin:0;',
											'data-acfe-settings-field'  => 1
										]
									] );
									?>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
		}
	}
}

// Instantiate the `acfe_pro_admin_settings` class.
acf_new_instance( 'acfe_pro_admin_settings' );

endif; // End if `acfe_pro_admin_settings` class.
