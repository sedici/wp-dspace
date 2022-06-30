<?php
/**
 * Description of Configuration
 *
 * @author Paula Salamone
 */
namespace Wp_dspace\Configuration;

include_once 'config.php';
define('S_CONECTOR4', '%2C');
define('S_CONECTOR5', '%20');
define('S_SEPARATOR', '\|\|\|');
define('S_FILTER2', '/discover?fq=author_filter%3A');
define('S_FILTER', '/discover?filtertype=author&filter_relational_operator=equals&filter=');
class Configuration
{

    protected $config;

    /**
     * Creates a new Configuration object for $configuration repository from its configuration file
     * @param $configuration strting required: name of the repository (to be searched as "name" field inside all config files)
     **/
    public function __construct($configuration)
    {
        //FIXME what if file is not found?
        $this->config = $this->findConfigFileBy('name', $configuration);

    }

    /**
     * Returns a list (array) of a .ini file names (config files) stored in $base_path directory. If $base_path is not a valid directory, or if it's unreadable,
     * returns an empty list
     * @param $base_path string: Required. Full path (including trailing slash) to the directory where .ini files are stored
     * TODO Instead of returning an empty array, this should throw an exception for each condition error
     **/
    private function getConfigFiles($base_path)
    {
        return (file_exists($base_path) && is_dir($base_path) && is_readable($base_path))
        ? glob($base_path . "*.ini")
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

    private function findConfigFileBy($key, $configuration)
    {
        $config = array_filter(get_option('config_repositorios')['repositorios'], function ($repo) use ($configuration, $key) {
            return $repo[$key] == $configuration;
        }
        );
        return array_pop($config);
    }

    final public function get_name()
    {
        return $this->config['name'];
    }
    final public function get_protocol_domain()
    {
        return $this->config['protocol'].'://' . $this->config['domain'];
    }

    final public function get_subtype_query()
    {
        return $this->config['subtype'];
    }
    final public function get_support_subtype()
    {
        return $this->config['support'];
    }
    final public function get_standar_query($max_results)
    {
        return ("?rpp=" . $max_results . "&format=" . $this->config['format'] . "&sort_by=" . Q_SORTBY . "&order=" . Q_ORDER . "&start=" . S_START);
    }

    final public function get_base_url()
    {
        return $this->config['protocol'] .'://'. $this->config['domain'] . $this->config['base_path'];
    }

    final public function standar_query($max_results)
    {
        return $this->get_base_url() . $this->get_standar_query($max_results);
    }

    final public function author($words)
    {
        $Authors = $this->queryAuthor($words);
        $conditions = array();
        foreach ($Authors as $author) {
            $conditions[] = $this->config['author'] . "\"" . $author . "\"";
        }
        return "(" . implode('%20OR%20', $conditions) . ")";
    }
    

    public function search_author($author)
    {
        return $author;
    }
    final public function queryAuthor($words)
    {
        $Authors = array();
        foreach ($words as $author) {
            array_push($Authors, $this->search_author($author));
        }
        return $Authors;
    }
    public function degree($degree){
        return "(" . $this->config['degree'] . '"' . $degree . '"' . ")";
    }
    
   // Arma el string de la consulta:

    final public function subject($subjectWords)      
      {
        $Subjects = $this->querySubject($subjectWords);
        $conditions = array();
        foreach ($Subjects as $subject) {
            $conditions[] = $this->config['subject'] . "\"" . $subject . "\"";
        }
        return "(" . implode('%20OR%20', $conditions) . ")";
    }
    
   // Pone las palabras ingresadas en un arreglo

    public function querySubject($subjectWords)
    {
        $Subjects = array();
        foreach ($subjectWords as $subject) 
        {
            array_push($Subjects, $this-> search_subject($subject));
        }
        return $Subjects;
    }

    public function search_subject($subject) 
    {
        return $subject;
    }

    
    public function get_default_query()
    {
        return $this->config['default_query'];
    }
    public function get_key_query()
    {
        return $this->config['query'];
    }
    public function remplace($text)
    {
        return str_replace(" ", S_CONECTOR5, $text);
    }
    public function print_author($author)
    {
        $link = $this->get_protocol_domain();
        $link = $link . S_FILTER;
        $name = str_replace(",", S_CONECTOR4, $author);
        $name = $this->remplace($name);
        $link .= $name;
        return ('<a href=' . $link . ' target="_blank">' . $author . '</a>');

    }
/** ------------------- SHORTCODE DEFAULT --------------------- */
    public function is_description($description)
    {
        if ($description) {
            return "description";
        } else {
            return false;
        }
    }

    public function is_label_true($instance)
    {

        return ($this->config['support']) ? $instance : false;
    }
    public function all_documents()
    {
        return $this->config['support'];
    }
    public function instance_all($instance)
    {
        return ($this->config['support']) ? $instance : true;
    }
}
