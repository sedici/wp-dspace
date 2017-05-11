<?php
/**
 * Plugin Name: Dspace-Plugin
 * Plugin URI: http://sedici.unlp.edu.ar/
 * Description: This plugin connects the repository SEDICI in wordpress, with the purpose of showing the publications of authors or institutions
 * Version: 1.0
 * Author: SEDICI - Paula Salamone Lacunza
 * Author URI: http://sedici.unlp.edu.ar/
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 */
require_once 'Shortcode.php';
require_once 'Dspace-config.php';
require_once 'configuration/config.php';
require_once 'util/WidgetFilter.php';
require_once 'util/WidgetValidation.php';
require_once 'util/Query.php';
require_once 'util/XmlOrder.php';
require_once 'view/ShowShortcode.php';
require_once 'model/SimplepieModel.php';
require_once 'configuration/Configuration.php';
foreach ( glob ( "configuration/*_config.php" ) as $app ) {
    require_once $app;
}

function dspace_styles() {
	//require the style
	wp_register_style ( 'Dspace', plugins_url ( 'media/css/styles.css', __FILE__ ));
	wp_enqueue_style ( 'Dspace' );
}

function dspace_scripts_method() {
	// require js archives
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'Dspace', plugins_url ( 'media/js/scrips.js', __FILE__ ), array ("jquery"), null, true );
	wp_enqueue_script ( 'Dspace' );
}
require_once 'Dspace_Widget.php';

add_action ( 'admin_enqueue_scripts', 'dspace_scripts_method' );
add_action ( 'admin_enqueue_scripts', 'dspace_styles' );
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Dspace");' ) );
add_action( 'admin_menu', 'dspace_config' );
print_r( add_shortcode ( 'get_publications', 'DspaceShortcode' ));