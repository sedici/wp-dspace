<?php
/**
 * Description of Configuration
 *
 * @author Paula Salamone
 */
 include_once('config.php');
class Configuration {

    protected $config;


    /**
     * Creates a new Configuration object for $configuration repository from its configuration file
     * @param $configuration strting required: name of the repository (to be searched as "name" field inside all config files)
     **/
    public function Configuration($configuration){

       $ini_files = $this->getConfigFiles( get_configuration_directory() );

		//FIXME what if file is not found?
       $this->config = $this->findConfigFileBy('name',$configuration, $ini_files);


    }

    /**
     * Returns a list (array) of a .ini file names (config files) stored in $base_path directory. If $base_path is not a valid directory, or if it's unreadable,
     * returns an empty list
     * @param $base_path string: Required. Full path (including trailing slash) to the directory where .ini files are stored
     * TODO Instead of returning an empty array, this should throw an exception for each condition error
     **/
    private function getConfigFiles($base_path) {
		return (file_exists($base_path) && is_dir($base_path) && is_readable($base_path))
		   ? glob($base_path."*.ini")
		   : array();
	}


	/**
	 * Looks up the first ini file in $files_list collection having $key value as $configuration
	 * Returns the array corresponding to the ini file, if found, or null otherwise
	 * @param $key string required: value to search into ini files (e.g. 'name')
	 * @param $configuration string required: configuration value to compare against ini[$key] (e.g. 'conicet')
	 * @param $files_list  array required: list of ini files list to be parsed, searching for $key value
	 * TODO maybe null isn't a good return value here!
	 **/
	private function findConfigFileBy($key,$configuration, $files_list) {
		foreach ($files_list as $config_file) {

      $parsed_ini_array = parseFile($config_file);

				if ($parsed_ini_array[$key] == $configuration)
					return $parsed_ini_array;
        }

        return null; //config file not found
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
    function get_default_query(){
        return "";
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
