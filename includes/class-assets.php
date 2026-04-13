<?php
/**
 * Extend ACF assets class
 *
 * @package    Applied Content Forms
 * @subpackage Extend
 * @category   Core
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Assets {

	/**
	 * Storage for i18n data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $text = [];

	/**
	 * Storage for l10n data
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $data = [];

	/**
	 * List of enqueue flags
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    boolean
	 */
	private $enqueue = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_scripts' ] );
	}

	/**
	 * Magic __call method
	 *
	 * For backwards compatibility.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The method name.
	 * @param  array $arguments The array of arguments.
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		switch ( $name ) {
			case 'admin_enqueue_scripts':
			case 'admin_print_scripts':
			case 'admin_head':
			case 'admin_footer':
			case 'admin_print_footer_scripts':
				_doing_it_wrong( __FUNCTION__, 'The ACF_Assets class should not be accessed directly.', '5.9.0' );
		}
    }

	/**
	 * Append an array of i18n data
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $text An array of text for i18n.
	 * @return void
	 */
	public function add_text( $text ) {
		foreach( (array) $text as $k => $v ) {
			$this->text[ $k ] = $v;
		}
	}

	/**
	 * Append an array of l10n data
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $data An array of data for l10n.
	 * @return void
	 */
	public function add_data( $data ) {
		foreach( (array) $data as $k => $v ) {
			$this->data[ $k ] = $v;
		}
	}

	/**
	 * Register ACF scripts and styles
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function register_scripts() {

		// Extract vars.
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$ver = acf_get_setting( 'version' );

		// Register scripts.
		wp_register_script( 'acf', acf_get_url( "assets/js/acf{$min}.js" ), [ 'jquery' ], $ver );
		wp_register_script( 'acf-input', acf_get_url( "assets/js/acf-input{$min}.js" ), [ 'jquery', 'jquery-ui-sortable', 'jquery-ui-resizable', 'acf' ], $ver );
		wp_register_script( 'acf-field-group', acf_get_url( "assets/js/acf-field-group{$min}.js" ), [ 'acf-input' ], $ver );
		wp_register_script( 'acf-pro-input', acf_get_url( "assets/js/acf-pro-input{$min}.js" ), [ 'acf-input' ], $ver );
		wp_register_script( 'acf-pro-field-group', acf_get_url( "assets/js/acf-pro-field-group{$min}.js" ), [ 'acf-field-group' ], $ver );

		// Register styles.
		wp_register_style( 'acf-global', acf_get_url( 'assets/css/acf-global.css' ), [ 'dashicons' ], $ver );
		wp_register_style( 'acf-input', acf_get_url( 'assets/css/acf-input.css' ), [ 'acf-global' ], $ver );
		wp_register_style( 'acf-field-group', acf_get_url( 'assets/css/acf-field-group.css' ), [ 'acf-input' ], $ver );
		wp_register_style( 'acf-pro-input', acf_get_url( "assets/css/acf-pro-input{$min}.css" ), [ 'acf-input' ], $ver );
		wp_register_style( 'acf-pro-field-group', acf_get_url( "assets/css/acf-pro-field-group{$min}.css" ), [ 'acf-input' ], $ver );

		// Fires after core scripts and styles have been registered.
		do_action( 'acf/register_scripts', $ver, $min );
	}

	/**
	 * Enqueues script
	 *
	 * Enqueues a script and sets up actions for printing supplemental scripts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param string $name The script name.
	 * @return void
	 */
	public function enqueue_script( $name ) {
		wp_enqueue_script( $name );
		$this->add_actions();
	}

	/**
	 * Enqueues style
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name The style name.
	 * @return void
	 */
	public function enqueue_style( $name ) {
		wp_enqueue_style( $name );
	}

	/**
	 * Add action
	 *
	 * Adds the actions needed to print supporting inline scripts.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  void
	 * @return void
	 */
	private function add_actions() {

		// Only run once.
		if ( acf_has_done( 'ACF_Assets::add_actions' ) ) {
			return;
		}

		// Add actions.
		$this->add_action( 'admin_enqueue_scripts', 'enqueue_scripts' , 20 );
		$this->add_action( 'admin_print_scripts', 'print_scripts', 20 );
		$this->add_action( 'admin_print_footer_scripts', 'print_footer_scripts', 20 );
	}

	/**
	 * Add action
	 *
	 * Extends the add_action() function with two additional features:
	 * 1. Renames $action depending on the current page
	 * (customizer, login, front-end).
	 * 2. Alters the priority or calls the method directly if
	 * the action has already passed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $action The action name.
	 * @param  string $method The method name.
	 * @param  integer $priority See add_action().
	 * @param  integer $accepted_args See add_action().
	 * @return void
	 */
	public function add_action( $action, $method, $priority = 10, $accepted_args = 1 ) {

		// Generate an array of action replacements.
		$replacements = [
			'customizer' => [
				'admin_enqueue_scripts' => 'admin_enqueue_scripts',
				'admin_print_scripts'   => 'customize_controls_print_scripts',
				'admin_head'            => 'customize_controls_print_scripts',
				'admin_footer'          => 'customize_controls_print_footer_scripts',
				'admin_print_footer_scripts' => 'customize_controls_print_footer_scripts'
			],
			'login' => [
				'admin_enqueue_scripts' => 'login_enqueue_scripts',
				'admin_print_scripts'   => 'login_head',
				'admin_head'            => 'login_head',
				'admin_footer'          => 'login_footer',
				'admin_print_footer_scripts' => 'login_footer'
			],
			'wp' => [
				'admin_enqueue_scripts'      => 'wp_enqueue_scripts',
				'admin_print_scripts'        => 'wp_print_scripts',
				'admin_head'                 => 'wp_head',
				'admin_footer'               => 'wp_footer',
				'admin_print_footer_scripts' => 'wp_print_footer_scripts'
			]
		];

		// Determine the current context.
		if ( did_action( 'customize_controls_init' ) ) {
			$context = 'customizer';
		} elseif ( did_action( 'login_form_register' ) ) {
			$context = 'login';
		} elseif ( is_admin() ) {
			$context = 'admin';
		} else {
			$context = 'wp';
		}

		// Replace action if possible.
		if ( isset( $replacements[ $context ][ $action ] ) ) {
			$action = $replacements[ $context ][ $action ];
		}

		// Check if action is currently being or has already been run.
		if ( did_action( $action ) ) {
			$doing = acf_doing_action( $action );
			if ( $doing && $doing < $priority ) {
				// Allow action to be added as per usual.
			} else {
				// Call method directly.
				return call_user_func( [ $this, $method ] );
			}
		}

		// Add action.
		add_action( $action, [ $this, $method ], $priority, $accepted_args );
	}

	/**
	 * Enqueue
	 *
	 * Generic controller for enqueuing scripts and styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args {
	 * 		@type bool $uploader Whether or not to enqueue uploader scripts.
	 * }
	 * @return void
	 */
	public function enqueue( $args = [] ) {

		// Apply defaults.
		$args = wp_parse_args( $args, [
			'input'    => true,
			'uploader' => false
		] );

		// Set enqueue flags and add actions.
		if ( $args['input'] ) {
			$this->enqueue[] = 'input';
		}
		if ( $args['uploader'] ) {
			$this->enqueue[] = 'uploader';
		}
		$this->add_actions();
	}

	/**
	 * Enqueue uploader
	 *
	 * Enqueues the scripts and styles needed for the WP media uploader.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function enqueue_uploader() {

		// Only run once.
		if ( acf_has_done( 'ACF_Assets::enqueue_uploader' ) ) {
			return;
		}

		// Enqueue media assets.
		if ( current_user_can( 'upload_files' ) ) {
			wp_enqueue_media();
		}

		// Add actions.
		$this->add_action( 'admin_footer', 'print_uploader_scripts', 1 );

		// Fires when enqueuing the uploader.
		do_action( 'acf/enqueue_uploader' );
	}

	/**
	 * Enqueues and localizes scripts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function enqueue_scripts() {

		// Enqueue input scripts.
		if ( in_array( 'input', $this->enqueue ) ) {
			wp_enqueue_script( 'acf-input' );
			wp_enqueue_style( 'acf-input' );
			wp_enqueue_script( 'acf-pro-input' );
			wp_enqueue_style( 'acf-pro-input' );
		}

		// Enqueue media scripts.
		if ( in_array( 'uploader', $this->enqueue ) ) {
			$this->enqueue_uploader();
		}

		// Localize text.
		acf_localize_text( [

			// Tooltip.
			'Are you sure?' => __( 'Are you sure?','acf' ),
			'Yes'           => __( 'Yes','acf' ),
			'No'            => __( 'No','acf' ),
			'Remove'        => __( 'Remove','acf' ),
			'Cancel'        => __( 'Cancel','acf' ),
		] );

		// Localize "input" text.
		if ( wp_script_is( 'acf-input' ) ) {
			acf_localize_text( [

				// Unload.
				'The changes you made will be lost if you navigate away from this page'	=> __( 'The changes you made will be lost if you navigate away from this page', 'acf' ),

				// Validation.
				'Validation successful'       => __( 'Validation successful', 'acf' ),
				'Validation failed'           => __( 'Validation failed', 'acf' ),
				'1 field requires attention'  => __( '1 field requires attention', 'acf' ),
				'%d fields require attention' => __( '%d fields require attention', 'acf' ),

				// Other.
				'Edit field group' => __( 'Edit field group', 'acf' ),
			] );

			// Fires during "admin_enqueue_scripts" when ACF scripts are enqueued.
			do_action( 'acf/input/admin_enqueue_scripts' );
		}

		// Fire during "admin_enqueue_scripts" when ACF scripts are enqueued.
		do_action( 'acf/admin_enqueue_scripts' );
		do_action( 'acf/enqueue_scripts' );

		// Filter i18n translations that differ from English and localize script.
		$text = [];
		foreach ( $this->text as $k => $v ) {
			if ( str_replace( '.verb', '', $k ) !== $v ) {
				$text[ $k ] = $v;
			}
		}
		if ( $text ) {
			wp_localize_script( 'acf', 'acfL10n', $text );
		}
	}

	/**
	 * Print scripts in head
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function print_scripts() {

		if ( wp_script_is('acf-input') ) {

			// Fires during "admin_head" when ACF scripts are enqueued.
			do_action( 'acf/input/admin_head' );
			do_action( 'acf/input/admin_print_scripts' );
		}

		// Fires during "admin_head" when ACF scripts are enqueued.
		do_action( 'acf/admin_head' );
		do_action( 'acf/admin_print_scripts' );
	}

	/**
	 * Print scripts in footer
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function print_footer_scripts() {

		global $wp_version;

		$cp_version = '';
		if ( function_exists( 'classicpress_version' ) ) {
			$cp_version = classicpress_version();
		}

		// Bail early if 'acf' script was never enqueued (fixes Elementor enqueue reset conflict).
		if ( ! wp_script_is( 'acf' ) ) {
			return;
		}

		// Localize data.
		acf_localize_data( [
			'admin_url'   => admin_url(),
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'acf_nonce' ),
			'acf_version' => acf_get_setting( 'version' ),
			'wp_version'  => $wp_version,
			'cp_version'  => $cp_version,
			'browser'     => acf_get_browser(),
			'locale'      => acf_get_locale(),
			'rtl'         => is_rtl(),
			'screen'      => acf_get_form_data( 'screen' ),
			'post_id'     => acf_get_form_data( 'post_id' ),
			'validation'  => acf_get_form_data( 'validation' ),
			'editor'      => acf_is_block_editor() ? 'block' : 'classic'
		] );

		// Print inline script.
		printf( "<script>\n%s\n</script>\n", 'acf.data = ' . wp_json_encode( $this->data ) . ';' );

		if ( wp_script_is( 'acf-input' ) ) {

			$compat_l10n = apply_filters( 'acf/input/admin_l10n', [] );
			if ( $compat_l10n ) {
				printf( "<script>\n%s\n</script>\n", 'acf.l10n = ' . wp_json_encode( $compat_l10n ) . ';' );
			}

			// Fire during "admin_footer" when ACF scripts are enqueued.
			do_action( 'acf/input/admin_footer' );
			do_action( 'acf/input/admin_print_footer_scripts' );
		}

		// Fire during "admin_footer" when ACF scripts are enqueued.
		do_action( 'acf/admin_footer' );
		do_action( 'acf/admin_print_footer_scripts' );

		/**
		 * Once all data is localized, trigger acf.prepare()
		 * to execute functionality before DOM ready.
		 */
		printf( "<script>\n%s\n</script>\n", "acf.doAction( 'prepare' );" );
	}

	/**
	 * Print uploader scripts in footer
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function print_uploader_scripts() {

		// @todo: investigate output-buffer to hide HTML.
		?>
		<div id="acf-hidden-wp-editor" style="display: none;">
			<?php wp_editor( '', 'acf_content' ); ?>
		</div>
		<?php

		// Fires when printing uploader scripts.
		do_action( 'acf/admin_print_uploader_scripts' );
	}
}
acf_new_instance( 'ACF_Assets' );

/**
 * Append an array of i18n data for localization
 *
 * @since  1.0.0
 * @param array $text An array of text for i18n.
 * @return void
 */
function acf_localize_text( $text ) {
	return acf_get_instance( 'ACF_Assets' )->add_text( $text );
}

/**
 * Append an array of l10n data for localization
 *
 * @since  1.0.0
 * @param  array $data An array of data for l10n.
 * @return void
 */
function acf_localize_data( $data ) {
	return acf_get_instance( 'ACF_Assets' )->add_data( $data );
}

/**
 * Enqueue a script with support for supplemental inline scripts
 *
 * @since  1.0.0
 * @param  string $name The script name.
 * @return void
 */
function acf_enqueue_script( $name ) {
	return acf_get_instance( 'ACF_Assets' )->enqueue_script( $name );
}

/**
 * Enqueue the input scripts required for fields
 *
 * @since  1.0.0
 * @param  array $args See ACF_Assets::enqueue_scripts() for a list of args.
 * @return void
 */
function acf_enqueue_scripts( $args = [] ) {
	return acf_get_instance( 'ACF_Assets' )->enqueue( $args );
}

/**
 * Enqueue the WP media uploader scripts and styles
 *
 * @since  1.0.0
 * @param  void
 * @return void
 */
function acf_enqueue_uploader() {
	return acf_get_instance( 'ACF_Assets' )->enqueue_uploader();
}
