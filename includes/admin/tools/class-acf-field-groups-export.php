<?php
/**
 * Field group export tool
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Tools
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Field_Groups_Export extends ACF_Admin_Tool {

	/**
	 * View context
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $view = '';

	/**
	 * Export data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $json = '';

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		parent :: __construct();
	}

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'export';
		$this->title = __( 'Export Field Groups', 'acf' );

    	if ( $this->is_active() ) {
			$this->title = __( 'Generated PHP: Field Groups', 'acf' );
		}
	}

	/**
	 * Load
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

    	if ( $this->is_active() ) {

	    	$selected = $this->get_selected_keys();
	    	if ( $selected ) {
		    	$count = count( $selected );
		    	$text  = sprintf( _n( 'Exported 1 field group.', 'Exported %s field groups.', $count, 'acf' ), $count );
		    	acf_add_admin_notice( $text, 'success' );
	    	}
		}
	}

	/**
	 * Metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {

		if ( $this->is_active() ) {
			$this->html_single();
		} else {
			$this->html_archive();
		}
	}

	/**
	 * HTML field selection
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_field_selection() {

		$choices      = [];
		$selected     = $this->get_selected_keys();
		$field_groups = acf_get_field_groups();

		if ( $field_groups ) {
			foreach ( $field_groups as $field_group ) {
				$choices[ $field_group['key'] ] = esc_html( $field_group['title'] );
			}
		}

		acf_render_field_wrap( [
			'label'   => __( 'Select Field Groups', 'acf' ),
			'type'    => 'checkbox',
			'name'    => 'keys',
			'prefix'  => false,
			'value'   => $selected,
			'toggle'  => true,
			'choices' => $choices,
		] );
	}

	/**
	 * HTML panel selection
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_panel_selection() {

		?>
		<div class="acf-panel acf-panel-selection">
			<h3 class="acf-panel-title"><?php _e( 'Select Field Groups', 'acf' ) ?> <i class="dashicons dashicons-arrow-right"></i></h3>
			<div class="acf-panel-inside">
				<?php $this->html_field_selection(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * HTML panel settings
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_panel_settings() {

		?>
		<div class="acf-panel acf-panel-settings">
			<h3 class="acf-panel-title"><?php _e( 'Settings', 'acf' ) ?> <span class="dashicons dashicons-arrow-right"></span></h3>
			<div class="acf-panel-inside">
				<?php

				/*
				acf_render_field_wrap(array(
					'label'		=> __('Empty settings', 'acf'),
					'type'		=> 'select',
					'name'		=> 'minimal',
					'prefix'	=> false,
					'value'		=> '',
					'choices'	=> array(
						'all'		=> __('Include all settings', 'acf'),
						'minimal'	=> __('Ignore empty settings', 'acf'),
					)
				));
				*/

				?>
			</div>
		</div>
		<?php
	}

	/**
	 * HTML archive
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_archive() {

		?>
		<p><?php _e( 'Select the field groups you would like to export and then select your export method. Use the download button to export to a .json file which you can then import to another ACF installation. Use the generate button to export to PHP code which you can place in your theme.', 'acf' ); ?></p>
		<div class="acf-fields">
			<?php $this->html_field_selection(); ?>
		</div>
		<p class="acf-submit">
			<button type="submit" name="action" class="button button-primary" value="download"><?php _e( 'Export File', 'acf' ); ?></button>
			<button type="submit" name="action" class="button" value="generate"><?php _e( 'Generate PHP', 'acf' ); ?></button>
		</p>
		<?php
	}

	/**
	 * HTML single
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_single() {

		?>
		<div class="acf-postbox-columns">
			<div class="acf-postbox-main">
				<?php $this->html_generate(); ?>
			</div>
			<div class="acf-postbox-side">
				<?php $this->html_panel_selection(); ?>
				<p class="acf-submit">
					<button type="submit" name="action" class="button button-primary" value="generate"><?php _e( 'Generate PHP', 'acf' ); ?></button>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * HTML generate
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_generate() {

		// Prevent default translation and fake __() within string.
		acf_update_setting( 'l10n_var_export', true );

		$json = $this->get_selected();
		$str_replace = [
			"  "			=> "\t",
			"'!!__(!!\'"	=> "__('",
			"!!\', !!\'"	=> "', '",
			"!!\')!!'"		=> "')",
			"array ("		=> "array("
		];
		$preg_replace = [
			'/([\t\r\n]+?)array/'	=> 'array',
			'/[0-9]+ => array/'		=> 'array'
		];

		?>
		<p><?php _e( "The following code can be used to register a local version of the selected field group(s). A local field group can provide many benefits such as faster load times, version control & dynamic fields/settings. Simply copy and paste the following code to your theme's functions.php file or include it within an external file.", 'acf' ); ?></p>
		<textarea id="acf-export-textarea" readonly="true"><?php

		echo "if( function_exists('acf_add_local_field_group') ):" . "\r\n" . "\r\n";

		foreach ( $json as $field_group ) {

			$code = var_export( $field_group, true );

			// Change double spaces to tabs.
			$code = str_replace( array_keys( $str_replace ), array_values( $str_replace ), $code );

			// Correctly formats "=> array(".
			$code = preg_replace( array_keys( $preg_replace ), array_values( $preg_replace ), $code );

			$code = esc_textarea( $code );

			echo "acf_add_local_field_group({$code});" . "\r\n" . "\r\n";
		}
		echo "endif;";

		?></textarea>
		<p class="acf-submit">
			<a class="button" id="acf-export-copy"><?php _e( 'Copy to clipboard', 'acf' ); ?></a>
		</p>
		<script type="text/javascript">
		(function($){

			var $a = $('#acf-export-copy');
			var $textarea = $('#acf-export-textarea');

			// Remove $a if 'copy' is not supported.
			if( !document.queryCommandSupported('copy') ) {
				return $a.remove();
			}

			$a.on('click', function( e ){
				e.preventDefault();
				$textarea.get(0).select();

				try {
					var copy = document.execCommand('copy');
					if( !copy ) return;

					acf.newTooltip({
						text: 		"<?php _e('Copied', 'acf' ); ?>",
						timeout:	250,
						target: 	$(this),
					});
				} catch (err) {
					// do nothing
				}
			});
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * Get selected keys
	 *
	 * This function will return an array of field group
	 * keys that have been selected.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_selected_keys() {

		if ( $keys = acf_maybe_get_POST( 'keys' ) ) {
			return (array) $keys;
		}
		if ( $keys = acf_maybe_get_GET( 'keys' ) ) {
			$keys = str_replace( ' ', '+', $keys );
			return explode( '+', $keys );
		}
		return false;
	}

	/**
	 * Get selected
	 *
	 * This function will return the JSON data for given $_POST args.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_selected() {

		$selected = $this->get_selected_keys();
		$json     = [];

		// Stop if no keys.
		if ( ! $selected ) {
			return false;
		}

		foreach ( $selected as $key ) {

			$field_group = acf_get_field_group( $key );

			if ( empty( $field_group ) ) {
				continue;
			}

			$field_group['fields'] = acf_get_fields( $field_group );
			$field_group = acf_prepare_field_group_for_export( $field_group );
			$json[] = $field_group;
		}
		return $json;
	}

	/**
	 * Submit
	 *
	 * This function will run when the tool's form has been submit.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function submit() {

		$action = acf_maybe_get_POST( 'action' );
		if ( 'download' === $action ) {
			$this->submit_download();
		} elseif ( 'generate' === $action ) {
			$this->submit_generate();
		}
	}

	/**
	 * Submit download
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function submit_download() {

		$json = $this->get_selected();
		if ( false === $json ) {
			return acf_add_admin_notice( __( 'No field groups selected', 'acf' ), 'warning' );
		}

		$file_name = 'acf-export-' . date( 'Y-m-d' ) . '.json';
		header( 'Content-Description: File Transfer' );
		header( "Content-Disposition: attachment; filename={$file_name}" );
		header( 'Content-Type: application/json; charset=utf-8' );

		echo acf_json_encode( $json );
		die;
	}

	/**
	 * Submit generate
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function submit_generate() {

		$keys = $this->get_selected_keys();
		if ( ! $keys ) {
			return acf_add_admin_notice( __( 'No field groups selected', 'acf' ), 'warning', true, false );
		}
		$url = add_query_arg( 'keys', implode( '+', $keys ), $this->get_url() );

		wp_redirect( $url );
		exit;
	}
}
acf_register_admin_tool( 'ACF_Field_Groups_Export' );
