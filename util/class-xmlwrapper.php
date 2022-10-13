<?php

namespace Wp_dspace\Util;

class xmlWrapper extends genericDocumentWrapper {
    public $document;
    public $type = "xml";
    
    
    public function set_link(){
        $this->link = $this->document->link->attributes()->href;
    }

    public function set_date(){
        $dc = $this->document->children('dc', TRUE);
        $this->date = date_create($dc->date);
    }

    public function set_authors(){
        $this->authors = $this->document->author;
    }

    public function set_abstract(){
        $this->abstract = $this->document->sumary;
    }

    public function set_title(){
        $this->title = $this->document->title;
    }

    
    public function get_author_name($author){
        return $author->name;
    }

}




?>