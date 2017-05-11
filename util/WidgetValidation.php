<?php
require_once 'FormValidation.php';
class WidgetValidation extends FormValidation{
    public function WidgetValidation(){
         parent::__construct();
    }
     public function description($description,$summary){
         if (strcmp($description, "description") == 0) {
		if ('on' ==$summary ) {
                    return "summary"; 
                    // checkbox description and summary ON
                } else { return "description"; } // checkbox description ON, summary OFF
            } elseif (strcmp($description, "summary") == 0) {
                      return "summary"; 
                }   
            return false;    
        }
        
    public function limit_text($limit,$max){
            if ('on' == $limit){
                if ( (empty($max)) || ($max < min_results())){
                    $max =  show_text(); //default lenght
                }
            } else { $max = null; }
            return $max;
        }
    public function getOrder($subtype,$date){
        if (($subtype) && ('on' == $date)){
            return $this->order['group_year_subtype'];
        }
        elseif ($subtype){
            return $this->order['group_subtype'];
        }
        elseif ('on' == $date){
            return $this->order['group_year'];
        }
        return null;
    }
}