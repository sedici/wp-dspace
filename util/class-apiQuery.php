<?php

namespace Wp_dspace\Util;
define('DEFAULT_URL' ,"https://host170.sedici.unlp.edu.ar/server/api/")
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
    $xml = file_get_contents("https://host170.sedici.unlp.edu.ar/server/api/discover/search/objects?f.title=visibilidad,contains");
            
    echo $xml;

    public function splitImputs($imput)
    {
        return explode(';', $imput);
    }

    // Hay que hacer lo siguiente:

    
    function executeQuery($query){

    }

    function standarQuery($handle, $author, $keywords , $subject , $degree , $max_results, $configuration){
    $query = Array();
      if(!empty($handle)){
        
      }
      if(!empty($author)){
        $auth = authorsQuery($author);
        array_push($query, $configuration->author($words));
      }
    }
    // Funciones auxiliares para procesar

    function authorsQuery($authors){
        $str= "";
        $words = $this->splitImputs($author);
        for each $words as $word{
             $str = $str+ 'f.author="' + $word + '"'+",contains&"
        }
      return $str;
    }

}