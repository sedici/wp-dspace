<?php

namespace Wp_dspace\Util\Wrappers;

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
        return html_entity_decode($this->abstract, ENT_COMPAT, 'UTF-8');

    }

    public function get_title(){
        return html_entity_decode($this->title, ENT_COMPAT, 'UTF-8');
    }
    
    public function get_date(){
        return $this->date;
    }

    public abstract function get_raw_date();

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

    /** 
	 * Carga directamente la información sobre la fecha (Se usa para completar desde las Query Class)
	 * @param String $rawdate El string con información sobre la fecha
     * @return Null
	*/
    public function fill_date($rawdate){
        $date = $this->process_date($rawdate);
        $this->date = $date;
    }

    /** 
	 * Autocompleta campos de la fecha recibida si es necesario
	 * @param String $rawdate El string con información sobre la fecha
     * @return String  Devuelve la fecha con los campos faltantes
	*/
    public function autocomplete_date($date){
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

    /** 
	 * Se encarga de autocompletar campos de la fecha si es necesario, y devolverla formateada
	 * @param String $rawdate El string con información sobre la fecha
     * @return Date  Devuelve la fecha formateada como un Objeto DateTime 
	*/
    public function process_date($rawdate){
        $date = $this->autocomplete_date($rawdate);
        $date = date_create($date);
        $date = date_format($date,"d/m/Y"); 
        return $date;
    }

        
}
?>