<?php
/**
 * CMS functions
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Functions
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function acf_dashicon_icons() {

		return [
			'## suggested'  => '##' . __( 'General', 'acf' ),
			'admin-post'    => __( 'Pin 1', 'acf' ),
			'sticky'        => __( 'Pin 2', 'acf' ),
			'edit'          => __( 'Pencil', 'acf' ),
			'edit-large'    => __( 'Pencil Large', 'acf' ),
			'edit-page'     => __( 'Edit Page', 'acf' ),
			'admin-page'    => __( 'Pages', 'acf' ),
			'text-page'     => __( 'Text on Page', 'acf' ),
			'admin-generic' => __( 'Gear', 'acf' ),

			'## admin'         => '##' . __( 'CMS Admin', 'acf' ),
			'admin-appearance' => __( 'Appearance', 'acf' ),
			'admin-collapse'   => __( 'Collapse', 'acf' ),
			'admin-comments'   => __( 'Comments', 'acf' ),
			'admin-customizer' => __( 'Customizer', 'acf' ),
			'dashboard'        => __( 'Dashboard', 'acf' ),
			'filter'           => __( 'Filter', 'acf' ),
			'admin-home'       => __( 'Home', 'acf' ),
			'admin-links'      => __( 'Links', 'acf' ),
			'admin-media'      => __( 'Media', 'acf' ),
			'menu'             => __( 'Menu 1', 'acf' ),
			'menu-alt'         => __( 'Menu 2', 'acf' ),
			'menu-alt2'        => __( 'Menu 3', 'acf' ),
			'menu-alt3'        => __( 'Menu 4', 'acf' ),
			'admin-multisite'  => __( 'Multisite', 'acf' ),
			'admin-network'    => __( 'Network', 'acf' ),
			'admin-plugins'    => __( 'Plugins', 'acf' ),
			'plugins-checked'  => __( 'Plugins Checked', 'acf' ),
			'admin-settings'   => __( 'Settings', 'acf' ),
			'admin-site'       => __( 'Site 1', 'acf' ),
			'admin-site-alt'   => __( 'Site 2', 'acf' ),
			'admin-site-alt2'  => __( 'Site 3', 'acf' ),
			'admin-site-alt3'  => __( 'Site 4', 'acf' ),
			'admin-tools'      => __( 'Tools', 'acf' ),
			'admin-users'      => __( 'Users', 'acf' ),
			'welcome-add-page'      => __( 'Welcome Add Page', 'acf' ),
			'welcome-comments'      => __( 'Welcome Comments', 'acf' ),
			'welcome-edit-page'     => __( 'Welcome Edit Page', 'acf' ),
			'welcome-learn-more'    => __( 'Welcome Learn more', 'acf' ),
			'welcome-view-site'     => __( 'Welcome View Site', 'acf' ),
			'welcome-widgets-menus' => __( 'Welcome Widgets Menus', 'acf' ),
			'welcome-write-blog'    => __( 'Welcome Write Blog', 'acf' ),

			'## media'   => '##' . __( 'Media', 'acf' ),
			'book'       => __( 'Book 1', 'acf' ),
			'book-alt'   => __( 'Book 2', 'acf' ),
			'camera'     => __( 'Camera 1', 'acf' ),
			'camera-alt' => __( 'Camera 2', 'acf' ),
			'controls-back'        => __( 'Controls: Back', 'acf' ),
			'controls-forward'     => __( 'Controls: Forward', 'acf' ),
			'controls-pause'       => __( 'Controls: Pause', 'acf' ),
			'controls-play'        => __( 'Controls: Play', 'acf' ),
			'controls-repeat'      => __( 'Controls: Repeat', 'acf' ),
			'controls-skipback'    => __( 'Controls: Skip Back', 'acf' ),
			'controls-skipforward' => __( 'Controls: Skip Forward', 'acf' ),
			'controls-volumeoff'   => __( 'Controls: Volume Off', 'acf' ),
			'controls-volumeon'    => __( 'Controls: Volume On', 'acf' ),
			'images-alt'  => __( 'Images 1', 'acf' ),
			'images-alt2' => __( 'Images 2', 'acf' ),
			'megaphone'   => __( 'Megaphone', 'acf' ),
			'microphone'  => __( 'Microphone', 'acf' ),
			'playlist-audio' => __( 'Playlist: Audio', 'acf' ),
			'playlist-video' => __( 'Playlist: Video', 'acf' ),
			'slides'      => __( 'Slides', 'acf' ),
			'video-alt'   => __( 'Video 1', 'acf' ),
			'video-alt2'  => __( 'Video 2', 'acf' ),
			'video-alt3'  => __( 'Video 3', 'acf' ),

			'## notifications' => '##' . __( 'Notifications', 'acf' ),
			'bell'        => __( 'Bell', 'acf' ),
			'dismiss'     => __( 'Dismiss', 'acf' ),
			'flag'        => __( 'Flag', 'acf' ),
			'minus'       => __( 'Minus', 'acf' ),
			'marker'      => __( 'Marker', 'acf' ),
			'no'          => __( 'No 1', 'acf' ),
			'no-alt'      => __( 'No 2', 'acf' ),
			'plus'        => __( 'Plus 1', 'acf' ),
			'plus-alt'    => __( 'Plus 2', 'acf' ),
			'plus-alt2'   => __( 'Plus 3', 'acf' ),
			'star-empty'  => __( 'Star Empty', 'acf' ),
			'star-filled' => __( 'Star Filled', 'acf' ),
			'star-half'   => __( 'Star Half', 'acf' ),
			'warning'     => __( 'Warning', 'acf' ),
			'yes'         => __( 'Yes 1', 'acf' ),
			'yes-alt'     => __( 'Yes 2', 'acf' ),

			'## misc'   => '##' . __( 'Miscellaneous', 'acf' ),
			'airplane'  => __( 'Airplane', 'acf' ),
			'album'     => __( 'Album', 'acf' ),
			'analytics' => __( 'Analytics', 'acf' ),
			'art'       => __( 'Art', 'acf' ),
			'awards'    => __( 'Awards', 'acf' ),
			'backup'    => __( 'Backup', 'acf' ),
			'bank'      => __( 'Bank', 'acf' ),
			'beer'      => __( 'Beer', 'acf' ),
			'building'  => __( 'Building', 'acf' ),
			'businessman'    => __( 'Businessman', 'acf' ),
			'businesswoman'  => __( 'Businesswoman', 'acf' ),
			'businessperson' => __( 'Businessperson', 'acf' ),
			'calculator'     => __( 'Calculator', 'acf' ),
			'calendar'       => __( 'Calendar 1', 'acf' ),
			'calendar-alt'   => __( 'Calendar 2', 'acf' ),
			'car'    => __( 'Car', 'acf' ),
			'carrot' => __( 'Carrot', 'acf' ),
			'cart'   => __( 'Cart', 'acf' ),
			'category'   => __( 'Category', 'acf' ),
			'chart-area' => __( 'Chart Area', 'acf' ),
			'chart-bar'  => __( 'Chart Bar', 'acf' ),
			'chart-line' => __( 'Chart Line', 'acf' ),
			'chart-pie'  => __( 'Chart Pie', 'acf' ),
			'clipboard'  => __( 'Clipboard', 'acf' ),
			'clock'      => __( 'Clock', 'acf' ),
			'code-standards' => __( 'Code Standards', 'acf' ),
			'coffee'         => __( 'Coffee', 'acf' ),
			'color-picker'   => __( 'Color Picker', 'acf' ),
			'database'         => __( 'Database', 'acf' ),
			'database-add'     => __( 'Database Add', 'acf' ),
			'database-export'  => __( 'Database Export', 'acf' ),
			'database-import'  => __( 'Database Import', 'acf' ),
			'database-remove'  => __( 'Database Remove', 'acf' ),
			'database-view'    => __( 'Database View', 'acf' ),
			'desktop'    => __( 'Desktop', 'acf' ),
			'archive'    => __( 'Document Archive', 'acf' ),
			'download'   => __( 'Download', 'acf' ),
			'drumstick'  => __( 'Drumbstick', 'acf' ),
			'email'      => __( 'Email 1', 'acf' ),
			'email-alt'  => __( 'Email 2', 'acf' ),
			'email-alt2' => __( 'Email 3', 'acf' ),
			'external'     => __( 'External', 'acf' ),
			'feedback'     => __( 'Feedback', 'acf' ),
			'food'   => __( 'Food', 'acf' ),
			'forms'  => __( 'Forms', 'acf' ),
			'fullscreen-alt'      => __( 'Full Screen', 'acf' ),
			'fullscreen-exit-alt' => __( 'Full Screen Exit', 'acf' ),
			'games'      => __( 'Games', 'acf' ),
			'groups'     => __( 'Groups', 'acf' ),
			'hammer'     => __( 'Hammer', 'acf' ),
			'heart'      => __( 'Heart', 'acf' ),
			'hidden'     => __( 'Hidden', 'acf' ),
			'hourglass'  => __( 'Hourglass', 'acf' ),
			'id'         => __( 'ID 1', 'acf' ),
			'id-alt'     => __( 'ID 2', 'acf' ),
			'image-crop'   => __( 'Image: Crop', 'acf' ),
			'image-filter' => __( 'Image: Filter', 'acf' ),
			'image-flip-horizontal' => __( 'Image: Flip Horizontal', 'acf' ),
			'image-flip-vertical'   => __( 'Image: Flip Vertical', 'acf' ),
			'image-rotate'          => __( 'Image: Rotate', 'acf' ),
			'image-rotate-left'     => __( 'Image: Rotate Left', 'acf' ),
			'image-rotate-right'    => __( 'Image: Rotate Right', 'acf' ),
			'index-card'   => __( 'Index Card', 'acf' ),
			'info'         => __( 'Info', 'acf' ),
			'laptop'       => __( 'Laptop', 'acf' ),
			'layout'       => __( 'Layout', 'acf' ),
			'lightbulb'    => __( 'Light Bulb', 'acf' ),
			'location'     => __( 'Location 1', 'acf' ),
			'location-alt' => __( 'Location 2', 'acf' ),
			'lock'   => __( 'Lock', 'acf' ),
			'migrate'     => __( 'Migrate', 'acf' ),
			'money'       => __( 'Money', 'acf' ),
			'money-alt'   => __( 'Dollar Sign', 'acf' ),
			'nametag'     => __( 'Name Tag', 'acf' ),
			'networking'  => __( 'Networking', 'acf' ),
			'open-folder' => __( 'Open Folder', 'acf' ),
			'palmtree'    => __( 'Palm Tree', 'acf' ),
			'paperclip'   => __( 'Paper Clip', 'acf' ),
			'performance' => __( 'Performance', 'acf' ),
			'pets'        => __( 'Pets', 'acf' ),
			'phone'       => __( 'Phone', 'acf' ),
			'portfolio'     => __( 'Portfolio', 'acf' ),
			'post-status'   => __( 'Post-status', 'acf' ),
			'pressthis'     => __( 'Pressthis', 'acf' ),
			'printer'       => __( 'Printer', 'acf' ),
			'privacy'       => __( 'Privacy', 'acf' ),
			'products'      => __( 'Products', 'acf' ),
			'redo'          => __( 'Redo', 'acf' ),
			'rest-api'      => __( 'Rest API', 'acf' ),
			'rss'           => __( 'RSS', 'acf' ),
			'schedule'      => __( 'Schedule', 'acf' ),
			'search'        => __( 'Search', 'acf' ),
			'shield'        => __( 'Shield 1', 'acf' ),
			'shield-alt'    => __( 'Shield 2', 'acf' ),
			'smartphone'    => __( 'Smartphone', 'acf' ),
			'smiley'        => __( 'Smiley', 'acf' ),
			'post-trash'    => __( 'Trash', 'acf' ),
			'sos'           => __( 'SOS', 'acf' ),
			'store'         => __( 'Store Front', 'acf' ),
			'superhero'     => __( 'Superhero 1', 'acf' ),
			'superhero-alt' => __( 'Superhero 2', 'acf' ),
			'tablet'        => __( 'Tablet', 'acf' ),
			'tag'           => __( 'Tag', 'acf' ),
			'tagcloud'      => __( 'Tag Cloud', 'acf' ),
			'testimonial'   => __( 'Testimonial', 'acf' ),
			'text'          => __( 'Text', 'acf' ),
			'thumbs-down'   => __( 'Thumbs down', 'acf' ),
			'thumbs-up'     => __( 'Thumbs up', 'acf' ),
			'tickets'       => __( 'Tickets 1', 'acf' ),
			'tickets-alt'   => __( 'Tickets 2', 'acf' ),
			'translation'   => __( 'Translation', 'acf' ),
			'trash'         => __( 'Trash', 'acf' ),
			'undo'          => __( 'Undo', 'acf' ),
			'universal-access'     => __( 'Universal Access 1', 'acf' ),
			'universal-access-alt' => __( 'Universal Access 2', 'acf' ),
			'unlock'     => __( 'Unlock', 'acf' ),
			'update'     => __( 'Update 1', 'acf' ),
			'update-alt' => __( 'Update 2', 'acf' ),
			'upload'     => __( 'Upload', 'acf' ),
			'vault'      => __( 'Vault', 'acf' ),
			'visibility' => __( 'Visibility', 'acf' ),

			'## editor'          => '##' . __( 'Content Editor', 'acf' ),
			'editor-break'       => __( 'Editor Break', 'acf' ),
			'editor-code'        => __( 'Editor Code', 'acf' ),
			'editor-contract'    => __( 'Editor Contract', 'acf' ),
			'editor-customchar'  => __( 'Editor Custom Character', 'acf' ),
			'editor-distractionfree' => __( 'Editor Full Screen', 'acf' ),
			'editor-expand'      => __( 'Editor Expand', 'acf' ),
			'editor-help'        => __( 'Editor Help', 'acf' ),
			'editor-insertmore'  => __( 'Editor Insert More', 'acf' ),
			'editor-kitchensink' => __( 'Editor Kitchen Sink', 'acf' ),
			'editor-ltr'         => __( 'Editor Left-to-Right', 'acf' ),
			'editor-ol-rtl'      => __( 'Editor Ordered List Left-to-Right', 'acf' ),
			'editor-paragraph'   => __( 'Editor Paragraph', 'acf' ),
			'editor-paste-text'  => __( 'Editor Paste Text', 'acf' ),
			'editor-paste-word'  => __( 'Editor Paste Word', 'acf' ),
			'editor-quote'       => __( 'Editor Quote', 'acf' ),
			'editor-removeformatting' => __( 'Editor Remove Formatting', 'acf' ),
			'editor-rtl'           => __( 'Editor Right-to-Left', 'acf' ),
			'editor-spellcheck'    => __( 'Editor Spell Check', 'acf' ),
			'editor-table'         => __( 'Editor Table', 'acf' ),
			'editor-textcolor'     => __( 'Editor Text Color', 'acf' ),
			'editor-video'         => __( 'Editor Video', 'acf' ),
			'align-center'         => __( 'Image Align Center', 'acf' ),
			'align-left'           => __( 'Image Align Left', 'acf' ),
			'align-none'           => __( 'Image Align None', 'acf' ),
			'align-right'          => __( 'Image Align Right', 'acf' ),
			'editor-aligncenter'   => __( 'Text Align Center', 'acf' ),
			'editor-justify'       => __( 'Text Align Justify', 'acf' ),
			'editor-alignleft'     => __( 'Text Align Left', 'acf' ),
			'editor-alignright'    => __( 'Text Align Right', 'acf' ),
			'editor-bold'          => __( 'Text Bold', 'acf' ),
			'editor-indent'        => __( 'Text Indent', 'acf' ),
			'editor-italic'        => __( 'Text Italic', 'acf' ),
			'editor-ol'            => __( 'Text Ordered List', 'acf' ),
			'editor-outdent'       => __( 'Text Outdent', 'acf' ),
			'editor-strikethrough' => __( 'Text Strike Through', 'acf' ),
			'editor-ul'            => __( 'Text Unordered List', 'acf' ),
			'editor-underline'     => __( 'Text Underline', 'acf' ),
			'editor-unlink'        => __( 'Text Unlink', 'acf' ),

			'## g-editor' => '##' . __( 'Block Editor', 'acf' ),
			'align-full-width' => __( 'Align Full Width', 'acf' ),
			'align-pull-left'  => __( 'Align Pull Left', 'acf' ),
			'align-pull-right' => __( 'Align Pull Right', 'acf' ),
			'align-wide'       => __( 'Align Wide', 'acf' ),
			'button'           => __( 'Button', 'acf' ),
			'cover-image'      => __( 'Cover Image', 'acf' ),
			'cloud'            => __( 'Cloud', 'acf' ),
			'cloud-saved'      => __( 'Cloud Saved', 'acf' ),
			'cloud-upload'     => __( 'Cloud Upload', 'acf' ),
			'columns'          => __( 'Columns', 'acf' ),
			'block-default'    => __( 'Default Block', 'acf' ),
			'ellipsis'      => __( 'Ellipsis', 'acf' ),
			'embed-audio'   => __( 'Embed Audio', 'acf' ),
			'embed-generic' => __( 'Embed Generic', 'acf' ),
			'embed-photo'   => __( 'Embed Photo', 'acf' ),
			'embed-post'    => __( 'Embed Post', 'acf' ),
			'embed-video'   => __( 'Embed Video', 'acf' ),
			'exit'          => __( 'Exit', 'acf' ),
			'heading'       => __( 'Heading', 'acf' ),
			'html'          => __( 'HTML', 'acf' ),
			'info-outline'  => __( 'Info Outline', 'acf' ),
			'insert'        => __( 'Insert', 'acf' ),
			'insert-after'  => __( 'Insert After', 'acf' ),
			'insert-before' => __( 'Insert Before', 'acf' ),
			'remove'        => __( 'Remove', 'acf' ),
			'saved'         => __( 'Saved', 'acf' ),
			'shortcode'     => __( 'Shortcode', 'acf' ),
			'table-col-after'  => __( 'Table Column After', 'acf' ),
			'table-col-before' => __( 'Table Column Before', 'acf' ),
			'table-col-delete' => __( 'Table Column Delete', 'acf' ),
			'table-row-after'  => __( 'Table Row After', 'acf' ),
			'table-row-before' => __( 'Table Row Before', 'acf' ),
			'table-row-delete' => __( 'Table Row Delete', 'acf' ),

			'## sorting'       => '##' . __( 'Sorting', 'acf' ),
			'arrow-up'         => __( 'Arrow Up 1', 'acf' ),
			'arrow-up-alt'     => __( 'Arrow Up 2', 'acf' ),
			'arrow-up-alt2'    => __( 'Arrow Up 3', 'acf' ),
			'arrow-down'       => __( 'Arrow Down 1', 'acf' ),
			'arrow-down-alt'   => __( 'Arrow Down 2', 'acf' ),
			'arrow-down-alt2'  => __( 'Arrow Down 3', 'acf' ),
			'arrow-left'       => __( 'Arrow Left 1', 'acf' ),
			'arrow-left-alt'   => __( 'Arrow Left 2', 'acf' ),
			'arrow-left-alt2'  => __( 'Arrow Left 3', 'acf' ),
			'arrow-right'      => __( 'Arrow Right 1', 'acf' ),
			'arrow-right-alt'  => __( 'Arrow Right 2', 'acf' ),
			'arrow-right-alt2' => __( 'Arrow Right 3', 'acf' ),
			'leftright'     => __( 'Left-Right', 'acf' ),
			'move'          => __( 'Move', 'acf' ),
			'randomize'     => __( 'Randomize', 'acf' ),
			'screenoptions' => __( 'Screen Options', 'acf' ),
			'sort'          => __( 'Sort', 'acf' ),
			'excerpt-view'  => __( 'View: Excerpt', 'acf' ),
			'grid-view'     => __( 'View: Grid', 'acf' ),
			'list-view'     => __( 'View: List', 'acf' ),

			'## format' => '##' . __( 'Post Formats', 'acf' ),
			'format-aside'    => __( 'Format: Aside', 'acf' ),
			'format-audio'    => __( 'Format: Audio', 'acf' ),
			'format-chat'     => __( 'Format: Chat', 'acf' ),
			'format-gallery'  => __( 'Format: Gallery', 'acf' ),
			'format-image'    => __( 'Format: Image', 'acf' ),
			'format-links'    => __( 'Format: Links', 'acf' ),
			'format-quote'    => __( 'Format: Quote', 'acf' ),
			'format-standard' => __( 'Format: Standard', 'acf' ),
			'format-status'   => __( 'Format: Status', 'acf' ),
			'format-video'    => __( 'Format: Video', 'acf' ),

			'## files' => '##' . __( 'File Types', 'acf' ),
			'media-archive'  => __( 'File: Archive', 'acf' ),
			'media-audio'    => __( 'File: Audio', 'acf' ),
			'media-code'     => __( 'File: Code', 'acf' ),
			'media-default'  => __( 'File: Default', 'acf' ),
			'media-document' => __( 'File: Document', 'acf' ),
			'media-interactive' => __( 'File: Interactive', 'acf' ),
			'pdf'               => __( 'File: PDF', 'acf' ),
			'media-spreadsheet' => __( 'File: Spreadsheet', 'acf' ),
			'media-text'  => __( 'File: Text', 'acf' ),
			'media-video' => __( 'File: Video', 'acf' ),

			'## social'  => '##' . __( 'Social Content', 'acf' ),
			'share'      => __( 'Share 1', 'acf' ),
			'share-alt'  => __( 'Share 2', 'acf' ),
			'share-alt2' => __( 'Share 3', 'acf' ),
			'buddicons-bbpress-logo'    => __( 'bbPress Logo', 'acf' ),
			'buddicons-buddypress-logo' => __( 'BuddyPress Logo', 'acf' ),
			'buddicons-activity'  => __( 'Buddicons Activity', 'acf' ),
			'buddicons-community' => __( 'Buddicons Community', 'acf' ),
			'buddicons-forums'    => __( 'bbPress Forums', 'acf' ),
			'buddicons-friends'   => __( 'Buddicons Friends', 'acf' ),
			'buddicons-groups'    => __( 'Buddicons Groups', 'acf' ),
			'buddicons-pm'        => __( 'Buddicons PM', 'acf' ),
			'buddicons-replies'   => __( 'Buddicons Replies', 'acf' ),
			'buddicons-topics'    => __( 'Buddicons Topics', 'acf' ),
			'buddicons-tracking'  => __( 'Buddicons Tracking', 'acf' ),
			'amazon'       => __( 'Amazon', 'acf' ),
			'facebook'     => __( 'Facebook 1', 'acf' ),
			'facebook-alt' => __( 'Facebook 2', 'acf' ),
			'google'       => __( 'Google', 'acf' ),
			'googleplus'   => __( 'Google+', 'acf' ),
			'instagram'    => __( 'Instagram', 'acf' ),
			'linkedin'     => __( 'LinkedIn', 'acf' ),
			'pinterest'    => __( 'Pinterest', 'acf' ),
			'podio'        => __( 'Podio', 'acf' ),
			'reddit'       => __( 'Reddit', 'acf' ),
			'twitch'       => __( 'Twitch', 'acf' ),
			'twitter'      => __( 'Twitter', 'acf' ),
			'spotify'      => __( 'Spotify', 'acf' ),
			'whatsapp'     => __( 'WhatsApp', 'acf' ),
			'xing'         => __( 'Xing', 'acf' ),
			'youtube'      => __( 'YouTube', 'acf' )
		];
	}

/**
 * Get object type
 *
 * Returns a CMS object type.
 *
 * @since  1.0.0
 * @param  string $object_type The object type (post, term, user, etc).
 * @param  string $object_subtype Optional object subtype (post type, taxonomy).
 * @return object
 */
