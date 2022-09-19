<?php

namespace Wp_dspace\Util;

class apiQuery {

    protected $view;
    protected $model;
    protected $order;
    protected $subtype_query;
    protected $base_url;

    public function __construct()
    {
        $this->model = new \Wp_dspace\Model\SimpleXMLModel(); // Remplazar por el JSON
        $this->order = new XmlOrder();   // Remplazar por JSON Order ¿?
        $this->view = new \Wp_dspace\View\View(); 
        $this->base_url = "https://host170.sedici.unlp.edu.ar/server/api/";
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

    // Hay que hacer lo siguiente:

    
    function executeQuery($query)

}