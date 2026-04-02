<?php

// Register store for form data.
acf_register_store( 'form' );

/**
 * acf_set_form_data
 *
 * Sets data about the current form.
 *
 * @date	6/10/13
 * @since	5.0.0
 *
 * @param	string $name The store name.
 * @param	array $data Array of data to start the store with.
 * @return	ACF_Data
 */
function acf_set_form_data( $name = '', $data = false ) {
	return acf_get_store( 'form' )->set( $name, $data );
}

/**
 * acf_get_form_data
 *
 * Gets data about the current form.
 *
 * @date	6/10/13
 * @since	5.0.0
 *
 * @param	string $name The store name.
 * @return	mixed
 */
function acf_get_form_data( $name = '' ) {
	return acf_get_store( 'form' )->get( $name );
}

/**
 * acf_form_data
 *
 * Called within a form to set important information and render hidden inputs.
 *
 * @date	15/10/13
 * @since	5.0.0
 *
 * @param	void
 * @return	void
 */
function acf_form_data( $data = array() ) {

	// Apply defaults.
	$data = wp_parse_args($data, array(

		/** @type string The current screen (post, user, taxonomy, etc). */
		'screen' => 'post',

		/** @type int|string The ID of current post being edited. */
		'post_id' => 0,

		/** @type bool Enables AJAX validation. */
		'validation' => true,
	));

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
		foreach( $data as $name => $value ) {
			acf_hidden_input(array(
				'id'	=> '_acf_' . $name,
				'name'	=> '_acf_' . $name,
				'value'	=> $value
			));
		}

		/**
		 * Fires within the #acf-form-data element to add extra HTML.
		 *
		 * @date	15/10/13
		 * @since	5.0.0
		 *
		 * @param	array $data The form data.
		 */
		do_action( 'acf/form_data', $data );
		do_action( 'acf/input/form_data', $data );

		?>
	</div>
	<?php
}


/**
 * acf_save_post
 *
 * Saves the $_POST data.
 *
 * @date	15/10/13
 * @since	5.0.0
 *
 * @param	int|string $post_id The post id.
 * @param	array $values An array of values to override $_POST.
 * @return	bool True if save was successful.
 */
function acf_save_post( $post_id = 0, $values = null ) {

	// Override $_POST data with $values.
	if( $values !== null ) {
		$_POST['acf'] = $values;
	}

	// Bail early if no data to save.
	if( empty($_POST['acf']) ) {
		return false;
	}

	// Set form data (useful in various filters/actions).
	acf_set_form_data( 'post_id', $post_id );

	// Filter $_POST data for users without the 'unfiltered_html' capability.
	if( !acf_allow_unfiltered_html() ) {
		$_POST['acf'] = wp_kses_post_deep( $_POST['acf'] );
	}

	// Do generic action.
	do_action( 'acf/save_post', $post_id );

	// Return true.
	return true;
}

/**
 * _acf_do_save_post
 *
 * Private function hooked into 'acf/save_post' to actually save the $_POST data.
 * This allows developers to hook in before and after ACF has actually saved the data.
 *
 * @date	11/1/19
 * @since	5.7.10
 *
 * @param	int|string $post_id The post id.
 * @return	void
 */
function _acf_do_save_post( $post_id = 0 ) {

	// Check and update $_POST data.
	if( $_POST['acf'] ) {
		acf_update_values( $_POST['acf'], $post_id );
	}
}

// Run during generic action.
add_action( 'acf/save_post', '_acf_do_save_post' );

/**
 * acfe_get_pretty_forms
 *
 * Similar to acf_get_pretty_post_types() but for ACFE Forms
 *
 * @param array $forms
 *
 * @return array
 */
function acfe_get_pretty_forms($forms = array()){

    if(empty($forms)){

        $forms = get_posts(array(
            'post_type'         => 'acfe-form',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'title',
            'order'             => 'ASC',
        ));

    }

    $return = array();

    // Choices
    if(!empty($forms)){

        foreach($forms as $form_id){

            $form_name = get_the_title($form_id);

            $return[$form_id] = $form_name;

        }

    }

    return $return;

}

