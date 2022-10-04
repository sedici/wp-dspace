<?php

namespace Wp_dspace\Util;
define('DEFAULT_URL' ,"https://host170.sedici.unlp.edu.ar");
define('ENDPOINT', "/server/api/discover/search/objects?");
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

    public function splitImputs($imput)
    {
        return explode(';', $imput);
    }

    
    function executeQuery($query, $cache){
       $model = $this->get_model();
       $json = $model->jsonLoadPath($str,$cache);
       $entrys = array();
       if(!empty($xpath))
       foreach ($xpath->entry as $key => $value) {
           $entrys[]= $value;
       }

       //ACA HAY QUE RETORNAR UN VECTOR DE ITEMS
   return $entrys;
}

    function standarQuery($baseURL, $handle, $author, $keywords , $subject , $degree , $max_results, $configuration, $all, $subtypes_selected){
      //Falta agregar $all y $subtypes_selected a la lista de parametros reales
      $query = $baseURL . " ";
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

      #FIXME: No es la mejor forma, pero asi elimino el ultimo simbolo & 
      $query = substr($query, 0, -1); 
  
      return $query;
    }
    
    // Funciones auxiliares para armar el query
    
    // Para filtros como: Autor, Keywords
    function buildFilter($field,$values){
        $str= "";
        $values = $this->splitImputs($values);
        foreach ($values as $value) {
             $str = $str . 'f.'. $field .'=' . "'" . $value . "'" .",contains&";
        }
      return $str;
    }
    
    function lugarDesarrolloFilter($institution){
        return "f.lugarDesarrollo=" . $institution . ",contains&";
    }
    


}