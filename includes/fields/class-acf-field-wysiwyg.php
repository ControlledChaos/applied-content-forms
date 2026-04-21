<?php

if( ! class_exists('acf_field_wysiwyg') ) :

class acf_field_wysiwyg extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function initialize() {

		// vars
		$this->name = 'wysiwyg';
		$this->label = __("Wysiwyg Editor",'acf');
		$this->category = 'content';
		$this->defaults = array(
			'tabs'			=> 'all',
			'toolbar'		=> 'full',
			'media_upload' 	=> 1,
			'default_value'	=> '',
			'delay'			=> 0
		);


    	// add acf_the_content filters
    	$this->add_filters();

    	// actions
    	add_action('acf/enqueue_uploader', array($this, 'acf_enqueue_uploader'));

		add_action('acf/field_group/admin_head',                        array($this, 'prepare_toolbars'));

		add_action('template_redirect',                                 array($this, 'source_code_iframe'));
	}


	/*
	*  add_filters
	*
	*  This function will add filters to 'acf_the_content'
	*
	*  @type	function
	*  @date	20/09/2016
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function add_filters() {

		// WordPress 5.5 introduced new function for applying image tags.
		$wp_filter_content_tags = function_exists('wp_filter_content_tags') ? 'wp_filter_content_tags' : 'wp_make_content_images_responsive';

		// Mimic filters added to "the_content" in "wp-includes/default-filters.php".
		add_filter( 'acf_the_content', 'capital_P_dangit', 11 );
		//add_filter( 'acf_the_content', 'do_blocks', 9 ); Not yet supported.
		add_filter( 'acf_the_content', 'wptexturize' );
		add_filter( 'acf_the_content', 'convert_smilies', 20 );
		add_filter( 'acf_the_content', 'wpautop' );
		add_filter( 'acf_the_content', 'shortcode_unautop' );
		//add_filter( 'acf_the_content', 'prepend_attachment' ); Causes double image on attachment page.
		add_filter( 'acf_the_content', $wp_filter_content_tags );
		add_filter( 'acf_the_content', 'do_shortcode', 11);

		// Mimic filters added to "the_content" in "wp-includes/class-wp-embed.php"
		if(	isset($GLOBALS['wp_embed']) ) {
			add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
			add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
		}

		// WP TinyMCE
        //add_filter('mce_buttons', ($this, 'mce_buttons'));
        //add_filter('tiny_mce_before_init', ($this, 'mce_init'), 10, 2);
        //add_filter('wp_editor_settings', ($this, 'mce_settings'));
		add_filter('acfe/field_wrapper_attributes/type=wysiwyg',        array($this, 'field_wrapper'), 10, 2);

		add_filter('acf/fields/wysiwyg/toolbars',                       array($this, 'toolbars'));

        // ACF Editor
        add_filter('mce_external_plugins',                              array($this, 'mce_plugins'));
	}


	/*
	*  get_toolbars
	*
	*  This function will return an array of toolbars for the WYSIWYG field
	*
	*  @type	function
	*  @date	18/04/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	(array)
	*/

   	function get_toolbars() {

		// vars
		$editor_id = 'acf_content';
		$toolbars = array();


		// mce buttons (Full)
		$mce_buttons = array( 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'blockquote', 'alignleft', 'aligncenter', 'alignright', 'link', 'wp_more', 'spellchecker', 'fullscreen', 'wp_adv' );
		$mce_buttons_2 = array( 'strikethrough', 'hr', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo', 'wp_help' );

		// mce buttons (Basic)
		$teeny_mce_buttons = array('bold', 'italic', 'underline', 'blockquote', 'strikethrough', 'bullist', 'numlist', 'alignleft', 'aligncenter', 'alignright', 'undo', 'redo', 'link', 'fullscreen');


		// WP < 4.7
		if( acf_version_compare('wp', '<', '4.7') ) {

			$mce_buttons = array( 'bold', 'italic', 'strikethrough', 'bullist', 'numlist', 'blockquote', 'hr', 'alignleft', 'aligncenter', 'alignright', 'link', 'unlink', 'wp_more', 'spellchecker', 'fullscreen', 'wp_adv' );
			$mce_buttons_2 = array( 'formatselect', 'underline', 'alignjustify', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo', 'wp_help' );
		}


		// Full
   		$toolbars['Full'] = array(
   			1 => apply_filters('mce_buttons',	$mce_buttons,	$editor_id),
   			2 => apply_filters('mce_buttons_2', $mce_buttons_2,	$editor_id),
   			3 => apply_filters('mce_buttons_3', array(),		$editor_id),
   			4 => apply_filters('mce_buttons_4', array(),		$editor_id)
   		);


   		// Basic
   		$toolbars['Basic'] = array(
   			1 => apply_filters('teeny_mce_buttons', $teeny_mce_buttons, $editor_id)
   		);


   		// Filter for 3rd party
   		$toolbars = apply_filters( 'acf/fields/wysiwyg/toolbars', $toolbars );


   		// return
	   	return $toolbars;

   	}

	   /*
     * Add Basic Enhanced Toolbar
     */
    function toolbars($toolbars){

        $toolbars['Basic Enhanced'] = array(
            1 => array('formatselect', 'link', 'bold', 'italic', 'underline', 'blockquote', '|', 'bullist', 'numlist', 'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|', 'source_code', 'wp_add_media')
        );

        return $toolbars;

    }

    /*
     * WP Editor: Load Source Code plugin
     */
    function mce_plugins($plugins){

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        $plugins['source_code'] = acf_get_url('assets/inc/tinymce/source-code' . $suffix . '.js');

        return $plugins;

    }

    /*
     * Source Code iFrame
     */
    function source_code_iframe(){

        if(!acf_maybe_get_GET('acfe_wysiwyg_source_code'))
            return;

        wp_enqueue_script('code-editor');
        wp_enqueue_style('code-editor');

        ?><!DOCTYPE html>
        <html lang="en-US">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <?php wp_head(); ?>
            <style type="text/css" media="print">#wpadminbar{display:none;}</style>
            <style type="text/css" media="screen">
                html,
                * html body{
                    margin-top:0 !important;
                }

                @media screen and (max-width:782px){

                    html,
                    * html body{
                        margin-top:0 !important;
                    }

                }
            </style>

            <script>
                var pjQuery = parent.jQuery;
                var tinymce = parent.tinymce;
                var editor = tinymce.activeEditor;

                var $source = pjQuery(editor.container).closest('.wp-editor-container').find('.wp-editor-area');
                var source = $source[0];
                var codemirror;

                (function($){

                    window.onload = function(){

                        // Textarea
                        var textarea = document.body.querySelector('textarea');

                        // Value
                        textarea.value = source.value;

                        // Settings
                        var Settings = {};

                        Settings.codemirror = $.extend(wp.codeEditor.defaultSettings.codemirror, {
                            autofocus: true,
                            lineNumbers: true,
                            lineWrapping: true,
                            styleActiveLine: true,
                            continueComments: true,
                            indentUnit: 4,
                            tabSize: 1,
                            indentWithTabs: true,
                            mode: 'text/html',
                            extraKeys: {
                                Tab: function(cm){
                                    cm.execCommand("indentMore")
                                },
                                "Shift-Tab": function(cm){
                                    cm.execCommand("indentLess")
                                },
                            },
                        });

                        // Init
                        codemirror = wp.codeEditor.initialize(textarea, Settings);

                    };

                    document.onkeydown = function(e){

                        e = e || window.event;
                        var isEscape = false;

                        isEscape = (e.keyCode === 27);

                        if("key" in e){
                            isEscape = (e.key === "Escape" || e.key === "Esc");
                        }

                        if(isEscape){
                            tinymce.activeEditor.windowManager.close();
                        }

                    };

                })(jQuery);

                function submit(){

                    var code = codemirror.codemirror.getValue();

                    parent.window.switchEditors.go(editor.id);

                    source.value = code;

                    parent.window.switchEditors.go(editor.id);

                }

            </script>

            <style type="text/css">
                html,
                body{
                    height:100%;
                    margin: 0;
                    padding:0;
                }

                .CodeMirror{
                    height: 100%;
                    font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
                    font-size: 14px;
                    line-height: 1.4;
                }

                .CodeMirror-activeline-background{
                    background:#f9f9f9;
                }

                .CodeMirror-selected{
                    background:#f0f0f0 !important;
                }

                .CodeMirror-gutters{
                    background:#f9f9f9;
                }
            </style>

        </head>

        <body>
        <textarea style="border:0; visibility:hidden;"></textarea>
        </body>

        </html>
        <?php
        exit;

    }


   	/*
	*  acf_enqueue_uploader
	*
	*  Registers toolbars data for the WYSIWYG field.
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	void
	*  @return	void
	*/

	function acf_enqueue_uploader() {

		// vars
		$data = array();
		$toolbars = $this->get_toolbars();

		// loop
		if( $toolbars ) {
		foreach( $toolbars as $label => $rows ) {

			// vars
			$key = $label;
			$key = sanitize_title( $key );
			$key = str_replace('-', '_', $key);


			// append
			$data[ $key ] = array();

			if( $rows ) {
				foreach( $rows as $i => $row ) {
					$data[ $key ][ $i ] = implode(',', $row);
				}
			}
		}}

		// localize
	   	acf_localize_data(array(
		   	'toolbars'	=> $data
	   	));
	}

   	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function render_field( $field ) {

		// enqueue
		acf_enqueue_uploader();


		// vars
		$id = uniqid('acf-editor-');
		$default_editor = 'html';
		$show_tabs = true;


		// get height
		$height = acf_get_user_setting('wysiwyg_height', 300);
		$height = max( $height, 300 ); // minimum height is 300


		// detect mode
		if( !user_can_richedit() ) {

			$show_tabs = false;

		} elseif( $field['tabs'] == 'visual' ) {

			// case: visual tab only
			$default_editor = 'tinymce';
			$show_tabs = false;

		} elseif( $field['tabs'] == 'text' ) {

			// case: text tab only
			$show_tabs = false;

		} elseif( wp_default_editor() == 'tinymce' ) {

			// case: both tabs
			$default_editor = 'tinymce';

		}


		// must be logged in tp upload
		if( !current_user_can('upload_files') ) {

			$field['media_upload'] = 0;

		}


		// mode
		$switch_class = ($default_editor === 'html') ? 'html-active' : 'tmce-active';


		// filter
		$field['value'] = apply_filters( 'acf_the_editor_content', $field['value'], $default_editor );


		// attr
		$wrap = array(
			'id'			=> 'wp-' . $id . '-wrap',
			'class'			=> 'acf-editor-wrap wp-core-ui wp-editor-wrap ' . $switch_class,
			'data-toolbar'	=> $field['toolbar']
		);


		// delay
		if( $field['delay'] ) {
			$wrap['class'] .= ' delay';
		}


		// vars
		$textarea = acf_get_textarea_input(array(
			'id'	=> $id,
			'class'	=> 'wp-editor-area',
			'name'	=> $field['name'],
			'style'	=> $height ? "height:{$height}px;" : '',
			'value'	=> '%s'
		));

		?>
		<div <?php acf_esc_attr_e($wrap); ?>>
			<div id="wp-<?php echo esc_attr( $id ); ?>-editor-tools" class="wp-editor-tools hide-if-no-js">
				<?php if( $field['media_upload'] ): ?>
				<div id="wp-<?php echo esc_attr( $id ); ?>-media-buttons" class="wp-media-buttons">
					<?php
					if( !function_exists( 'media_buttons' ) ) {
						require ABSPATH . 'wp-admin/includes/media.php';
					}
					do_action( 'media_buttons', $id );
					?>
				</div>
				<?php endif; ?>
				<?php if( user_can_richedit() && $show_tabs ): ?>
					<div class="wp-editor-tabs">
						<button id="<?php echo esc_attr( $id ); ?>-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="<?php echo esc_attr( $id ); ?>" type="button"><?php echo __('Visual', 'acf'); ?></button>
						<button id="<?php echo esc_attr( $id ); ?>-html" class="wp-switch-editor switch-html" data-wp-editor-id="<?php echo esc_attr( $id ); ?>" type="button"><?php echo _x( 'Text', 'Name for the Text editor tab (formerly HTML)', 'acf' ); ?></button>
					</div>
				<?php endif; ?>
			</div>
			<div id="wp-<?php echo esc_attr( $id ); ?>-editor-container" class="wp-editor-container">
				<?php if( $field['delay'] ): ?>
					<div class="acf-editor-toolbar"><?php _e('Click to initialize TinyMCE', 'acf'); ?></div>
				<?php endif; ?>
				<?php printf( $textarea, $field['value'] ); ?>
			</div>
		</div>
		<?php

	}


	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function render_field_settings( $field ) {

		// vars
		$toolbars = $this->get_toolbars();
		$choices = array();

		if( !empty($toolbars) ) {

			foreach( $toolbars as $k => $v ) {

				$label = $k;
				$name = sanitize_title( $label );
				$name = str_replace('-', '_', $name);

				$choices[ $name ] = $label;
			}
		}


		// default_value
		acf_render_field_setting( $field, array(
			'label'			=> __('Default Value','acf'),
			'instructions'	=> __('Appears when creating a new post','acf'),
			'type'			=> 'textarea',
			'name'			=> 'default_value',
		));


		// tabs
		acf_render_field_setting( $field, array(
			'label'			=> __('Tabs','acf'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'tabs',
			'choices'		=> array(
				'all'			=>	__("Visual & Text",'acf'),
				'visual'		=>	__("Visual Only",'acf'),
				'text'			=>	__("Text Only",'acf'),
			)
		));


		// toolbar
		acf_render_field_setting( $field, array(
			'label'			=> __('Toolbar','acf'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'toolbar',
			'choices'		=> $choices,
			'conditions'	=> array(
				'field'		=> 'tabs',
				'operator'	=> '!=',
				'value'		=> 'text'
			)
		));


		// media_upload
		acf_render_field_setting( $field, array(
			'label'			=> __('Show Media Upload Buttons?','acf'),
			'instructions'	=> '',
			'name'			=> 'media_upload',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));


		// delay
		acf_render_field_setting( $field, array(
			'label'			=> __('Delay initialization?','acf'),
			'instructions'	=> __('TinyMCE will not be initialized until field is clicked','acf'),
			'name'			=> 'delay',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'conditions'	=> array(
				'field'		=> 'tabs',
				'operator'	=> '!=',
				'value'		=> 'text'
			)
		));


		// Height
        acf_render_field_setting($field, array(
            'label'             => __('Height'),
            'name'              => 'acfe_wysiwyg_height',
            'key'               => 'acfe_wysiwyg_height',
            'instructions'      => '',
            'type'              => 'number',
            'default_value'     => '300',
            'min'               => 0,
            'conditional_logic' => array(
                array(
                    array(
                        'field'     => 'acfe_wysiwyg_autoresize',
                        'operator'  => '!=',
                        'value'  => '1',
                    ),
                )
            )
        ));

        // Min Height (Autoresize)
        acf_render_field_setting($field, array(
            'label'             => __('Height'),
            'name'              => 'acfe_wysiwyg_min_height',
            'key'               => 'acfe_wysiwyg_min_height',
            'instructions'      => '',
            'type'              => 'number',
            'default_value'     => '300',
            'min'               => 0,
            'prepend'           => 'min',
            'append'            => 'px',
            'conditional_logic' => array(
                array(
                    array(
                        'field'     => 'acfe_wysiwyg_autoresize',
                        'operator'  => '==',
                        'value'  => '1',
                    ),
                )
            )
        ));

        // Max Height (Autoresize)
        acf_render_field_setting($field, array(
            'label'             => __('Height'),
            'name'              => 'acfe_wysiwyg_max_height',
            'key'               => 'acfe_wysiwyg_max_height',
            'instructions'      => '',
            'type'              => 'number',
            'default_value'     => '',
            'min'               => 0,
            'prepend'           => 'max',
            'append'            => 'px',
            '_append'           => 'acfe_wysiwyg_min_height',
            'conditional_logic' => array(
                array(
                    array(
                        'field'     => 'acfe_wysiwyg_autoresize',
                        'operator'  => '==',
                        'value'  => '1',
                    ),
                )
            )
        ));

        // Valid Elements
        acf_render_field_setting($field, array(
            'label'             => __('Valid Elements'),
            'name'              => 'acfe_wysiwyg_valid_elements',
            'key'               => 'acfe_wysiwyg_valid_elements',
            'instructions'      => __('Set custom valid HTML tags'),
            'type'              => 'text',
            'placeholder'       => 'Use comma-separated values. ie: p,div,strong/b,em/i,br,a',
            'wrapper'           => array(
                'data-enable-switch' => true
            )
        ));

        // Prepend
        $prepend = acfe_get_setting('theme_folder') ? trailingslashit(acfe_get_setting('theme_folder')) : '';

        // Style
        $prepend = apply_filters("acfe/wysiwyg/prepend/style",                         $prepend, $field);
        $prepend = apply_filters("acfe/wysiwyg/prepend/style/name={$field['name']}",   $prepend, $field);
        $prepend = apply_filters("acfe/wysiwyg/prepend/style/key={$field['key']}",     $prepend, $field);

        // Custom Style
        acf_render_field_setting($field, array(
            'label'             => __('Custom Style'),
            'name'              => 'acfe_wysiwyg_custom_style',
            'key'               => 'acfe_wysiwyg_custom_style',
            'instructions'      => __('Add multiple files separated with comma'),
            'type'              => 'text',
            'prepend'           => $prepend,
            'placeholder'       => 'style.css',
            'wrapper'           => array(
                'data-enable-switch' => true
            )
        ));

        // Disable WP Style
        acf_render_field_setting($field, array(
            'label'             => __('Disable WP Style'),
            'name'              => 'acfe_wysiwyg_disable_wp_style',
            'key'               => 'acfe_wysiwyg_disable_wp_style',
            'instructions'      => __('Remove TinyMCE builtin stylesheets'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Auto Resize
        acf_render_field_setting($field, array(
            'label'             => __('Autoresize'),
            'name'              => 'acfe_wysiwyg_autoresize',
            'key'               => 'acfe_wysiwyg_autoresize',
            'instructions'      => __('Height will be based on the editor content'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Disable resize
        acf_render_field_setting($field, array(
            'label'             => __('Disable Resize'),
            'name'              => 'acfe_wysiwyg_disable_resize',
            'key'               => 'acfe_wysiwyg_disable_resize',
            'instructions'      => __('Remove the editor resize functionality'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Disable Path
        acf_render_field_setting($field, array(
            'label'             => __('Disable Path'),
            'name'              => 'acfe_wysiwyg_remove_path',
            'key'               => 'acfe_wysiwyg_remove_path',
            'instructions'      => __('Hide the editor path status bar'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Menu Bar
        acf_render_field_setting($field, array(
            'label'             => __('Menu Bar'),
            'name'              => 'acfe_wysiwyg_menubar',
            'key'               => 'acfe_wysiwyg_menubar',
            'instructions'      => __('Show the menu bar on top of the editor'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Transparent Editor
        acf_render_field_setting($field, array(
            'label'             => __('Transparent Editor'),
            'name'              => 'acfe_wysiwyg_transparent',
            'key'               => 'acfe_wysiwyg_transparent',
            'instructions'      => __('Set the editor\'s background as transparent'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Merge Toolbars
        acf_render_field_setting($field, array(
            'label'             => __('Merge Toolbars'),
            'name'              => 'acfe_wysiwyg_merge_toolbar',
            'key'               => 'acfe_wysiwyg_merge_toolbar',
            'instructions'      => __('Glue editor toolbars together'),
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));

        // Customize Toolbar
        acf_render_field_setting($field, array(
            'label'             => __('Customize Toolbar'),
            'name'              => 'acfe_wysiwyg_custom_toolbar',
            'key'               => 'acfe_wysiwyg_custom_toolbar',
            'instructions'      => '',
            'type'              => 'true_false',
            'message'           => '',
            'default_value'     => false,
            'ui'                => true,
        ));


		$wysiwyg = acf_get_field_type('wysiwyg');

        $toolbars = $wysiwyg->get_toolbars();
        $toolbar_label = false;
        $toolbars_default = array();

        // Get selected toolbar label
        foreach($toolbars as $label => $value){

            $name = sanitize_title($label);
            $name = str_replace('-', '_', $name);

            if($field['toolbar'] !== $name)
                continue;

            $toolbar_label = $label;

        }

        // Construct default toolbars
        if(isset($toolbars[$toolbar_label])){

            foreach($toolbars[$toolbar_label] as $key => $rows){

                foreach($rows as $i => $value){

                    $toolbars_default[$key]["row-$i"]['acfe_wysiwyg_toolbar_row'] = $value;

                }

            }

        }


        // Add missing toolbars (in case there is less than 4)
        $count = count($toolbars_default);

        if($count < 4){

            for($i=$count; $i < 4; $i++){

                $toolbars_default[] = array();

            }

        }

        $toolbars = array();

        foreach($toolbars_default as $key => $rows){

            $toolbars[] = array(
                'label'         => '',
                'name'          => 'acfe_wysiwyg_toolbar_' . $key,
                'key'           => 'acfe_wysiwyg_toolbar_' . $key,
                'instructions'  => '',
                'type'          => 'repeater',
                'button_label'  => __('+ Add button'),
                'required'      => false,
                'layout'        => 'table',
                'default_value' => $toolbars_default[$key],
                'wrapper'           => array(
                    'width' => 25,
                    'class' => '',
                    'id'    => '',
                ),
                'sub_fields'    => array(
                    array(
                        'ID'                => false,
                        'label'             => 'Toolbar ' . $key,
                        'name'              => 'acfe_wysiwyg_toolbar_row',
                        'key'               => 'acfe_wysiwyg_toolbar_row',
                        'type'              => 'text',
                        'prefix'            => '',
                        '_name'             => '',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'default_value'     => '',
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                    ),
                ),
            );

        }

        ob_start();
        ?>
        <br />
        Special buttons:
        <br />
        <br />
        <table class="acf-table">
            <tr>
                <td>Source Code</td>
                <td><code>source_code</code></td>
            </tr>
            <tr>
                <td>WP Media</td>
                <td><code>wp_add_media</code></td>
            </tr>
        </table>

        <br />
        <br />

        Common buttons:
        <br />
        <br />
        <table class="acf-table">
            <tr>
                <td>Text Format</td>
                <td><code>formatselect</code></td>
            </tr>
            <tr>
                <td>Font Size</td>
                <td><code>fontsizeselect</code></td>
            </tr>
            <tr>
                <td>Font Family</td>
                <td><code>fontselect</code></td>
            </tr>
            <tr>
                <td>Text Color</td>
                <td><code>forecolor</code></td>
            </tr>
            <tr>
                <td>Formats</td>
                <td><code>styleselect</code></td>
            </tr>
            <tr>
                <td>Link</td>
                <td><code>link</code></td>
            </tr>
            <tr>
                <td>More Toolbars</td>
                <td><code>wp_adv</code></td>
            </tr>
            <tr>
                <td>Divider</td>
                <td><code>|</code></td>
            </tr>
        </table>
        <?php

        $instructions = ob_get_clean();

        // Toolbar buttons
        acf_render_field_setting($field, array(
            'label'                 => __('Custom Toolbar Buttons'),
            'name'                  => 'acfe_wysiwyg_toolbar_buttons',
            'key'                   => 'acfe_wysiwyg_toolbar_buttons',
            'instructions'          => $instructions,
            'type'                  => 'group',
            'required'              => false,
            'sub_fields'            => $toolbars,
            'conditional_logic'     => array(
                array(
                    array(
                        'field'     => 'acfe_wysiwyg_custom_toolbar',
                        'operator'  => '==',
                        'value'     => '1',
                    ),
                ),
            )
        ));

	}/*
     * Field Wrapper
     */
    function field_wrapper($wrapper, $field){

        // Auto-resize
        if(acf_maybe_get($field, 'acfe_wysiwyg_autoresize')){

            $wrapper['data-acfe-wysiwyg-autoresize'] = 1;

            // Min Height
            if(is_numeric(acf_maybe_get($field, 'acfe_wysiwyg_min_height'))){

                $wrapper['data-acfe-wysiwyg-min-height'] = $field['acfe_wysiwyg_min_height'];

            }

            // Max Height
            if(is_numeric(acf_maybe_get($field, 'acfe_wysiwyg_max_height'))){

                $wrapper['data-acfe-wysiwyg-max-height'] = $field['acfe_wysiwyg_max_height'];

            }

        }elseif(is_numeric(acf_maybe_get($field, 'acfe_wysiwyg_height'))){

            $wrapper['data-acfe-wysiwyg-height'] = $field['acfe_wysiwyg_height'];

        }

        // Custom Style
        if(acf_maybe_get($field, 'acfe_wysiwyg_custom_style')){

            $custom_styles = array();

            $styles = acf_maybe_get($field, 'acfe_wysiwyg_custom_style');
            $styles = explode(',', $styles);

            foreach($styles as $style){

                // URL starting with current domain
                if(stripos($style, home_url()) === 0){

                    $style = str_replace(home_url(), '', $style);

                }

                // Locate
                $located = acfe_locate_file_url($style);

                if(!empty($located)){

                    // URL starting with current domain
                    if(stripos($located, home_url()) === 0){

                        $located = str_replace(home_url(), '', $located);

                    }

                    $custom_styles[] = $located;

                }

            }

            $wrapper['data-acfe-wysiwyg-custom-style'] = $custom_styles;

        }

        // Valid Elements
        if(acf_maybe_get($field, 'acfe_wysiwyg_valid_elements')){

            $wrapper['data-acfe-wysiwyg-valid-elements'] = $field['acfe_wysiwyg_valid_elements'];

        }

        // Disable WP Style
        if(acf_maybe_get($field, 'acfe_wysiwyg_disable_wp_style')){

            $wrapper['data-acfe-wysiwyg-disable-wp-style'] = 1;

        }

        // Disable Resize
        if(acf_maybe_get($field, 'acfe_wysiwyg_disable_resize')){

            $wrapper['data-acfe-wysiwyg-disable-resize'] = 1;

        }

        // Disable Path
        if(acf_maybe_get($field, 'acfe_wysiwyg_remove_path')){

            $wrapper['data-acfe-wysiwyg-remove-path'] = 1;

        }

        // Menu Bar
        if(acf_maybe_get($field, 'acfe_wysiwyg_menubar')){

            $wrapper['data-acfe-wysiwyg-menubar'] = 1;

        }

        // Transparent Editor
        if(acf_maybe_get($field, 'acfe_wysiwyg_transparent')){

            $wrapper['data-acfe-wysiwyg-transparent'] = 1;

        }

        // Merge Toolbar
        if(acf_maybe_get($field, 'acfe_wysiwyg_merge_toolbar')){

            $wrapper['data-acfe-wysiwyg-merge-toolbar'] = 1;

        }

        // Custom Toolbar
        if(acf_maybe_get($field, 'acfe_wysiwyg_custom_toolbar')){

            $buttons = acf_maybe_get($field, 'acfe_wysiwyg_toolbar_buttons');

            if($buttons){

                $wrapper['data-acfe-wysiwyg-custom-toolbar'] = 1;

                $toolbars = array();

                for($i=1; $i <= 4; $i++){

                    $values = array();

                    if(acf_maybe_get($buttons, 'acfe_wysiwyg_toolbar_' . $i)){

                        foreach($buttons['acfe_wysiwyg_toolbar_' . $i] as $row => $value){

                            $values[] = $value['acfe_wysiwyg_toolbar_row'];

                        }

                    }

                    $toolbars[$i] = $values;

                }

                $wrapper['data-acfe-wysiwyg-custom-toolbar-buttons'] = $toolbars;

            }

        }

        return $wrapper;

    }

	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/

	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {

			return $value;

		}


		// apply filters
		$value = apply_filters( 'acf_the_content', $value );


		// follow the_content function in /wp-includes/post-template.php
		$value = str_replace(']]>', ']]&gt;', $value);


		return $value;
	}

										   /*
     * WP TinyMCE: Force Buttons
     */
    function mce_buttons($buttons){

        array_push($buttons, '|', 'source_code', 'wp_add_media');

        return $buttons;

    }

    /*
     * WP TinyMCE: Force Save onChange
     * This allow the source code button to correctly get latest value
     */
    function mce_init($init, $editor_id){

        if($editor_id !== 'content') return $init;

        $init['setup'] = ''
                         . 'function(editor){'
                         . '   editor.on("change", function(e){'
                         . '       editor.save();'
                         . '   });'
                         . '}';

        return $init;

    }

    /*
     * WP TinyMCE: Disable Tab / Add Media
     */
    function mce_settings($settings){

        // Disable "Text" Tab
        $settings['quicktags'] = false;

        // Disable "Add Media" Tab
        $settings['media_buttons'] = false;

        return $settings;

    }

										   /*
     * Prepare Toolbars
     */
    function prepare_toolbars(){

        add_filter('acf/prepare_field/name=acfe_wysiwyg_toolbar_row', function($field){

            $field['prefix'] = str_replace('row-', '', $field['prefix']);
            $field['name'] = str_replace('row-', '', $field['name']);

            return $field;

        });

    }

}


// initialize
acf_register_field_type( 'acf_field_wysiwyg' );

endif; // class_exists check

?>