<?php
/**
 * Form functions
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

// Register store for form data.
acf_register_store( 'form' );

/**
 * Set form data
 *
 * Sets data about the current form.
 *
 * @since  1.0.0
 * @param  string $name The store name.
 * @param  array $data Array of data to start the store with.
 * @return object ACF_Data
 */
function acf_set_form_data( $name = '', $data = false ) {
	return acf_get_store( 'form' )->set( $name, $data );
}

/**
 * Get form data
 *
 * Gets data about the current form.
 *
 * @since  1.0.0
 * @param  string $name The store name.
 * @return mixed
 */
function acf_get_form_data( $name = '' ) {
	return acf_get_store( 'form' )->get( $name );
}

/**
 * Form data
 *
 * Called within a form to set important information and render hidden inputs.
 *
 * @since  1.0.0
 * @param  array
 * @return void
 */
function acf_form_data( $data = [] ) {

	// Apply defaults.
	$data = wp_parse_args( $data, [
		'screen'     => 'post',
		'post_id'    => 0,
		'validation' => true
	] );

	// Create nonce using screen.
	$data['nonce'] = wp_create_nonce( $data['screen'] );

	// Append "changed" input used within "_wp_post_revision_fields" action.
	$data['changed'] = 0;

	// Set data.
	acf_set_form_data( $data );

	// Render HTML.
	?>
	<div id="acf-form-data" class="acf-hidden">
		<?php

		// Create hidden inputs from $data
		foreach ( $data as $name => $value ) {
			acf_hidden_input( [
				'id'    => '_acf_' . $name,
				'name'  => '_acf_' . $name,
				'value' => $value
			] );
		}
		do_action( 'acf/form_data', $data );
		do_action( 'acf/input/form_data', $data );

		?>
	</div>
	<?php
}


/**
 * Save post
 *
 * Saves the $_POST data.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @param  array $values An array of values to override $_POST.
 * @return boolean True if save was successful.
 */
function acf_save_post( $post_id = 0, $values = null ) {

	// Override $_POST data with $values.
	if ( $values !== null ) {
		$_POST['acf'] = $values;
	}

	// Stop if no data to save.
	if ( empty( $_POST['acf'] ) ) {
		return false;
	}

	// Set form data (useful in various filters/actions).
	acf_set_form_data( 'post_id', $post_id );

	// Filter $_POST data for users without the 'unfiltered_html' capability.
	if ( ! acf_allow_unfiltered_html() ) {
		$_POST['acf'] = wp_kses_post_deep( $_POST['acf'] );
	}

	// Do generic action.
	do_action( 'acf/save_post', $post_id );

	// Return true.
	return true;
}

/**
 * Do save post
 *
 * Private function hooked into 'acf/save_post' to actually save the $_POST data.
 * This allows developers to hook in before and after ACF has actually saved the data.
 *
 * @since  1.0.0
 * @param  mixed $post_id The post id.
 * @return void
 */
function _acf_do_save_post( $post_id = 0 ) {

	// Check and update $_POST data.
	if ( $_POST['acf'] ) {
		acf_update_values( $_POST['acf'], $post_id );
	}
}

// Run during generic action.
add_action( 'acf/save_post', '_acf_do_save_post' );

/**
 * Get pretty forms
 *
 * Similar to acf_get_pretty_post_types() but for ACFE Forms
 *
 * @since  1.0.0
 * @param array $forms
 * @return array
 */
