<?php
include_once plugin_dir_path( __FILE__ ).'/configuration/config.php';
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
        $ini_array = parseFile($value);
        echo $ini_array['name'];
    }

}
