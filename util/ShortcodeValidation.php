<?php
require_once 'FormValidation.php';
class ShortcodeValidation extends FormValidation{
   
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
        if (('true' === $subtype) && ('true' === $date)){
            return $this->order['group_year_subtype'];
        }
        elseif ('true' === $subtype){
            return $this->order['group_subtype'];
        }
        elseif ('true' === $date){
            return $this->order['group_year'];
        }
        return null;
    }
    
}
