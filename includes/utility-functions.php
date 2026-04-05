<?php
/**
 * ACF utility functions
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

// Globals.
global $acf_stores, $acf_instances;

// Initialize placeholders.
$acf_stores    = [];
$acf_instances = [];

/**
 * Class instance
 *
 * Creates a new instance of the given class and stores it in the instances data store.
 *
 * @since  1.0.0
 * @param  string $class The class name.
 * @return object The instance.
 */
function acf_new_instance( $class = '' ) {
	global $acf_instances;
	return $acf_instances[ $class ] = new $class();
}

/**
 * Get class instance
 *
 * Returns an instance for the given class.
 *
 * @since  1.0.0
 * @param  string $class The class name.
 * @return object The instance.
 */
function acf_get_instance( $class = '' ) {

	global $acf_instances;

	if ( ! isset( $acf_instances[ $class ] ) ) {
		$acf_instances[ $class ] = new $class();
	}
	return $acf_instances[ $class ];
}

/**
 * Register store
 *
 * Registers a data store.
 *
 * @since  1.0.0
 * @param  string $name The store name.
 * @param  array $data Array of data to start the store with.
 * @return object ACF_Data
 */
function acf_register_store( $name = '', $data = false ) {

	// Create store.
	$store = new ACF_Data( $data );

	// Register store.
	global $acf_stores;
	$acf_stores[ $name ] = $store;

	// Return store.
	return $store;
 }

/**
 * Get store
 *
 * Returns a data store.
 *
 * @since  1.0.0
 * @param  string $name The store name.
 * @return object ACF_Data
 */
function acf_get_store( $name = '' ) {

	global $acf_stores;

	if ( isset( $acf_stores[ $name ] ) ) {
		return $acf_stores[ $name ];
	}
	return false;
}

/**
 * Switch stores
 *
 * Triggered when switching between sites on a multisite installation.
 *
 * @since  1.0.0
 * @param  integer $site_id New blog ID.
 * @param  integer $prev_blog_id Previous blog ID.
 * @return void
 */
function acf_switch_stores( $site_id, $prev_site_id ) {

	global $acf_stores;

	// Loop over stores and call switch_site().
	foreach( $acf_stores as $store ) {
		$store->switch_site( $site_id, $prev_site_id );
	}
}
add_action( 'switch_blog', 'acf_switch_stores', 10, 2 );

/**
 * Get file path
 *
 * Returns the plugin path to a specified file.
 *
 * @since  1.0.0
 * @param  string $filename The specified file.
 * @return string
 */
function acf_get_path( $filename = '' ) {
	return ACF_PATH . ltrim( $filename, '/' );
}

/**
 * Get URL
 *
 * Returns the plugin url to a specified file.
 * This function also defines the ACF_URL constant.
 *
 * @since  1.0.0
 * @param  string $filename The specified file.
 * @return string
 */
function acf_get_url( $filename = '' ) {

	if ( ! defined( 'ACF_URL' ) ) {
		define( 'ACF_URL', acf_get_setting( 'url' ) );
	}
	return ACF_URL . ltrim( $filename, '/' );
}

/**
 * Include file
 *
 * Includes a file within the ACF plugin.
 *
 * @since  1.0.0
 * @param  string $filename The specified file.
 * @return void
 */
function acf_include( $filename = '' ) {

	$file_path = acf_get_path( $filename );
	if ( file_exists( $file_path ) ) {
		include_once( $file_path );
	}
}

/**
 * acfe_include
 *
 * Includes a file within the plugin
 *
 * @param string $filename
 */
function acfe_include($filename = ''){

    $file_path = ACFE_PATH . ltrim($filename, '/');

    if(file_exists($file_path)){
        return include_once($file_path);
    }

    return false;

}

/**
 * acfe_get_path
 *
 * Returns the plugin path
 *
 * @param string $filename
 *
 * @return string
 */
function acfe_get_path($filename = ''){
    return ACFE_PATH . ltrim($filename, '/');
}

/**
 * acfe_get_url
 *
 * Returns the plugin url
 *
 * @param string $filename
 *
 * @return string
 */
function acfe_get_url($filename = ''){

    if(!defined('ACFE_URL')){
        define('ACFE_URL', acf_get_setting('acfe/url'));
    }

    return ACFE_URL . ltrim($filename, '/');
}

/**
 * acfe_get_view
 *
 * Load in a file from the 'admin/views' folder and allow variables to be passed through
 * Based on acf_get_view()
 *
 * @param string $path
 * @param array  $args
 */
function acfe_get_view($path = '', $args = array()){

    // allow view file name shortcut
    if(substr($path, -4) !== '.php'){
        $path = acfe_get_path("includes/admin/views/{$path}.php");
    }

    // include
    if(file_exists($path)){

        extract($args);
        include($path);

    }

}
