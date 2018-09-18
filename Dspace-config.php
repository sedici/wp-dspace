<?php
include_once plugin_dir_path( __FILE__ ).'/configuration/config.php';



/**
 * Build the options page
 */
function dspace_options_page() {
    $directorio =  WP_CONTENT_DIR."/plugins/wp-dspace/config-files/";
    foreach (glob($directorio."*.ini") as $value) {
        $ini_array = parseFile($value);
        echo $ini_array['name'];
    }

}




function register_my_setting() {
	$args = array( 'Canciones' => array());

	array_push($args['Canciones'],array('title' => 'titulo1',
		'autor' => 'autor1')
	);
	array_push($args['Canciones'],array(
		'title' => 'titulo2',
		'autor' => 'autor2'
	));
	array_push($args['Canciones'],array(
		'title' => 'titulo3',
		'autor' => 'autor3'
	));
	//var_dump($args);
	//die;

	//update_option('eze_canciones',$args);


}
add_action( 'admin_init', 'register_my_setting' );

