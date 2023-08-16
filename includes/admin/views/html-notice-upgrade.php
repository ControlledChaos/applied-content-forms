<?php
/**
 * Admin database upgrade notice
 *
 * Shows the database upgrade process.
 *
 * @package  ACF
 * @category Views
 * @since    5.7.4
 */

// Calculate add-ons.
$plugins = [];

if ( ! acf_get_setting( 'pro' ) ) {

	if ( is_plugin_active( 'acf-repeater/acf-repeater.php' ) ) {
		$plugins[] = __( 'Repeater', 'acf' );
	}
	if ( is_plugin_active( 'acf-flexible-content/acf-flexible-content.php' ) ) {
		$plugins[] = __( 'Flexible Content', 'acf' );
	}
	if ( is_plugin_active( 'acf-gallery/acf-gallery.php' ) ) {
		$plugins[] = __( 'Gallery', 'acf' );
	}
	if ( is_plugin_active( 'acf-options-page/acf-options-page.php' ) ) {
		$plugins[] = __( 'Options Page', 'acf' );
	}
}

?>
<div id="acf-upgrade-notice" class="notice">

	<div class="col-content">

		<h2><?php _e( 'Database Upgrade Required', 'acf' ); ?></h2>
		<p><?php _e( 'The active version of the Applied Content Fields plugin contains improvements to your database and requires an upgrade.', 'acf' ); ?></p>
		<?php if ( ! empty( $plugins ) ) : ?>
			<p><?php printf( __( 'Please also check all compatible Advanced Custom Fields add-ons (%s) are updated to the latest version.', 'acf' ), implode( ', ', $plugins ) ); ?></p>
		<?php endif; ?>
	</div>

	<div class="col-actions">
		<a id="acf-upgrade-button" href="<?php echo $button_url; ?>" class="button button-primary button-hero"><?php echo $button_text; ?></a>
	</div>

</div>
<?php

if ( $confirm ) : ?>
<script type="text/javascript">
(function($) {
	$( '#acf-upgrade-button' ).on( 'click', function(){
		return confirm( "<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'acf' ); ?>" );
	});
})(jQuery);
</script>
<?php

endif;
