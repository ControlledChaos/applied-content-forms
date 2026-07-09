<?php
/**
 * Top level admin page
 *
 * Gets content from the `ACF_Admin` class.
 *
 * @package    Applied Content Forms
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

?>
<?php do_action( 'acf/before_admin_page' ); ?>

<div class="wrap acf-content-intro">

	<h1><?php echo acf()->admin->admin_page_title(); ?></h1>

	<?php echo acf()->admin->admin_page_content(); ?>

</div>
<?php do_action( 'acf/after_admin_page' ); ?>
