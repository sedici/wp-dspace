<?php

namespace Wp_dspace\Util;

abstract class abstractWrapper{
  

    private $document;
    public $type;

    public function setDocument($item){
        $this->document = $item;
    }
    
    public abstract function get_link();

    public abstract function get_authors();

    public abstract function get_abstract();

    public abstract function get_title();

    public abstract function get_metadata($metaField);
    
    public abstract function get_date();

    public function getDocumentType(){
        return $this->type;
    }

}
?>