<?php
/**
 * Admin database upgrade page
 *
 * Shows the database upgrade process.
 *
 * @package  ACF
 * @category Views
 * @since    5.7.4
 */

?>
<style type="text/css">
	.step-1,
	.step-2,
	.step-3 {
		display: none;
	}
</style>
<div id="acf-upgrade-wrap" class="wrap">

	<h1><?php _e( 'Upgrade Database', 'acf' ); ?></h1>

	<?php if ( acf_has_upgrade() ) : ?>

	<p><?php _e( 'Reading upgrade tasks...', 'acf' ); ?></p>

	<p class="step-1"><i class="acf-loading"></i> <?php printf( __( 'Upgrading data to version %s', 'acf' ), ACF_VERSION ); ?></p>

	<p class="step-2"></p>

	<p class="step-3"><?php echo sprintf( __( 'Database upgrade complete. <a href="%s">See what\'s new</a>', 'acf' ), admin_url( 'edit.php?post_type=acf-field-group' ) ); ?></p>

	<script type="text/javascript">
	( function($) {

		var upgrader = new acf.Model( {

			initialize : function() {

				// Allow user to read message for 1 second.
				this.setTimeout( this.upgrade, 1000 );
			},
			upgrade : function() {

				// Show step 1.
				$( '.step-1' ).show();

				var response = '';
				var success  = false;

				// Send AJAX request to upgrade the database.
				$.ajax( {
					url      : acf.get( 'ajaxurl' ),
					dataType : 'json',
					type     : 'post',
					data     : acf.prepareForAjax( {
						action : 'acf/ajax/upgrade'
					} ),
					success : function( json ) {
						success = true;
					},
					error : function( jqXHR, textStatus, errorThrown ) {
						response = '<?php _e( 'Upgrade failed.', 'acf' ); ?>';
						if ( error = acf.getXhrError( jqXHR ) ) {
							response += ' <code>' + error +  '</code>';
						}
					},
					complete : this.proxy( function() {

						// Remove spinner.
						$( '.acf-loading' ).hide();

						// Display response.
						if ( response ) {
							$( '.step-2' ).show().html( response );
						}

						// Display success.
						if ( success ) {
							$( '.step-3' ).show();
						}
					} )
				} );
			}
		} );
	} )(jQuery);
	</script>

	<?php else : ?>

	<p><?php _e( 'No updates available.', 'acf' ); ?></p>

	<?php endif; ?>
</div>
