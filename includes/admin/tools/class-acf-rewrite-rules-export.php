<?php
/**
 * Rewrite rules export tool
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

final class ACF_Rewrite_Rules_Export extends ACF_Admin_Tool {

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		$this->name  = 'ACF_Rewrite_Rules_Export';
		$this->title = __( 'Export Rewrite Rules', 'acf' );
		if ( $this->is_active() ) {
			$this->title = __( 'Generated PHP: Rewrite Rules', 'acf' );
		}
		$this->default_action  = 'php';
		$this->allowed_actions = [ 'php' ];
	}

	/**
	 * Load
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		if ( ! $this->is_active() ) {
			return;
		}

		$this->action = $this->get_action();
		$this->data   = $this->get_data();

		if ( 'php' === $this->action ) {
			if ( ! empty( $this->data ) ) {
				acf_add_admin_notice( __( 'Rewrite rules exported.', 'acf' ), 'success' );
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
	 * HTML archive
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_archive() {

		?>
		<p><?php _e( 'Provides an array of all rewrite rules in the database.' ); ?></p>

		<?php
		$rewrite_rules = $GLOBALS['wp_rewrite']->wp_rewrite_rules();
		if ( ! $rewrite_rules ) {
			$disabled = 'disabled="disabled"';
		} else {
			$disabled = '';
		} ?>
		<p class="acf-submit">
			<button type="submit" name="action" class="button" value="php" <?php echo $disabled; ?>><?php _e( 'Generate PHP', 'acf' ); ?></button>
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
				<?php

				$str_replace = [
					"  "         => "\t",
					"'!!__(!!\'" => "__('",
					"!!\', !!\'" => "', '",
					"!!\')!!'"   => "')",
					"array ("    => "array("
				];

				$preg_replace = [
					'/([\t\r\n]+?)array/' => 'array',
					'/[0-9]+ => array/'   => 'array'
				];

				?>
				<div id="acf-admin-tool-export">
					<textarea id="acf-export-textarea" readonly="true"><?php

					$code = var_export( $this->data, true );

					// Change double spaces to tabs.
					$code = str_replace( array_keys( $str_replace ), array_values( $str_replace ), $code );

					// Correctly formats "=> array(".
					$code = preg_replace( array_keys( $preg_replace ), array_values( $preg_replace ), $code );

					// Preceding comment.
					$comment = '// All rewrite rules' . "\n";

					// Escape textarea.
					$esc_code = esc_textarea( $comment . $code );
					echo $esc_code;

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
							var copy = document.execCommand('copy');
							if(!copy)
								return;
							acf.newTooltip({
								text:       "<?php _e('Copied', 'acf' ); ?>",
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

		$this->action = $this->get_action();
		$this->data   = $this->get_data();

		if ( 'php' === $this->action ) {

			$url = add_query_arg(
				[ 'action' => 'php' ],
				$this->get_url()
			);
			wp_redirect( $url );
		}
		exit;
	}

	/**
	 * Get export data
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_data() {

		$rewrite_rules = $GLOBALS['wp_rewrite']->wp_rewrite_rules();
		return $rewrite_rules;
	}

	/**
	 * Get tool action
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function get_action() {
		return 'php';
	}
}
acf_register_admin_tool( 'ACF_Rewrite_Rules_Export' );
