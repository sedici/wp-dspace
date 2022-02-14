<?php
namespace Wp_dspace\Util;
define ( 'CMP_DATE_SUBTYPE', "cmpDateSubtype" );
define ( 'CMP_DATE', "cmpDate" );
define ( 'CMP_SUBTYPE', "cmpSubtype");
require_once  WP_CONTENT_DIR."/plugins/wp-dspace/configuration/class-configuration.php";
foreach ( glob ( WP_CONTENT_DIR."/plugins/wp-dspace/configuration/*_config.php" ) as $app ) { 
    require_once $app;
}        
class FormValidation {
    protected $order;
    
    public function __construct(){
        $this->order= array ('group_year_subtype'=>  CMP_DATE_SUBTYPE,
                             'group_year'=>CMP_DATE,
                             'group_subtype'=>CMP_SUBTYPE
                 );
    }   
    
    public function create_configuration($configuration){
        // $config = ucfirst($configuration)."_config";
        // if (class_exists($config,true)){
        //     return (new $config($configuration));
        // }
        // else {
        //     return new Configuration($configuration);
        // }
        return new \Wp_dspace\Configuration\Configuration($configuration);
    }
    public function get_support_subtype($configuration){
        $config= $this-> create_configuration($configuration);
        return $config->get_support_subtype();
    }
    public function labelValidation($author,$handle,$keywords,$subject){
            if (( is_null($author) && is_null($handle) && is_null($keywords)) && is_null($subject)||
                ( empty($author) && empty($handle) && empty($subject) && empty($keywords)) ){
                echo "Ingrese al menos una de las opciones: handle - author - keywords - subject";
                return false;
            } 
            else 
               return true; 
        }

    public function maxResults($max_results){
            if ( $max_results < min_results()) { $max_results = min_results();}
            else { if ( $max_results > max_results()) { $max_results = max_results();} }
            return $max_results;
        }
    public function maxLenght($max_lenght){
            if (!is_null($max_lenght)){
		 if ( $max_lenght < min_results()) { $max_lenght = show_text();}
            }
            return $max_lenght;
        }       
}
