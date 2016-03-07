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
define ( 'RPP', '100' );
define ( 'FORMAT', 'atom' );
define ( 'SORTBY', '0' );
define ( 'ORDER', 'desc' );
define ( CONECTOR2, '%5C' );
define ( CONECTOR3, '%7C' );
define ( _PROTOCOL, "http://" );
define ( _DOMAIN, "sedici.unlp.edu.ar" );
define ( _BASE_PATH, "/open-search/discover" );
function conector() {
	return CONECTOR2 . '+';
}
function get_conector() {
	return (CONECTOR2 . CONECTOR3 . CONECTOR2 . CONECTOR3 . CONECTOR2 . CONECTOR3);
}
function get_base_url() {
	return _PROTOCOL . _DOMAIN . _BASE_PATH;
}
function get_protocol_domain() {
	return _PROTOCOL . _DOMAIN;
}
class Query {
	protected $max_lenght_text;
	protected $query;
	protected $one_day;
	protected $cache_days;
	protected $total_results;	
	public function Query() {
		$this->query = get_base_url () . "?rpp=" . RPP . "&format=" . FORMAT . "&sort_by=" . SORTBY . "&order=" . ORDER;
		$this->cache_days = array (7,1,3,14);
		$this->one_day = 86400;
		$this->total_results = array(0,10,25,50,100);
		$this->max_lenght_text = 150;
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
	function UrlSedici($filter, $handle) {
		//URL : go to sedici by subtype and handle
		$filter = strtolower ( $filter ); 
		$word = explode ( " ", $filter ); 
		$url = get_protocol_domain () . "/handle/" . $handle . "/discover?fq=type_filter%3A";
		$cant = count ( $word ); 
		$url = $url . $word [0]; 
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . conector () . $word [$i];
		}
		$uppercase  = ucfirst ( $filter );
		$word = explode ( " ", $uppercase  );
		$url = $url . get_conector ();
		$cant = count ( $word );
		$url = $url . $word [0];
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . conector () . $word [$i];
		}
		return $url;
	}
	function queryAllHandle($start, $context) {
		//all results of query handle
		$query = $this->query;
		$query .= $start . "&scope=" . $context;
		return $query;
	}
	function queryHandle($start, $context, $subtypes) {
		//weapon query handle for publications particular subtype
		$i = 1;
		$query = $this->query;
		$query .= $start . "&scope=" . $context . "&query=sedici.subtype:";
		$count_filter = count ( $subtypes );
		foreach ( $subtypes as $f ) {
			$query .= "\"" . $f . "\"";
			if ($i != $count_filter) {
				$query .= "%20OR%20sedici.subtype:";
			}
			$i ++;
		}
		return $query;
	}
	function queryAuthor($start, $context) {
		//query for author
		$consulta = $this->query;
		$consulta .= $start . "&query=sedici.creator.person:\"$context\"";
		return $consulta;
	}
        function queryFree($start, $context) {
		//query for author
		$consulta = $this->query;
		$consulta .= $start . "&query=\"$context\"";
		return $consulta;
	}
        
        function concatenar($typeFilter,$filters){
            $words = explode ( ";", $filters );
            $query = $typeFilter;
            $count_filter = count ( $words );
            $i=1;
		foreach ( $words as $w ) {
			$query .= "\"" . $w . "\"";
			if ($i != $count_filter) {
				$query .= "%20OR%20".$typeFilter;
			}
			$i ++;
		}
		return $query;
        }
        function concatenarFree($typeFilter,$filters){
            $words = explode ( ";", $filters );
            $query = $typeFilter;
            $count_filter = count ( $words );
            $i=1;
		foreach ( $words as $w ) {
			$query .= "\"" . $w . "\"";
			if ($i != $count_filter) {
				$query .= "%20OR%20";
			}
			$i ++;
		}
		return $query;
        }
        
        
        function queryByAll($handle, $author, $free ,$cache) {
		$start = 0; 
		$count = 0;
		$model = new SimplepieModel();
                $queryEstandar = $this->query;
                if (!empty($handle)) $queryEstandar .= "&scope=" . $handle;
                if (!empty($author)) {
                    $queryEstandar .= $this->concatenar ("&query=sedici.creator.person:", $author);
                }
                if (!empty($free)) {
                    $queryEstandar .= $this->concatenarFree ("&query=", $free);
                }
                $groups = array ();
		do {
			$query = $queryEstandar . "&start=". $start;
			$xpath = $model->loadPath ( $query, $cache );
			$count += $model->itemQuantity ( $xpath ); // number of entrys
			$totalResults = $model->totalResults ( $xpath );
			$entry = $model->entry ( $xpath ); //all documents
			$start += 100;
			array_push ( $groups, $entry );
		} while ( $count < $totalResults );
		return ($groups);
	}
        
        
        
        
        
	function group_subtypes($type, $all, $context, $selected_subtypes, $groups,$cache) {
		$start = 0; 
		$count = 0;
		$model = new SimplepieModel();
		do {
			if ($type == "handle") {
				if ($all) {
					$query = $this->queryAllHandle ( $start, $context );
				} else {
					$query = $this->queryHandle ( $start, $context, $selected_subtypes );
				}
			} else {
                            if ($type == "author"){
				$query = $this->queryAuthor ( $start, $context );
                            }
                            else {
                                //Is free search
                                $query = $this->queryFree($start, $context);
                            }
			}
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
	function view_subtypes($selected_subtypes, $type ,$context) {
		$publications = array (); // documents for the view
		while ( list ( $key, $val ) = each ( $selected_subtypes ) ) {
			// $val: all documents by subtype
			$elements = count ( $val );
			if ($elements > 0) {
				// $key: document subtype
				if ($type == 'handle') {
					$url = $this->UrlSedici ( $key, $context );
					$colection = array (
							'view' => $val,
							'url' => $url,
							'filter' => $key 
					);
				} else { // author and free search
					$colection = array (
							'view' => $val,
							'filter' => $key 
					);
				}
				array_push ( $publications, $colection );
			}
		}
		return ($publications);
	}
	function group_attributes($description, $date, $show_author, $max_results, $context, $maxlenght) {
		return ( array (
				'description' => $description,
				'show_author' => $show_author,
				'max_results' => $max_results,
				'context' => $context,
				'max_lenght' => $maxlenght,
				'date' => $date 
		));
	}
	function render($type, $all, $groups, $attributes) {
		$view = new View();
		if ($type != 'author') {
			$attributes['show_author'] = TRUE;
			
		} 
			if ($all) {
				return ($view->all_publications ( $groups, $attributes,$type ));
			} else {
				return ($view-> publications( $groups, $attributes,$type ));
			}
		}
}