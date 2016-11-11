<?php
/**
 * Description of Configuration
 *
 * @author Paula Salamone
 */
class Configuration {
    //put your code here
    protected $config;
    
    public function Configuration($configuration){
        $directorio = get_configuration_directory();
	foreach (glob($directorio."*.ini") as $value) {
            $ini_array = parse_ini_file($value);
            if ($ini_array['name'] == $configuration){
                $this->config= $ini_array;
            }
        }    
    }
            
    function print_author($author){
        return $author;
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
    public function is_description($instance){
           return ($instance === 'true' ? "description" : false);
    }   
    public function is_label_true($instance){
           return ($instance === 'true');
    } 
    public function all_documents(){
        return true; 
    }
}
