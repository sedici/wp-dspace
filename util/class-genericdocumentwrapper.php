<?php

namespace Wp_dspace\Util;

abstract class genericDocumentWrapper{
  
    
    public $document;
    public $type;
    
    private $link;
    private $authors;
    private $date;
    private $abstract;
    private $title;

    public function __construct($doc)
    {
        $this->document = $doc;
        $this->set_link();
        $this->set_authors();
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
    
    public abstract function get_author_name($author);

    #       <---SETTERS--->

    public abstract function set_link();

    public abstract function set_authors();

    public abstract function set_abstract();

    public abstract function set_title();
    
    public abstract function set_date();


        
}
?>