function acf_get_object_type( $object_type, $object_subtype = '' ) {

	$props = [
		'type'    => $object_type,
		'subtype' => $object_subtype,
		'name'    => '',
		'label'   => '',
		'icon'    => ''
	];

	// Set unique identifier as name.
	if ( $object_subtype ) {
		$props['name'] = "$object_type/$object_subtype";
	} else {
		$props['name'] = $object_type;
	}

	// Set label and icon.
	switch ( $object_type ) {
		case 'post':
			if ( $object_subtype ) {
				$post_type = get_post_type_object( $object_subtype );
				if ( $post_type ) {
					$props['label'] = $post_type->labels->name;
					$props['icon']  = acf_with_default( $post_type->menu_icon, 'dashicons-admin-post' );
				} else {
					return false;
				}
			} else {
				$props['label'] = __( 'Posts', 'acf' );
				$props['icon']  = 'dashicons-admin-post';
			}
			break;
		case 'term':
			if ( $object_subtype ) {
				$taxonomy = get_taxonomy( $object_subtype );
				if ( $taxonomy ) {
					$props['label'] = $taxonomy->labels->name;
				} else {
					return false;
				}
			} else {
				$props['label'] = __( 'Taxonomies', 'acf' );
			}
			$props['icon'] = 'dashicons-tag';
			break;
		case 'attachment':
			$props['label'] = __( 'Attachments', 'acf' );
			$props['icon']  = 'dashicons-admin-media';
			break;
		case 'comment':
			$props['label'] = __( 'Comments', 'acf' );
			$props['icon']  = 'dashicons-admin-comments';
			break;
		case 'widget':
			$props['label'] = __( 'Widgets', 'acf' );
			$props['icon']  = 'dashicons-screenoptions';
			break;
		case 'menu':
			$props['label'] = __( 'Menus', 'acf' );
			$props['icon']  = 'dashicons-admin-appearance';
			break;
		case 'menu_item':
			$props['label'] = __( 'Menu items', 'acf' );
			$props['icon']  = 'dashicons-admin-appearance';
			break;
		case 'user':
			$props['label'] = __( 'Users', 'acf' );
			$props['icon']  = 'dashicons-admin-users';
			break;
		case 'option':
			$props['label'] = __( 'Options', 'acf' );
			$props['icon']  = 'dashicons-admin-generic';
			break;
		case 'block':
			$props['label'] = __( 'Blocks', 'acf' );
			$props['icon']  = acf_version_compare( 'wp', '>=', '5.5' ) ? 'dashicons-block-default' : 'dashicons-layout';
			break;
		default:
			return false;
	}

	// Convert to object.
	$object = (object) $props;

	return apply_filters( 'acf/get_object_type', $object, $object_type, $object_subtype );
}

