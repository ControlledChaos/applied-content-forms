<?php
/**
 * ACF admin screen
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

class ACF_Admin {

	/**
	 * ACF screen
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $screen = false;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );
		add_action( 'admin_body_class', [ $this, 'admin_body_class' ] );
		add_action( 'admin_menu', [ $this, 'add_admin_page' ], 9 );
		add_action( 'admin_menu', [ $this, 'categories_screen' ] );
		add_action( 'acf/init', [ $this, 'settings_page' ] );
		add_filter( 'default_hidden_meta_boxes', [ $this, 'show_page_excerpt_metabox' ], 10, 2 );
		add_action( 'add_meta_boxes', [ $this, 'page_excerpt_metabox' ] );
	}

	/**
	 * Admin page title text
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function admin_page_title() {

		// Return the title from the content page option.
		$option = get_field( 'acf_admin_page_content', 'option' );
		$option = get_post( $option );
		if ( is_object( $option ) ) {
			return $option->post_title;
		}

		// Return the default, filterable title.
		return apply_filters( 'acf/acf_admin_page_title', __( 'Content Management', 'acf' ) );
	}

	/**
	 * Admin page menu text
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function admin_page_menu() {
		return apply_filters( 'acf/acf_admin_page_menu', __( 'Content', 'acf' ) );
	}

	/**
	 * Admin page menu icon
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function admin_page_icon() {
		return apply_filters( 'acf/acf_admin_page_icon', 'dashicons-edit-large' );
	}

	/**
	 * Admin page
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_admin_page() {

		// Stop if ACF is hidden.
		if ( ! acf_get_setting( 'show_admin' ) ) {
			return;
		}

		$title = $this->admin_page_title();
		$menu  = $this->admin_page_menu();
		$icon  = $this->admin_page_icon();

		add_menu_page(
			$title,
			$menu,
			acf_get_setting( 'capability' ),
			acf()->admin_slug(),
			[ $this, 'admin_page_callback' ],
			$icon,
			acf_get_setting( 'menu_position' ),
		);
	}

	/**
	 * Admin page callback
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_page_callback() {
		$content = acf_get_view( 'html-admin-page' );
		return apply_filters( 'acf/admin_page', $content );
	}

	/**
	 * Admin page content
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_page_content() {

		$option = get_field( 'acf_admin_page_content', 'option' );
		$option = get_post( $option );
		$styles = get_field( 'acf_admin_page_css', 'option' );

		if ( $styles ) {
			$styles = sprintf(
				'<style>%s</style>',
				$styles
			);
		} else {
			$styles = '';
		}

		if ( is_object( $option ) ) {
			$content  = $styles;
			$content .= wpautop( $option->post_content );
		} else {
			$content  = $this->admin_page_grid();
			$content .= $this->admin_page_search();
		}
		return apply_filters( 'acf/admin_page_content', $content );
	}

	public function admin_page_search() {

		$content_id = 'site-' . get_current_blog_id() . '-acf-search-content';
		$media_id   = 'site-' . get_current_blog_id() . '-acf-search-media';

		?>
		<h2><?php _e( 'Search Content', 'acf' ); ?></h2>

		<div class="acf-content-search">
			<form role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" target="_blank" rel="nofollow noreferrer noopener">
				<fieldset>
					<label class="screen-reader-text" for="<?php echo $content_id; ?>" aria-label="<?php _e( 'Search Content', 'acf' ); ?>"><?php _e( 'Search Content', 'acf' ); ?></label>

					<input type="search" name="s" id="<?php echo $content_id; ?>" aria-labelledby="<?php _e( 'Search Content', 'acf' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off" placeholder="<?php _e( 'Enter title or content search terms', 'acf' ); ?>" aria-placeholder="<?php _e( 'Enter content search terms', 'acf' ); ?>" />
					<?php submit_button( __( 'Search Content', 'acf' ), '', false, false, [ 'id' => 'submit-' . $content_id ] ); ?>
				</fieldset>
			</form>

			<?php if ( current_user_can( 'upload_files' ) ) : ?>
			<form role="search" action="<?php echo self_admin_url( 'upload.php' ); ?>" method="get">
				<fieldset>
					<label class="screen-reader-text" for="<?php echo $media_id; ?>" aria-label="<?php _e( 'Search Media', 'acf' ); ?>"><?php _e( 'Search Media', 'acf' ); ?></label>

					<input type="search" name="search" id="<?php echo $media_id; ?>" aria-labelledby="<?php _e( 'Search Media', 'acf' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off" placeholder="<?php _e( 'Enter media title, meta, or filename', 'acf' ); ?>" aria-placeholder="<?php _e( 'Enter media title or filename', 'acf' ); ?>" />
					<?php submit_button( __( 'Search Media', 'acf' ), '', false, false, [ 'id' => 'submit-' . $media_id ] ); ?>
				</fieldset>
			</form>
		</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Categories screen
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function categories_screen() {

		// Stop if ACF is hidden.
		if ( ! acf_get_setting( 'show_admin' ) ) {
			return;
		}

		add_submenu_page(
			acf()->admin_slug(),
			__( 'Field Categories', 'acf' ),
			__( 'Field Categories', 'acf' ),
			acf_get_setting( 'capability' ),
			'edit-tags.php?taxonomy=acf-field-group-category'
		);
	}

	public function settings_page() {

		acf_add_options_page( [
			'page_title'      => __( 'Content Settings', 'acf' ),
			'page_desc'       => __( 'Choose which content features to use and how to use them.', 'acf' ),
			'menu_title'      => __( 'Settings', 'acf' ),
			'menu_slug'       => acf()->admin_slug() . '-settings',
			'parent'          => acf()->admin_slug(),
			'capability'      => 'manage_options',
			'redirect'        => false,
			'update_location' => 'bottom',
			'update_title'    => __( 'Update Settings','acf' ),
			'update_button'   => __( 'Save Changes', 'acf' ),
			'update_message'  => __( 'Settings Updated', 'acf' ),
			'before_form'     => false,
			'after_form'      => false
		] );
	}

	/**
	 * Enqueues global admin styling.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'acf-global' );
	}

	/**
	 * Appends custom admin body classes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $classes Space-separated list of CSS classes.
	 * @return string
	 */
	public function admin_body_class( $classes ) {

		if ( ! $this->screen ) {
			return $classes;
		}
		$classes .= ' acf-admin';
		$classes .= ' acf-browser-' . acf_get_browser();

		return $classes;
	}

	/**
	 * Current screen
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $screen
	 * @return void
	 */
	public function current_screen( $screen ) {

		// ACF page bases.
		$pages = [
			'toplevel_page_' . acf()->admin_slug(),
			'content_page_acf-tools',
			'content_page_content-settings'
		];

		// ACF post types.
		$types = [
			'acf-post-type',
			'acf-taxonomy',
			'acf-block-type',
			'acf-form',
			'acf-template',
			'acf-field-group'
		];

		if ( isset( $screen->base ) && in_array( $screen->base, $pages ) ) {
			$this->screen = true;
		}

		if ( isset( $screen->post_type ) && in_array( $screen->post_type, $types ) ) {
			$this->screen = true;
		}

		if ( isset( $screen->taxonomy ) && 'acf-field-group-category' == $screen->taxonomy ) {
			$this->screen = true;
		}

		if ( isset( $screen->base ) && $screen->base === 'toplevel_page_' . acf()->admin_slug() ) {

			add_action(
				'admin_enqueue_scripts', function() {

					$version = acf_get_setting( 'version' );
					$suffix  = '';
					if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
						$suffix  = '.min';
					}

					wp_enqueue_style( 'acf-intro', acf_get_url( 'assets/css/intro-page' . $suffix . '.css' ), [], $version, 'screen' );
				}
			);
			$this->setup_help_tab();
		}
	}

	/**
	 * Sets up the admin help tab.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function setup_help_tab() {

		$screen = get_current_screen();

		// Overview tab.
		$screen->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'acf' ),
				'content' =>
					'<h4>' . acf_get_setting( 'name' ) . '</h4>' .
					'<p>' . __( 'The Applied Content Forms plugin provides a visual form builder to customize edit screens with extra fields, and an intuitive API to display custom field values in any theme template file. This began as a fork of Advanced Custom Fields PRO version 5.9.6, the last version developed by Eliot Condon before selling the plugin to Delicious Brains.', 'acf' ) . '</p>'
			)
		);

		// Help tab.
		$screen->add_help_tab(
			array(
				'id'      => 'help',
				'title'   => __( 'Help & Support', 'acf' ),
				'content' =>
					'<h4>' . __( 'Help & Support', 'acf' ) . '</h4>' .
					'<p>' . __( 'We are fanatical about support, and want you to get the best out of your website with ACF. If you run into any difficulties, there are several places you can find help:', 'acf' ) . '</p>' .
					'<ul>' .
						'<li>' . sprintf(
							__( '<a href="%s" target="_blank">Documentation</a>. Our extensive documentation contains references and guides for most situations you may encounter.', 'acf' ),
							'https://www.advancedcustomfields.com/resources/'
						) . '</li>' .
						'<li>' . sprintf(
							__( '<a href="%s" target="_blank">Discussions</a>. We have an active and friendly community on our Community Forums who may be able to help you figure out the ‘how-tos’ of the ACF world.', 'acf' ),
							'https://support.advancedcustomfields.com/'
						) . '</li>' .
					'</ul>'
			)
		);

		// Sidebar.
		$screen->set_help_sidebar( $this->help_sidebar() );
	}

	/**
	 * Help sidebar
	 *
	 * Content of the intro screen help tab sidebar.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function help_sidebar() {

		$html = sprintf(
			'<h5>%s</h5>',
			__( 'Information', 'acf' )
		);
		$html .= '<ul>';
		$html .= sprintf(
			'<li>%s %s</li>',
			__( 'Plugin version', 'acf' ),
			acf_get_setting( 'version' )
		);
		$html .= sprintf(
			'<li><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>',
			acf_get_setting( 'website' ),
			__( 'Visit the website', 'acf' )
		);
		$html .= '</ul>';

		return $html;
	}

	/**
	 * Admin page grid
	 *
	 * The HTML markup for the grid of content types
	 * on the first, default tab of the content screens.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_page_grid() {

		// Array of data from relevant post types.
		$types = $this->get_post_type_data();

		// Begin grid wrapping element and list.
		$html = '<div class="acf-content-grid"><ul>';

		foreach ( $types as $type ) {

			// Post count bubble tooltip.
			$tooltip = $type['count'] . ' ' . __( 'published', 'acf' );
			if ( 'attachment' === $type['slug'] ) {
				$tooltip = $type['count'] . ' ' . __( 'uploads', 'acf' );
			}

			// Post count bubble in grid item heading.
			$count = sprintf(
				'<span class="acf-js-tooltip post-count post-count-has-published" role="tooltip" title="%s">%s</span>',
				$tooltip,
				$type['count']
			);

			// Add new post link, different for `post` and `attachment`.
			$add_new = admin_url( 'post-new.php?post_type=' . $type['slug'] );
			if ( 'post' === $type['slug'] ) {
				$add_new = admin_url( 'post-new.php' );
			} elseif ( 'attachment' === $type['slug'] ) {
				$add_new = admin_url( 'media-new.php' );
			}

			// Manage posts link, different for `post` and `attachment`.
			$manage = admin_url( 'edit.php?post_type=' . $type['slug'] );
			if ( 'post' === $type['slug'] ) {
				$manage = admin_url( 'edit.php' );
			} elseif ( 'attachment' === $type['slug'] ) {
				$manage = admin_url( 'upload.php' );
			}

			// List item markup.
			$html .= '<li>';
			$html .= sprintf(
				'<h3>%s %s</h3>',
				$type['name'],
				$count
			);
			$html .= '<figure>';
			$html .= sprintf(
				'<div class="acf-grid-icon dashicons %s"></div>',
				$type['icon']
			);
			$html .= sprintf(
				'<figcaption><a href="%s">%s</a><a href="%s">%s</a></figcaption>',
				$add_new,
				__( 'Add New', 'acf' ),
				$manage,
				__( 'Manage', 'acf' )
			);
			$html .= '</figure>';
			$html .= '</li>';
		}

		// Field group categories.
		$html .= '<li>';
		$html .= sprintf(
			'<h3>%s <span class="post-count tax-count" title="%s %s">%s</span></h3>',
			__( 'Field Categories', 'acf' ),
			wp_count_terms( 'acf-field-group-category' ),
			_n( 'Category', 'Categories', intval( wp_count_terms( 'acf-field-group-category' ) ), 'acf' ),
			wp_count_terms( 'acf-field-group-category' )
		);
		$html .= '<figure>';
		$html .= sprintf(
			'<div class="acf-grid-icon dashicons %s"></div>',
			'dashicons-category'
		);
		$html .= sprintf(
			'<figcaption><a href="%s">%s</a></figcaption>',
			admin_url( 'edit-tags.php?taxonomy=acf-field-group-category' ),
			__( 'Manage', 'acf' )
		);
		$html .= '</figure>';
		$html .= '</li>';

		// End list and wrapping element.
		$html .= '</ul></div>';

		// Print the markup.
		echo $html;
	}

	/**
	 * ACF post types
	 *
	 * Post types registered by ACF and
	 * enabled by website options.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function builtin_post_types() {

		// Begin array of post types.
		$types = [];

		// If dynamic post types.
		if ( acf_get_setting( 'post_types' ) ) {
			$types = array_merge( $types, [ 'acf-post-type' ] );
		}

		// If dynamic taxonomies.
		if ( acf_get_setting( 'taxonomies' ) ) {
			$types = array_merge( $types, [ 'acf-taxonomy' ] );
		}

		// If dynamic block types.
		if ( ! function_exists( 'classicpress_version' ) ) {
			if ( acf_get_setting( 'block_types' ) ) {
				$types = array_merge( $types, [ 'acf-block-type' ] );
			}
		}

		// If dynamic forms.
		if ( acf_get_setting( 'forms' ) ) {
			$types = array_merge( $types, [ 'acf-form' ] );
		}

		// If dynamic templates.
		if ( acf_get_setting( 'templates' ) ) {
			$types = array_merge( $types, [ 'acf-template' ] );
		}

		// If dynamic options pages.
		if ( get_field( 'acf_options_pages', 'option' ) ) {
			$types = array_merge( $types, [ 'acf-options-page' ] );
		}

		$types = array_merge( $types, [ 'acf-field-group' ] );
		return apply_filters( 'acf/builtin_post_types', $types );
	}

	/**
	 * Custom post types query
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array Returns an array of queried post types.
	 */
	public function custom_post_types() {

		// Query public post types not built into the CMS.
		$query = [
			'public'   => true,
			'_builtin' => false
		];

		/**
		* Return post types query.
		* Escape namespace for native function.
		*/
		return get_post_types( $query, 'names', 'and' );
	}

	/**
	 * Get post type
	 *
	 * Gets registered post types for grid display.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_all_post_types() {

		// Get built-in and enabled ACF types
		$acf_types  = $this->builtin_post_types();

		/**
		* Native post types
		*
		* This array has the opinion that users should
		* upload and manage media to be published prior
		* to creating the post or page where the media
		* file will be used. Thus the `attachment` post
		* type is first, also placing it first in the
		* intro page grid.
		*
		* If you don't like this, a filter is applied.
		*/
		$native = [ 'attachment', 'post', 'page' ];

		// Get custom post types.
		$custom = $this->custom_post_types();
		asort( $custom );

		// Merge custom post with native types.
		$registered = array_merge( $native, $custom );

		// Merge plugin post types with native and custom types.
		$types = array_merge( $registered, $acf_types );

		return apply_filters( 'acf_get_post_types', $types );
	}

	/**
	 * Get post type data
	 *
	 * Gets data from the registered post types for grid display.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_post_type_data() {

		// Get queried post types and set up return fallback.
		$types = $this->get_all_post_types();
		$data  = null;

		// Loop through post types, if any found in custom query.
		if ( $types ) {
			foreach ( $types as $key => $type ) {

				/**
				* Set up variables.
				*
				* $object  Get the post type object
				* $count   Count the posts of the type.
				* $publish Number of post with publish status.
				* $cap     User capability for the post type.
				* $icon    The default menu if not set.
				*/
				$object  = get_post_type_object( $type );
				$count   = wp_count_posts( $type, '' );
				$publish = $count->publish;
				$cap     = $object->cap->edit_posts;
				$icon    = 'dashicons-admin-post';

				// Override default icon if set for the type.
				if ( $object->menu_icon ) {
					$icon = $object->menu_icon;
				}

				// The attachment post type doesn't use `publish` status.
				if ( 'attachment' === $type ) {
					$publish = $count->inherit;
				}

				// Set return array keys and values.
				$data[$key]['slug']  = $object->name;
				$data[$key]['cap']   = $cap;
				$data[$key]['name']  = $object->labels->menu_name;
				$data[$key]['icon']  = $icon;
				$data[$key]['count'] = intval( $publish );
			}
		}
		return $data;
	}

	/**
	 * Page excerpt default
	 *
	 * Make excerpts visible by default if used as meta descriptions.
	 * Add your post types as necessary.
	 *
	 * @since  1.0.0
	 * @param  array $hidden
	 * @param  object $screen
	 * @return array Unsets the hidden value in the screen base array.
	 */
	function show_page_excerpt_metabox( $hidden, $screen ) {

		$post_types = get_post_types();

		foreach ( $post_types as $post_type ) {
			if (
				$post_type == $screen->base &&
				post_type_supports( $post_type, 'excerpt' )
			) {
				foreach ( $hidden as $key=>$value ) {
					if ( 'postexcerpt' == $value ) {
						unset( $hidden[$key] );
						break;
					}
				}
			}
		}
		return $hidden;
	}

	/**
	 * Page excerpt metabox
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $wp_meta_boxes Access the metaboxes array.
	 * @return void
	 */
	public function page_excerpt_metabox() {

		// Access global variables.
		global $wp_meta_boxes;

		$post_types = [ 'page' ];
		if ( $post_types ) {

			foreach ( $post_types as $post_type ) {

				$type = get_post_type_object( $post_type );

				$wp_meta_boxes[ $type->name ]['normal']['core']['postexcerpt']['args'] = [];
				$wp_meta_boxes[ $type->name ]['normal']['core']['postexcerpt']['id'] = 'postexcerpt';
				$wp_meta_boxes[ $type->name ]['normal']['core']['postexcerpt']['title'] = __( 'Excerpt', 'acf' );
				$wp_meta_boxes[ $type->name ]['normal']['core']['postexcerpt']['callback'] = [ $this, 'page_excerpt_metabox_cb' ];
			}
		}
	}

	/**
	 * Page excerpt markup
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  WP_Post $post The post object.
	 * @return void
	 */
	public function page_excerpt_metabox_cb( $post ) {

	?>
	<p>
		<?php _e( 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="https://wordpress.org/documentation/article/what-is-an-excerpt-classic-editor/" target="_blank" rel="noopener noreferrer">Learn more about manual excerpts.</a>', 'acf' ); ?>
	</p>
	<label class="screen-reader-text" for="excerpt">
		<?php _e( 'Excerpt', 'acf' ); ?>
	</label>
	<textarea rows="1" cols="40" name="excerpt" id="excerpt">
		<?php echo $post->post_excerpt; ?>
	</textarea>
	<?php

	}
}

// Instantiate the class.
acf()->admin = acf_new_instance( 'ACF_Admin' );
