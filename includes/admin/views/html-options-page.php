<?php
/**
 * Options page template
 *
 * @package    Applied Content Forms
 * @subpackage Admin
 * @category   Views
 * @since      1.0.0
 */

$field_groups = acf_get_field_groups( [
	'options_page' => $menu_slug
] );

?>
<div class="wrap acf-settings-wrap">

	<h1><?php echo $page_title; ?></h1>
	<p class="description"><?php echo $page_desc; ?></p>

	<?php do_action( 'acf/options_page/before_form' ); ?>

	<form id="post" method="post" name="post">
		<?php
		acf_form_data( [
			'screen'  => 'options',
			'post_id' => $post_id,
		] );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

		?>
		<div id="poststuff" class="poststuff">
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( 'acf_options_page', 'side', null ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( 'acf_options_page', 'normal', null ); ?>
				</div>
			</div>
			<br class="clear" />
			<?php if ( ! empty( $field_groups ) && 'bottom' === $update_location ) : ?>
			<p class="submit">
				<input type="submit" accesskey="p" value="<?php echo $update_button; ?>" class="button button-primary button-large" id="publish" name="publish" />
				<span class="spinner"></span>
			</p>
			<?php endif; ?>
		</div>
	</form>
	<?php do_action( 'acf/options_page/after_form' ); ?>
</div>
