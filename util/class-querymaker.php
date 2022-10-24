<?php
namespace Wp_dspace\Util;

abstract class queryMaker
{
    protected $model;
    protected $order;
    
    public function __construct()
    {
        $this->model = new \Wp_dspace\Model\SimpleXMLModel();
        $this->order = new XmlOrder();
    }

    
    #Builds the Query String. Returns the query (String)

    public abstract function buildQuery($handle, $author, $keywords, $subject, $degree, $max_results,$configuration,$all = "", $subtypes_selected= "");
    
    #Executes the Query. Returns an array of Wrappers (jsonWrapper or xmlWrapper)

    public abstract function getPublications($all, $queryStandar, $cache, $subtypes_selected);
    
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

    function group_attributes($description, $date, $show_author, $maxlenght, $show_subtypes, $share)
    {
        return (array(
            'description' => $description,
            'show_author' => $show_author,
            'max_lenght' => $maxlenght,
            'show_subtypes' => $show_subtypes,
            'share' => $share,
            'date' => $date
        ));
    }
    
    
}

?>