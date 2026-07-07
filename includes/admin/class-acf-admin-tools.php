<?php
/**
 * ACF admin tools screen
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

class ACF_Admin_Tools {

	/**
	 * Tools
	 *
	 * Contains an array of admin tool instances.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $tools = [];

	/**
	 * The active tool
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $active = '';


	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Register tool
	 *
	 * This function will store a tool tool class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $class
	 * @return void
	 */
	public function register_tool( $class ) {
		$instance = new $class();
		$this->tools[ $instance->name ] = $instance;
	}

	/**
	 * Get tool
	 *
	 * This function will return a tool tool class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function get_tool( $name ) {
		return isset( $this->tools[$name] ) ? $this->tools[$name] : null;
	}

	/**
	 * Get tools
	 *
	 * This function will return an array of all tools.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return array
	 */
	public function get_tools() {
		return $this->tools;
	}

	/**
	 * Admin menu
	 *
	 * This function will add the ACF menu item to the CMS admin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		// Stop if no show_admin.
		if ( ! acf_get_setting( 'show_admin' ) ) {
			return;
		}

		$page = add_submenu_page(
			acf()->admin_slug(),
			__( 'Content Tools', 'acf' ),
			__( 'Tools', 'acf' ),
			acf_get_setting( 'capability' ),
			acf()->admin_slug() . '-tools',
			[ $this, 'html' ]
		);
		add_action( 'load-' . $page, [ $this, 'load' ] );
	}

	/**
	 * Load tools
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {
		acf_disable_filters();
		$this->include_tools();
		$this->check_submit();
		acf_enqueue_scripts();
	}

	/**
	 * Include tools
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function include_tools() {

		acf_include( 'includes/admin/tools/class-acf-admin-tool.php' );
		acf_include( 'includes/admin/tools/class-acf-native-export-import.php' );
		acf_include( 'includes/admin/tools/class-acf-module-export.php' );
		acf_include( 'includes/admin/tools/class-acf-module-import.php' );
		acf_include( 'includes/admin/tools/class-acf-field-groups-export.php' );
		acf_include( 'includes/admin/tools/class-acf-field-groups-import.php' );
		acf_include( 'includes/admin/tools/class-acf-local-field-groups-export.php' );
		acf_include( 'includes/admin/tools/class-acf-post-types-export.php' );
		acf_include( 'includes/admin/tools/class-acf-post-types-import.php' );
		acf_include( 'includes/admin/tools/class-acf-taxonomies-export.php' );
		acf_include( 'includes/admin/tools/class-acf-taxonomies-import.php' );
		acf_include( 'includes/admin/tools/class-acf-block-types-export.php' );
		acf_include( 'includes/admin/tools/class-acf-block-types-import.php' );
		acf_include( 'includes/admin/tools/class-acf-forms-export.php' );
		acf_include( 'includes/admin/tools/class-acf-forms-import.php' );
		acf_include( 'includes/admin/tools/class-acf-options-pages-export.php' );
		acf_include( 'includes/admin/tools/class-acf-options-pages-import.php' );
		acf_include( 'includes/admin/tools/class-acf-templates-export.php' );
		acf_include( 'includes/admin/tools/class-acf-templates-import.php' );
		acf_include( 'includes/admin/tools/class-acf-rewrite-rules-export.php' );

		do_action( 'acf/include_admin_tools' );
	}

	/**
	 * Check submit
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function check_submit() {

		foreach ( $this->get_tools() as $tool ) {
			$tool->load();

			if ( acf_verify_nonce( $tool->name ) ) {
				$tool->submit();
			}
		}
	}

	/**
	 * HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function html() {

		$screen = get_current_screen();
		$active = acf_maybe_get_GET( 'tool' );

		$view = [
			'screen_id' => $screen->id,
			'active'    => $active
		];

		foreach ( $this->get_tools() as $tool ) {

			if ( $active && $active !== $tool->name ) {
				continue;
			}

			add_meta_box(
				'acf-admin-tool-' . $tool->name,
				acf_esc_html( $tool->title ),
				[ $this, 'metabox_html' ],
				$screen->id,
				'normal',
				'default',
				[ 'tool' => $tool->name ]
			);
		}
		acf_get_view( 'html-admin-tools', $view );
	}

	/**
	 * Metabox HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $post
	 * @param  array $metabox
	 * @return void
	 */
	public function metabox_html( $post, $metabox ) {

		$tool = $this->get_tool( $metabox['args']['tool'] );

		?>
		<form method="post" class="acf-admin-tool-form">
			<?php $tool->html(); ?>
			<?php acf_nonce_input( $tool->name ); ?>
		</form>
		<?php
	}
}
acf()->admin_tools = new ACF_Admin_Tools();

/**
 * Register admin tool
 *
 * Alias of acf()->admin_tools->register_tool().
 *
 * @since  1.0.0
 * @param  string $class
 * @return string
 */
function acf_register_admin_tool( $class ) {
	return acf()->admin_tools->register_tool( $class );
}

/**
 * Get admin tools URL
 *
 * This function will return the admin URL to the tools page.
 *
 * @since  1.0.0
 * @return string
 */
function acf_get_admin_tools_url() {
	return admin_url( 'admin.php?page=acf-tools' );
}

/**
 * Get admin tools URL
 *
 * This function will return the admin URL to the tools page.
 *
 * @since  1.0.0
 * @param  string $tool
 * @return string
 */
function acf_get_admin_tool_url( $tool = '' ) {
	return acf_get_admin_tools_url() . '&tool=' . $tool;
}
