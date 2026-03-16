<?php
/**
*  Admin tools page
*
*  View to output admin tools for both archive and single
*
*  @since 1.0.0
*/

$class = $active ? 'single' : 'grid';

?>
<div class="wrap" id="acf-admin-tools">

	<h1><?php _e( 'Content Tools', 'acf' ); ?> <?php if ( $active ) : ?><a class="page-title-action" href="<?php echo acf_get_admin_tools_url(); ?>"><?php _e( 'Back to all tools', 'acf' ); ?></a><?php endif; ?></h1>

	<div class="acf-meta-box-wrap -<?php echo $class; ?>">
		<?php do_meta_boxes( $screen_id, 'normal', '' ); ?>
	</div>
</div>
