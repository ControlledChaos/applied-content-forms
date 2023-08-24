<?php
/**
 * Custom content settings/intro page
 *
 * @package    ACF
 * @subpackage Views
 */

// Get filtered menu options.
$menu = acf_admin_menu();

// Page description.
$desc = apply_filters(
	'acf_page_description',
	sprintf(
		'<p class="description">%s</p><hr />',
		__( 'A suite of tools for adding and managing custom content types and user forms.', 'acf' )
	)
);

// Get the active tab from the $_GET param.
$tab = null;
if ( isset( $_GET['tab'] ) ) {
	$tab = $_GET['tab'];
}

// Conditional tab links.
$default_link = sprintf(
	'<a href="%s" class="nav-tab%s">%s</a>',
	'',
	'',
	__( '', 'acf' )
);
$settings_link = sprintf(
	'<a href="%s" class="nav-tab%s">%s</a>',
	'',
	'',
	__( '', 'acf' )
);
$about_link = sprintf(
	'<a href="%s" class="nav-tab%s">%s</a>',
	'',
	'',
	__( '', 'acf' )
);

?>
<div class="wrap">

	<h1><?php echo $menu['page']; ?></h1>
	<?php echo $desc; ?>

	<nav class="nav-tab-wrapper">
		<style>.nav-tab-active { cursor: default; pointer-events: none; }</style>
		<ul>
			<li><a href="?page=<?php echo $menu['slug']; ?>" class="nav-tab <?php if ( $tab === null ) : ?>nav-tab-active<?php endif; ?>"><?php _e( 'Content', 'acf' ); ?></a>
			<li><a href="?page=<?php echo $menu['slug']; ?>&tab=settings" class="nav-tab <?php if ( $tab === 'settings' ) : ?>nav-tab-active<?php endif; ?>"><?php _e( 'Settings', 'acf' ); ?></a>
			<li><a href="<?php echo admin_url( 'admin.php?page=acf-tools' ); ?>" class="nav-tab <?php if ( $tab === 'tools' ) : ?>nav-tab-active<?php endif; ?>"><?php _e( 'Tools', 'acf' ); ?></a>
			<li><a href="?page=<?php echo $menu['slug']; ?>&tab=about" class="nav-tab <?php if ( $tab === 'about' ) : ?>nav-tab-active<?php endif; ?>"><?php _e( 'About', 'acf' ); ?></a>
		</ul>
	</nav>

	<div class="tab-content">
	<?php switch( $tab ) :
		case 'settings':
			acf_get_view_partial( 'intro-settings' );
		break;
		case 'tools':
			acf_get_view_partial( 'intro-tools' );
		break;
		case 'about':
			acf_get_view_partial( 'intro-about' );
		break;
		default:
			acf_get_view_partial( 'intro-default' );
		break;
	endswitch; ?>
	</div>
</div>