/**
 * acfe_form_decrypt_args
 *
 * Wrapper to decrypt ACF & ACFE Forms arguments
 *
 * @return false|mixed
 */
function acfe_form_decrypt_args(){

    if(!acf_maybe_get_POST('_acf_form'))
        return false;

    $form = json_decode(acf_decrypt($_POST['_acf_form']), true);

    if(empty($form))
        return false;

    return $form;

}

/**
 * acfe_is_form_success
 *
 * Check if the current page is a success form page
 *
 * @param false $form_name
 *
 * @return bool
 */
function acfe_is_form_success($form_name = false){

    if(!acf_maybe_get_POST('_acf_form')){
        return false;
    }

    $form = acfe_form_decrypt_args();

    if(empty($form)){
        return false;
    }

    if(!empty($form_name) && acf_maybe_get($form, 'name') !== $form_name){
        return false;
    }

    // avoid multiple submissions
    if(headers_sent()){

        // check filter
        if(!acf_is_filter_enabled('acfe/form/is_success')){
            ?>
            <script>
            if(window.history.replaceState){
                window.history.replaceState(null, null, window.location.href);
            }
            </script>
            <?php

            // only once
            acf_enable_filter('acfe/form/is_success');
        }

    }

    return true;

}

/**
 * acfe_form_is_submitted
 *
 * check if the current page is a success form page
 *
 * @param false $form_name
 *
 * @return bool
 *
 * @deprecated
 */
function acfe_form_is_submitted($form_name = false){

    _deprecated_function('ACF Extended - Dynamic Forms: "acfe_form_is_submitted()" function', '0.8.7.5', "acfe_is_form_success()");

    return acfe_is_form_success($form_name);

}

/**
 * acfe_form_unique_action_id
 *
 * Make actions names unique
 *
 * @param $form
 * @param $type
 *
 * @return string
 */
function acfe_form_unique_action_id($form, $type){

    $name = $form['name'] . '-' . $type;

    global $acfe_form_uniqid;

    $acfe_form_uniqid = acf_get_array($acfe_form_uniqid);

    if(!isset($acfe_form_uniqid[$type])){

        $acfe_form_uniqid[$type] = 1;

    }

    if($acfe_form_uniqid[$type] > 1)
        $name = $name . '-' . $acfe_form_uniqid[$type];

    $acfe_form_uniqid[$type]++;

    return $name;

}

/**
 * acfe_form_get_actions
 *
 * Retrieve all actions output
 *
 * @return mixed
 */
function acfe_form_get_actions(){

    return get_query_var('acfe_form_actions', array());

}

/**
 * acfe_form_get_action
 *
 * Retrieve the latest action output
 *
 * @param false $name
 * @param false $key
 *
 * @return false|mixed|null
 */
function acfe_form_get_action($name = false, $key = false){

    $actions = acfe_form_get_actions();

    // No action
    if(empty($actions))
        return false;

    // Action name
    if(!empty($name)){
        $return = acf_maybe_get($actions, $name, false);
    }else{
        $return = end($actions);
    }

    if($key !== false || is_numeric($key))
        $return = acf_maybe_get($return, $key, false);

    return $return;

}

/**
 * acfe_form_is_admin
 *
 * Check if current screen is back-end
 *
 * @return bool
 *
 * @deprecated
 */
function acfe_form_is_admin(){

    _deprecated_function('ACF Extended: acfe_form_is_admin()', '0.8.8', "acfe_is_admin()");

    return acfe_is_admin();

}

/**
 * acfe_form_is_front
 *
 * Check if current screen is front-end
 *
 * @return bool
 *
 * @deprecated
 */
function acfe_form_is_front(){

    _deprecated_function('ACF Extended: acfe_form_is_front()', '0.8.8', "acfe_is_front()");

    return acfe_is_front();

}
