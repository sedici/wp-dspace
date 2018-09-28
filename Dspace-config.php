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
	// $args = array( 'repositorios' => array());

	// array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'sedici',
		// 'domain' => 'sedici.unlp.edu.ar','protocol'=>'http://','subtype' =>'sedici.subtype:','handle'=>'scope','author'=>'author:','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query' )
	// );
	// array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'cic',
		// 'domain' => 'digital.cic.gba.gob.ar','protocol'=>'https://','subtype' =>'dc.type:','handle'=>'scope','author'=>'author:','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query' )
	// );
	// /*array_push($args['Canciones'],array(
		// 'title' => 'titulo2',
		// 'autor' => 'autor2'
	// ));*/
	// 
	

	// update_option('config_repositorios',$args);


}
add_action( 'admin_init', 'register_my_setting' );

