<?php
define ( 'CMP_DATE_SUBTYPE', "cmpDateSubtype" );
define ( 'CMP_DATE', "cmpDate" );
define ( 'CMP_SUBTYPE', "cmpSubtype");
        
class FormValidation {
    protected $order;
    
    public function FormValidation(){
        $this->order= array ('group_year_subtype'=>  CMP_DATE_SUBTYPE,
                             'group_year'=>CMP_DATE,
                             'group_subtype'=>CMP_SUBTYPE
                 );
    }   
    
    public function validete($author,$handle,$keywords){
            if (( is_null($author) && is_null($handle) && is_null($keywords)) ||
                ( empty($author) && empty($handle) && empty($keywords)) ){
                echo "Ingrese al menos una de las opciones: handle - author - keywords";
                return false;
            } 
            else { return true; }
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
