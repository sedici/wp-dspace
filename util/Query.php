<?php
define ( 'ACTIVE_SUBTYPE', "subtype" );
define ( 'ACTIVE_DATE', "date" );

include_once dirname(__DIR__)."/view/View.php";
class Query {	
        protected $view;
        protected $model;
        protected $order;
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

        public function remplace($text){
		return str_replace(" ", S_CONECTOR5, $text);
	}
        public function queryAuthor($words){
            $Authors = Array();
            foreach ( $words as $author ) {
                $name = str_replace(",", S_CONECTOR4, $author);
                $name = $this->remplace($name);
                $query= strtolower($name). S_SEPARATOR . $name;
                array_push($Authors, $query);
            }    
            return $this->concatenarCondiciones($Authors,SQ_AUTHORFILTER);
        }
        public function concatenarCondiciones($words, $filterPrefix = ''){
            $conditions = '';
            foreach ( $words as $word ) {
		$conditions[]= $filterPrefix. "\"" .$word ."\"" ;
            }
            return "(".implode('%20OR%20', $conditions).")";
        }
        
           
        public function splitImputs($imput){
            return explode(';',$imput);
        }
        
        public function standarQuery($handle, $author, $keywords,$max_results){
            $queryEstandar = standar_query($max_results);
            $query= Array();
                if (!empty($handle)) {$queryEstandar .="&". SQ_HANDLE . "=".$handle;}
                if (!empty($author)) {
                    $words = $this->splitImputs($author);
                    array_push($query, $this->queryAuthor($words));
                }
                if (!empty($keywords)) {
                    $words = $this->splitImputs($keywords);
                    array_push($query, $this->concatenarCondiciones($words));
                }
                if (!empty($query)) { $queryEstandar.="&". Q_QUERY."=". implode('%20AND%20', $query); }
                return $queryEstandar;
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
            return  $query."(".SQ_SUBTYPE."\"" .$type ."\"".")";
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

	function render ($results,$attributes,$cmp){
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