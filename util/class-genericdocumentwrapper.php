<?php

namespace Wp_dspace\Util;

abstract class genericDocumentWrapper{
  
    
    public $document;
    public $type;
    
    public $link;
    public $authors;
    public $date;
    public $abstract;
    public $title;

    public $subtype;

    public function __construct($doc)
    {
        $this->document = $doc;
        $this->set_link();
        $this->set_authors();
        $this->format_authors();
        $this->set_subtype();
        $this->set_abstract();
        $this->set_title();
        $this->set_date();
    }

    
    #---GETTERS
    public function get_link(){
      return $this->link;
    }

    public function get_authors(){
        return $this->authors;
    }

    public function get_abstract(){
        return $this->abstract;
    }

    public function get_title(){
        return $this->title;
    }
    
    public function get_date(){
        return $this->date;
    }

    public function getDocumentType(){
        return $this->type;
    }

    public function get_subtype(){
        return $this->subtype;
    }
    
    public abstract function get_author_name($author);

    public abstract function format_authors();


    ## Funcion para hacer Debugg de los Wrappers
    public function toString(){
        $str = " | Titulo: " . $this->get_title() . ". Subtipos: ". $this->get_subtype() . " . Link: " . $this->get_link() . ". Resumen: " . $this->get_abstract() . ". Fecha:" .  $this->get_date() . " | ";
        return $str;
    }

    #       <---SETTERS--->

    public abstract function set_link();

    public abstract function set_authors();

    public abstract function set_subtype();

    public abstract function set_abstract();

    public abstract function set_title();
    
    public abstract function set_date();



        
}
?>