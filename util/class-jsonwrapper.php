<?php

namespace Wp_dspace\Util;

class jsonWrapper extends genericDocumentWrapper {
    public $document;
    public $type = "json";
    
    public function set_link(){
        $this->link = $this->document['_links']['indexableObject']['href'];
    }

    public function set_authors(){
        $this->authors = $this->get_metadata("dcterms.creator.author");
    }

    public function set_abstract(){
        $this->abstract = $this->get_metadata("dcterms.abstract");
    }

    public function set_title(){
        $this->title = $this->document["_embedded"]["indexableObject"]["name"];
    }

    public function set_date(){
        $this->date =  date_create($this->get_metadata("dc.date.available"));
    }
    
    public function get_metadata($metaField){
        return $this->document["_embedded"]["indexableObject"]['metadata'][$metaField];
    }
 
    public function get_author_name($author){
       return $author["value"];
    }

}




?>