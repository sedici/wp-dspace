<?php
define ( 'ACTIVE_SUBTYPE', "subtype" );
define ( 'ACTIVE_DATE', "date" );
define ('DEFAULT_QUERY',"&query=*:*");
include_once dirname(__DIR__)."/view/View.php";
class Query {
        protected $view;
        protected $model;
        protected $order; 
        protected $subtype_query;
	public function Query() {
                $this-> model = new SimplepieModel();
                $this-> order = new XmlOrder();
                $this-> view = new View();
	}
        public function get_model (){
            return $this->model;
        }
        
        public function setCmp($value){
            $this->order->setCmp($value);
        }
        
        public function concatenarCondiciones($words){
            $conditions = array();
            $filterPrefix = '';
            foreach ( $words as $word ) {
		$conditions[]= $filterPrefix. "\"" .$word ."\"" ;
            }
            return "(".implode('%20OR%20', $conditions).")";
        }
           
        public function splitImputs($imput){
            return explode(';',$imput);
        }

        public function standarQuery($handle, $author, $keywords,$max_results,$configuration){
            $this->subtype_query = $configuration->get_subtype_query();
            $queryEstandar = $configuration->standar_query($max_results);
            $query= Array();
                if (!empty($handle)) {$queryEstandar .="&". SQ_HANDLE . "=".$handle;}
                if (!empty($author)) {
                    $words = $this->splitImputs($author);
                    array_push($query, $configuration->author($words));
                }
                if (!empty($keywords)) {
                    $words = $this->splitImputs($keywords);
                    array_push($query, $this->concatenarCondiciones($words));
                }
                if (!empty($query)) { $queryEstandar.="&". Q_QUERY."=". implode('%20AND%20', $query); }
                else{
                    $queryEstandar.=$configuration->get_default_query();
                }
                return $queryEstandar;//.DEFAULT_QUERY;
        }
        
        function executeQuery($query ,$cache) {
    		$model = $this->get_model();
    		$xpath = $model->loadPath ( $query, $cache );
    		$entrys = $model->entry ( $xpath ); //all documents
            return $entrys;
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
        
        function entrysBySubtype ($queryStandar,$subtype,$cache){
             $query = $this->querySubtype($queryStandar,$subtype);
             $entrys =  $this->executeQuery( $query,  $cache);
             return $entrys;
        }
        function executeQueryBySubtypes($subtypes,$cache,$queryStandar){
            $results = Array ();
            foreach ( $subtypes as $type ) {
                $entrys = $this->entrysBySubtype($queryStandar,$type,$cache);
                $results=array_merge($results,$entrys);
            }
            return $results;
        }
        
        function getPublications($all, $queryStandar, $cache, $subtypes_selected){
            if($all){
                $results = $this->executeQuery($queryStandar, $cache);
            } else {
                $results = $this->executeQueryBySubtypes($subtypes_selected, $cache, $queryStandar);
            }
            return $this->order->cmpXml($results);
        }
        
	function group_attributes($description, $date, $show_author, $maxlenght,$show_subtypes,$share) {
		return ( array (
				'description' => $description,
				'show_author' => $show_author,
				'max_lenght' => $maxlenght,
                                'show_subtypes' => $show_subtypes,
                                'share'=> $share,
				'date' => $date
		));
	}

	function render ($results,$attributes,$cmp,$configuration){
            $this->view->set_configuration($configuration);
                if(strcmp($cmp, CMP_DATE_SUBTYPE)==0){ 
                    return ($this->view->publicationsByDateSubtype ( $results, $attributes,ACTIVE_DATE,ACTIVE_SUBTYPE));
                }
                if (strcmp($cmp, CMP_DATE)==0){
                     return ($this->view->publicationsByGroup( $results, $attributes,ACTIVE_DATE));
                }
                if (strcmp($cmp, CMP_SUBTYPE)==0){
                     return ($this->view->publicationsByGroup( $results, $attributes,ACTIVE_SUBTYPE));
                }
                return $this->view->allPublications($results, $attributes);
	}
}
