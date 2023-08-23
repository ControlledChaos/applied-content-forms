<?php
/**
 * About section on the intro page
 *
 * @package    ACF
 * @subpackage Views
 *
 * @todo Update development text as needed.
 */

// Access namespaced functions.
use function ACF\acf;

// Get filtered menu options.
$menu = acf_admin_menu();

?>
<section id="acf-intro-about" class="acf-intro-section">

	<h2><?php echo __( 'About ', 'acf' ) . acf()->get_setting( 'name' ); ?></h2>

	<p><?php printf(
		__( 'The <a href="%s" target="_blank" rel="noopener nofollow">%s</a> plugin is a fork (<a href="%s" target="_blank" rel="noopener nofollow">legal copy</a>) of the <a href="%s" target="_blank" rel="noopener nofollow">Advanced Custom Fields PRO</a> plugin version %s. It includes a fork of <a href="%s" target="_blank" rel="noopener nofollow">Advanced Custom Fields: Extended PRO</a> plugin version %s for added functionality.', 'acf' ),
		esc_url( acf()->get_setting( 'site' ) ),
		acf()->get_setting( 'name' ),
		esc_url( 'https://www.gnu.org/licenses/old-licenses/gpl-2.0.html' ),
		esc_url( 'https://www.advancedcustomfields.com/pro/' ),
		acf()->get_setting( 'version' ),
		esc_url( 'https://www.acf-extended.com/pro' ),
		acfe()->version
	); ?></p>
	<p><?php _e( 'Please follow the provided links to find documentation and code examples for ACF PRO and ACFE PRO features.', 'acf' ); ?></p>

	<h3><?php _e( 'Development Plan', 'acf' ); ?></h3>

	<p><?php _e( 'The current development of the plugin is as follows.' ); ?></p>
	<ul style="list-style: disc; padding: 0 1em;">
		<li><?php _e( 'Cleaning up code, bringing them to WordPress coding standards and making the easier to read.' ); ?></li>
		<li><?php _e( 'Consolidating the ACF and ACFE plugin files.' ); ?></li>
		<li><?php _e( 'Implementing any security updates added to the two original plugins since forking.' ); ?></li>
		<li><?php _e( 'Implementing select feature updates added to the two original plugins since forking.' ); ?></li>
		<li><?php _e( 'Perhaps adding field types from other plugins.' ); ?></li>
	</ul>

	<h3><?php _e( 'Reason for Forking ACF', 'acf' ); ?></h3>

	<p><?php printf(
		'The Advanced Custom Fields (ACF) plugin was <a href="%s" target="_blank" rel="noopener nofollow">launched in 2011</a> by Australian Eliot Condon, and it was a game changer for WordPress website development. From the moment I discovered ACF I was hooked and began to use it for all client websites.',
		esc_url( 'https://www.advancedcustomfields.com/blog/10-years-of-acf-a-truly-wonderful-time/' )
	); ?></p>
	<p><?php _e( 'One of the many things I appreciated about Eliot\'s approach to developing ACF was that while he did build a successful business from the product the plugin integrated seamlessly in the the native WordPress and ClassicPress admin screens. Branding was minimal, with a small logo in an upgrade metabox, which did not appear in the PRO version. All of the user interfaces inherited native admin styles and at no time did Eliot try to force his style onto we users.', 'acf' ); ?></p>
	<p><?php _e( 'When I heard that Eliot Condon had sold Advanced Custom Fields to a large web corporation in late May, 2021, I immediately worried that the new owners would put their branding stink all over my admin screens, and make admin theming more difficult. These worries have been proven not be be in vain, as the post-Eliot ACF interface steps greatly outside of native styles.', 'acf' ); ?></p>

	<h3><?php _e( 'Reason for Forking ACFE', 'acf' ); ?></h3>

	<p><?php _e( 'The Advanced Custom Fields: Extended PRO was added to the Applied Content Forms project because it is a great enhancement to Advanced Custom Fields. However, it is far to opinionated for me. It modifies the look and layout various native WordPress/ClassicPress user interface elements with no option of filter to override.', 'acf' ); ?></p>
	<p><?php _e( 'So I felt the need to take the plugin in a different direction. I was also concerned that the new owner of ACF would begin to cannibalize ACFE and/or implement their own versions of the ACFE features. Sure enough, ACF now offers custom post types and custom taxonomies.', 'acf' ); ?></p>
	<p><?php _e( 'ACFE was not added until more than two years after first forking Advanced Custom Fields PRO. And the PRO version used was nearly two years old. I paid the developer in full for a copy of this PRO version.', 'acf' ); ?></p>
	<p><?php _e( 'Also, the development of ACFE has slowed significantly in 2023. As of August 2023 the last update was March 2023. Updates had previous been quite frequent. It seems appropriate to now modify ACFE PRO.', 'acf' ); ?></p>

	<h3><?php _e( 'Applied Content Forms', 'acf' ); ?></h3>

	<p><?php _e( 'So upon hearing the news of ACF going corporate I put the most recent copy, at that time, of the PRO version of the plugin on GitHub.', 'acf' ); ?></p>
	<p><?php _e( 'Now, it is legal to publicly post a copy of software released under the GPL license but it is generally frowned upon to make freely available the work of someone whose income is reliant on the product. But Eliot had received all he was going to to receive from the Advanced Custom fields project. I knew changes were coming and just 32 days after the release of version 5.9.6 by Eliot Condon, version 5.9.7 was released by the new corporate owner.', 'acf' ); ?></p>

	<h3><?php _e( 'The Bottom Line', 'acf' ); ?></h3>

	<p><?php _e( 'The Applied Content Forms plugin does not take money out of Eliot Condon\'s pocket since was paid out and it does not take money out of the pocket of the new corporate owner since the code was written by Eliot before their tenure or by me thereafter. The plugin does not take money out of the pocket of the ACFE developer since he was paid for the copy used here.', 'acf' ); ?></p>

	<?php printf(
		'<p><strong>%s</strong> <br />%s</p>',
		'Greg Sweet',
		'Controlled Chaos Design'
	); ?>

</section>
