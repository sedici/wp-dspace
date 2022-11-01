<?php

namespace Wp_dspace\Util;


define('DEFAULT_URL' ,"https://host170.sedici.unlp.edu.ar");
define('ENDPOINT', "/server/api/discover/search/objects?");
include_once dirname(__DIR__) . "/view/class-view.php";

class apiQuery extends queryMaker{

  
    protected $model;
    protected $order;
    protected $subtype_query;


    public function splitImputs($input)
    {
        return explode(';', $input);
    }


    function buildQuery($handle, $author, $keywords , $subject , $degree , $max_results, $configuration, $all = "", $subtypes_selected= ""){
      //Falta agregar $all y $subtypes_selected a la lista de parametros reales
      $baseURL = $configuration->get_api_url();
      $query = $baseURL . "/discover/search/objects?";
      if(!empty($handle)){

      // Si el usuario ingresa el handle y no el ID, hacemos esta consulta para obtener el UUID :
       if (strpos($handle, '/') !== false){
         $str = $query . 'query=handle:"'. $handle . '"';
         $request = wp_remote_get($str);
         $result = wp_remote_retrieve_body($request);
         $result = json_decode($result,true);
         $handle = $result['_embedded']['searchResult']['_embedded']['objects'][0]['_embedded']['indexableObject']['uuid'];
      }
        $query = $query . "scope=" . $handle . "&"; 
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
     /* if($all != true){
        $query= $query . $this->buildFilter('itemtype',$subtypes_selected);
      }
   */
      $query = substr($query, 0, -1); 
     
      return $query;
    }
    
    function getPublications($all, $query, $cache, $subtypes_selected)
    { 
      $model = $this->get_model();
      $json = $model->loadJsonPath($query,$cache);
      $wrappedItems = [];
      $items = $json['_embedded']['searchResult']['_embedded']['objects'];
      foreach ($items as $item){
        $wrapped = new jsonWrapper($item);
        array_push($wrappedItems,$wrapped);
      }
      return $wrappedItems;
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