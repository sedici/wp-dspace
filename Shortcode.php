<?php
function LoadShortcode($atts) {
	$shortcode = new Shortcode ();
        ob_start();
	$shortcode->plugin_sedici ($atts);
        $res =ob_get_contents();
        ob_clean();
        return $res;
}
function DspaceShortcode($atts) {
	return LoadShortcode ( $atts );
}
class Shortcode {
    
        function querySubtypes ($instance, $util, $queryStandar) {
             $filter = new Filter ();
             $cache = $instance ['cache'];//default value from filer.php
             $groups = array ();
             $subtypes = $filter->subtypes ();
			// $subtypes: all names of subtypes
			foreach ( $subtypes as $subtype ) {
				// compares the user marked subtypes, if TRUE, save the subtype.
				$type = $filter->convertirEspIng ( $subtype );
				if ('true' === $instance [$type]) {
                                   $groups = $util->entrys($queryStandar,$subtype,$cache, $groups);
				}
			}
			if ($instance ['thesis'] === 'true') {
				// if thesis is true, save subtypes thesis
				$all_thesis = $filter->vectorTesis ();
				// $all_thesis: all subtypes thesis
				foreach ( $all_thesis as $type ) {
                                    $groups = $util->entrys($queryStandar,$type,$cache, $groups);
				}
			}
               return $groups;
        }
        function maxResults($max_results){
            if ( $max_results < 1) { $max_results = 10;}
            else { if ( $max_results > 100) { $max_results = 100;} }
            return $max_results;
        }
	function plugin_sedici($atts) {
            $instance = shortcode_atts ( default_shortcode (), $atts );
            $handle = $instance ['handle'] ;
            $author = $instance ['author']; 
            $keywords = $instance ['keywords'] ;
            if ( is_null($author) && is_null($handle) && is_null($keywords)) {
                echo "Ingrese al menos una de las opciones: handle - author - keywords";
            } 
            else {
                    $description = $instance ['description'] === 'true' ? "description" : false;
                    $date = ($instance ['date'] === 'true');
                    $show_author = ($instance ['show_author'] === 'true');
                    $cache = $instance ['cache'];//default value from filer.php
                    $all = ($instance ['all'] === 'true');
                    $max_results = $this->maxResults($instance ['max_results']);
                    $maxlenght = $instance ['max_lenght'];
                    $util = new Query();
                    $queryStandar = $util->standarQuery($handle, $author, $keywords,$max_results);
                    $groups = array ();
                    if (!$all){
                       $groups = $this->querySubtypes($instance, $util, $queryStandar);
                    }else { 
                            $groups =$util->createQuery( $queryStandar,  $cache);
                        }
                    $attributes = $util->group_attributes ( $description, $date, $show_author, $maxlenght);
                    $util->render ( $all, $groups, $attributes );
		}
	}
}
