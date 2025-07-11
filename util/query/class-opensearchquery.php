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

            if ($xpath === false) {
            // Registramos el problema para futura referencia.
            error_log('WP-DSpace: Datos corruptos en cache detectados para: ' . $query . '. Limpiando cache.');
            
            // Le pedimos al modelo que elimine la caché para esta URL específica.
            $model->deleteCacheByUrl($query);
            
            // Devolvemos un array vacío para que el resto de la página no se rompa.
            return array(); 
        }





        // FIXME: TERMINAR. ESTO SIRVE PARA CHEQUEAR LA PAGINACIÓN. 
            $namespaces = $xpath->getNamespaces(true);
            // Acceder a los elementos utilizando los namespaces adecuados
            $itemsPerPage = (int) $xpath->children($namespaces['opensearch'])->itemsPerPage;
            $totalResults = (int) $xpath->children($namespaces['opensearch'])->totalResults;


        $entrys = array();
        if(!empty($xpath))
            foreach ($xpath->entry as $key => $value) {
                $wrapper = new \Wp_dspace\Util\Wrappers\xmlWrapper($value);
                // Si la fecha esta vacía, la intento recuperar
                if (empty($wrapper->get_date())){
                    $wrapper->fill_date($this->recoverDate($value));
                }
                array_push($entrys,$wrapper);
            }
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
	 * @param SimpleXmlObject $document Objeto XML que tiene los datos de un item de SEDICI
	 * @return DateObject Devuelve la fecha del articulo
	*/
    function recoverDate($document){
            $transient_dates = get_transient("dspace-dates");
            $url =  (string) $document->link['href'][0];

            // Si no esta el array de fechas en cache o no esta el dato que busco
            if ((!$transient_dates) or (empty($transient_dates[$url])) ) {
                // FIXME : Parametrizar según repositorio
                $tag_values = array('citation_publication_date','citation_date');
                $date = $this->http_handler->getMetaTag($url,$tag_values);
                // Si no existe el array en cache, lo creo
                if(!is_array($transient_dates)){
                    $transient_dates = array();
                }
                // Actualizo el array en cache
                $transient_dates[$url] = $date;
                set_transient("dspace-dates", $transient_dates);
            }
            else{
                // Si el dato estaba en cache, lo obtengo de ahí.
                $date = $transient_dates[$url];
            }
            return $date;
        }

    function dateIsEmpty($document){
        $dc_values= $document->children('dc', TRUE);
        if(empty($dc_values->date)){
            return true;
        }
        return false;
    }



}
