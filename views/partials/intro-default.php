<?php
/**
 * Default section on the intro page
 *
 * @package    ACF
 * @subpackage Views
 */

// Get filtered menu options.
$menu = acf_admin_menu();

?>
<section id="acf-intro" class="acf-intro-section">

	<h2><?php _e( 'Content Types', 'acf' ); ?></h2>

	<p><?php printf(
		__( 'Manage content types and add new using the following links. See the <a href="%s">about tab</a> for links to documentation and code examples.', 'acf' ),
		admin_url( 'admin.php?page=' . $menu['slug'] . '&tab=about' )
	); ?></p>

	<div class="acf-tab-grid">
		<ul>

			<li>
				<h3><?php _e( 'Field Groups', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-feedback"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acf-field-group' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acf-field-group' ); ?>"><?php _e( 'View All', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>

			<?php if ( acf_get_setting( 'acfe/modules/post_types' ) ) : ?>
			<li>
				<h3><?php _e( 'Post Types', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-admin-post"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-dpt' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-dpt' ); ?>"><?php _e( 'View All', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/taxonomies' ) ) : ?>
			<li>
				<h3><?php _e( 'Taxonomies', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-tag"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-dt' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-dt' ); ?>"><?php _e( 'View All', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/block_types' ) ) : ?>
			<li>
				<h3><?php _e( 'Block Types', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-block-default"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-dbt' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-dbt' ); ?>"><?php _e( 'View All', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/forms' ) ) : ?>
			<li>
				<h3><?php _e( 'Forms', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-forms"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-form' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-form' ); ?>"><?php _e( 'View All', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>

			<?php if ( acf_get_setting( 'acfe/modules/forms' ) ) : ?>
			<li>
				<h3><?php _e( 'Templates', 'acf' ); ?></h3>
				<figure>
					<div class="acf-tab-grid-icon dashicons dashicons-edit-page"></div>
					<figcaption>
						<a href="<?php echo admin_url( 'post-new.php?post_type=acfe-template' ); ?>"><?php _e( 'Add New', 'acf' ); ?></a>
						<a href="<?php echo admin_url( 'edit.php?post_type=acfe-template' ); ?>"><?php _e( 'View All', 'acf' ); ?></a>
					</figcaption>
				</figure>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</section>
