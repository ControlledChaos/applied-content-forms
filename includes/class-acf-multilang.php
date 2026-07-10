<?php
/**
 * Multilang class
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

class ACF_Multilang {

	/**
	 * WPML
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $is_wpml = false;

	/**
	 * Polylang
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $is_polylang = false;

	/**
	 * Multilang
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $is_multilang = false;

	/**
	 * Options pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $options_pages = false;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// WPML.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$this->is_wpml      = true;
			$this->is_multilang = true;
		}

		// PolyLang.
		if ( defined( 'POLYLANG_VERSION' ) && function_exists( 'pll_default_language' ) ) {
			$this->is_polylang  = true;
			$this->is_multilang = true;
		}

		if ( $this->is_multilang ) {
			add_action( 'acf/init', [ $this, 'init' ], 99 );
		}
	}

	/**
	 * Initialize
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function init() {

		// Check setting.
		if ( ! acf_get_setting( 'multilang' ) ) {
			return;
		}

		// Polylang specific.
		if ( $this->is_polylang ) {

			// Default/current language.
			$dl = pll_default_language( 'locale' );
			$cl = pll_current_language( 'locale' );

			// Update settings.
			acf_update_setting( 'default_language', $dl );
			acf_update_setting( 'current_language', $cl );

			add_filter( 'acf/pre_load_reference', [ $this, 'polylang_preload_reference' ], 10, 3 );
			add_filter( 'acf/pre_load_value', [ $this, 'polylang_preload_value' ], 10, 3 );
		}

		// Options page message.
		add_action( 'acf/options_page/submitbox_before_major_actions', [ $this, 'options_page_message' ] );

		// Options post ID.
		add_filter( 'acf/validate_post_id', [ $this, 'set_options_post_id' ], 99, 2 );
	}

	/**
	 * PolyLang preload reference
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $null
	 * @param  string $field_name
	 * @param  integer $post_id
	 * @return mixed
	 */
	public function polylang_preload_reference( $null, $field_name, $post_id ) {

		// Validate post ID.
		$original_post_id = $this->polylang_validate_preload_post_id( $post_id );

		if ( ! $original_post_id ) {
			return $null;
		}

		$reference = acf_get_metadata( $post_id, $field_name, true );

		if ( $reference !== null ) {
			return $null;
		}
		return acf_get_metadata( $original_post_id, $field_name, true );
	}

	/**
	 * PolyLang preload value
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  null $null
	 * @param  integer $post_id
	 * @param  string $field
	 * @return mixed
	 */
	public function polylang_preload_value( $null, $post_id, $field ) {

		// Validate post ID.
		$original_post_id = $this->polylang_validate_preload_post_id( $post_id );

		if ( ! $original_post_id ) {
			return $null;
		}

		// Get field name.
		$field_name = $field['name'];

		// Check store.
		$store = acf_get_store( 'values' );

		if ( $store->has( "$post_id:$field_name" ) ) {
			return $null;
		}

		// Load value from database.
		$value = acf_get_metadata( $post_id, $field_name );

		// Use field's default_value if no meta was found.
		if ( null !== $value ) {
			return $null;
		}
		return acf_get_value( $original_post_id, $field );
	}

	/**
	 * PolyLang validate preload post ID
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @return mixed
	 */
	public function polylang_validate_preload_post_id( $post_id ) {

		// Stop if admin screen.
		if ( is_admin() || ! is_string( $post_id ) ) {
			return false;
		}

		// Get post ID info.
		$data = acf_get_post_id_info( $post_id );

		// Stop if post id isn't an option type.
		if ( $data['type'] !== 'option' ) {
			return false;
		}

		// Stop if not localized.
		if ( ! $this->is_localized( $post_id ) ) {
			return false;
		}

		$original_post_id = preg_replace( '/([_\-][A-Za-z]{2}_[A-Za-z]{2})$/', '', $post_id );

		// Check the regex.
		if ( $original_post_id === $post_id ) {
			return false;
		}

		// Stop if no options page found with given post ID.
		if ( ! $this->is_options_page( $original_post_id ) ) {
			return false;
		}
		return $original_post_id;
	}

	/**
	 * PolyLang get languages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $pluck
	 * @param  string $type
	 * @return array
	 */
	public function polylang_get_languages( $pluck = '', $type = 'all' ) {

		$languages = [];

		switch( $type ) {

			// Active.
			case 'active' :

				// Convert pluck.
				$pluck = $pluck === 'code' ? 'slug' : $pluck;

				// https://polylang.wordpress.com/documentation/documentation-for-developers/functions-reference/
				$languages = pll_languages_list( [
					'hide_empty' => false,
					'fields'     => $pluck
				] );
				return $languages;

			// All.
			case '' :
			case 'all' :
				$languages = PLL_Settings :: get_predefined_languages();

				if ( $pluck ) {
					$languages = wp_list_pluck( $languages, $pluck, true );
				}
				return $languages;
		}
		return $languages;
	}

	/**
	 * WPML get languages
	 *
	 * @link https://wpml.org/documentation/support/wpml-coding-api/wpml-hooks-reference/
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $pluck
	 * @param  string $type
	 * @return array
	 */
	public function wpml_get_languages( $pluck = '', $type = 'all' ) {

		$languages = [];
		$pluck     = $pluck === 'locale' ? 'default_locale' : $pluck;

		switch( $type ) {

			// Active.
			case 'active' :

				// https://wpml.org/wpml-hook/wpml_active_languages/
				$languages = apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => 0 ] );

				// Set locale as key.
				$_languages = $languages;
				$languages  = [];

				foreach ( $_languages as $lang ) {
					$languages[ $lang['default_locale'] ] = $lang;
				}

				if ( $pluck ) {
					$languages = wp_list_pluck( $languages, $pluck, true );
				}
				return $languages;

			// All.
			case '' :
			case 'all' :

				// https://wpml.org/wpml-hook/wpml_active_languages/
				$languages = apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => 0 ] );
				$languages = wp_list_pluck( $languages, 'code', 'default_locale' );

				// Default Languages.
				$_languages = icl_get_languages_locales();
				$_languages = array_flip( $_languages );

				if ( ! empty( $_languages ) ) {
					$languages = array_merge( $languages, $_languages );
					$languages = array_unique( $languages );
				}

				if ( $pluck ) {
					$languages = $pluck === 'code' ? array_values( $_languages ) : array_keys( $_languages );
				}
				return $languages;
		}
		return $languages;
	}

	/**
	 * Set options post ID
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $post_id
	 * @param  integer $original_post_id
	 * @return integer
	 */
	public function set_options_post_id( $post_id, $original_post_id ) {

		// Stop if original post ID is 'options' or 'option'.
		if ( ! is_string( $post_id ) ) {
			return $post_id;
		}

		$data = acf_get_post_id_info( $post_id );

		// Stop if post ID isn't an option type.
		if ( $data['type'] !== 'option' ) {
			return $post_id;
		}

		/**
		 * Options exception
		 *
		 * $post_id already translated during the native
		 * acf/validate_post_id.
		 */
		if ( in_array( $original_post_id, [ 'options', 'option' ] ) ) {

			// Exclude filter.
			$exclude = apply_filters( 'acfe/modules/multilang/exclude_options', [] );

			if ( in_array( 'options', $exclude ) ) {
				return 'options';
			}
			return $post_id;
		}

		// Stop if no options page found with that post ID.
		if ( ! $this->is_options_page( $post_id ) ) {
			return $post_id;
		}

		// Stop if already localized: 'my-options_en_US'.
		if ( $this->is_localized( $post_id ) ) {
			return $post_id;
		}

		// Append current language to post ID.
		$dl = acf_get_setting( 'default_language' );
		$cl = acf_get_setting( 'current_language' );

		// Add language.
		if ( $cl && $cl !== $dl ) {
			$post_id .= '_' . $cl;
		}
		return $post_id;
	}

	/**
	 * Is localized
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer  $post_id
	 * @return boolean
	 */
	public function is_localized( $post_id ) {

		// Check if post ID ends with '-en_US', '_en_US', '-en', '_en'.
		preg_match( '/(?P<locale>[_\-][A-Za-z]{2}_[A-Za-z]{2})$|(?P<code>[_\-][A-Za-z]{2})$/', $post_id, $matches );

		if ( empty( $matches ) ) {
			return false;
		}

		// Cleanup matches.
		$lang = [];
		foreach ( $matches as $key => $val ) {

			if ( is_int( $key ) || empty( $val ) ) {
				continue;
			}

			$lang = [
				'type' => $key,
				'lang' => strtolower( substr( $val, 1 ) )
			];
		}
		if ( empty( $lang ) ) {
			return false;
		}

		// Get WPML/Polylang languages list.
		$languages = $this->get_languages( $lang['type'] );
		$languages = array_map( 'strtolower', $languages );

		// Compare Matches vs WPML/Polylang languages list.
		return in_array( $lang['lang'], $languages );
	}

	/**
	 * Is options page
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer  $post_id
	 * @return boolean
	 */
	public function is_options_page( $post_id ) {

		// Get options pages.
		if ( false === $this->options_pages ) {

			// Get options pages.
			$options_pages = acf_get_array( acf_get_options_pages() );
			$list = wp_list_pluck( $options_pages, 'post_id', true );

			// Add 'Post Types List' location.
			$post_types = acf_get_post_types( [
				'show_ui' => 1,
				'exclude' => [ 'attachment' ]
			] );

			if ( ! empty( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					$list[] = $post_type . '_options';
				}
			}

			// Add 'Taxonomy List' location.
			$taxonomies = acf_get_taxonomies();

			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$list[] = 'tax_' . $taxonomy . '_options';
				}
			}

			// Deprecated filter.
			$list = apply_filters_deprecated( 'acfe/modules/multilang/options', [ $list ], '0.8.8.2', 'acfe/modules/multilang/exclude_options' );

			// Include filter.
			$list = apply_filters( 'acfe/modules/multilang/include_options', $list );

			// Exclude filter.
			$exclude = apply_filters( 'acfe/modules/multilang/exclude_options', [] );

			if ( is_array( $exclude ) && ! empty( $exclude ) ) {
				foreach ( $list as $i => $option ) {
					if ( ! in_array( $option, $exclude ) ) {
						continue;
					}
					unset( $list[$i] );
				}
				$list = array_values( $list );
			}
			$this->options_pages = $list;
		}

		if ( is_array( $this->options_pages ) && ! empty( $this->options_pages ) ) {
			return in_array( $post_id, $this->options_pages );
		}
		return false;
	}

	/**
	 * Get languages
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $pluck
	 * @param  string $type
	 * @param  string $plugin
	 * @return array
	 */
	public function get_languages( $pluck = '', $type = '', $plugin = '' ) {

		// Polylang.
		if ( $this->is_polylang || 'polylang' === $plugin ) {
			return $this->polylang_get_languages( $pluck, $type );

		// WPML.
		} elseif ( $this->is_wpml || 'wpml' === $plugin ) {
			return $this->wpml_get_languages( $pluck, $type );
		}
		return [];
	}

	/**
	 * Options page message
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function options_page_message() {

		$default_language = acf_get_setting( 'default_language' );
		$current_language = acf_get_setting( 'current_language' );

		$message = false;

		// Polylang.
		if ( $this->is_polylang ) {

			if ( ! $current_language ) {
				$current_language = $default_language;
			}
			$message = "Language: {$current_language}";

			$nice_language = false;
			$nice_flag     = false;

			$languages = pll_languages_list( [
				'hide_empty' => false,
				'fields'     => false
			] );

			if ( $languages ) {
				foreach ( $languages as $language ) {
					if ( $language->locale !== $current_language ) {
						continue;
					}
					$nice_language = $language->name;
					$nice_flag = $language->flag_url;
					break;
				}
			}

			if ( $nice_language ) {

				$message = "<img src='{$nice_flag}' style='margin-right:5px;vertical-align:-1px;' /> Language: {$nice_language}";
			}

			if ( $default_language === $current_language ) {
				$message .= ' (Default)';
			}

		// WPML.
		} elseif ( $this->is_wpml ) {

			if ( $current_language === 'all' ) {
				$current_language = 'All';
			}
			$message = "Language: {$current_language}";

			if ( 'All' !== $current_language ) {

				$nice_language = false;
				$nice_flag = false;

				$languages = apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => 0 ] );

				if ( $languages ) {
					foreach ( $languages as $language ) {

						if ( $language['language_code'] !== $current_language ) {
							continue;
						}
						$nice_language = $language['native_name'];
						$nice_flag = $language['country_flag_url'];
						break;
					}
				}

				if ( $nice_language ) {
					$message = "<img src='{$nice_flag}' style='margin-right:5px;vertical-align:-1px; width:16px; height:11px;' /> Language: {$nice_language}";
				}
			}
		}
		if ( empty( $message ) ) {
			return;
		}

		echo "<div class='misc-pub-section' style='padding-top:15px; padding-bottom:15px;'>{$message}</div>";
	}
}
acf_new_instance( 'ACF_Multilang' );
