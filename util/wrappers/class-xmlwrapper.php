<?php

namespace Wp_dspace\Util\Wrappers;

class xmlWrapper extends genericDocumentWrapper {
    public $document;
    public $type = "xml";
    
    
    public function set_link(){
        $this->link = $this->document->link->attributes()->href;
    }

    public function set_date(){

        $dc_values= $this->document->children('dc', TRUE);
        if(!empty($dc_values->date)){
            $date = $this->process_date($dc_values->date);
            $this->date = $date;
        }
    }
    

    public function get_raw_date(){
        // Si la fecha no se encontraba en el item, y se recupero mediante HTTPQuery
        if (isset($this->raw_date)){
            return date_create($this->raw_date);
        }
        // Si la fecha si esta en el documento
        $dc_values= $this->document->children('dc', TRUE);
        if(!empty($dc_values->date)){
            return date_create($dc_values->date);
        }
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

    public function set_abstract(){
        $dc_values= $this->document->children('dc', TRUE);
        if(!empty($dc_values->description)){
            $abstract = $dc_values->description;
            if(strlen($abstract) > 1100){
              $abstract = substr($abstract, 0, strpos($abstract,' ', 1000));
              $abstract = $abstract . "...";
            }
        }
        else{
            $abstract = $this->document->summary;
            $abstract = explode ( "\n", $abstract );
            $abstract = (string) $abstract[2];
        }
        $this->abstract = $abstract;
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
        if(!empty($authors)){
            foreach ($authors as $auth){
                array_push($new_array,$auth->name);
            }
        }
        $this->authors = $new_array;
        return $new_array;

}

}




?>