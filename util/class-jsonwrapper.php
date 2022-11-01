<?php

namespace Wp_dspace\Util;

class jsonWrapper extends genericDocumentWrapper {
    public $document;
    public $type = "json";
    
    public function set_link(){
        $this->link = $this->get_metadata("dc.identifier.uri")[0]["value"];
    }

    public function set_authors(){
        $this->authors = $this->get_metadata("dcterms.creator.author");
    }

    public function set_abstract(){
        $abstract = $this->get_metadata("dcterms.abstract");
        $this->abstract = $abstract[0]["value"];
    }

    public function set_title(){
        $this->title = $this->document["_embedded"]["indexableObject"]["name"];
    }

    public function set_date(){
        $date =  date_create($this->get_metadata("dc.date.available")[0]["value"]);
        $this->date = date_format($date,"d/m/Y");
    }
    
    public function get_metadata($metaField){
        return $this->document["_embedded"]["indexableObject"]['metadata'][$metaField];
    }
 
    public function get_author_name($author){
       return $author["value"];
    }

    public function set_subtype(){
        $this->subtype =$this->get_metadata("dc.type")[0]["value"];
    }

    public function format_authors(){
        $authors = $this->get_authors();
        $new_array = [];
        foreach ($authors as $auth){
            array_push($new_array,$auth["value"]);
    }
    $this->authors = $new_array;
    return $new_array;

}

}




?>