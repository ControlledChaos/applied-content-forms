<?php
/**
 * Post types module
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Modules
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Post_Types extends ACF_Module {

	public $active = false;

	/**
	 * Post type
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $post_type = 'acf-post-type';

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function initialize() {

		if ( acf_get_setting( 'post_types' ) ) {
			$this->active = true;
		}
		$this->settings   = 'modules.post_types';
		$this->label      = 'Post Type Label';
		$this->textdomain = 'Post Types';

		$this->tool    = 'ACF_Post_Types_Export';
		$this->tools   = [ 'php', 'json' ];
		$this->columns = [
			'acf-name'       => __( 'Name', 'acf' ),
			'acf-taxonomies' => __( 'Taxonomies', 'acf' ),
			'acf-posts'      => __( 'Posts', 'acf' ),
		];
	}

	/**
	 * Actions
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function actions() {

		// Features.
		add_action( 'admin_footer-edit.php', [ $this, 'admin_config' ] );
		add_action( 'pre_get_posts', [ $this, 'admin_archive_posts' ] );
		add_filter( 'edit_posts_per_page', [ $this, 'admin_archive_ppp' ], 10, 2 );
		add_action( 'pre_get_posts', [ $this, 'front_archive_posts' ] );
		add_filter( 'template_include', [ $this, 'front_template' ], 999 );

		// Validate.
		add_filter( 'acf/validate_value/name=acfe_dpt_name', [ $this, 'validate_name' ], 10, 4 );
		add_filter( 'acf/update_value/name=acfe_dpt_name', [ $this, 'update_name' ], 10, 3 );

		// Save.
		add_filter( 'acfe/post_type/save_args', [ $this, 'save_args' ], 10, 3 );
		add_action( 'acfe/post_type/save', [ $this, 'save' ], 10, 3 );

		// Import.
		add_action( 'acfe/post_type/import_fields', [ $this, 'import_fields' ], 10, 3 );
		add_action( 'acfe/post_type/import', [ $this, 'after_import' ], 10, 2 );

		// Multilang.
		add_action( 'acfe/post_type/save', [ $this, 'l10n_save' ], 10, 3 );
		add_filter( 'acfe/post_type/register', [ $this, 'register' ], 15, 2 );
		add_filter( 'acfe/post_type/register', [ $this, 'l10n_register' ], 10, 2 );

		// Help.
		add_action( 'load-post.php', [ $this, 'help_tabs_type_post' ] );
		add_action( 'load-post-new.php', [ $this, 'help_tabs_type_post' ] );
		add_action( 'load-edit.php', [ $this, 'help_tabs_type_edit' ] );

		// Footer scripts.
		add_action( 'admin_print_footer_scripts', [ $this, 'menu_icon_script' ] );
	}

	/**
	 * Get name
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @return string Returns the post type name.
	 */
	public function get_name( $post_id ) {
		return get_field( 'acfe_dpt_name', $post_id );
	}

	/**
	 * Get ACF post types
	 *
	 * And array of all ACF dynamic post types data,
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_acf_post_types() {

		$query = [
			'public'     => true,
			'_builtin'   => false,
			'is_acf_dpt' => true
		];

		$get_types = get_post_types( $query, 'names', 'and' );
		$acf_types = [];

		foreach ( $get_types as $type ) {
			$acf_types[] = get_object_vars( get_post_type_object( $type ) );
		}
		return $acf_types;
	}

	/**
	 * Init
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {
		$this->register_acf_post_types();
	}

	/**
	 * Register
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed
	 */
	function register( $args, $name ) {

		// Check if active.
		if ( ! acf_maybe_get( $args, 'active', true ) ) {
			return false;
		}
		return $args;
	}

	/**
	 * Register ACF post types
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_acf_post_types() {

		$cap = acf_get_setting( 'capability' );

		register_post_type( $this->post_type, [
			'label'       => __( 'Post Types', 'acf' ),
			'description' => __( 'Create custom post types without code.', 'acf' ),
			'labels'      => [
				'name'          => __( 'Post Types', 'acf' ),
				'singular_name' => __( 'Post Type', 'acf' ),
				'menu_name'     => __( 'Post Types', 'acf' ),
				'edit_item'     => __( 'Edit Post Type', 'acf' ),
				'add_new_item'  => __( 'New Post Type', 'acf' ),
			],
			'supports'            => [ 'title' ],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => acf()->admin_slug(),
			'menu_icon'           => 'dashicons-sticky',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capabilities'        => [
				'publish_posts'       => $cap,
				'edit_posts'          => $cap,
				'edit_others_posts'   => $cap,
				'delete_posts'        => $cap,
				'delete_others_posts' => $cap,
				'read_private_posts'  => $cap,
				'edit_post'           => $cap,
				'delete_post'         => $cap,
				'read_post'           => $cap,
			],
			'acfe_admin_orderby' => 'title',
			'acfe_admin_order'   => 'ASC',
			'acfe_admin_ppp'     => 999,
		] );

		$settings = apply_filters( 'acfe/post_type/prepare_register', acfe_get_settings( $this->settings ) );

		if ( empty( $settings ) ) {
			return;
		}

		foreach ( $settings as $name => $args ) {

			if ( empty( $name ) || post_type_exists( $name ) ) {
				continue;
			}

			// Filters.
			$args = apply_filters( 'acfe/post_type/register',                 $args, $name );
			$args = apply_filters( "acfe/post_type/register/name={$name}",    $args, $name );

			if ( false === $args ) {
				continue;
			}

			// Register.
			register_post_type( $name, $args );
		}
	}

	/**
	 * Post screen
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function post_screen() {
		flush_rewrite_rules();
	}

	/**
	 * Edit row actions view
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post
	 * @param  string $name
	 * @return string
	 */
	public function edit_row_actions_view( $post, $name ) {
		return sprintf(
			'<a href="%s">%s</a>',
			admin_url( "edit.php?post_type={$name}" ),
			__( 'View', 'acf' )
		);
	}

	/**
	 * Edit columns HTML
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $column
	 * @param  integer $post_id
	 * @return void
	 */
	public function edit_columns_html( $column, $post_id ) {

		switch ( $column ) {

			case 'acf-name' :
				printf(
					'<code>%s</code>',
					$this->get_name( $post_id )
				);
				break;

			case 'acf-taxonomies':

				$column = '—';
				$taxes  = acf_get_array( get_field( 'taxonomies', $post_id ) );

				if ( ! empty( $taxes ) ) {

					$taxonomies = [];
					foreach ( $taxes as $tax ) {

						if ( ! taxonomy_exists( $tax ) ) {
							continue;
						}
						$taxonomies[] = $tax;
					}

					if ( ! empty( $taxonomies ) ) {

						$labels = acf_get_taxonomy_labels( $taxonomies );
						if ( ! empty( $labels ) ) {
							$column = implode( ', ', $labels );
						}
					}
				}
				echo $column;
				break;

			case 'acf-posts':

				$column = '—';
				$name   = $this->get_name( $post_id );
				$count  = wp_count_posts( $name );

				if ( ! empty( $count ) && isset( $count->publish ) ) {

					$publish = $count->publish;
					$column  = sprintf(
						'<a href="%s">%s</a>',
						admin_url( 'edit.php?post_type=' . $name ),
						$publish
					);
				}
				echo $column;
				break;
		}
	}

	/**
	 * Admin config button
	 *
	 * @since  1.0.0
	 * @access public
	 * @global string $typenow Current post type.
	 * @return void
	 */
	public function admin_config() {

		// Access global variables.
		global $typenow;

		if ( ! acf_current_user_can_admin() ) {
			return;
		}

		if ( empty( $typenow ) ) {
			return;
		}

		$post_type_obj = get_post_type_object( $typenow );
		if ( ! isset( $post_type_obj->acfe_admin_ppp ) ) {
			return;
		}

		$post = get_page_by_path( $typenow, 'OBJECT', $this->post_type );
		if ( empty( $post ) ) {
			return;
		}

		?>
		<script type="text/html" id="tmpl-acf-post-type-title-config">
			<a href="<?php echo admin_url( "post.php?post={$post->ID}&action=edit" ); ?>" class="page-title-action acf-post-type-admin-config"><span class="dashicons dashicons-admin-generic"></span></a>
		</script>

		<script type="text/javascript">
		(function($){
			$('.wrap .page-title-action').before($('#tmpl-acf-post-type-title-config').html());
		})(jQuery);
		</script>
		<?php
	}

	/**
	 * Admin archive
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $query
	 * @global string $pagenow
	 * @return void
	 */
	public function admin_archive_posts( $query ) {

		global $pagenow;

		if ( ! is_admin() || ! $query->is_main_query() || $pagenow !== 'edit.php' ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		$object    = get_post_type_object( $post_type );

		$admin_order_by = acf_maybe_get( $object, 'acfe_admin_orderby' );
		$admin_order    = acf_maybe_get( $object, 'acfe_admin_order' );

		if ( $admin_order_by && ! acf_maybe_get_REQUEST( 'orderby' ) ) {
			$query->set('orderby', $admin_order_by);
		}

		if ( $admin_order && ! acf_maybe_get_REQUEST( 'order' ) ) {
			$query->set('order', $admin_order);
		}
	}

	/**
	 * Admin posts per page
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $ppp
	 * @param  string $post_type
	 * @global string $pagenow
	 * @return integer
	 */
	public function admin_archive_ppp( $ppp, $post_type ) {

		global $pagenow;

		if ( 'edit.php' !== $pagenow ) {
			return $ppp;
		}

		$object    = get_post_type_object( $post_type );
		$admin_ppp = acf_maybe_get( $object, 'acfe_admin_ppp' );
		$user_ppp  = get_user_option( "edit_{$post_type}_per_page" );

		if ( ! $admin_ppp || ! empty( $user_ppp ) ) {
			return $ppp;
		}
		return $admin_ppp;
	}

	/**
	 * Front archive
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $query
	 * @return void
	 */
	public function front_archive_posts( $query ) {

		if ( is_admin() || ! $query->is_main_query() || ! is_post_type_archive() ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		$object    = get_post_type_object( $post_type );

		$archive_ppp     = acf_maybe_get( $object, 'acfe_archive_ppp' );
		$archive_orderby = acf_maybe_get( $object, 'acfe_archive_orderby' );
		$archive_order   = acf_maybe_get( $object, 'acfe_archive_order' );

		if ( $archive_ppp ) {
			$query->set( 'posts_per_page', $archive_ppp );
		}
		if ( $archive_orderby ) {
			$query->set( 'orderby', $archive_orderby );
		}
		if ( $archive_order ) {
			$query->set( 'order', $archive_order );
		}
	}

	/**
	 * Frontend template
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $template
	 * @return string
	 */
	public function front_template( $template ) {

		if ( ! is_single() && ! is_post_type_archive() && ! is_home() ) {
			return $template;
		}

		$query_var = get_query_var( 'post_type', false );
		if ( is_array( $query_var ) && ! empty( $query_var ) ) {
			$query_var = $query_var[0];
		}

		foreach ( get_post_types( [], 'objects' ) as $post_type ) {

			$is_query_var  = ( $query_var && $query_var === $post_type->name );
			$get_post_type = ( get_post_type() === $post_type->name );

			// Archive template.
			$acf_archive_template = false;
			if ( isset( $post_type->acfe_archive_template ) && ! empty( $post_type->acfe_archive_template ) ) {
				$acf_archive_template = true;
			}

			// Single template.
			$acf_single_template = false;
			if ( isset( $post_type->acfe_single_template ) && ! empty( $post_type->acfe_single_template ) ) {
				$acf_single_template = true;
			}

			// Global check.
			if ( ! $get_post_type || ! $is_query_var || ( ! $acf_archive_template && ! $acf_single_template ) ) {
				continue;
			}

			$rule = [];
			$rule['is_archive']  = is_post_type_archive( $post_type->name );
			$rule['has_archive'] = $post_type->has_archive;
			$rule['is_single']   = is_singular( $post_type->name );

			// Post exception.
			if ( 'post' === $post_type->name ) {
				$rule['is_archive']  = is_home();
				$rule['has_archive'] = true;
			}

			// Archive.
			if ( $rule['has_archive'] && $rule['is_archive'] && $acf_archive_template && ( $locate = locate_template( [ $post_type->acfe_archive_template ] ) ) ) {
				return $locate;

			// Single.
			} elseif ( $rule['is_single'] && $acf_single_template && ( $locate = locate_template( [ $post_type->acfe_single_template ] ) ) ) {
				return $locate;
			}
		}
		return $template;
	}

	 /**
	  * Validate name
	  *
	  * @since  1.0.0
	  * @access public
	  * @param  boolean $valid
	  * @param  string $value
	  * @param  string $field
	  * @param  string $input
	  * @global array $wp_post_types
	  * @return mixed
	  */
	public function validate_name( $valid, $value, $field, $input ) {

		// Access global variables.
		global $wp_post_types;

		if ( ! $valid ) {
			return $valid;
		}

		// Reserved post types
		// @see: https://codex.wordpress.org/Function_Reference/register_post_type#Reserved_Post_Types
		$exclude = [
			'post',
			'posts',
			'page',
			'attachment',
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset',
			'oembed_cache',
			'user_request',
			'wp_block',
			'action',
			'author',
			'order',
			'theme',
		];

		$exclude = array_merge( $exclude, acf_get_setting( 'reserved_post_types', [] ) );

		// Reserved names.
		if ( in_array( $value, $exclude ) ) {
			return __( 'This post type name is reserved.', 'acf' );
		}

		// Editing current dynamic post type.
		$current_post_id = acf_maybe_get_POST( 'post_ID' );

		if ( ! empty( $current_post_id ) ) {

			$current_name = get_field( $field['name'], $current_post_id );

			if ( $value === $current_name ) {
				return $valid;
			}
		}

		if ( ! empty( $wp_post_types ) ) {
			foreach ( $wp_post_types as $post_type ) {

				if ( $value !== $post_type->name ) {
					continue;
				}
				$valid = __( 'This post type name already exists', 'acf' );
			}
		}
		return $valid;
	}

	/**
	 * Update name
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $value
	 * @param  integer $post_id
	 * @param  string $field
	 * @return string
	 */
	public function update_name( $value, $post_id, $field ) {

		// Previous value.
		$_value = get_field( $field['name'], $post_id );

		// Value changed, delete option.
		if ( $_value !== $value ) {
			acfe_delete_settings( "{$this->settings}.{$_value}" );
		}
		return $value;
	}

	/**
	 * ACF save post
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @return void
	 */
	public function save_post( $post_id ) {

		$args = [];
		$name = $this->get_name( $post_id );

		// Filters
		$args = apply_filters( 'acfe/post_type/save_args', $args, $name, $post_id );
		$args = apply_filters( "acfe/post_type/save_args/name={$name}", $args, $name, $post_id );
		$args = apply_filters( "acfe/post_type/save_args/id={$post_id}", $args, $name, $post_id );

		if ( false === $args ) {
			return;
		}

		// Actions.
		do_action( 'acfe/post_type/save', $name, $args, $post_id );
		do_action( "acfe/post_type/save/name={$name}",  $name, $args, $post_id );
		do_action( "acfe/post_type/save/id={$post_id}", $name, $args, $post_id );
	}

	/**
	 * Save args
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @param  string $name
	 * @param  integer $post_id
	 * @return array
	 */
	public function save_args( $args, $name, $post_id ) {

		$active = get_field( 'acfe_dpt_active', $post_id );
		$active = $active === null ? true : $active;
		$label  = get_post_field( 'post_title', $post_id );
		$name   = get_field( 'acfe_dpt_name', $post_id );
		$public = get_field( 'public', $post_id );
		$description  = get_field( 'description', $post_id );
		$hierarchical = get_field( 'hierarchical', $post_id );
		$supports     = get_field( 'supports', $post_id );
		$taxonomies   = acf_get_array( get_field( 'taxonomies', $post_id ) );
		$exclude_from_search = get_field( 'exclude_from_search', $post_id );
		$publicly_queryable  = get_field( 'publicly_queryable', $post_id );
		$can_export       = get_field( 'can_export', $post_id );
		$delete_with_user = get_field( 'delete_with_user', $post_id );

		// Labels.
		$labels = acf_get_array( get_field( 'labels', $post_id ) );
		$labels_args = [];
		foreach ( $labels as $k => $l ) {
			if ( empty( $l ) ) {
				continue;
			}
			$labels_args[$k] = $l;
		}

		// Menu.
		$menu_position = get_field( 'menu_position', $post_id );
		$menu_icon     = 'dashicons-' . get_field( 'acf_dpt_menu_icon', $post_id );
		$show_ui       = get_field( 'show_ui', $post_id );
		$show_in_menu  = get_field( 'show_in_menu', $post_id );
		$show_in_menu_text = get_field( 'show_in_menu_text', $post_id );
		$show_in_nav_menus = get_field( 'show_in_nav_menus', $post_id );
		$show_in_admin_bar = get_field( 'show_in_admin_bar', $post_id );

		// Capability.
		$capability_type = acf_decode_choices( get_field( 'capability_type', $post_id ), true );
		$capabilities = acf_decode_choices( get_field( 'capabilities', $post_id ) );
		$map_meta_cap = get_field( 'map_meta_cap', $post_id );

		// Archive.
		$archive_template = get_field( 'acfe_dpt_archive_template', $post_id );
		$archive_posts_per_page = (int) get_field( 'acfe_dpt_archive_posts_per_page', $post_id );
		$archive_orderby  = get_field( 'acfe_dpt_archive_orderby', $post_id );
		$archive_order    = get_field( 'acfe_dpt_archive_order', $post_id );
		$has_archive      = get_field( 'has_archive', $post_id );
		$has_archive_slug = get_field( 'has_archive_slug', $post_id );

		// Single.
		$single_template = get_field( 'acfe_dpt_single_template', $post_id );
		$rewrite = get_field( 'rewrite', $post_id );
		$rewrite_args_select = get_field( 'rewrite_args_select', $post_id );
		$rewrite_args = get_field( 'rewrite_args', $post_id );

		// Admin.
		$admin_archive = get_field('acfe_dpt_admin_archive', $post_id);
		$admin_posts_per_page = (int) get_field( 'acfe_dpt_admin_posts_per_page', $post_id );
		$admin_orderby = get_field( 'acfe_dpt_admin_orderby', $post_id );
		$admin_order   = get_field( 'acfe_dpt_admin_order', $post_id );

		// Help tabs.
		$edit_help_tabs = acf_get_array( get_field( 'field_acf_dpt_edit_help_tabs', $post_id ) );
		$post_help_tabs = acf_get_array( get_field( 'field_acf_dpt_post_help_tabs', $post_id ) );
		$edit_sidebar = get_field( 'field_acf_dpt_edit_help_sidebar', $post_id );
		$post_sidebar = get_field( 'field_acf_dpt_post_help_sidebar', $post_id );
		$edit_sidebar_heading = get_field( 'field_acf_dpt_edit_sidebar_heading', $post_id );
		$edit_sidebar_content = get_field( 'field_acf_dpt_edit_sidebar_content', $post_id );
		$post_sidebar_heading = get_field( 'field_acf_dpt_post_sidebar_heading', $post_id );
		$post_sidebar_content = get_field( 'field_acf_dpt_post_sidebar_content', $post_id );

		// REST.
		$show_in_rest = get_field( 'show_in_rest', $post_id );
		$rest_base    = get_field( 'rest_base', $post_id );
		$rest_controller_class = get_field( 'rest_controller_class', $post_id );

		// Register args.
		$args = [
			'is_acf_dpt'            => true,
			'acf_dpt_id'            => $post_id,
			'label'                 => $label,
			'description'           => $description,
			'hierarchical'          => $hierarchical,
			'supports'              => $supports,
			'taxonomies'            => $taxonomies,
			'public'                => $public,
			'exclude_from_search'   => $exclude_from_search,
			'publicly_queryable'    => $publicly_queryable,
			'can_export'            => $can_export,
			'delete_with_user'      => $delete_with_user,

			// Labels.
			'labels'                => $labels_args,

			// Menu.
			'menu_icon'             => $menu_icon,
			'show_ui'               => $show_ui,
			'show_in_menu'          => $show_in_menu,
			'show_in_nav_menus'     => $show_in_nav_menus,
			'show_in_admin_bar'     => $show_in_admin_bar,

			// Single.
			'rewrite'               => $rewrite,
			'acfe_single_template'  => $single_template,

			// Archive.
			'has_archive'           => $has_archive,
			'acfe_archive_template' => $archive_template,
			'acfe_archive_ppp'      => $archive_posts_per_page,
			'acfe_archive_orderby'  => $archive_orderby,
			'acfe_archive_order'    => $archive_order,
			'acfe_admin_archive'    => $admin_archive,
			'acfe_admin_ppp'        => $admin_posts_per_page,
			'acfe_admin_orderby'    => $admin_orderby,
			'acfe_admin_order'      => $admin_order,

			// Help tabs.
			'edit_help_tabs'       => $edit_help_tabs,
			'post_help_tabs'       => $post_help_tabs,
			'edit_help_sidebar'    => $edit_sidebar,
			'post_help_sidebar'    => $post_sidebar,
			'edit_sidebar_heading' => $edit_sidebar_heading,
			'edit_sidebar_content' => $edit_sidebar_content,
			'post_sidebar_heading' => $post_sidebar_heading,
			'post_sidebar_content' => $post_sidebar_content,

			// REST.
			'show_in_rest'          => $show_in_rest,
			'rest_base'             => $rest_base,
			'rest_controller_class' => $rest_controller_class
		];

		$args['active'] = $active;

		// Menu position.
		if ( ! acf_is_empty( $menu_position ) ) {
			$args['menu_position'] = (int) $menu_position;
		}

		// Has archive override.
		if ( $has_archive && $has_archive_slug ) {
			$args['has_archive'] = $has_archive_slug;
		}

		// Rewrite override.
		if ( $rewrite && $rewrite_args_select ) {

			$args['rewrite'] = [
				'slug'       => $rewrite_args['acfe_dpt_rewrite_slug'],
				'with_front' => $rewrite_args['acfe_dpt_rewrite_with_front'],
				'feeds'      => $rewrite_args['feeds'],
				'pages'      => $rewrite_args['pages'],
			];
		}

		// Show in menu (text).
		if ( $show_in_menu && ! empty( $show_in_menu_text ) ) {
			$args['show_in_menu'] = $show_in_menu_text;
		}

		if ( empty( $menu_icon ) ) {
			$args['menu_icon'] = 'dashicons-admin-post';
		}

		// Capability type.
		$args['capability_type'] = $capability_type;
		if ( is_array( $capability_type ) && count( $capability_type ) == 1 ) {
			$args['capability_type'] = $capability_type[0];
		}

		// Capabilities.
		$args['capabilities'] = $capabilities;

		// Map meta cap.
		$args['map_meta_cap'] = null;

		if ( 'false' === $map_meta_cap ) {
			$args['map_meta_cap'] = false;
		} elseif ( 'true' === $map_meta_cap ) {
			$args['map_meta_cap'] = true;
		}

		return $args;
	}

	/**
	 * Save
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @param  array $args
	 * @param  integer $post_id
	 * @return void
	 */
	public function save( $name, $args, $post_id ) {

		// Get option.
		$settings = acfe_get_settings( $this->settings );

		// Create option.
		$settings[$name] = $args;

		// Sort keys ASC.
		ksort( $settings );

		// Update option.
		acfe_update_settings( $this->settings, $settings );

		// Update post
		wp_update_post( [
			'ID'          => $post_id,
			'post_name'   => $name,
			'post_status' => $args['active'] ? 'publish' : 'acf-disabled',
		] );
	}

	/**
	 * Trashed post type
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @return void
	 */
	public function trashed_post( $post_id ) {

		$name = $this->get_name( $post_id );

		// Get option.
		$settings = acfe_get_settings( $this->settings );

		// Unset option.
		acf_unset( $settings, $name );

		// Update option.
		acfe_update_settings( $this->settings, $settings );

		// Flush permalinks.
		flush_rewrite_rules();
	}

	/**
	 * Import post types
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  name $name
	 * @param  array $args
	 * @return mixed
	 */
	public function import( $name, $args ) {

		$settings = acfe_get_settings( $this->settings );
		$title    = $args['label'];

		// Already exists.
		if ( isset( $settings[$name] ) ) {
			return new WP_Error( 'acfe_dpt_import_already_exists', __( "Post type \"{$title}\" already exists. Import aborted.", 'acf' ) );
		}

		// Import post.
		$post_id = false;
		$post    = [
			'post_title'  => $title,
			'post_name'   => $name,
			'post_type'   => $this->post_type,
			'post_status' => 'publish'
		];

		$post = apply_filters( 'acfe/post_type/import_post',                 $post, $name );
		$post = apply_filters( "acfe/post_type/import_post/name={$name}",    $post, $name );

		if ( $post !== false ) {
			$post_id = wp_insert_post( $post );
		}

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			return new WP_Error( 'acfe_dpt_import_error', __( "Something went wrong with the post type \"{$title}\". Import aborted.", 'acf' ) );
		}

		// Import Args
		$args = apply_filters( 'acfe/post_type/import_args',                 $args, $name, $post_id );
		$args = apply_filters( "acfe/post_type/import_args/name={$name}",    $args, $name, $post_id );
		$args = apply_filters( "acfe/post_type/import_args/name={$post_id}", $args, $name, $post_id );

		if ( false === $args ) {
			return $post_id;
		}

		// Import fields.
		acf_enable_filter( 'local' );

		do_action( 'acfe/post_type/import_fields', $name, $args, $post_id );
		do_action( "acfe/post_type/import_fields/name={$name}", $name, $args, $post_id );
		do_action( "acfe/post_type/import_fields/id={$post_id}", $name, $args, $post_id );

		acf_disable_filter( 'local' );

		// Save
		$this->save_post( $post_id );

		return $post_id;
	}

	/**
	 * Import fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @param  array $args
	 * @param  integer $post_id
	 * @return void
	 */
	public function import_fields( $name, $args, $post_id ) {

		// Register args.
		update_field( 'acfe_dpt_active', $args['active'], $post_id );
		update_field( 'acfe_dpt_name', $name, $post_id );
		update_field( 'description', $args['description'], $post_id );
		update_field( 'hierarchical', $args['hierarchical'], $post_id );
		update_field( 'supports', $args['supports'], $post_id );
		update_field( 'taxonomies', $args['taxonomies'], $post_id );
		update_field( 'public', $args['public'], $post_id );
		update_field( 'exclude_from_search', $args['exclude_from_search'], $post_id );
		update_field( 'publicly_queryable', $args['publicly_queryable'], $post_id );
		update_field( 'can_export', $args['can_export'], $post_id );
		update_field( 'delete_with_user', $args['delete_with_user'], $post_id );

		// Labels.
		if ( ! empty( $args['labels'] ) ) {

			foreach ( $args['labels'] as $label_key => $label_value ) {
				update_field( 'labels_' . $label_key, $label_value, $post_id );
			}
		}

		// Menu.
		update_field( 'menu_position', acf_maybe_get( $args, 'menu_position' ), $post_id );
		update_field( 'menu_icon', $args['menu_icon'], $post_id );
		update_field( 'show_ui', $args['show_ui'], $post_id );
		update_field( 'show_in_menu', $args['show_in_menu'], $post_id );
		update_field( 'show_in_nav_menus', $args['show_in_nav_menus'], $post_id );
		update_field( 'show_in_admin_bar', $args['show_in_admin_bar'], $post_id );

		// Capability.
		update_field( 'capability_type', acf_encode_choices( $args['capability_type'], false ), $post_id );
		update_field( 'map_meta_cap', $args['map_meta_cap'], $post_id );

		if ( isset( $args['capabilities'] ) ) {
			update_field( 'capabilities', acf_encode_choices( $args['capabilities'], false ), $post_id );
		}

		// Archive.
		update_field( 'acfe_dpt_archive_template', $args['acfe_archive_template'], $post_id );
		update_field( 'acfe_dpt_archive_posts_per_page', $args['acfe_archive_ppp'], $post_id );
		update_field( 'acfe_dpt_archive_orderby', $args['acfe_archive_orderby'], $post_id );
		update_field( 'acfe_dpt_archive_order', $args['acfe_archive_order'], $post_id );
		update_field( 'has_archive', $args['has_archive'], $post_id );

		// Single.
		update_field( 'acfe_dpt_single_template', $args['acfe_single_template'], $post_id );
		update_field( 'rewrite', $args['rewrite'], $post_id );

		// Admin.
		update_field( 'acfe_dpt_admin_posts_per_page', $args['acfe_admin_ppp'], $post_id );
		update_field( 'acfe_dpt_admin_orderby', $args['acfe_admin_orderby'], $post_id );
		update_field( 'acfe_dpt_admin_order', $args['acfe_admin_order'], $post_id );

		// Help tabs.
		update_field( 'acf_dpt_edit_help_tabs', $args['edit_help_tabs'], $post_id );
		update_field( 'acf_dpt_post_help_tabs', $args['post_help_tabs'], $post_id );
		update_field( 'acf_dpt_edit_help_sidebar', $args['edit_help_sidebar'], $post_id );
		update_field( 'acf_dpt_post_help_sidebar', $args['post_help_sidebar'], $post_id );
		update_field( 'acf_dpt_edit_sidebar_heading', $args['edit_sidebar_heading'], $post_id );
		update_field( 'acf_dpt_edit_sidebar_content', $args['edit_sidebar_content'], $post_id );
		update_field( 'acf_dpt_post_sidebar_heading', $args['post_sidebar_heading'], $post_id );
		update_field( 'acf_dpt_post_sidebar_content', $args['post_sidebar_content'], $post_id );

		// REST.
		update_field( 'show_in_rest', $args['show_in_rest'], $post_id );
		update_field( 'rest_base', $args['rest_base'], $post_id );
		update_field( 'rest_controller_class', $args['rest_controller_class'], $post_id );

		// Has archive override.
		if ( $args['has_archive'] && is_string( $args['has_archive'] ) ) {
			update_field( 'has_archive_slug', $args['has_archive'], $post_id );
		}

		// Rewrite override.
		if ( $args['rewrite'] && is_array( $args['rewrite'] ) ) {

			update_field( 'rewrite', true, $post_id );
			update_field( 'rewrite_args_select', true, $post_id );
			update_field( 'rewrite_args_acfe_dpt_rewrite_slug', $args['rewrite']['slug'], $post_id );
			update_field( 'rewrite_args_acfe_dpt_rewrite_with_front', $args['rewrite']['with_front'], $post_id );
			update_field( 'rewrite_args_feeds', $args['rewrite']['feeds'], $post_id );
			update_field( 'rewrite_args_pages', $args['rewrite']['pages'], $post_id );
		}

		// Show in menu (text).
		if ( $args['show_in_menu'] && is_string( $args['show_in_menu'] ) ) {
			update_field( 'show_in_menu_text', $args['show_in_menu'], $post_id );
		}

		// Map meta cap.
		if ( false === $args['map_meta_cap'] ) {
			update_field('map_meta_cap', 'false', $post_id );
		} elseif ( true === $args['map_meta_cap'] ) {
			update_field( 'map_meta_cap', 'true', $post_id );
		}
	}

	/**
	 * After import
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $ids
	 * @param  array $data
	 * @return void
	 */
	public function after_import( $ids, $data ) {
		flush_rewrite_rules();
	}

	/**
	 * Export choices
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function export_choices() {

		$choices  = [];
		$settings = acfe_get_settings( $this->settings );

		if ( ! $settings ) {
			return $choices;
		}

		foreach ( $settings as $name => $args ) {
			$choices[$name] = esc_html( $args['label'] );
		}
		return $choices;
	}

	/**
	 * Export data
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function export_data( $name ) {

		// Settings
		$settings = acfe_get_settings( $this->settings );

		// Doesn't exist
		if ( ! isset( $settings[$name] ) ) {
			return false;
		}

		$args = $settings[$name];
		$args = apply_filters( 'acfe/post_type/export_args',                 $args, $name );
		$args = apply_filters( "acfe/post_type/export_args/name={$name}",    $args, $name );

		return $args;
	}

	/**
	 * Export PHP
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $data
	 * @return void
	 */
	public function export_php( $data ) {

		// Prevent default translation and fake __() within string.
		acf_update_setting( 'l10n_var_export', true );

		$str_replace = [
			"  "            => "\t",
			"'!!__(!!\'"    => "__( '",
			"!!\', !!\'"    => "', '",
			"!!\')!!'"      => "' )",
			"array ("       => "array("
		];

		$preg_replace = [
			'/([\t\r\n]+?)array/'   => 'array',
			'/[0-9]+ => array/'     => 'array'
		];

		// Get settings.
		$l10n = acf_get_setting( 'l10n' );
		$l10n_textdomain = acf_get_setting( 'l10n_textdomain' );

		foreach ( $data as $post_type => $args ) {

			// Translate settings if textdomain is set.
			if ( $l10n && $l10n_textdomain ) {

				$args['label'] = acf_translate( $args['label'] );
				$args['description'] = acf_translate( $args['description'] );

				if ( ! empty( $args['labels'] ) ) {
					foreach ( $args['labels'] as $key => &$label ) {
						$args['labels'][$key] = acf_translate( $label );
					}
				}
			}

			$code = var_export( $args, true );
			$code = str_replace( array_keys( $str_replace ), array_values( $str_replace ), $code );

			// Correctly formats "=> array("
			$code = preg_replace( array_keys( $preg_replace ), array_values( $preg_replace ), $code );

			// esc_textarea
			$code = esc_textarea( $code );

			echo __( '// Post Type: ', 'acf' ) . $args['label'] . "\n" . "register_post_type( '{$post_type}', {$code} );" . "\r\n" . "\r\n";
		}
	}

	/**
	 * Reset
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function reset() {

		$args = apply_filters( 'acfe/post_type/reset_args', [
			'post_type'      => $this->post_type,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => [ 'publish', 'acf-disabled' ],
		] );

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return false;
		}

		foreach ( $posts as $post_id ) {
			$this->save_post( $post_id );
		}

		acf_log( 'Reset: Post Types' );

		return true;
	}

	/**
	 * Multilang save
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $name
	 * @param  array $args
	 * @param  integer $post_id
	 * @return void
	 */
	public function l10n_save( $name, $args, $post_id ) {

		if ( ! acfe_is_wpml() ) {
			return;
		}

		// Translate: Label
		if ( isset( $args['label'] ) ) {
			do_action( 'wpml_register_single_string', $this->textdomain, 'Label', $args['label'] );
		}

		// Translate: Description
		if ( isset( $args['description'] ) ) {
			do_action( 'wpml_register_single_string', $this->textdomain, 'Description', $args['description'] );
		}

		// Translate: Labels
		if ( isset( $args['labels'] ) ) {
			foreach ( $args['labels'] as $label_name => &$label_text ) {
				do_action( 'wpml_register_single_string', $this->textdomain, ucfirst( $label_name ), $label_text );
			}
		}
	}

	/**
	 * Multilang register
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $args
	 * @param  string $name
	 * @return array
	 */
	public function l10n_register( $args, $name ) {

		// Translate: Label
		if ( isset( $args['label'] ) ) {
			$args['label'] = acfe_translate( $args['label'], 'Label', $this->textdomain );
		}

		// Translate: Description
		if ( isset( $args['description'] ) ) {
			$args['description'] = acfe_translate( $args['description'], 'Description', $this->textdomain );
		}

		// Translate: Labels
		if ( isset($args['labels'] ) ) {
			foreach ( $args['labels'] as $label_name => &$label_text ) {
				$label_text = acfe_translate( $label_text, ucfirst( $label_name ), $this->textdomain );
			}
		}
		return $args;
	}

	/**
	 * Menu icon preview
	 *
	 * Prints an unordered list of all Dashicons icons
	 * plus a corresponding CSS block.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function menu_icon_preview() {

		$icons = acf_dashicon_icons();
		$list  = [];

		$post_id = false;
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		}
		$option = get_field( 'acf_dpt_menu_icon', $post_id );

		$css  = sprintf(
			'<style>%s %s %s %s %s</style>',
			'#icon-preview-list { list-style: none; }',
			'#icon-preview-list li { margin: 0 !important; }',
			'.icon-preview.dashicons { box-sizing: border-box; width: 100%; height: unset; text-align: unset; font-size: 3em; }',
			'.icon-preview.dashicons:before { display: inline-block; }',
			'.icon-preview:not( .active ) { display: none; }'
		);

		$html = '<ul id="icon-preview-list">';
		foreach ( $icons as $slug => $name ) {

			// Skip options group headings.
			if ( str_contains( $slug, '##' ) ) {
				continue;
			}

			$class  = 'icon-preview';
			if ( $slug == $option ) {
				$class = 'icon-preview active';
			}
			$list[] = sprintf(
				'<li><span id="icon-%s" class="%s dashicons dashicons-%s"><span class="screen-reader-text">%s</span></span></li>',
				$slug,
				$class,
				$slug,
				$name
			);
		}
		$html .= implode( '', $list );
		$html .= '</ul>';

		return $css . $html;
	}

	/**
	 * Menu icon JavaScript
	 *
	 * jQuery control of post type menu icon preview.
	 * Shows the selected icon from the dropdown.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function menu_icon_script() {

		$icons = acf_dashicon_icons();
		$list  = [];

		$post_id = false;
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		}
		$option = get_field( 'acf_dpt_menu_icon', $post_id );

		$script = '<script type="text/javascript">jQuery(document).ready( function($) {';
		$script .= "$( '#acf-field_acf_dpt_menu_icon' ).on( 'change', function() { var show = $(this).val();";

		foreach ( $icons as $slug => $name ) {

			// Skip options group headings.
			if ( str_contains( $slug, '##' ) ) {
				continue;
			}
			$script .= "if ( show == '{$slug}' ) { $( '#icon-{$slug}' ).addClass( 'active' ); } else if ( show != '{$slug}' ) { $( '#icon-{$slug}' ).removeClass( 'active' ); }";
		}
		$script .= '}); });</script>';

		echo $script;
	}

	/**
	 * Add local field groups
	 *
	 * All the fields for adding a post type.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function add_local_field_group() {

		acf_add_local_field_group( [
			'key'   => 'group_acf_post_type',
			'title' => __( 'Dynamic Post Type', 'acf' ),

			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => $this->post_type
					],
				],
			],
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'seamless',
			'hide_on_screen'  => '',
			'active'          => 1,
			'description'     => '',
			'label_placement' => 'left',
			'instruction_placement' => 'label',

			'fields' => [
				[
					'key'   => 'field_acfe_dpt_tab_general',
					'label' => __( 'General', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'instructions' => '',
					'required'     => 0,
					'placement'    => 'top',
					'endpoint'     => 0,
					'wrapper'      => [
						'width' => '',
						'class' => '',
						'id'    => '',
						'data-no-preference' => true
					],
					'acfe_permissions'  => '',
					'conditional_logic' => 0
				],
				[
					'key'      => 'field_acfe_dpt_name',
					'label'    => __( 'Name', 'acf' ),
					'name'     => 'acfe_dpt_name',
					'type'     => 'acfe_slug',
					'required' => 1,
					'instructions' => __( 'Post type name. Maximum 20 characters, cannot contain capital letters or spaces.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => 20
				],
				[
					'key'      => 'field_acfe_dpt_description',
					'label'    => __( 'Description', 'acf' ),
					'name'     => 'description',
					'type'     => 'text',
					'required' => 0,
					'instructions' => __( 'A short descriptive summary of the post type.', 'acf' ),
					'wrapper'  => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
					'conditional_logic' => 0
				],
				[
					'key'      => 'field_acfe_dpt_hierarchical',
					'label'    => __( 'Hierarchical', 'acf' ),
					'name'     => 'hierarchical',
					'type'     => 'true_false',
					'required' => 0,
					'instructions' => __( 'Whether the post type is hierarchical (e.g. page). Allows parent to be specified. The supports parameter should contain page attributes to show the parent select box on the editor page.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'message'       => '',
					'default_value' => 0,
					'ui'            => 1,
					'ui_on_text'    => __( 'Yes', 'acf' ),
					'ui_off_text'   => __( 'No', 'acf' ),
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => ''
				],
				[
					'key'   => 'field_acfe_dpt_supports',
					'label' => __( 'Supports', 'acf' ),
					'name'  => 'supports',
					'type'  => 'checkbox',
					'required'     => 0,
					'instructions' => __( 'An alias for calling add_post_type_support() directly. A boolean false can be passed as value instead of an array to prevent default (title and editor) behavior.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'choices' => [
						'title'     => __( 'Title', 'acf' ),
						'editor'    => __( 'Editor', 'acf' ),
						'excerpt'   => __( 'Excerpt', 'acf' ),
						'thumbnail' => __( 'Thumbnail', 'acf' ),
						'author'    => __( 'Author', 'acf' ),
						'comments'  => __( 'Comments', 'acf' ),
						'page-attributes' => __( 'Page Attributes', 'acf' ),
						'post-formats'    => __( 'Post Formats', 'acf' ),
						'trackbacks'      => __( 'Trackbacks', 'acf' ),
						'revisions'       => __( 'Revisions', 'acf' ),
						'custom-fields'   => __( 'Custom Fields', 'acf' )
					],
					'allow_custom'  => 1,
					'save_custom'   => 1,
					'default_value' => [
						0 => 'title',
						1 => 'editor',
						2 => 'excerpt',
						3 => 'thumbnail'
					],
					'layout' => 'vertical',
					'toggle' => 0,
					'return_format' => 'value'
				],
				[
					'key'   => 'field_acfe_dpt_taxonomies',
					'label' => __( 'Taxonomies', 'acf' ),
					'name'  => 'taxonomies',
					'type'  => 'acfe_taxonomies',
					'required'     => 0,
					'instructions' => __( 'An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling `register_taxonomy_for_object_type()` directly. Custom taxonomies still need to be registered with `register_taxonomy()`.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'field_type'        => 'checkbox',
					'return_format'     => 'name',
					'multiple'          => 0,
					'allow_null'        => 0
				],
				[
					'key'   => 'field_acfe_dpt_public',
					'label' => __( 'Public', 'acf' ),
					'name'  => 'public',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Controls how the type is visible to authors (show_in_nav_menus, show_ui) and readers (exclude_from_search, publicly_queryable).', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_exclude_from_search',
					'label' => __( 'Exclude from Search', 'acf' ),
					'name'  => 'exclude_from_search',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether to exclude posts with this post type from front end search results.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 0,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_publicly_queryable',
					'label' => __( 'Publicly Queryable', 'acf' ),
					'name'  => 'publicly_queryable',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether queries can be performed on the front end as part of `parse_request()`.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_can_export',
					'label' => __( 'Can Export', 'acf' ),
					'name'  => 'can_export',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Can this post type be exported.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => '',
					'ui_off_text' => ''
				],
				[
					'key'   => 'field_acfe_dpt_delete_with_user',
					'label' => __( 'Delete with User', 'acf' ),
					'name'  => 'delete_with_user',
					'type'  => 'select',
					'required'     => 0,
					'instructions' => __( 'Whether to delete posts of this type when deleting a user. If true, posts of this type belonging to the user will be moved to trash when then user is deleted.<br /><br />If false, posts of this type belonging to the user will not be trashed or deleted. If not set (the default), posts are trashed if the post type supports author. Otherwise posts are not trashed or deleted.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'choices' => [
						'null'  => __( 'Null (default)', 'acf' ),
						'false' => __( 'False', 'acf' ),
						'true'  => __( 'True', 'acf' )
					],
					'default_value' => [ 'null'  => __( 'Null (default)', 'acf' ) ],
					'allow_null'    => 0,
					'multiple'      => 0,
					'return_format' => 'value',
					'placeholder'   => '',
					'ajax' => 0,
					'ui'   => 0
				],
				[
					'key'   => 'field_acfe_dpt_tab_menu',
					'label' => __( 'Menu', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'instructions' => '',
					'required'     => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_menu_position',
					'label' => __( 'Menu Position', 'acf' ),
					'name'  => 'menu_position',
					'type'  => 'number',
					'required'     => 0,
					'instructions' => __( 'The position in the menu order the post type should appear. show_in_menu must be true.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => '',
					'prepend' => '',
					'append'  => '',
					'min'     => 0,
					'max'     => '',
					'step'    => ''
				],
				/*
				[
					'key'   => 'field_acfe_dpt_menu_icon',
					'label' => __( 'Menu Icon', 'acf' ),
					'name'  => 'menu_icon',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'The url to the icon to be used for this menu or the name of the icon from the <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">Dashicons</a> icon font.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => 'dashicons-admin-post',
					'placeholder'       => 'dashicons-admin-post',
					'prepend'   => '',
					'append'    => '',
					'maxlength' => ''
				],
				*/
				[
					'key'   => 'field_acf_dpt_menu_icon',
					'label' => 'Select Menu Icon',
					'name'  => 'acf_dpt_menu_icon',
					'type'  => 'select',
					'required'     => 0,
					'instructions' => 'Preview of the selected icon.<br />' . $this->menu_icon_preview(),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => 'dpt-menu-icon-preview'
					],
					'choices' => acf_dashicon_icons(),
					'aria-label'        => '',
					'conditional_logic' => 0,
					'default_value'     => 'admin-post',
					'allow_null' => 0,
					'multiple'   => 0,
					'max' => '',
					'ui'  => 0,
					'return_format' => 'value',
					'prepend' => '',
					'append'  => '',
					'acfe_field_group_condition' => 0,
					'ajax'        => 0,
					'placeholder' => '',
				],
				[
					'key'   => 'field_acfe_dpt_show_ui',
					'label' => __( 'Show UI', 'acf' ),
					'name'  => 'show_ui',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether to generate a default UI for managing this post type in the admin.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_show_in_menu',
					'label' => __( 'Show in Menu', 'acf' ),
					'name'  => 'show_in_menu',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether to show the post type in the admin menu. Show UI must be true.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_show_in_menu_text',
					'label' => __( 'Menu Parent', 'acf' ),
					'name'  => 'show_in_menu_text',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'If an existing top level page such as `tools.php` or `edit.php?post_type=page`, the post type will be placed as a sub menu of that page.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acfe_dpt_show_in_menu',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'acfe_validate'    => '',
					'acfe_update'      => '',
					'acfe_permissions' => '',
					'default_value'    => '',
					'placeholder'      => '',
					'prepend'   => '',
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_show_in_nav_menus',
					'label' => __( 'Show in Nav Menus', 'acf' ),
					'name'  => 'show_in_nav_menus',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether the post type is available for selection in navigation menus.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => '',
					'ui_off_text' => ''
				],
				[
					'key'   => 'field_acfe_dpt_show_in_admin_bar',
					'label' => __( 'Show in Admin Bar', 'acf' ),
					'name'  => 'show_in_admin_bar',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether to show the post type in the admin toolbar. show_ui must be true.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_tab_archive',
					'label' => __( 'Archive', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_archive_template',
					'label' => __( 'Template', 'acf' ),
					'name'  => 'acfe_dpt_archive_template',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Which template file to load for the archive query. More information on <a href="https://developer.wordpress.org/themes/basics/template-hierarchy/">Template hierarchy</a>.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => 'example-template.php',
					'prepend'   => trailingslashit( acf_get_setting( 'acfe/theme_folder' ) ),
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_has_archive',
					'label' => __( 'Has Archive', 'acf' ),
					'name'  => 'has_archive',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Enables post type archives.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_has_archive_slug',
					'label' => __( 'Slug', 'acf' ),
					'name'  => 'has_archive_slug',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Will use post type name as archive slug by default.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acfe_dpt_has_archive',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'acfe_validate'    => '',
					'acfe_update'      => '',
					'acfe_permissions' => '',
					'default_value'    => '',
					'placeholder'      => __( 'Default', 'acf' ),
					'prepend'   => '',
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_archive_posts_per_page',
					'label' => __( 'Posts per Page', 'acf' ),
					'name'  => 'acfe_dpt_archive_posts_per_page',
					'type'  => 'number',
					'required'     => 0,
					'instructions' => __( 'Number of posts to display in the archive page.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acfe_dpt_has_archive',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'acfe_validate'    => '',
					'acfe_update'      => '',
					'acfe_permissions' => '',
					'default_value'    => 10,
					'placeholder'      => '',
					'prepend' => '',
					'append'  => '',
					'min'     => -1,
					'max'     => '',
					'step'    => '',
				],
				[
					'key'   => 'field_acfe_dpt_archive_orderby',
					'label' => __( 'Order By', 'acf' ),
					'name'  => 'acfe_dpt_archive_orderby',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Sort retrieved posts by parameter in the archive page. Defaults to post date.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acfe_dpt_has_archive',
								'operator' => '==',
								'value'    => '1'
							],
						],
					],
					'acfe_validate' => '',
					'acfe_update'   => [
						'5c9479dec93c4' => [
							'acfe_update_function' => 'sanitize_title'
						]
					],
					'acfe_permissions' => '',
					'default_value'    => 'date',
					'placeholder'      => '',
					'prepend'   => '',
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_archive_order',
					'label' => __( 'Order', 'acf' ),
					'name'  => 'acfe_dpt_archive_order',
					'type'  => 'select',
					'required'     => 0,
					'instructions' => __( 'Designates the ascending or descending order of the `orderby` parameter in the archive page. Defaults to `DESC`.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acfe_dpt_has_archive',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'acfe_validate'    => '',
					'acfe_update'      => '',
					'acfe_permissions' => '',
					'placeholder'      => '',
					'choices'          => [
						'ASC'  => 'ASC',
						'DESC' => 'DESC'
					],
					'default_value' => [
						0 => 'DESC'
					],
					'allow_null'    => 0,
					'multiple'      => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'ui'   => 0
				],
				[
					'key'   => 'field_acfe_dpt_tab_single',
					'label' => __( 'Single', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_single_template',
					'label' => __( 'Template', 'acf' ),
					'name'  => 'acfe_dpt_single_template',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Which template file to load for the single query. More information on <a href="https://developer.wordpress.org/themes/basics/template-hierarchy/">Template hierarchy</a>.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => 'my-template.php',
					'prepend'   => trailingslashit( acf_get_setting( 'acfe/theme_folder' ) ),
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_rewrite',
					'label' => __( 'Rewrite', 'acf' ),
					'name'  => 'rewrite',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Triggers the handling of rewrites for this post type. To prevent rewrites, set to false.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'       => '',
					'default_value' => 1,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_rewrite_args_select',
					'label' => __( 'Rewrite Arguments', 'acf' ),
					'name'  => 'rewrite_args_select',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Use additional rewrite arguments.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acfe_dpt_rewrite',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'acfe_validate'    => '',
					'acfe_update'      => '',
					'acfe_permissions' => '',
					'message'       => '',
					'default_value' => 0,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_rewrite_args',
					'label' => __( 'Additional Arguments', 'acf' ),
					'name'  => 'rewrite_args',
					'type'  => 'group',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field' => 'field_acfe_dpt_rewrite',
								'operator' => '==',
								'value' => '1'
							],
							[
								'field' => 'field_acfe_dpt_rewrite_args_select',
								'operator' => '==',
								'value' => '1'
							]
						]
					],
					'acfe_validate'    => '',
					'acfe_update'      => '',
					'acfe_permissions' => '',
					'layout'     => 'row',
					'sub_fields' => [
						[
							'key'   => 'field_acfe_dpt_rewrite_slug',
							'label' => __( 'Slug', 'acf' ),
							'name'  => 'acfe_dpt_rewrite_slug',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => __( 'Customize the permalink structure slug. Defaults to the post type name value. Should be translatable.', 'acf' ),
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => [
								[
									[
										'field'    => 'field_acfe_dpt_rewrite_args_select',
										'operator' => '==',
										'value'    => '1'
									],
								],
							],
							'acfe_validate'    => '',
							'acfe_update'      => '',
							'acfe_permissions' => '',
							'default_value'    => '',
							'placeholder'      => __( 'Default', 'acf' ),
							'prepend'   => '',
							'append'    => '',
							'maxlength' => ''
						],
						[
							'key'   => 'field_acfe_dpt_rewrite_with_front',
							'label' => __( 'With Front', 'acf' ),
							'name'  => 'acfe_dpt_rewrite_with_front',
							'type'  => 'true_false',
							'required'     => 0,
							'instructions' => __( 'Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true.', 'acf' ),
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => [
								[
									[
										'field'    => 'field_acfe_dpt_rewrite_args_select',
										'operator' => '==',
										'value'    => '1'
									]
								]
							],
							'acfe_validate'    => '',
							'acfe_update'      => '',
							'acfe_permissions' => '',
							'message'          => '',
							'default_value'    => 1,
							'ui'          => 1,
							'ui_on_text'  => __( 'Yes', 'acf' ),
							'ui_off_text' => __( 'No', 'acf' )
						],
						[
							'key'   => 'field_acfe_dpt_rewrite_feeds',
							'label' => __( 'Feeds', 'acf' ),
							'name'  => 'feeds',
							'type'  => 'true_false',
							'required'     => 0,
							'instructions' => __( 'Should a feed permalink structure be built for this post type. Defaults to has_archive value.', 'acf' ),
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => [
								[
									[
										'field'    => 'field_acfe_dpt_rewrite_args_select',
										'operator' => '==',
										'value'    => '1'
									]
								]
							],
							'acfe_validate'    => '',
							'acfe_update'      => '',
							'acfe_permissions' => '',
							'message'          => '',
							'default_value'    => 1,
							'ui'          => 1,
							'ui_on_text'  => __( 'Yes', 'acf' ),
							'ui_off_text' => __( 'No', 'acf' )
						],
						[
							'key'   => 'field_acfe_dpt_rewrite_pages',
							'label' => __( 'Pages', 'acf' ),
							'name'  => 'pages',
							'type'  => 'true_false',
							'required'     => 0,
							'instructions' => __( 'Should the permalink structure provide for pagination. Defaults to true.', 'acf' ),
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => [
								[
									[
										'field'    => 'field_acfe_dpt_rewrite_args_select',
										'operator' => '==',
										'value'    => '1'
									]
								]
							],
							'acfe_validate'    => '',
							'acfe_update'      => '',
							'acfe_permissions' => '',
							'message'       => '',
							'default_value' => 1,
							'ui'          => 1,
							'ui_on_text'  => __( 'Yes', 'acf' ),
							'ui_off_text' => __( 'No', 'acf' )
						]
					]
				],
				[
					'key'   => 'field_acfe_dpt_tab_admin',
					'label' => __( 'Admin', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_admin_archive',
					'label' => __( 'Archive Page', 'acf' ),
					'name'  => 'acfe_dpt_admin_archive',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Add an archive options page as submenu of the post type.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'       => '',
					'default_value' => 0,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_admin_posts_per_page',
					'label' => __( 'Posts per Page', 'acf' ),
					'name'  => 'acfe_dpt_admin_posts_per_page',
					'type'  => 'number',
					'required'     => 0,
					'instructions' => __( 'Number of posts to display on the admin list screen.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => 10,
					'placeholder'       => '',
					'prepend' => '',
					'append'  => '',
					'min'     => -1,
					'max'     => '',
					'step'    => ''
				],
				[
					'key'   => 'field_acfe_dpt_admin_orderby',
					'label' => __( 'Order By', 'acf' ),
					'name'  => 'acfe_dpt_admin_orderby',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Sort retrieved posts by parameter in the admin list screen. Defaults to date (post_date).', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => [
						'5c9479dec93c4' => [
							'acfe_update_function' => 'sanitize_title'
						]
					],
					'acfe_permissions' => '',
					'default_value'    => 'date',
					'placeholder'      => '',
					'prepend'   => '',
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_admin_order',
					'label' => __( 'Order', 'acf' ),
					'name'  => 'acfe_dpt_admin_order',
					'type'  => 'select',
					'required'     => 0,
					'instructions' => __( 'Designates the ascending or descending order of the `orderby` parameter in the admin list screen. Defaults to `DESC`.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'choices' => [
						'ASC'  => 'ASC',
						'DESC' => 'DESC'
					],
					'default_value' => [
						0 => 'DESC'
					],
					'allow_null'    => 0,
					'multiple'      => 0,
					'placeholder'   => '',
					'return_format' => 'value',
					'ajax' => 0,
					'ui'   => 0
				],
				[
					'key'   => 'field_acf_dpt_edit_help_tabs',
					'label' => __( 'Edit Help Tabs', 'acf' ),
					'name'  => 'acf_dpt_edit_help_tabs',
					'type'  => 'repeater',
					'required'     => 0,
					'instructions' => __( 'Add contextual help tabs to the post type list screen.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'collapsed'         => 'field_acf_dpt_edit_help_tab_heading',
					'min'    => 0,
					'max'    => 0,
					'layout' => 'row',
					'button_label' => __( 'Add Tab', 'acf' ),
					'aria-label'   => '',
					'acfe_field_group_condition'    => 0,
					'acfe_repeater_stylised_button' => 1,
					'sub_fields' => [
						[
							'key'   => 'field_acf_dpt_edit_help_tab_heading',
							'label' => __( 'Heading', 'acf' ),
							'name'  => 'acf_dpt_edit_help_tab_heading',
							'type'  => 'text',
							'required'     => 1,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'aria-label'        => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'   => '',
							'append'    => '',
							'maxlength' => '',
							'acfe_field_group_condition' => 0
						],
						[
							'key'   => 'field_acf_dpt_edit_help_tab_content',
							'label' => __( 'Content', 'acf' ),
							'name'  => 'acf_dpt_edit_help_tab_content',
							'type'  => 'wysiwyg',
							'required'     => 1,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'default_value'     => '',
							'aria-label'   => '',
							'toolbar'      => 'basic_enhanced',
							'media_upload' => 0,
							'tabs'  => 'all',
							'delay' => 0,
							'acfe_wysiwyg_height'           => 300,
							'acfe_wysiwyg_max_height'       => '',
							'acfe_wysiwyg_valid_elements'   => '',
							'acfe_wysiwyg_custom_style'     => '',
							'acfe_wysiwyg_disable_wp_style' => 0,
							'acfe_wysiwyg_autoresize'       => 0,
							'acfe_wysiwyg_disable_resize'   => 0,
							'acfe_wysiwyg_remove_path'      => 0,
							'acfe_wysiwyg_menubar'          => 0,
							'acfe_wysiwyg_transparent'      => 0,
							'acfe_wysiwyg_merge_toolbar'    => 0,
							'acfe_wysiwyg_custom_toolbar'   => 0,
							'acfe_field_group_condition'    => 0
						]
					]
				],
				[
					'key'   => 'field_acf_dpt_edit_help_sidebar',
					'label' => __( 'Edit Help Sidebar', 'acf' ),
					'name'  => 'acf_dpt_edit_help_sidebar',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acf_dpt_edit_help_tabs',
								'operator' => '>',
								'value'    => '0'
							]
						]
					],
					'message'       => '',
					'default_value' => 0,
					'aria-label'    => '',
					'ui'            => 1,
					'ui_on_text'    => __( 'Yes', 'acf' ),
					'ui_off_text'   => __( 'No', 'acf' ),
					'acfe_field_group_condition' => 0
				],
				[
					'key'   => 'field_acf_dpt_edit_sidebar_heading',
					'label' => __( 'Sidebar Heading', 'acf' ),
					'name'  => 'acf_dpt_edit_sidebar_heading',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Basic HTML tags are allowed, including style.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acf_dpt_edit_help_sidebar',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'aria-label'    => '',
					'default_value' => '',
					'placeholder'   => '',
					'prepend'       => '',
					'append'        => '',
					'maxlength'     => '',
					'acfe_field_group_condition' => 0
				],
				[
					'key'   => 'field_acf_dpt_edit_sidebar_content',
					'label' => __( 'Sidebar Content', 'acf' ),
					'name'  => 'acf_dpt_edit_sidebar_content',
					'type'  => 'wysiwyg',
					'required'     => 1,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acf_dpt_edit_help_sidebar',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'aria-label'    => '',
					'default_value' => '',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'tabs'  => 'all',
					'delay' => 0,
					'acfe_wysiwyg_height'           => 300,
					'acfe_wysiwyg_max_height'       => '',
					'acfe_wysiwyg_valid_elements'   => '',
					'acfe_wysiwyg_custom_style'     => '',
					'acfe_wysiwyg_disable_wp_style' => 0,
					'acfe_wysiwyg_autoresize'       => 0,
					'acfe_wysiwyg_disable_resize'   => 0,
					'acfe_wysiwyg_remove_path'      => 0,
					'acfe_wysiwyg_menubar'          => 0,
					'acfe_wysiwyg_transparent'      => 0,
					'acfe_wysiwyg_merge_toolbar'    => 0,
					'acfe_wysiwyg_custom_toolbar'   => 0,
					'acfe_field_group_condition'    => 0
				],
				[
					'key'   => 'field_acf_dpt_post_help_tabs',
					'label' => __( 'Post Help Tabs', 'acf' ),
					'name'  => 'acf_dpt_post_help_tabs',
					'type'  => 'repeater',
					'required'     => 0,
					'instructions' => __( 'Add contextual help tabs to the post type edit and new post screens.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_repeater_stylised_button' => 1,
					'collapsed'    => 'field_acf_dpt_post_help_tab_heading',
					'min'          => 0,
					'max'          => 0,
					'layout'       => 'row',
					'aria-label'   => '',
					'button_label' => __( 'Add Tab', 'acf' ),
					'acfe_field_group_condition' => 0,
					'sub_fields' => [
						[
							'key'   => 'field_acf_dpt_post_help_tab_heading',
							'label' => __( 'Heading', 'acf' ),
							'name'  => 'acf_dpt_post_help_tab_heading',
							'type'  => 'text',
							'required'     => 1,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'aria-label'        => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'   => '',
							'append'    => '',
							'maxlength' => '',
							'acfe_field_group_condition' => 0
						],
						[
							'key'   => 'field_acf_dpt_post_help_tab_content',
							'label' => __( 'Content', 'acf' ),
							'name'  => 'acf_dpt_post_help_tab_content',
							'type'  => 'wysiwyg',
							'required'     => 1,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'aria-label'        => '',
							'default_value'     => '',							'toolbar'           => 'basic_enhanced',
							'media_upload'      => 0,
							'tabs'  => 'all',
							'delay' => 0,
							'acfe_wysiwyg_height'           => 300,
							'acfe_wysiwyg_max_height'       => '',
							'acfe_wysiwyg_valid_elements'   => '',
							'acfe_wysiwyg_custom_style'     => '',
							'acfe_wysiwyg_disable_wp_style' => 0,
							'acfe_wysiwyg_autoresize'       => 0,
							'acfe_wysiwyg_disable_resize'   => 0,
							'acfe_wysiwyg_remove_path'      => 0,
							'acfe_wysiwyg_menubar'          => 0,
							'acfe_wysiwyg_transparent'      => 0,
							'acfe_wysiwyg_merge_toolbar'    => 0,
							'acfe_wysiwyg_custom_toolbar'   => 0,
							'acfe_field_group_condition'    => 0
						]
					]
				],
				[
					'key'   => 'field_acf_dpt_post_help_sidebar',
					'label' => __( 'Post Help Sidebar', 'acf' ),
					'name'  => 'acf_dpt_post_help_sidebar',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acf_dpt_post_help_tabs',
								'operator' => '>',
								'value'    => '0'
							]
						]
					],
					'message'       => '',
					'aria-label'    => '',
					'default_value' => 0,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' ),
					'acfe_field_group_condition' => 0
				],
				[
					'key'   => 'field_acf_dpt_post_sidebar_heading',
					'label' => __( 'Sidebar Heading', 'acf' ),
					'name'  => 'acf_dpt_post_sidebar_heading',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'Basic HTML tags are allowed, including style.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acf_dpt_post_help_sidebar',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'aria-label'    => '',
					'default_value' => '',
					'placeholder'   => '',
					'prepend'       => '',
					'append'        => '',
					'maxlength'     => '',
					'acfe_field_group_condition' => 0
				],
				[
					'key'   => 'field_acf_dpt_post_sidebar_content',
					'label' => __( 'Sidebar Content', 'acf' ),
					'name'  => 'acf_dpt_post_sidebar_content',
					'type'  => 'wysiwyg',
					'required'     => 1,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_acf_dpt_post_help_sidebar',
								'operator' => '==',
								'value'    => '1'
							]
						]
					],
					'aria-label'    => '',
					'default_value' => '',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'tabs'  => 'all',
					'delay' => 0,
					'acfe_wysiwyg_height'           => 300,
					'acfe_wysiwyg_max_height'       => '',
					'acfe_wysiwyg_valid_elements'   => '',
					'acfe_wysiwyg_custom_style'     => '',
					'acfe_wysiwyg_disable_wp_style' => 0,
					'acfe_wysiwyg_autoresize'       => 0,
					'acfe_wysiwyg_menubar'          => 0,
					'acfe_wysiwyg_transparent'      => 0,
					'acfe_wysiwyg_merge_toolbar'    => 0,
					'acfe_wysiwyg_custom_toolbar'   => 0,
					'acfe_field_group_condition'    => 0
				],
				[
					'key'   => 'field_acfe_dpt_tab_labels',
					'label' => __( 'Labels', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_labels',
					'label' => __( 'Labels', 'acf' ),
					'name'  => 'labels',
					'type'  => 'group',
					'required'     => 0,
					'instructions' => __( 'An array of labels for this post type. By default, post labels are used for non-hierarchical post types and page labels for hierarchical ones.<br /><br />
Default: if empty, `name` is set to value of `label`, and `singular_name` is set to value of `name`.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'layout'     => 'row',
					'sub_fields' => [
						[
							'key'   => 'field_acfe_dpt_singular_name',
							'label' => __( 'Singular Name', 'acf' ),
							'name'  => 'singular_name',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_add_new',
							'label' => __( 'Add New', 'acf' ),
							'name'  => 'add_new',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_add_new_item',
							'label' => __( 'Add New Item', 'acf' ),
							'name'  => 'add_new_item',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_edit_item',
							'label' => __( 'Edit Item', 'acf' ),
							'name'  => 'edit_item',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_new_item',
							'label' => __( 'New Item', 'acf' ),
							'name'  => 'new_item',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_view_item',
							'label' => __( 'View Item', 'acf' ),
							'name'  => 'view_item',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_view_items',
							'label' => __( 'View Items', 'acf' ),
							'name'  => 'view_items',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_search_items',
							'label' => __( 'Search Items', 'acf' ),
							'name'  => 'search_items',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_not_found',
							'label' => __( 'Not Found', 'acf' ),
							'name'  => 'not_found',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_not_found_in_trash',
							'label' => __( 'Not Found in Trash', 'acf' ),
							'name'  => 'not_found_in_trash',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_parent_item_colon',
							'label' => __( 'Parent Item Colon', 'acf' ),
							'name'  => 'parent_item_colon',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_all_items',
							'label' => __( 'All Items', 'acf' ),
							'name'  => 'all_items',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_archives',
							'label' => __( 'Archives', 'acf' ),
							'name'  => 'archives',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_attributes',
							'label' => __( 'Attributes', 'acf' ),
							'name'  => 'attributes',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_insert_into_item',
							'label' => __( 'Insert Into Item', 'acf' ),
							'name'  => 'insert_into_item',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_uploaded_to_this_item',
							'label' => __( 'Uploaded to This Item', 'acf' ),
							'name'  => 'uploaded_to_this_item',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_featured_image',
							'label' => __( 'Featured Image', 'acf' ),
							'name'  => 'featured_image',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_set_featured_image',
							'label' => __( 'Set Featured Image', 'acf' ),
							'name'  => 'set_featured_image',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_remove_featured_image',
							'label' => __( 'Remove Featured Image', 'acf' ),
							'name'  => 'remove_featured_image',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_use_featured_image',
							'label' => __( 'Use Featured Image', 'acf' ),
							'name'  => 'use_featured_image',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_menu_name',
							'label' => __( 'Menu Name', 'acf' ),
							'name'  => 'menu_name',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_filter_items_list',
							'label' => __( 'Filter Items List', 'acf' ),
							'name'  => 'filter_items_list',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_items_list_navigation',
							'label' => __( 'Items List Navigation', 'acf' ),
							'name'  => 'items_list_navigation',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_items_list',
							'label' => __( 'Items List', 'acf' ),
							'name'  => 'items_list',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_name_admin_bar',
							'label' => __( 'Name Admin Bar', 'acf' ),
							'name'  => 'name_admin_bar',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_item_published',
							'label' => __( 'Item Published', 'acf' ),
							'name'  => 'item_published',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_item_published_privately',
							'label' => __( 'Item Published Privately', 'acf' ),
							'name'  => 'item_published_privately',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_item_reverted_to_draft',
							'label' => __( 'Item Reverted to Draft', 'acf' ),
							'name'  => 'item_reverted_to_draft',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_item_scheduled',
							'label' => __( 'Item Scheduled', 'acf' ),
							'name'  => 'item_scheduled',
							'type' => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						],
						[
							'key'   => 'field_acfe_dpt_item_updated',
							'label' => __( 'Item Updated', 'acf' ),
							'name'  => 'item_updated',
							'type'  => 'text',
							'required'     => 0,
							'instructions' => '',
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id'    => ''
							],
							'conditional_logic' => 0,
							'acfe_validate'     => '',
							'acfe_update'       => '',
							'acfe_permissions'  => '',
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => ''
						]
					]
				],
				[
					'key'   => 'field_acfe_dpt_tab_capability',
					'label' => __( 'Capability', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_capability_type',
					'label' => __( 'Capability Type', 'acf' ),
					'name'  => 'capability_type',
					'type'  => 'textarea',
					'required'     => 0,
					'instructions' => __( 'The string to use to build the read, edit, and delete capabilities.<br />
May be passed as an array to allow for alternative plurals when using this argument as a base to construct the capabilities, like this:<br /><br />

story<br />
stories', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => 'post',
					'placeholder'       => '',
					'maxlength' => '',
					'rows'      => '',
					'new_lines' => ''
				],
				[
					'key'   => 'field_acfe_dpt_capabilities',
					'label' => __( 'Capabilities', 'acf' ),
					'name'  => 'capabilities',
					'type'  => 'textarea',
					'required'     => 0,
					'instructions' => __( 'An array of the capabilities for this post type. Specify capabilities like this:<br /><br />
publish_posts : publish_posts<br />
edit_post  : edit_post<br />
edit_posts  : edit_posts<br />
read_post : read_post<br />
delete_post : delete_post<br />
etc...', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => '',
					'maxlength' => '',
					'rows'      => '',
					'new_lines' => ''
				],
				[
					'key'   => 'field_acfe_dpt_map_meta_cap',
					'label' => __( 'Map Meta Cap', 'acf' ),
					'name'  => 'map_meta_cap',
					'type'  => 'select',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'choices' => [
						'null'  => __( 'Null (default)', 'acf' ),
						'false' => __( 'False', 'acf' ),
						'true'  => __( 'True', 'acf' )
					],
					'default_value' => [
						0 => 'null'
					],
					'allow_null'    => 0,
					'multiple'      => 0,
					'placeholder'   => '',
					'return_format' => 'value',
					'ui'   => 0,
					'ajax' => 0
				],
				[
					'key'   => 'field_acfe_dpt_tab_rest',
					'label' => __( 'REST', 'acf' ),
					'name'  => '',
					'type'  => 'tab',
					'required'     => 0,
					'instructions' => '',
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_permissions'  => '',
					'placement' => 'top',
					'endpoint'  => 0
				],
				[
					'key'   => 'field_acfe_dpt_show_in_rest',
					'label' => __( 'Show in REST', 'acf' ),
					'name'  => 'show_in_rest',
					'type'  => 'true_false',
					'required'     => 0,
					'instructions' => __( 'Whether to expose this post type in the REST API. Set this to true for the post type to be available in the block editor.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'message'           => '',
					'default_value'     => 0,
					'ui'          => 1,
					'ui_on_text'  => __( 'Yes', 'acf' ),
					'ui_off_text' => __( 'No', 'acf' )
				],
				[
					'key'   => 'field_acfe_dpt_rest_base',
					'label' => __( 'REST Base', 'acf' ),
					'name'  => 'rest_base',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'The base slug that this post type will use when accessed using the REST API.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'   => '',
					'append'    => '',
					'maxlength' => ''
				],
				[
					'key'   => 'field_acfe_dpt_rest_controller_class',
					'label' => __( 'REST Controller Class', 'acf' ),
					'name'  => 'rest_controller_class',
					'type'  => 'text',
					'required'     => 0,
					'instructions' => __( 'An optional custom controller to use instead of `WP_REST_Posts_Controller`. Must be a subclass of `WP_REST_Controller`.', 'acf' ),
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'conditional_logic' => 0,
					'acfe_validate'     => '',
					'acfe_update'       => '',
					'acfe_permissions'  => '',
					'default_value'     => 'WP_REST_Posts_Controller',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => ''
				],
			],
		] );

		acf_add_local_field_group( [
			'key'   => 'group_acf_post_type_side',
			'title' => __( 'Post Type Side', 'acf' ),
			'acfe_display_title' => __( 'Status', 'acf' ),
			'fields' => [
				[
					'key'          => 'field_acfe_dpt_active',
					'label'        => '',
					'name'         => 'acfe_dpt_active',
					'type'         => 'true_false',
					'instructions' => __( 'Set the status of this post type.', 'acf' ),
					'required'     => 0,
					'wrapper'      => [
						'width' => '',
						'class' => '',
						'id'    => ''
					],
					'message'       => '',
					'default_value' => 1,
					'ui'            => 1,
					'ui_on_text'    => __( 'Active', 'acf' ),
					'ui_off_text'   => __( 'Inactive', 'acf' ),
					'conditional_logic' => 0
				],
			],
			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => $this->post_type
					],
				],
			],
			'menu_order'      => 0,
			'position'        => 'side',
			'style'           => 'default',
			'label_placement' => 'top',
			'hide_on_screen'  => false,
			'active'          => true,
			'description'     => '',
			'instruction_placement' => 'label'
		] );
	}

	/**
	 * Edit screen help tabs
	 *
	 * Contextual help tabs that appear on
	 * the post type lists screen.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function help_tabs_type_edit() {

		$types  = $this->get_acf_post_types();
		$screen = get_current_screen();

		foreach ( $types as $type ) {

			if ( ! isset( $_GET['post_type'] ) ) {
				continue;
			}

			if ( isset( $_GET['post_type'] ) && $_GET['post_type'] !== $type['name'] ) {
				continue;
			}

			if ( isset( $type['edit_help_tabs'] ) ) {
				$tabs = $type['edit_help_tabs'];

				foreach ( $tabs as $tab ) {
					$id = random_int( 1000, 9999 );
					$screen->add_help_tab( [
						'id'       => $id,
						'title'    => $tab['acf_dpt_edit_help_tab_heading'],
						'content'  => $tab['acf_dpt_edit_help_tab_content'],
						'callback' => null
					] );
				}
			}
			if ( isset( $type['edit_help_sidebar'] ) ) {

				$sidebar = '';
				if ( $type['edit_sidebar_heading'] ) {
					$sidebar .= wp_kses( $type['edit_sidebar_heading'], acf_help_heading_allowed() );
				}
				$sidebar .= $type['edit_sidebar_content'];
				$screen->set_help_sidebar( $sidebar );
			}
		}
	}

	/**
	 * Post screen help tabs
	 *
	 * Contextual help tabs that appear on
	 * the post type new & edit screens.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function help_tabs_type_post() {

		if ( isset( $_GET['post_type'] ) ) {
			$post_type = $_GET['post_type'];
		} elseif ( isset( $_GET['post'] ) ) {
			$post_type = get_post_type( $_GET['post'] );
		} else {
			return;
		}

		$types  = $this->get_acf_post_types();
		$screen = get_current_screen();

		foreach ( $types as $type ) {

			if ( $post_type !== $type['name'] ) {
				continue;
			}

			if ( $type['post_help_tabs'] ) {
				$tabs = $type['post_help_tabs'];

				foreach ( $tabs as $tab ) {
					$id = random_int( 1000, 9999 );
					$screen->add_help_tab( [
						'id'       => $id,
						'title'    => $tab['acf_dpt_post_help_tab_heading'],
						'content'  => $tab['acf_dpt_post_help_tab_content'],
						'callback' => null
					] );
				}
			}
			if ( $type['post_help_sidebar'] ) {

				$sidebar = '';
				if ( $type['post_sidebar_heading'] ) {
					$sidebar .= wp_kses(
						$type['post_sidebar_heading'], acf_help_heading_allowed()
					);
				}
				$sidebar .= $type['post_sidebar_content'];
				$screen->set_help_sidebar( $sidebar );
			}
		}
	}
}
acf_new_instance( 'ACF_Post_Types' );
