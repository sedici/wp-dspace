<?php

namespace Wp_dspace\Util;

define('ACTIVE_SUBTYPE', "subtype");
define('ACTIVE_DATE', "date");
define('DEFAULT_QUERY', "&query=*:*");
include_once dirname(__DIR__) . "/view/class-view.php";
class Query
{
    protected $view;
    protected $model;
    protected $order;
    protected $subtype_query;
    public function __construct()
    {
        $this->model = new \Wp_dspace\Model\SimpleXMLModel();
        $this->order = new XmlOrder();
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

    public function concatenarCondiciones($words)
    {
        $conditions = array();
        $filterPrefix = '';
        foreach ($words as $word) {
            $conditions[] = $filterPrefix . "\"" . $word . "\"";
        }
        return "(" . implode('%20OR%20', $conditions) . ")";
    }

    public function splitImputs($imput)
    {
        return explode(';', $imput);
    }

    /**
	 * @return SimpleXMLElement with all documents
	 */
    function executeQuery($query, $cache)
    {   
        $model = $this->get_model();
        $xpath = $model->loadPath($query, $cache);
        $entrys = array();
        if(!empty($xpath))
            foreach ($xpath->entry as $key => $value) {
                $entrys[]= $value;
            }
        return $entrys;
    }


        public function standarQuery($handle, $author, $keywords, $subject, $max_results,$configuration){
        $this->subtype_query = $configuration->get_subtype_query();
        $queryEstandar = $configuration->standar_query($max_results);
        $query= Array();
            if (!empty($handle)) {$queryEstandar .="&". SQ_HANDLE . "=".$handle;}
            if (!empty($author)) {
                $words = $this->splitImputs($author);
                array_push($query, $configuration->author($words));
            }
            if(!empty($subject)) { 
                $subjectWords = $this->splitImputs($subject);
                array_push($query, $configuration->subject($subjectWords));
            }

            if (!empty($keywords)) {
                $words = $this->splitImputs($keywords);
                array_push($query, $this->concatenarCondiciones($words));
            }
            if (!empty($query)) { $queryEstandar.="&". $configuration->get_key_query()."=". implode('%20AND%20', $query); }
            else{
                $default_query=$configuration->get_default_query();
                $queryEstandar = (empty($default_query)) ? $queryEstandar : $queryEstandar.'&query='.$configuration->get_default_query() ;
            }
            return $queryEstandar;//.DEFAULT_QUERY;
    }

    public function querySubtype ($query , $type) {
        if (strpos($query, Q_QUERY) === false) {
            $query .= "&". Q_QUERY."=";
        }
        else {
            $query .= '%20AND%20';
        }
        return  $query."(".$this->subtype_query."\"" .$type ."\"".")";
    }

    function entrysBySubtype($queryStandar, $subtype, $cache)
    {
        
        $query = $this->querySubtype($queryStandar, $subtype);
       
        $entrys =  $this->executeQuery($query,  $cache);
        return $entrys;
    }
    function executeQueryBySubtypes($subtypes, $cache, $queryStandar)
    {
        $results = array();
        foreach ($subtypes as $type) {
            $entrys = $this->entrysBySubtype($queryStandar, $type, $cache);
            $results = array_merge($results, $entrys);
        }
        return $results;
    }

    function getPublications($all, $queryStandar, $cache, $subtypes_selected)
    {   
        
      
        if ($all) {
            $results = $this->executeQuery($queryStandar, $cache);
           
        } else {
            $results = $this->executeQueryBySubtypes($subtypes_selected, $cache, $queryStandar);
        }
        return ($results) ? $this->order->cmpXml($results) : $results;
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
        
        $this->view->set_configuration($configuration);
        if (strcmp($cmp, CMP_DATE_SUBTYPE) == 0) {
            return ($this->view->publicationsByDateSubtype($results, $attributes, ACTIVE_DATE, ACTIVE_SUBTYPE));
        }
        if (strcmp($cmp, CMP_DATE) == 0) {
            return ($this->view->publicationsByGroup($results, $attributes, ACTIVE_DATE));
        }
        if (strcmp($cmp, CMP_SUBTYPE) == 0) {
            return ($this->view->publicationsByGroup($results, $attributes, ACTIVE_SUBTYPE));
        }
        return $this->view->allPublications($results, $attributes);
    }
}
