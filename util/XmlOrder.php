<?php
class XmlOrder {

    protected $cmp;
    protected $model;
    public function XmlOrder() {
                $this-> model = new SimplepieModel();
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
            if ($model->date($b) == $model->date($a)){
                return strcmp($model->type($a), $model->type($b));}
            else {
            return strcmp($model->date($b), $model->date($a));}
        } 
        
        function cmpSubtype($a, $b)
        {
            $model = $this->get_model();
            if ($model->type($b) == $model->type($a)){
                return strcmp($model->date($b), $model->date($a));}
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
            usort($results,  array($this,$this->getCmp()));
            return $results;
        }
}
