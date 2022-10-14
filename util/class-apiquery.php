<?php

namespace Wp_dspace\Util;


define('DEFAULT_URL' ,"https://host170.sedici.unlp.edu.ar");
define('ENDPOINT', "/server/api/discover/search/objects?");
include_once dirname(__DIR__) . "/view/class-view.php";

class apiQuery {

  
    protected $model;
    protected $order;
    protected $subtype_query;


    public function get_model()
    {
        return $this->model;
    }
    

    public function setCmp($value)
    {
        $this->order->setCmp($value);
    }

    public function splitImputs($input)
    {
        return explode(';', $input);
    }


    function standarQuery($baseURL, $handle, $author, $keywords , $subject , $degree , $max_results, $configuration, $all, $subtypes_selected){
      //Falta agregar $all y $subtypes_selected a la lista de parametros reales
      $query = $baseURL;
      if(!empty($handle)){
        $query = $query . "scope=" . $handle . "&"; //Funciona solo con el UUID del centro
        //$str= $str . lugarDesarrolloFilter($handle);
      }
      if(!empty($author)){
        $query= $query . $this->buildFilter('author',$author);
      }
      if(!empty($subject)){
        $query= $query . $this->buildFilter('subject',$subject);
      }
      if(!empty($keywords)){
        $query= $query . $this->buildFilter('keyword',$keywords);
      }
      if(!empty($max_results)){
        $query= $query . "size=" . $max_results . "&";
      }
/*      Para agregar en actualizaciones posteriores el filtro por título 
      if (!empty($title)){
        $str= $str . buildFilter('title',$title);
      }
*/
      if($all != true){
        $query= $query . $this->buildFilter('itemtype',$subtypes_selected);
      }

      $query = substr($query, 0, -1); 
     
      return $query;
    }
    
    function executeQuery($query,$cache){

      $model = $this->get_model();
      $json = $model->loadJsonPath($query,$cache);
      $articles = $json['_embedded']['searchResult']['_embedded']['objects'];
      return $articles;
    }
   
    function buildArticles($articles,$configuration){
      foreach ($articles as $art){
        $wrapper = new jsonWrapper($art);
        echo "TITULO: " . $wrapper->get_title();
        echo "/---------------/";
        $authors = $wrapper->get_authors();
        foreach ($authors as $auth){
          echo $configuration->print_author($auth["value"]);
        }
        
      
      
      }
      return $articles;
    }
    

    // Funciones auxiliares para armar el query
    
    // Para filtros como: Autor, Keywords
    function buildFilter($field,$values){
        $str= "";
        $values = $this->splitImputs($values);
   
        foreach ($values as $value) {
             $value= strtr($value,[' '=>'%20.']);
             $str = $str . 'f.'. $field .'=' . "'" . $value . "'" .",contains&";
        }
        return $str;
    }
    
    function lugarDesarrolloFilter($institution){
        return "f.lugarDesarrollo=" . $institution . ",contains&";
    }
    


}