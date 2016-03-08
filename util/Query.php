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
include_once 'config.php';
class Query {
	protected $max_lenght_text;
	protected $query;
	protected $one_day;
	protected $cache_days;
	protected $total_results;	
	public function Query() {
		$this->query = get_base_url () . get_standar_query();
		$this->cache_days = array (7,1,3,14);
		$this->one_day = 86400;
		$this->total_results = array(0,10,25,50,100);
		$this->max_lenght_text = 150;
	}
        public function standar_query(){
            return $this->query;
        }
	public function max_lenght_text(){
		return $this->max_lenght_text;
	}
	public function one_day(){
		return $this->one_day;
	}
	public function cache_days(){
		return $this->cache_days;
	}
	public function total_results() {
		return  $this->total_results;
	}
        function concatenarCondiciones($words, $filterPrefix = ''){
            $conditions = '';
            foreach ( $words as $word ) {
		$conditions[]= $filterPrefix. $word ;
            }
            return implode(' OR ', $conditions);
        }
        
        public function standarQuery($handle, $author, $keywords,$all,$subtypes){
            $queryEstandar = $this->standar_query();
                if (!empty($handle)) $queryEstandar .="&". SQ_HANDLE . "=".$handle;
                if (!empty($author)) {
                    $words = explode(';',$author);
                    $queryEstandar .= "&". Q_QUERY."=".$this->concatenarCondiciones($words , SQ_AUTHOR);
                }
                if (!empty($keywords)) {
                    $words = explode(';',$keywords);
                    $queryEstandar .= "&". Q_QUERY."=".$this->concatenarCondiciones($words);
                }
                if (!$all) {
                    $queryEstandar .="&". Q_QUERY."=". $this->concatenarCondiciones($subtypes,SQ_SUBTYPE);
                }
                return $queryEstandar;
        }
        
        function createQuery($queryStandar ,$cache , $groups ="", $all) {
		$start = 0; 
		$count = 0;
                if(!is_array($groups)) { $groups = array (); }
		$model = new SimplepieModel();
		do {
			$query = $queryStandar . "&start=". $start;
			$xpath = $model->loadPath ( $query, $cache );
			$count += $model->itemQuantity ( $xpath ); // number of entrys
			$totalResults = $model->totalResults ( $xpath );
			$entry = $model->entry ( $xpath ); //all documents
			$start += 100;
			if ($all) {
				array_push ( $groups, $entry );
			} else {
				foreach ( $entry as $e ) {
					$subtype = $model->type ( $e ); // document subtype
					if (array_key_exists ( $subtype, $groups )) {
						array_push ( $groups [$subtype], $e );
					}
				}
			}
		} while ( $count < $totalResults );
		return ($groups);
	}
        
	function view_subtypes($selected_subtypes) {
		$publications = array (); // documents for the view
		while ( list ( $key, $val ) = each ( $selected_subtypes ) ) {
			// $val: all documents by subtype
			$elements = count ( $val );
			if ($elements > 0) {
				// $key: document subtype
				$colection = array ( 'view' => $val, 'filter' => $key);
				array_push ( $publications, $colection );
			}
		}
		return ($publications);
	}
	function group_attributes($description, $date, $show_author, $max_results, $maxlenght) {
		return ( array (
				'description' => $description,
				'show_author' => $show_author,
				'max_results' => $max_results,
				'context' => $context,
				'max_lenght' => $maxlenght,
				'date' => $date 
		));
	}
	function render($all, $groups, $attributes) {
		$view = new View();
		if ($all) {
                    return ($view->all_publications ( $groups, $attributes));
		} else {
                    return ($view-> publications( $groups, $attributes ));
			}
	}
}