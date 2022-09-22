<?php

namespace Wp_dspace\Util;
define('DEFAULT_URL' ,"https://host170.sedici.unlp.edu.ar/server/api/");
include_once dirname(__DIR__) . "/view/class-view.php";
class apiQuery {

    protected $view;
    protected $model;
    protected $order;
    protected $subtype_query;

    public function __construct()
    {
        $this->model = new \Wp_dspace\Model\SimpleXMLModel(); // Remplazar por el JSON
        $this->order = new XmlOrder();   // Remplazar por JSON Order ¿?
        $this->view = new \Wp_dspace\View\View(); 
    }
    public function get_model()
    {
        return $this->model;
    }

    public function setCmp($value)
    {
        $this->order->setCmp($value);
    }

    // Query
    $Get = file_get_contents("https://host170.sedici.unlp.edu.ar/server/api/discover/search/objects?f.title=visibilidad,contains");
            
    echo $Get;

    public function splitImputs($imput)
    {
        return explode(';', $imput);
    }

 

    
    function executeQuery($query){

    }

    function standarQuery($handle, $author, $keywords , $subject , $degree , $max_results, $configuration){
      $query =" ";
      if(!empty($handle)){
        
      }
      if(!empty($author)){
        $str= $str . buildFilter('author',$author);
      }
      if(!empty($keywords)){
        $str= $str . buildFilter('subject',$keywords);
      }
      if(!empty($max_results)){
        $str= $str . "size=" . $max_results . "&";
      }
      
    }

    

    // Funciones auxiliares para armar el query
    
    // Para filtros como: Autor, Keywords
    function buildFilter($field,$values){
        $str= "";
        $values = $this->splitImputs($values);
        foreach $values as $value{
             $str = $str . 'f.'. $field .'=' . $author .",contains&";
        }
      return $str;
    }
    
    function lugarDesarrolloFilter($institution){
        return "f.lugarDesarrollo=" . $institution . ",contains&";
    }
}