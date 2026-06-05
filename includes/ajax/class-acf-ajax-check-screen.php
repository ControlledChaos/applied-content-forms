<?php
/**
 * AJAX check screen
 *
 * @package    Applied Content Forms
 * @subpackage Includes
 * @category   AJAX
 * @since      1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACF_Ajax_Check_Screen extends ACF_Ajax {
	
	/**
	 * AJAX action name
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $action = 'acf/ajax/check_screen';
	
	/**
	 * Privacy
	 *
	 * Prevents access for non-logged in users.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    boolean
	 */
	public $public = false;
	
	/**
	 * Get response
	 *
	 * Returns the response data to sent back.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $request The request args.
	 * @return mixed The response data or WP_Error.
	 */
	public function get_response( $request ) {
		
		$args = wp_parse_args( $this->request, [
			'screen'  => '',
			'post_id' => 0,
			'ajax'    => true,
			'exists'  => []
		] );
		$response = [
			'results' => [],
			'style'   => ''
		];
		
		// Get field groups.
		$field_groups = acf_get_field_groups( $args );
		
		// Loop through field groups.
		if ( $field_groups ) {
			foreach ( $field_groups as $i => $field_group ) {

				$item = [
					'id'       => 'acf-' . $field_group['key'],
					'key'      => $field_group['key'],
					'title'    => $field_group['title'],
					'position' => $field_group['position'],
					'style'    => $field_group['style'],
					'label'    => $field_group['label_placement'],
					'edit'     => acf_get_field_group_edit_link( $field_group['ID'] ),
					'html'     => ''
				];
				
				// Append HTML if doesn't already exist on page.
				if ( ! in_array( $field_group['key'], $args['exists'] ) ) {
					
					// Load fields.
					$fields = acf_get_fields( $field_group );
	
					// Get field HTML.
					ob_start();
					
					// Render.
					acf_render_fields( $fields, $args['post_id'], 'div', $field_group['instruction_placement'] );
					
					$item['html'] = ob_get_clean();
				}
				
				// Append.
				$response['results'][] = $item;
			}
			
			// Get style from first field group.
			$response['style'] = acf_get_field_group_style( $field_groups[0] );
		}
		
		// Custom metabox order.
		if ( 'post' == $this->get( 'screen' ) ) {
			$response['sorted'] = get_user_option( 'meta-box-order_' . $this->get('post_type') );
		}
		return $response;
	}
}
acf_new_instance( 'ACF_Ajax_Check_Screen' );
