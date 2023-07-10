<?php
namespace Wp_dspace\Util\Query;

abstract class queryMaker
{
    protected $model;
    protected $order;
    protected $http_handler;

    public function __construct()
    {
        $this->model = new \Wp_dspace\Model\SimpleXMLModel();
        $this->order = new \Wp_dspace\Util\XmlOrder();
        $this->http_handler = new httpQuery();
    }

    
    #Builds the Query String. Returns the query (String)

    public abstract function buildQuery($handle, $author, $keywords, $subject, $degree, $max_results,$configuration,$all = "", $subtypes_selected= "");
    
    #Executes the Query. Returns an array of Wrappers (jsonWrapper or xmlWrapper)

    public abstract function getPublications($all, $queryStandar, $cache, $subtypes_selected, $max_results);
    
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

    function group_attributes($description, $date, $show_author, $maxlenght, $show_subtypes, $share, $show_videos = false)
    {
        return (array(
            'description' => $description,
            'show_author' => $show_author,
            'max_lenght' => $maxlenght,
            'show_subtypes' => $show_subtypes,
            'share' => $share,
            'date' => $date,
            'show_videos' => $show_videos
        ));
    }
    
    
}

?>