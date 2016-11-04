<?php
/**
 * Description of Configuration
 *
 * @author Paula Salamone
 */
require_once 'Sedici_config.php';
require_once 'Cic_config.php';
class Configuration {
    //put your code here
    protected $config;
    protected $configuration;
    
    function set_configuration($configuration){
        $directorio =  WP_CONTENT_DIR."/plugins/wp-dspace/config-files/";
	foreach (glob($directorio."*.ini") as $value) {
            $ini_array = parse_ini_file($value);
            if ($ini_array['name'] == $configuration){
                $this->config= $ini_array;
                $config = ucfirst($configuration)."_config";
                $this->configuration = new $config;
            }
        }    
    }
    function get_name(){
        return $this->config['name'];
    }
    function get_protocol_domain() {
        return _PROTOCOL . $this->config['domain'];
}
    
    function get_subtype_query(){
        return $this->config['subtype'];
    }
            
    function get_standar_query($max_results){
        return ("?rpp=" . $max_results . "&format=" . Q_FORMAT . "&sort_by=" . Q_SORTBY . "&order=" . Q_ORDER . "&start=".S_START);
    }
    
    function get_base_url() {
	return  _PROTOCOL. $this->config['domain']. _BASE_PATH;
    }
    
    function standar_query($max_results){
        return $this->get_base_url () . $this->get_standar_query($max_results);
    }
    
    function author($Authors){
        $conditions = '';
        foreach ( $Authors as $author ) {
            $conditions[]= $this->config['author']. "\"" .$author ."\"" ;
        }
        return "(".implode('%20OR%20', $conditions).")";
    }
    
}
