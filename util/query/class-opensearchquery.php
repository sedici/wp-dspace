<?php

namespace Wp_dspace\Util\Query;

define('DEFAULT_QUERY', "&query=*:*");

class opensearchQuery extends queryMaker
{
    protected $model;
    protected $order;
    protected $subtype_query;

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
                $wrapper = new \Wp_dspace\Util\Wrappers\xmlWrapper($value);
                array_push($entrys,$wrapper);
            }
            //ACA HAY QUE RETORNAR UN VECTOR DE ITEMS
        return $entrys;
    }


    public function buildQuery($handle, $author, $keywords, $subject, $degree, $max_results,$configuration,$all = "", $subtypes_selected= ""){
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
            if(!empty($degree)) {
                array_push($query, $configuration->degree($degree));
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

    public function querySubtype ($query , $type) 
    {
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

    /** 
	 * Determina si la consulta debe ser por subtipos o no, y luego la ejecuta
	 * @param Boolean $all
     * @param String $queryStandar
     * @param Cache
     * @param String $subtypes_selected
     * @param Integer $max_results
	 * @return Array  Devuelve un array con los items mapeados como objetos de la clase XMLWrapper
	*/
    function getPublications($all, $queryStandar, $cache, $subtypes_selected, $max_results)
    {   
        if ($all) {
            $results = $this->executeQuery($queryStandar, $cache);
           
        } else {
            $results = $this->executeQueryBySubtypes($subtypes_selected, $cache, $queryStandar);
        }
        return ($results) ? $this->order->cmpXml($results) : $results;
    }

  /** 
	 * Chequea si un articulo tiene fecha, si no la tiene, la recupera de cache o con web Scrapping a SEDICI
	 * @param XmlObject $document Objeto XML que tiene los datos de un item de SEDICI
	 * @return DateObject Devuelve la fecha del articulo
	*/
    function checkDate($document){
        $dc_values= $document->children('dc', TRUE);
        // Si no esta seteada la fecha en el documento
        if(empty($dc_values->date)){
            $transient_dates = get_transient("dspace-dates");
            // Si no esta el array de fechas en cache o no esta el dato que busco
            if (!$transient_dates) {
                $query_url =  (string) $document->link['href'][0];

            }
        }
    }



}
