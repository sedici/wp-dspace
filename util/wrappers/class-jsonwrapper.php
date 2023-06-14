<?php

namespace Wp_dspace\Util\Wrappers;

class jsonWrapper extends genericDocumentWrapper {
    public $document;
    public $type = "json";
    
    public function set_link(){
        $this->link = $this->get_metadata("dc.identifier.uri")[0]["value"];
    }

    public function set_authors(){
        $authors = $this->get_metadata("dcterms.creator.author");
        if ($authors == ""){
            $authors = $this->get_metadata("dcterms.creator.compilator"); // En el caso especial de que sean Anales
        }
        $this->authors= $authors;
    }

    public function set_abstract(){
        $abstract = $this->get_metadata("dcterms.abstract");
        if (!empty($abstract)){
            $this->abstract = $abstract[0]["value"];
        }
        else
            $this->abstract = $abstract;
    }

    public function set_title(){
        $this->title = $this->document["_embedded"]["indexableObject"]["name"];
    }

    public function set_date(){      
        $date = $this->get_metadata("dcterms.issued")[0]["value"];
        $date = $this->autocompleteDate($date);
        $date = date_create($date);
        $this->date = date_format($date,"d/m/Y");
    }

    public function get_raw_date(){
        $date = $this->get_metadata("dcterms.issued")[0]["value"];
        $date = $this->autocompleteDate($date);
        return date_create($date);
        
    }
    
    public function autocompleteDate($date){
        switch (substr_count($date,"-")){
            case 0:
                return $date . "-01-01";
                break;
            case 1:
                return $date . "-01";
                break;
            default:
                return $date;
                break;
        }
    }

    public function get_metadata($metaField){
        if (isset($this->document["_embedded"]["indexableObject"]['metadata'][$metaField])){
            return $this->document["_embedded"]["indexableObject"]['metadata'][$metaField];
        }
        else {
            return "";
        }
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
        if(!empty($authors)){
            foreach ($authors as $auth){
                array_push($new_array,$auth["value"]);
            }
         } 
    $this->authors = $new_array;
    return $new_array;

}

}




?>