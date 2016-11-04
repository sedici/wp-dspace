<?php
/**
 * Description of Dspace-config
 *
 * @author SEDICI - Paula Salamone
 */
function dspace_config() {
    add_options_page(
        'Dspace Plugin',
        'Dspace configuration',
        'manage_options',
        'dspace_extra_information',
        'dspace_options_page' 
    );
}
 
/**
 * Register the settings
 */
function dspace_register_settings() {
     register_setting(
        'dspace_settings',  // settings section
        'configuration' // setting name
     );
}
add_action( 'admin_init', 'dspace_register_settings' ); 


/**
 * Build the options page
 */
function dspace_options_page() {
    $directorio =  WP_CONTENT_DIR."/plugins/wp-dspace/config-files/";
    foreach (glob($directorio."*.ini") as $value) {
        $ini_array = parse_ini_file($value);
        echo $ini_array['name'];
    }

}
