<?php
/**
 * Settings section on the intro page
 *
 * @package    ACF
 * @subpackage Views
 */

?>
<section id="acf-intro-settings" class="acf-intro-section">

	<h2><?php _e( 'Content Settings', 'acf' ); ?></h2>

	<p><?php _e( 'Please use these settings with caution, particularly those under the Options tab.', 'acf' ); ?></p>

	<?php do_action( 'acfe/admin_settings/html' ); ?>

</section>
