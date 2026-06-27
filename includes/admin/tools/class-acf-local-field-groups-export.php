<?php
/**
 * Local field group export tool
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

// @todo Finish tool functionality or delete.
return;

final class ACF_Local_Field_Groups_Export extends ACF_Admin_Tool {

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
	function initialize() {

		$this->name  = 'local-fields';
		$this->title = __( 'Export Local Field Groups', 'acf' );
		if ( $this->is_active() ) {
			$this->title = __( 'Generated PHP: Local Field Groups', 'acf' );
		}
	}

	/**
	 * Load
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function load() {

		if ( $ids = acf_maybe_get_GET( 'local-fields-sync' ) ) {

			$ids = explode( ' ', $ids );

			// Count number of imported field groups.
			$total = count( $ids );

			// Generate text.
			$text = sprintf( _n( 'Imported 1 field group', 'Imported %s field groups', $total, 'acf' ), $total );

			// Add links to text.
			$links = [];
			foreach ( $ids as $id ) {
				$links[] = '<a href="' . get_edit_post_link( $id ) . '">' . get_the_title( $id ) . '</a>';
			}
			$text .= ' ' . implode( ', ', $links );

			acf_add_admin_notice( $text, 'success' );
		}

		if ( $this->is_active() ) {
			$data   = $this->get_data();
			$keys   = $this->get_keys();
			$action = $this->get_action();

			if ( empty( $data ) ) {
				return acf_add_admin_notice( __( 'No field group selected', 'acf' ), 'warning' );
			}

			if ( 'json' === $action ) {
				$slugs = implode( '-', $keys );
				$date  = date( 'Y-m-d' );
				$file_name = 'acfe-export-local-' . $slugs . '-' .  $date . '.json';

				header( 'Content-Description: File Transfer' );
				header( "Content-Disposition: attachment; filename={$file_name}" );
				header( 'Content-Type: application/json; charset=utf-8' );

				echo acf_json_encode( $data );
				die;

			} elseif ( 'sync' === $action ) {

				$ids = [];
				foreach ( $data as $field_group ) {

					// Search database for existing field group.
					$post = acf_get_field_group_post( $field_group['key'] );

					if ( $post ) {
						$field_group['ID'] = $post->ID;
					}
					$field_group = acf_import_field_group( $field_group );
					$ids[] = $field_group['ID'];
				}

				$url = add_query_arg( 'local-fields-sync', implode( '+', $ids ), acf_get_admin_tools_url() );

				wp_redirect( $url );
				exit;
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
	function html() {

		if ( $this->is_active() ) {
			$data = $this->get_data();

			?>
			<div class="acf-postbox-columns">
				<div class="acf-postbox-main">
					<?php

					acf_update_setting( 'l10n_var_export', true );

					$str_replace = [
						"  "         => "\t",
						"'!!__(!!\'" => "__( '",
						"!!\', !!\'" => "', '",
						"!!\')!!'"   => "' )",
						"array ("    => "array("
					];

					$preg_replace = [
						'/([\t\r\n]+?)array/'   => 'array',
						'/[0-9]+ => array/'     => 'array'
					];

					?>
					<p><?php _e( "The following code can be used to register a local version of the selected field group(s). A local field group can provide many benefits such as faster load times, version control & dynamic fields/settings. Simply copy and paste the following code to your theme's functions.php file or include it within an external file.", 'acf' ); ?></p>

					<div id="acf-admin-tool-export">
						<textarea id="acf-export-textarea" readonly="true"><?php

						echo "if ( function_exists( 'acf_add_local_field_group' ) ) :" . "\r\n" . "\r\n";

						foreach ( $data as $field_group ) {

							$code = var_export( $field_group, true );

							// Change double spaces to tabs.
							$code = str_replace( array_keys( $str_replace ), array_values( $str_replace), $code );

							// Correctly formats "=> array(".
							$code = preg_replace( array_keys( $preg_replace ), array_values( $preg_replace ), $code );

							$code = esc_textarea( $code );
							echo "acf_add_local_field_group( {$code} );" . "\r\n" . "\r\n";
						}
						echo "endif;";

						?></textarea>
					</div>
					<p class="acf-submit">
						<a class="button" id="acf-export-copy"><?php _e( 'Copy to Clipboard', 'acf' ); ?></a>
					</p>

					<script type="text/javascript">
						(function($){
							var $a = $('#acf-export-copy');
							var $textarea = $('#acf-export-textarea');

							if(!document.queryCommandSupported('copy')){
								return $a.remove();
							}
							$a.on('click', function(e){
								e.preventDefault();
								$textarea.get(0).select();
								try{
									// copy
									var copy = document.execCommand('copy');
									if(!copy)
										return;
									// tooltip
									acf.newTooltip({
										text:       "<?php _e( 'Copied', 'acf' ); ?>",
										timeout:    250,
										target:     $(this),
									});
								}catch(err){
									// do nothing
								}
							});
						})(jQuery);
					</script>
				</div>
			</div>
			<?php
		} else {
			$this->html_archive();
		}
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
		<p><?php _e( 'Local field groups are those which have been regitered by a theme or a plugin using the <code>acf_add_local_field_group()</code> function.', 'acf' ); ?></p>
		<div class="acf-fields">
			<?php // Checkbox selection. ?>
		</div>
		<p class="acf-submit">
			<button type="submit" name="action" class="button button-primary" value="download"><?php _e( 'Export File', 'acf' ); ?></button>
			<button type="submit" name="action" class="button" value="generate"><?php _e( 'Generate PHP', 'acf' ); ?></button>
		</p>
		<?php
	}

	/**
	 * Get data
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_data() {

		$data = [];
		$keys = $this->get_keys();

		if ( ! $keys ) {
			return $data;
		}
		acf_enable_filters();
		acf_disable_filter( 'clone' );

		// Get desync PHP field groups.
		$desync_php_field_groups = acfe_get_desync_php_field_groups();

		foreach ( $desync_php_field_groups as $file_key => $file_path ) {
			require_once( $file_path );
		}

		foreach ( $keys as $field_group_key ) {

			$field_group = acf_get_field_group( $field_group_key );

			if ( empty($field_group ) ) {
				continue;
			}

			$field_group['fields'] = acf_get_fields( $field_group );
			$field_group = acf_prepare_field_group_for_export( $field_group );

			$data[] = $field_group;
		}
		return $data;
	}

	/**
	 * Get selection keys
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_keys() {

		$keys_post = acf_maybe_get_POST( 'keys' );
		$keys_get  = acf_maybe_get_GET( 'keys' );
		$keys      = [];

		if ( $keys_post ) {
			$keys = (array) $keys_post;
		} elseif ( $keys_get ) {
			$keys_get = str_replace( ' ', '+', $keys_get );
			$keys     = explode( '+', $keys_get );
		}
		return $keys;
	}

	/**
	 * Get tool action
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	function get_action() {

		$default = 'json';
		$action  = acfe_maybe_get_REQUEST( 'action', $default );

		if ( ! in_array( $action, [ 'json', 'php', 'sync' ] ) ) {
			$action = $default;
		}
		return $action;
	}
}
acf_register_admin_tool( 'ACF_Local_Field_Groups_Export' );
