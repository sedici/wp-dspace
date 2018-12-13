<?php
namespace Wp_dspace\Util;
//require_once 'class-formvalidation.php';
include_once ( plugin_dir_path( __FILE__ ).'../configuration/config.php');
class ShortcodeValidation extends FormValidation {

    public function create_configuration($configuration){
        return parent::create_configuration($configuration);
    }

    function maxResults($max_results){
            if ( $max_results < min_results()) { $max_results = min_results();}
            else { if ( $max_results > max_results()) { $max_results = max_results();} }
            return $max_results;
    }
    function maxLenght($max_lenght){
            if (!is_null($max_lenght)){
		 if ( $max_lenght < min_results()) { $max_lenght = show_text();}
            }
            return $max_lenght;
    }
    public function getOrder($subtype,$date){
        if (($subtype) && ('true' === $date)){
            return $this->order['group_year_subtype'];
        }
        elseif ($subtype){
            return $this->order['group_subtype'];
        }
        elseif ('true' === $date){
            return $this->order['group_year'];
        }
        return null;
    }

}
