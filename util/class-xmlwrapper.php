<?php

namespace Wp_dspace\Util;

class xmlWrapper extends abstractWrapper {
    private $document;
    public $type = "xml";
    
    public function __construct($xml)
    {
      $this->document = $xml;
    }
    
    public function get_link(){
        return $this->document->link->attributes()->href;
    }

    
    #GET Date of creation

    public function get_date(){
        $dc = $this->document->children('dc', TRUE);
        return date_create($dc->date);
    }

    public function get_authors(){
        return $this->document->author;
    }

    #------------------------------------------

    public function get_abstract(){
        return $this->get_metadata("dcterms.abstract");
    }

    public function get_title(){
        return $this->document->title;
    }

    

    public function get_metadata($metaField){
        return $this->document["_embedded"]["indexableObject"]['metadata'][$metaField];
    }


}




?>