/**
 * Decode post ID
 *
 * Decodes a post_id value such as 1 or "user_1" into
 * an array containing the type and ID.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @return array
 */
function acf_decode_post_id( $post_id = 0 ) {

	$type = '';
	$id   = 0;

	// Interpret numeric value (123).
	if ( is_numeric( $post_id ) ) {
		$type = 'post';
		$id   = $post_id;

	// Interpret string value ("user_123" or "option").
	} elseif ( is_string( $post_id ) ) {
		$i = strrpos( $post_id, '_' );
		if ( $i > 0 ) {
			$type = substr( $post_id, 0, $i );
			$id   = substr( $post_id, $i + 1 );
		} else {
			$type = $post_id;
			$id   = '';
		}

	// Handle incorrect param type.
	} else {
		return compact( 'type', 'id' );
	}

	// Validate props based on param format.
	$format = $type . '_' . ( is_numeric( $id ) ? '%d' : '%s' );
	switch ( $format ) {
		case 'post_%d':
			$type = 'post';
			$id   = absint( $id );
			break;
		case 'term_%d':
			$type = 'term';
			$id   = absint( $id );
			break;
		case 'attachment_%d':
			$type = 'post';
			$id   = absint( $id );
			break;
		case 'comment_%d':
			$type = 'comment';
			$id = absint( $id );
			break;
		case 'widget_%s':
		case 'widget_%d':
			$type = 'option';
			$id   = $post_id;
			break;
		case 'menu_%d':
			$type = 'term';
			$id   = absint( $id );
			break;
		case 'menu_item_%d':
			$type = 'post';
			$id   = absint( $id );
			break;
		case 'user_%d':
			$type = 'user';
			$id   = absint( $id );
			break;
		case 'block_%s':
			$type = 'block';
			$id   = $post_id;
			break;
		case 'option_%s':
			$type = 'option';
			$id   = $post_id;
			break;
		case 'blog_%d':
		case 'site_%d':
			// Allow backwards compatibility for custom taxonomies.
			$type = taxonomy_exists( $type ) ? 'term' : 'blog';
			$id = absint( $id );
			break;
		default:
			// Check for taxonomy name.
			if ( taxonomy_exists( $type ) && is_numeric( $id ) ) {
				$type = 'term';
				$id   = absint( $id );
				break;
			}

			// Treat unknown post_id format as an option.
			$type = 'option';
			$id   = $post_id;
			break;
	}

	return apply_filters( 'acf/decode_post_id', compact( 'type', 'id' ), $post_id );
}

