<?php
/**
 * ACF upgrade notice
 *
 * @package    Applied Content Forms
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

?>
<div id="acf-upgrade-notice" class="notice">
	<h2><?php _e( 'Database Upgrade Required','acf'); ?></h2>
		<p><?php printf( __( 'Thank you for updating %s.', 'acf' ), acf_get_setting( 'name' ) ); ?><br /><?php _e( 'This version contains improvements to your database and requires an upgrade.', 'acf' ); ?></p>
		<p><a id="acf-upgrade-button" href="<?php echo $button_url; ?>" class="button button-primary button-hero"><?php echo $button_text; ?></a></p>
</div>
<?php if( $confirm ): ?>
<script type="text/javascript">
(function($) {

	$("#acf-upgrade-button").on("click", function(){
		return confirm("<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'acf' ); ?>");
	});

})(jQuery);
</script>
<?php endif;
