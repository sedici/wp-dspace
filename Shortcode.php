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
	function plugin_sedici($atts) {
            $filter = new Filter ();
            $instance = shortcode_atts ( $filter->default_shortcode (), $atts );
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
                    $max_results = $instance ['max_results'];
                    if ( $max_results < 1) { $max_results = 10;}
                    else { if ( $max_results > 100) { $max_results = 100;} }
                    $maxlenght = $instance ['max_lenght'];
                    $util = new Query();
                    $queryStandar = $util->standarQuery($handle, $author, $keywords,$max_results);
                    $groups = array ();
                    if (!$all){
                        $subtypes = $filter->subtypes ();
			// $subtypes: all names of subtypes
			foreach ( $subtypes as $subtype ) {
				// compares the user marked subtypes, if TRUE, save the subtype.
				$type = $filter->convertirEspIng ( $subtype );
				if ('true' === $instance [$type]) {
                                    $query = $util->querySubtype($queryStandar,$subtype);
                                    $entrys =  $util->createQuery( $query,  $cache);
                                    if (!empty($entrys)) { 
                                            $groups[$subtype]=array ();
                                            $groups[$subtype] = $entrys;
                                    }
				}
			}
			if ($instance ['thesis'] === 'true') {
				// if thesis is true, save subtypes thesis
				$all_thesis = $filter->vectorTesis ();
				// $all_thesis: all subtypes thesis
				foreach ( $all_thesis as $type ) {
                                    $query = $util->querySubtype($queryStandar,$type);
                                    $entrys =  $util->createQuery( $query,  $cache);
                                    if (!empty($entrys)) { 
                                            $groups[$type]=array ();
                                            $groups[$type] = $entrys;
                                    }
				}
			}
                    }else { 
                            $groups =$util->createQuery( $queryStandar,  $cache);
                        }
                    $attributes = $util->group_attributes ( $description, $date, $show_author, $maxlenght);
                    $util->render ( $all, $groups, $attributes );
		}
	}
}
