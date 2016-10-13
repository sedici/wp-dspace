<?php
require_once 'FormValidation.php';
class WidgetValidation extends FormValidation{
    public function WidgetValidation(){
         parent::__construct();
    }
     public function description($description,$summary){
            If ('on' == $description) {
		if ('on' ==$summary ) {
                    return "summary"; 
                    // checkbox description and summary ON
                } else { return "description"; } // checkbox description ON, summary OFF
            } else { return false;}
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
        if (('on' == $subtype) && ('on' == $date)){
            return $this->order['group_year_subtype'];
        }
        elseif ('on' == $subtype){
            return $this->order['group_subtype'];
        }
        elseif ('on' == $date){
            return $this->order['group_year'];
        }
        return null;
    }
}

