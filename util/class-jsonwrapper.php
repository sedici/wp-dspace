<?php

namespace Wp_dspace\Util;

class jsonWrapper extends abstractWrapper {
    private $document;
    public $type = "json";
    
    public function __construct($json = null)
    {
      $this->document = $json;
    }

    
    public function get_link(){
        return $this->document['_links']['indexableObject']['href'];
    }

    public function get_authors(){
        return $this->get_metadata("dcterms.creator.author");
    }

    public function get_abstract(){
        return $this->get_metadata("dcterms.abstract");
    }

    public function get_title(){
        return $this->document["_embedded"]["indexableObject"]["name"];
    }

    public function get_date(){
        return $this->get_metadata("dc.date.available");
    }

    public function get_metadata($metaField){
        return $this->document["_embedded"]["indexableObject"]['metadata'][$metaField];
    }
 
    

}




?>