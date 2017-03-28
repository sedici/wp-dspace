<?php
/**
 * Description of Configuration
 *
 * @author Paula Salamone
 */
class Configuration {

    protected $config;
    
    public function Configuration($configuration){
        
		foreach ($this->get_config_files( get_configuration_directory() ) as $value) {
				$ini_array = parse_ini_file($value);
				if ($ini_array['name'] == $configuration){
					$this->config= $ini_array;
				}
        }    
    }
    
    /** 
     * Returns a list (array) of a .ini file names (config files) stored in $base_path directory. If $base_path is not a valid directory, or if it's unreadable,
     * returns an empty list
     * @param $base_path string: Required. Full path (including trailing slash) to the directory where .ini files are stored
     * TODO Instead of returning an empty array, this should throw an exception for each condition error
     **/        
    private function get_config_files($base_path) {
		return (file_exists($base_path) && is_dir($base_path) && is_readable($base_path)) 
		   ? glob($base_path."*.ini")
		   : array();
	}
    
    final function get_name(){
        return $this->config['name'];
    }
    final function get_protocol_domain() {
        return _PROTOCOL . $this->config['domain'];
    }
    
    final function get_subtype_query(){
        return $this->config['subtype'];
    }
            
    final function get_standar_query($max_results){
        return ("?rpp=" . $max_results . "&format=" . Q_FORMAT . "&sort_by=" . Q_SORTBY . "&order=" . Q_ORDER . "&start=".S_START);
    }
    
    final function get_base_url() {
	return  _PROTOCOL. $this->config['domain']. _BASE_PATH;
    }
    
    final function standar_query($max_results){
        return $this->get_base_url () . $this->get_standar_query($max_results);
    }
    
    final function author($words){
        $Authors = $this->queryAuthor($words);
        $conditions = '';
        foreach ( $Authors as $author ) {
            $conditions[]= $this->config['author']. "\"" .$author ."\"" ;
        }
        return "(".implode('%20OR%20', $conditions).")";
    }
    
    function search_author($author){
        return $author;
    }
    final public function queryAuthor($words){
            $Authors = Array();
            foreach ( $words as $author ) {
                array_push($Authors, $this->search_author($author));
            }    
            return $Authors;
    }   
    
/** ------------------- SHORTCODE DEFAULT --------------------- */    
    public function is_description($description){
        if ($description){
            return "description";
        }
        else {
            return false;
        }
    }   
    public function is_label_true($instance){
           return $instance;
    } 
    public function all_documents(){
        return true; 
    }
    public function instance_all($instance){
        return $instance;
    }
}