/**
 * Get registered image sizes
 *
 * Clone of wp_get_registered_image_subsizes.
 *
 * @since  1.0.0
 * @param  mixed $filter
 * @return mixed
 */
function acf_get_registered_image_sizes( $filter = false ) {

	$additional = wp_get_additional_image_sizes();
	$all_sizes  = [];
	$wp_sizes   = get_intermediate_image_sizes();
	$wp_sizes[] = 'full';

	foreach ( $wp_sizes as $size_name ) {

		if ( $filter && $size_name !== $filter ) {
			continue;
		}
		$size_data = [
			'name'   => $size_name,
			'width'  => 0,
			'height' => 0,
			'crop'   => false
		];

		// For sizes added by plugins and themes.
		if ( isset( $additional[ $size_name ]['width'] ) ) {
			$size_data['width'] = (int) $additional[ $size_name ]['width'];

		// For default sizes set in options.
		} else {
			$size_data['width'] = (int) get_option( "{$size_name}_size_w" );
		}

		if ( isset( $additional[ $size_name ]['height'] ) ) {
			$size_data['height'] = (int) $additional[ $size_name ]['height'];
		} else {
			$size_data['height'] = (int) get_option( "{$size_name}_size_h" );
		}

		if ( isset( $additional[ $size_name ]['crop'] ) ) {
			$size_data['crop'] = $additional[ $size_name ]['crop'];
		} else {
			$size_data['crop'] = get_option( "{$size_name}_crop" );
		}

		if ( ! is_array( $size_data['crop'] ) || empty( $size_data['crop'] ) ) {
			$size_data['crop'] = (bool) $size_data['crop'];
		}
		$all_sizes[ $size_name ] = $size_data;
	}

	if ( $filter && isset( $all_sizes[ $filter ] ) ) {
		return $all_sizes[ $filter ];
	}
	return $all_sizes;
}

