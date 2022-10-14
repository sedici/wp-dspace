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

    
    #Builds the Query String
    #public abstract function buildQueryString();
    
    #Executes the Query. Returns an array of Wrappers (jsonWrapper or xmlWrapper)
    public abstract function executeQuery($query,$cache);
    
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


    function render($results, $attributes, $cmp, $configuration)
    {
        $this->set_configuration($configuration);
        if (strcmp($cmp, CMP_DATE_SUBTYPE) == 0) {
            return ($this->publicationsByDateSubtype($results, $attributes, ACTIVE_DATE, ACTIVE_SUBTYPE));
        }
        if (strcmp($cmp, CMP_DATE) == 0) {
            return ($this->publicationsByGroup($results, $attributes, ACTIVE_DATE));
        }
        if (strcmp($cmp, CMP_SUBTYPE) == 0) {
            return ($this->publicationsByGroup($results, $attributes, ACTIVE_SUBTYPE));
        }
        return $this->allPublications($results, $attributes);
    }

    
    
}

?>