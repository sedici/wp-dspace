<?php

namespace Wp_dspace\Util;

class xmlWrapper extends genericDocumentWrapper {
    public $document;
    public $type = "xml";
    
    
    public function set_link(){
        $this->link = $this->document->link->attributes()->href;
    }

    public function set_date(){
        $dc_values= $this->document->children('dc', TRUE);
        $date = date_create($dc_values->date);
        $this->date = date_format($date,"d/m/Y"); 
    }

    public function set_authors(){
        $this->authors = $this->document->author;
    }

    public function set_subtype()
    {
        $description = $this->document->summary; 
		$dctype = explode ( "\n", $description );
		$this->subtype= $dctype[0];
    }

    // Recibe $attributes['description']
    public function set_abstract(){
        $this->abstract = $this->document->summary;
    }

    public function set_title(){
        $this->title = $this->document->title;
    }

    
    public function get_author_name($author){
        return $author->name;
    }

    public function format_authors(){
        $authors = $this->get_authors();
        $new_array = [];
        foreach ($authors as $auth){
            array_push($new_array,$auth->name);
    }
    $this->authors = $new_array;
    return $new_array;

}

}




?>