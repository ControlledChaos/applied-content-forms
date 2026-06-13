<?php
/**
 * CMS functions
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   Walkers
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Taxonomy_Field_Walker extends Walker {

	/**
	 * Tree type
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $tree_type = 'category';

	/**
	 * DB fields
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $db_fields = [
		'parent' => 'parent',
		'id'     => 'term_id'
	];

	/**
	 * The field being rendered
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $field;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $field The field being rendered.
	 * @return self
	 */
	public function __construct( $field ) {
		$this->field = $field;
	}

	/**
	 * Start level
	 *
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $output Used to append additional content
	 *                        (passed by reference).
	 * @param  integer $depth Depth of category. Used for tab indentation.
	 * @param  array  $args An array of arguments. @see wp_terms_checklist()
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = [] ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children acf-field-settings-list'>\n";
	}

	/**
	 * End level
	 *
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $output Used to append additional content (passed by reference).
	 * @param  integer $depth Depth of category. Used for tab indentation.
	 * @param  array $args An array of arguments. @see wp_terms_checklist()
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = [] ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output
	 *
	 * @see Walker::start_el()
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $output Used to append additional content
	 *                       (passed by reference).
	 * @param  object $term The current term object.
	 * @param  integer $depth Depth of the term in reference to parents.
	 *                        Default 0.
	 * @param  array $args An array of arguments. @see wp_terms_checklist()
	 * @param  integer $id ID of the current term.
	 * @return void
	 */
	public function start_el( &$output, $term, $depth = 0, $args = [], $id = 0 ) {

		$is_selected = in_array( $term->term_id, $this->field['value'] );

		// Generate array of checkbox input attributes.
		$input_attrs = [
			'type'	=> $this->field['field_type'],
			'name'	=> $this->field['name'],
			'value' => $term->term_id
		];
		if ( $is_selected ) {
			$input_attrs['checked'] = true;
		}

		$output .= "\n" . '<li data-id="' . esc_attr( $term->term_id ) . '">' .
			'<label' . ( $is_selected ? ' class="selected"' : '' ) . '>' .
				'<input ' . acf_esc_attrs( $input_attrs ) . '/> ' .
				'<span>' . acf_esc_html( $term->name ) . '</span>'.
			'</label>';
	}

	/**
	 * Ends the element output
	 *
	 * @see Walker::end_el()
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $output Used to append additional content
	 *                        (passed by reference).
	 * @param  object $category The current term object.
	 * @param  integer $depth Depth of the term in reference to parents. Default 0.
	 * @param  array $args An array of arguments. @see wp_terms_checklist()
	 * @return void
	 */
	public function end_el( &$output, $category, $depth = 0, $args = [] ) {
		$output .= "</li>\n";
	}
}
