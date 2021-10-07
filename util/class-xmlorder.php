<?php
namespace Wp_dspace\Util;
class XmlOrder {

    protected $cmp;
    protected $model;
    public function __construct() {
                $this-> model = new \Wp_dspace\Model\SimpleXMLModel();
	}
    public function get_model (){
            return $this->model;
        }
    
    public function getCmp(){
        return $this->cmp;
    }
    public function setCmp($cmp){
        $this->cmp=$cmp;
    }
    
            
     function cmpDate($a, $b)
        {
            $model = $this->get_model();

            
           if ($model->date_utf_fotmat($b) == $model->date_utf_fotmat($a)){
                return strcmp($model->type($a), $model->type($b));}
            else 
            return strtotime($model->date_utf_fotmat($b)) - strtotime($model->date_utf_fotmat($a)) ;  
        
        } 
        
        function cmpSubtype($a, $b)
        {
            $model = $this->get_model();
    
            if ($model->type($b) == $model->type($a)){
                return strtotime($model->date_utf_fotmat($b)) - strtotime($model->date_utf_fotmat($a)) ;}
            else {
                return strcmp($model->type($a), $model->type($b));}
        }
        
        function cmpDateSubtype($a, $b)
        {
            $model = $this->get_model();
            if ($model->year($b) == $model->year($a)){
                return $this->cmpSubtype($a, $b);}
            else {
            return strcmp($model->year($b), $model->year($a));}
        }
    
        function cmpXml($results){
            
            $cmp=$this->getCmp();
            $array_results= array();
          
            if(!is_null($cmp)){
                usort($results, array($this,$cmp));
            }    
            return $results;
        }

}