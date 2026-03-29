<?php

?>
<div class="wrap acf-content-intro">
	<h1><?php _e( 'Content Management', 'acf' ); ?></h1>
	<p class="description"><?php echo acf_get_setting( 'desc' ); ?></p>

	<?php do_action( 'acf/post_types_grid' ); ?>

	<h2><?php _e( 'Search Content', 'acf' ); ?></h2>

	<div class="acf-content-search">
		<form role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" target="_blank" rel="nofollow noreferrer noopener">
		<?php $content_id = 'site-' . get_current_blog_id() . '-acf-search-content'; ?>
			<fieldset>
				<label class="screen-reader-text" for="<?php echo $content_id; ?>" aria-label="<?php _e( 'Search Content', 'acf' ); ?>"><?php _e( 'Search Content', 'acf' ); ?></label>

				<input type="search" name="s" id="<?php echo $content_id; ?>" aria-labelledby="<?php _e( 'Search Content', 'acf' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off" placeholder="<?php _e( 'Enter title or content search terms', 'acf' ); ?>" aria-placeholder="<?php _e( 'Enter content search terms', 'acf' ); ?>" />
				<?php submit_button( __( 'Search Content', 'acf' ), '', false, false, [ 'id' => 'submit-' . $content_id ] ); ?>
			</fieldset>
		</form>

		<?php if ( current_user_can( 'upload_files' ) ) : ?>
		<form role="search" action="<?php echo self_admin_url( 'upload.php' ); ?>" method="get">
			<?php $media_id = 'site-' . get_current_blog_id() . '-acf-search-media'; ?>
			<fieldset>
				<label class="screen-reader-text" for="<?php echo $media_id; ?>" aria-label="<?php _e( 'Search Media', 'acf' ); ?>"><?php _e( 'Search Media', 'acf' ); ?></label>

				<input type="search" name="search" id="<?php echo $media_id; ?>" aria-labelledby="<?php _e( 'Search Media', 'acf' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off" placeholder="<?php _e( 'Enter media title, meta, or filename', 'acf' ); ?>" aria-placeholder="<?php _e( 'Enter media title or filename', 'acf' ); ?>" />
				<?php submit_button( __( 'Search Media', 'acf' ), '', false, false, [ 'id' => 'submit-' . $media_id ] ); ?>
			</fieldset>
		</form>
		<?php endif; ?>
	</div>
</div>
