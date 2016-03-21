<?php
/**
 * Plugin Name: Sedici-Plugin
 * Plugin URI: http://sedici.unlp.edu.ar/
 * Description: This plugin connects the repository SEDICI in wordpress, with the purpose of showing the publications of authors or institutions
 * Version: 1.0
 * Author: SEDICI - Paula Salamone Lacunza
 * Author URI: http://sedici.unlp.edu.ar/
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 */
?>
<?php
include_once dirname(__DIR__)."/view/View.php";
class Query {
	protected $cache_days;
	protected $total_results;	
        protected $model;
	public function Query() {
		$this->cache_days = array (3,7,14);
		$this->total_results = array(10,25,50,100);
                $this-> model = new SimplepieModel();
	}
        public function get_model (){
            return $this->model;
        }
        public function cache_days(){
		return $this->cache_days;
	}
	public function total_results() {
		return  $this->total_results;
	}
        
        public function concatenarCondiciones($words, $filterPrefix = ''){
            $conditions = '';
            foreach ( $words as $word ) {
		$conditions[]= $filterPrefix. "\"" .$word ."\"" ;
            }
            return "(".implode('%20OR%20', $conditions).")";
        }
        public function validete($author,$handle,$keywords){
            if (( is_null($author) && is_null($handle) && is_null($keywords)) ||
                ( empty($author) && empty($handle) && empty($keywords)) ){
                echo "Ingrese al menos una de las opciones: handle - author - keywords";
                return false;
            } 
            else { return true; }
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
        
        public function standarQuery($handle, $author, $keywords,$max_results){
            $queryEstandar = standar_query($max_results);
            $query= Array();
                if (!empty($handle)) {$queryEstandar .="&". SQ_HANDLE . "=".$handle;}
                if (!empty($author)) {
                    $words = explode(';',$author);
                    array_push($query, $this->concatenarCondiciones($words , SQ_AUTHOR));
                }
                if (!empty($keywords)) {
                    $words = explode(';',$keywords);
                    array_push($query, $this->concatenarCondiciones($words));
                }
                if (!empty($query)) { $queryEstandar.="&". Q_QUERY."=". implode('%20AND%20', $query); }
                return $queryEstandar;
        }
        
        function createQuery($query ,$cache) {
		$model = $this->get_model();
		$xpath = $model->loadPath ( $query, $cache );
		$entry = $model->entry ( $xpath ); //all documents
		return $entry;
	}
        
        function entrys ($queryStandar,$subtype,$cache,$groups){
             $query = $this->querySubtype($queryStandar,$subtype);
             $entrys =  $this->createQuery( $query,  $cache);
             if (!empty($entrys)) { 
                $groups[$subtype]=array ();
                $groups[$subtype] = $entrys;
            }
            return $groups;
        }
        function concatenarSubtypes($subtypes,$cache,$queryStandar){
            $groups = Array ();
            foreach ( $subtypes as $type ) {
            //compares the user marked subtypes, if ON, save the subtype.
                $groups = $this->entrys($queryStandar,$type,$cache,$groups);  
            }
            return $groups;
        }
        
        function getPublications($all, $queryStandar, $cache, $subtypes = ""){
            if(!$all) {
                $groups = $this->concatenarSubtypes($subtypes,$cache,$queryStandar);
            }
            else { 
                $groups =$this->createQuery( $queryStandar,  $cache);
            }
            return $groups;
        }
        
	function group_attributes($description, $date, $show_author, $maxlenght) {
		return ( array (
				'description' => $description,
				'show_author' => $show_author,
				'max_lenght' => $maxlenght,
				'date' => $date 
		));
	}
	function render($all, $groups, $attributes) {
		$view = new View();
		if ($all) {
                    return ($view->all_publications ( $groups, $attributes));
		} else {
                    return ($view->publications( $groups, $attributes ));
			}
	}
}