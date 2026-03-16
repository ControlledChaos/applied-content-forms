<?php

// Get filtered menu options.
$menu = acf_admin_menu();
?>
<div class="wrap">
	<h1><?php echo acf_get_setting( 'name' ); ?></h1>
	<p class="description"><?php echo acf_get_setting( 'desc' ); ?></p>
</div>
