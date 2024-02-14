<?php

namespace Wp_dspace\Util\Query;
define('DEFAULT_URL' ,"https://host170.sedici.unlp.edu.ar");
define('ENDPOINT', "/server/api/discover/search/objects?");


class apiQuery extends queryMaker{

  
    protected $model;
    protected $order;
    protected $subtype_query;

    // TODO : Falta parametrizar los campos author, subject, keyword para que se puedan configurar desde el formulario externo
    
    function buildQuery($handle, $author, $keywords , $subject , $degree , $max_results, $configuration, $all = true, $subtypes_selected= ""){
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
      if($all != true){
        /*
        $query = $query . "query="
        $query= $query . $this->buildFilter('itemtype',$subtypes_selected);
        */
      }
   
      $query = substr($query, 0, -1); 

      return $query;
    }
    
    function getPublications($all, $query, $cache, $subtypes_selected, $max_results)
    { 
      $model = $this->get_model();
      $json = $model->loadJsonPath($query,$cache);
      $wrappedItems = [];
      $items = $json['_embedded']['searchResult']['_embedded']['objects'];
      $pages = $json['_embedded']['searchResult']['page'];
      if ($this->needsPagination($pages,$max_results)){
        $items = $this->addPages($pages,$items,$model,$query,$cache,$max_results);
      }
      foreach ($items as $item){

        // Este endpoint devuelve colecciones, items y comunidades. Hay que filtrar todo lo que no sea un item
        if($item["_embedded"]['indexableObject']["type"] == "item"){
          $wrapped = new \Wp_dspace\Util\Wrappers\jsonWrapper($item);
          array_push($wrappedItems,$wrapped);
        }    

      }
      return ($wrappedItems) ? $this->order->cmpXml($wrappedItems) : $wrappedItems;
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
    
     /**
     * Calcula si es necesario o no volver a realizar la consulta para obtener mas páginas
     *      
     * @param JsonObject $pages Contiene información sobre las páginas disponibles para la consulta
     * @param Integer $max_results La cantidad maxima de resultados a mostrar, seteado en el Shortcode
     * 
     * @return Boolean Devuelve verdadero si es necesario volver a realizar la consulta para traer mas páginas, falso de lo contrario
    */
    function needsPagination($pages, $max_results){
      // TODO : Hay que parametrizar la cantidad máxima de resultados que puede devolver la API (Actual: 100)
      if ($max_results > 100){
        if ($pages["totalPages"] > 1){
          return true;
        }
      }
      return false;
    }

     /**
     * Ejecuta consultas para obtener las páginas faltantes, y agrega los resultados a la lista de items
     * @param JsonObject $pages Contiene información sobre las páginas disponibles para la consulta
     * @param Integer $max_results La cantidad maxima de resultados a mostrar, seteado en el Shortcode
     * 
     * @return Array Devuelve el listado de items modificado.
    */
    function addPages($pages,$items,$model,$query, $cache, $max_results){
      // Obtengo la cantidad de páginas que la consulta tiene disponibles
      $total_pages = $pages["totalPages"];
      // Comparo la cantidad de páginas que necesito con las que tengo disponibles
      $pages_requiered = intdiv($max_results,100);
      if($pages_requiered < $total_pages){
        $total_pages = $pages_requiered;
      }

      for ($i = 1; $i < $total_pages ; $i++){
        $query_aux = $query . "&page=" . strval($i);
        //Ejecuto la consulta para la página actual
        $new_items = $model->loadJsonPath($query_aux,$cache);
        $new_items = $new_items['_embedded']['searchResult']['_embedded']['objects'];
        $items = array_merge($items, $new_items);
      }

      return $items; 
    }

    public function splitImputs($input)
    {
        return explode(';', $input);
    }


 }