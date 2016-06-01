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
        protected $model;
	public function Query() {
                $this-> model = new SimplepieModel();
	}
        public function get_model (){
            return $this->model;
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
        public function validete($author,$handle,$keywords){
            if (( is_null($author) && is_null($handle) && is_null($keywords)) ||
                ( empty($author) && empty($handle) && empty($keywords)) ){
                echo "Ingrese al menos una de las opciones: handle - author - keywords";
                return false;
            } 
            else { return true; }
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

        function cmpDate($a, $b)
        {
            $model = $this->get_model();
            return strcmp($model->date($b), $model->date($a));
        } 
        
        function cmpSubtype($a, $b)
        {
            $model = $this->get_model();
            if($model->type($b) == $model->type($a)){ 
                 return 0;   
             }
             return ($model->type($b) > $model->type($a)) ? -1 : 1;
        }
        
        function cmpDateSubtype($a, $b)
        {
         $model = $this->get_model();
         if ($model->date($b) == $model->date($a)){
             if($model->type($b) == $model->type($a)){ 
                 return 0;   
             }
             return ($model->type($b) > $model->type($a)) ? -1 : 1;
         }
          else{
          return strcmp($model->date($b), $model->date($a));}
        }
        
        function group($group_year,$group_subtype,$results){
            if ($group_year && $group_subtype){
               usort($results,  array($this,"cmpDateSubtype"));
               return $results;
            }
            if($group_year){
              usort($results,  array($this,"cmpDate"));
              return $results;
            }
            if($group_subtype){
              usort($results,  array($this,"cmpSubtype"));
              return $results;
            }
            return $results;
        }  
        
        function getPublications($all, $queryStandar, $cache, $subtypes_selected ,$group_subtype,$group_year){
            if($all){
                $results = $this->executeQuery($queryStandar, $cache);
            } else {
                $results = $this->executeQueryBySubtypes($subtypes_selected, $cache, $queryStandar);
            }
            return $this->group($group_year, $group_subtype, $results);
        }
        
	function group_attributes($description, $date, $show_author, $maxlenght,$show_subtypes) {
		return ( array (
				'description' => $description,
				'show_author' => $show_author,
				'max_lenght' => $maxlenght,
                                'show_subtypes' => $show_subtypes,
				'date' => $date 
		));
	}
	function render ($results,$attributes,$group_subtype,$group_date){
		$view = new View();
                if ($group_date && $group_subtype) {
                    return ($view->publicationsByDateSubtype ( $results, $attributes));
                }
                if ($group_date){
                     return ($view->publicationsByDate ( $results, $attributes,"date"));
                }
                if ($group_subtype){
                     return ($view->publicationsBySubtype ( $results, $attributes,"subtype"));
                }
                return $view->allPublications($results, $attributes);
	}
}