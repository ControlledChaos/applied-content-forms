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
		admin_url( "admin.php?page={$menu['slug']}&tab=about" )
	); ?></p>

	<p><?php _e( 'Number icons next to content type names represent the number of that content type which has been published.', 'acf' ); ?></p>

	<?php do_action( 'acf/post_types_grid' ); ?>
</section>