/**
 * Remove class filter
 *
 * Removes hook from inaccessible PHP class.
 * @link https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
 *
 * @since  1.0.0
 * @param  string $tag
 * @param  string $class_name
 * @param  string $method_name
 * @param  integer $priority
 * @global array $wp_filter
 * @return boolean
 */
function acf_remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

	// Access global variables.
	global $wp_filter;

	// Check that filter actually exists first.
	if ( ! isset( $wp_filter[ $tag ] ) ) {
		return FALSE;
	}

	/**
	 * To be backwards compatible, set $callbacks equal
	 * to the correct array as a reference so $wp_filter
	 * is updated.
	 */
	if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {

		// Create $fob object from filter tag to use below.
		$fob       = $wp_filter[ $tag ];
		$callbacks = &$wp_filter[ $tag ]->callbacks;
	} else {
		$callbacks = &$wp_filter[ $tag ];
	}

	// Exit if there aren't any callbacks for specified priority.
	if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
		return false;
	}

	// Loop through each filter for the specified priority to look for class & method.
	foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

		// Filter should always be an array - array( $this, 'method' ), if not go to next.
		if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
			continue;
		}

		// If first value in array is not an object, it can't be a class.
		if ( ! is_object( $filter['function'][0] ) ) {
			continue;
		}

		// Method doesn't match the one looked for, go to next.
		if ( $filter['function'][1] !== $method_name ) {
			continue;
		}

		// Method matched, now check the class.
		if ( get_class( $filter['function'][0] ) === $class_name ) {

			if ( isset( $fob ) ) {

				// Handles removing filter, reseting callback priority keys mid-iteration, etc.
				$fob->remove_filter( $tag, $filter['function'], $priority );

			} else {

				// Use legacy removal process (pre 4.7).
				unset( $callbacks[ $priority ][ $filter_id ] );

				// If it was the only filter in that priority, unset that priority.
				if ( empty( $callbacks[ $priority ] ) ) {
					unset( $callbacks[ $priority ] );
				}

				// If the only filter for that tag, set the tag to an empty array.
				if ( empty( $callbacks ) ) {
					$callbacks = [];
				}

				// Remove this filter from merged_filters, which specifies if filters have been sorted
				unset( $GLOBALS['merged_filters'][ $tag ] );
			}
			return true;
		}
	}
	return false;
}

/**
 * Remove class action
 *
 * @since  1.0.0
 * @param  string $tag
 * @param  string $class_name
 * @param  string $method_name
 * @param  integer $priority
 * @return boolean
 */
function acf_remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
	return acf_remove_class_filter( $tag, $class_name, $method_name, $priority );
}