function acfe_get_pretty_forms( $forms = [] ) {

    if ( empty( $forms ) ) {

        $forms = get_posts( [
            'post_type'      => 'acf-form',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
    }
    $return = [];

    // Choices'
    if ( ! empty( $forms ) ) {
        foreach ( $forms as $form_id ) {
            $form_name = get_the_title( $form_id );
            $return[$form_id] = $form_name;
        }
    }
    return $return;
}

/**
 * Form decrypt args
 *
 * Wrapper to decrypt ACF & ACFE Forms arguments.
 *
 * @since  1.0.0
 * @return mixed
 */
function acfe_form_decrypt_args() {

    if ( ! acf_maybe_get_POST( '_acf_form' ) ) {
        return false;
    }

    $form = json_decode( acf_decrypt( $_POST['_acf_form'] ), true );

    if ( empty( $form) ) {
        return false;
    }
    return $form;
}

/**
 * Is form success
 *
 * Check if the current page is a success form page.
 *
 * @since  1.0.0
 * @param  false $form_name
 * @return boolean
 */
function acfe_is_form_success( $form_name = false ) {

    if ( ! acf_maybe_get_POST( '_acf_form' ) ) {
        return false;
    }

    $form = acfe_form_decrypt_args();

    if ( empty( $form ) ) {
        return false;
    }

    if ( ! empty( $form_name ) && acf_maybe_get( $form, 'name' ) !== $form_name ){
        return false;
    }

    // Avoid multiple submissions.
    if ( headers_sent() ) {
        if ( ! acf_is_filter_enabled( 'acfe/form/is_success' ) ) {
            ?>
            <script>
            if ( window.history.replaceState ) {
                window.history.replaceState( null, null, window.location.href );
            }
            </script>
            <?php
            acf_enable_filter( 'acfe/form/is_success' );
        }
    }
    return true;
}

/**
 * Form is submitted
 *
 * Check if the current page is a success form page.
 * @deprecated
 *
 * @since  1.0.0
 * @param  boolean $form_name
 * @return boolean
 */
function acfe_form_is_submitted( $form_name = false ) {

    _deprecated_function( 'Dynamic Forms: `acfe_form_is_submitted()` function', '0.8.7.5', 'acfe_is_form_success()' );

    return acfe_is_form_success( $form_name );
}

/**
 * Form unique action id
 *
 * Make actions names unique.
 *
 * @since  1.0.0
 * @param  array $form
 * @param  string $type
 * @global array $acfe_form_uniqid
 * @return string
 */
function acfe_form_unique_action_id( $form, $type ) {

    // Access global variables.
    global $acfe_form_uniqid;

    $name = $form['name'] . '-' . $type;
    $acfe_form_uniqid = acf_get_array( $acfe_form_uniqid );

    if ( ! isset( $acfe_form_uniqid[$type] ) ) {
        $acfe_form_uniqid[$type] = 1;
    }
    if ( $acfe_form_uniqid[$type] > 1 ) {
        $name = $name . '-' . $acfe_form_uniqid[$type];
    }
    $acfe_form_uniqid[$type]++;
    return $name;
}

/**
 * Form get actions
 *
 * Retrieve all actions output.
 *
 * @since  1.0.0
 * @return mixed
 */
function acfe_form_get_actions() {
    return get_query_var( 'acfe_form_actions', [] );
}

/**
 * acfe_form_get_action
 *
 * Retrieve the latest action output.
 *
 * @since  1.0.0
 * @param  boolean $name
 * @param  boolean $key
 * @return mixed
 */
function acfe_form_get_action( $name = false, $key = false ) {

    $actions = acfe_form_get_actions();

    // No action.
    if ( empty( $actions ) ) {
        return false;
    }

    // Action name.
    if ( ! empty( $name ) ) {
        $return = acf_maybe_get( $actions, $name, false );
    } else {
        $return = end( $actions );
    }

    if ( $key !== false || is_numeric( $key ) ) {
        $return = acf_maybe_get( $return, $key, false );
    }
    return $return;
}

/**
 * Form is admin
 *
 * Check if current screen is back-end.
 * @deprecated
 *
 * @since  1.0.0
 * @return boolean
 */
function acfe_form_is_admin() {
    _deprecated_function( 'acfe_form_is_admin()', '0.8.8', 'acfe_is_admin()' );
    return acfe_is_admin();
}

/**
 * Form is front
 *
 * Check if current screen is front-end.
 * @deprecated
 *
 * @since  1.0.0
 * @return boolean
 */
function acfe_form_is_front() {
    _deprecated_function( 'acfe_form_is_front()', '0.8.8', 'acfe_is_front()' );
    return acfe_is_front();
}
