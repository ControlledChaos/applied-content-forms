<?php
/**
 * ACF module export tool parent class
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

class ACF_Module_Export extends ACF_Admin_Tool {

	/**
	 * Tool instance
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object
	 */
	public $instance;

	/**
	 * Tool action
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action;

	/**
	 * Tool data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data = [];

	/**
	 * Tool description
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $description;

	/**
	 * Selection
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $select;

	/**
	 * Default action
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $default_action;

	/**
	 * Allowed actions
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $allowed_actions = [];

	/**
	 * JSON file
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $file;

	/**
	 * JSON files
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $files;

	/**
	 * Messages
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $messages = [];

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
	 * Archive metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_archive() {

		$choices = $this->instance->export_choices();

		?>
		<p><?php echo $this->description; ?></p>
		<div class="acf-fields">
			<?php
			if ( ! empty( $choices ) ) {
				acf_render_field_wrap( [
					'label'   => $this->select,
					'type'    => 'checkbox',
					'name'    => 'keys',
					'prefix'  => false,
					'value'   => false,
					'toggle'  => true,
					'choices' => $choices
				] );
			} else {
				printf(
					'<div style="padding:1rerm .75rem;">%s</div>',
					$this->messages['not_found']
				);
			}

			?>
		</div>
		<?php $disabled = empty( $choices ) ? 'disabled="disabled"' : ''; ?>
		<p class="acf-submit">
			<?php if ( in_array( 'json', $this->allowed_actions ) ) { ?>
				<button type="submit" name="action" class="button button-primary" value="json" <?php echo $disabled; ?>><?php _e( 'Export File', 'acf' ); ?></button>
			<?php } ?>
			<?php if ( in_array( 'php', $this->allowed_actions ) ) { ?>
				<button type="submit" name="action" class="button" value="php" <?php echo $disabled; ?>><?php _e( 'Generate PHP', 'acf' ); ?></button>
			<?php } ?>

		</p>
		<?php
	}

	/**
	 * Single metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html_single() {

		?>
		<div class="acf-postbox-columns">
			<div class="acf-postbox-main">

				<p><?php _e( "You can copy and paste the following code to your theme's functions.php file or include it within an external file.", 'acf' ); ?></p>

				<div id="acf-admin-tool-export">
					<textarea id="acf-export-textarea" readonly="true"><?php $this->instance->export_php( $this->data ); ?></textarea>
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
	 * Run the tool
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

		if ( 'json' === $this->action ) {
			$this->submit();
		} elseif ( 'php' === $this->action ) {

			// Add notice.
			if ( ! empty( $this->data ) ) {
				$count = count( $this->data );
				$text  = sprintf(
					_n( $this->messages['success_single'], $this->messages['success_multiple'], $count, 'acf' ),
					$count
				);
				acf_add_admin_notice( $text, 'success' );
			}
		}
	}

	/**
	 * Submit tool action
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function submit() {

		$this->action = $this->get_action();
		$this->data   = $this->get_data();
		$keys = array_keys( $this->data );

		if ( ! $this->data ) {
			return acf_add_admin_notice( $this->messages['not_selected'], 'warning' );
		}

		if ( 'json' === $this->action ) {

			$prefix = ( count( $keys ) > 1 ) ? $this->file : $this->files;
			$slugs  = implode( '-', $keys );
			$date   = date( 'Y-m-d' );

			$file_name = 'acfe-export-' .  $prefix  . '-' . $slugs . '-' .  $date . '.json';

			header( 'Content-Description: File Transfer' );
			header( "Content-Disposition: attachment; filename={$file_name}" );
			header( 'Content-Type: application/json; charset=utf-8' );

			echo acf_json_encode( $this->data );
		} elseif ( 'php' === $this->action ) {

			$url = add_query_arg(
				[
					'keys'   => implode( '+', $keys ),
					'action' => 'php'
				],
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

		$keys = $this->get_keys();
		$data = [];

		foreach ( $keys as $key ) {
			$args = $this->instance->export_data( $key );
			if ( ! $args ) {
				continue;
			}
			$data[$key] = $args;
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
	public function get_keys() {

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
	public function get_action() {

		$default = $this->default_action;
		$action  = acfe_maybe_get_REQUEST( 'action', $default );

		if ( ! in_array( $action, $this->allowed_actions ) ) {
			$action = $default;
		}
		return $action;
	}
}
