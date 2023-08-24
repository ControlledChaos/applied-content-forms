<?php
/**
 * Default section on the intro page
 *
 * @package    ACF
 * @subpackage Views
 */

// Get filtered menu options.
$menu = acf_admin_menu();

// Get post type objects.
$get_post = get_post_type_object( 'post' );
$get_page = get_post_type_object( 'page' );

// Count published pages.
$posts_count = '';
if ( post_type_exists( 'post' ) ) {
	$posts = wp_count_posts( 'post', '' );
	if ( $posts && $posts->publish > 0 ) {
		$posts_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$posts->publish . ' ' . __( 'published' ),
			$posts->publish
		);
	}
}

// Count published pages.
$pages_count = '';
if ( post_type_exists( 'page' ) ) {
	$pages = wp_count_posts( 'page', '' );
	if ( $pages && $pages->publish > 0 ) {
		$pages_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$pages->publish . ' ' . __( 'published' ),
			$pages->publish
		);
	}
}

// Count published field groups.
$fields_count = '';
if ( post_type_exists( 'acf-field-group' ) ) {
	$fields = wp_count_posts( 'acf-field-group', '' );
	if ( $fields && $fields->publish > 0 ) {
		$fields_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$fields->publish . ' ' . __( 'published' ),
			$fields->publish
		);
	}
}

// Count published post types.
$types_count = '';
if ( post_type_exists( 'acfe-dpt' ) ) {
	$types = wp_count_posts( 'acfe-dpt', '' );
	if ( $types && $types->publish > 0 ) {
		$types_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$types->publish . ' ' . __( 'published' ),
			$types->publish
		);
	}
}

// Count published taxonomies.
$taxes_count = '';
if ( post_type_exists( 'acfe-dt' ) ) {
	$taxes = wp_count_posts( 'acfe-dt', '' );
	if ( $taxes && $taxes->publish > 0 ) {
		$taxes_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$taxes->publish . ' ' . __( 'published' ),
			$taxes->publish
		);
	}
}

// Count published block types.
$blocks_count = '';
if ( post_type_exists( 'acfe-dbt' ) ) {
	$blocks = wp_count_posts( 'acfe-dbt', '' );
	if ( $blocks && $blocks->publish > 0 ) {
		$blocks_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$blocks->publish . ' ' . __( 'published' ),
			$blocks->publish
		);
	}
}

// Count published forms.
$forms_count = '';
if ( post_type_exists( 'acfe-form' ) ) {
	$forms = wp_count_posts( 'acfe-form', '' );
	if ( $forms && $forms->publish > 0 ) {
		$forms_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$forms->publish . ' ' . __( 'published' ),
			$forms->publish
		);
	}
}

// Count published templates.
$templates_count = '';
if ( post_type_exists( 'acfe-template' ) ) {
	$templates = wp_count_posts( 'acfe-template', '' );
	if ( $templates && $templates->publish > 0 ) {
		$templates_count = sprintf(
			' <span class="post-count" title="%s">%s</span>',
			$templates->publish . ' ' . __( 'published' ),
			$templates->publish
		);
	}
}

?>
<section id="acf-intro" class="acf-intro-section">

	<h2><?php _e( 'Content Types', 'acf' ); ?></h2>

	<p><?php printf(
		__( 'Manage content types and add new using the following links. See the <a href="%s">about tab</a> for links to documentation and code examples.', 'acf' ),
		admin_url( "admin.php?page={$menu['slug']}&tab=about" )
	); ?></p>

	<p><?php _e( 'Number icons next to content type names represent the number of that content type which has been published.', 'acf' ); ?></p>

	<div class="acf-tab-grid">
		<ul>

			<li>
				<h3><?php _e( 'Media', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-admin-media"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'media-new.php' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'upload.php' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>

			<li>
				<h3><?php echo $get_post->labels->menu_name; echo $posts_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons <?php echo $get_post->menu_icon; ?>"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>

			<li>
				<h3><?php echo $get_page->labels->menu_name; echo $pages_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons <?php echo $get_page->menu_icon; ?>"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=' . $get_page->name ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=' . $get_page->name ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>

			<?php if ( acf_get_setting( 'acfe/modules/field_groups' ) ) : ?>
			<li>
				<h3><?php _e( 'Field Groups', 'acf' ); echo $fields_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-feedback"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acf-field-group' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acf-field-group' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/post_types' ) ) : ?>
			<li>
				<h3><?php _e( 'Post Types', 'acf' ); echo $types_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-sticky"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-dpt' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-dpt' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/taxonomies' ) ) : ?>
			<li>
				<h3><?php _e( 'Taxonomies', 'acf' ); echo $taxes_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-tag"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-dt' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-dt' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/block_types' ) ) : ?>
			<li>
				<h3><?php _e( 'Block Types', 'acf' ); echo $blocks_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-block-default"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-dbt' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-dbt' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/forms' ) ) : ?>
			<li>
				<h3><?php _e( 'Forms', 'acf' ); echo $forms_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-forms"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-form' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-form' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/templates' ) ) : ?>
			<li>
				<h3><?php _e( 'Templates', 'acf' ); echo $templates_count; ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-edit-page"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-template' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-template' ); ?>"><?php _e( 'Manage', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>
		</ul>
		<?php echo acf_content_types_message(); ?>
	</div>
</section>
