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

function default_shortcode(){
	return ( array (
		'handle' => null,
		'author' => null,
                'keywords' => null,
		'max_results' => 10,
		'max_lenght' => null,
		'all' => false,
		'description' => false,
		'date' => false,
		'show_author' => false,
		'cache' => defaultCache(),
		'article' => false,
		'preprint' => false,
		'book' => false,
		'working_paper' => false,
		'technical_report' => false,
		'conference_object' => false,
		'revision' => false,
		'work_specialization' => false,
                'learning_object'=>false,
		'thesis' => false 
	));
}

class Shortcode {
        function querySubtypes ($instance,$all) {
            //this function returns all active subtypes
            if (!$all){
             $groups = array ();
             $filter = new Filter ();
             $subtypes = $filter->subtypes ();
			// $subtypes: all names of subtypes
			foreach ($subtypes as $key => $subtype){
				// compares the user marked subtypes, if TRUE, save the subtype.
				if ('true' === $instance [$key]) {
                                    array_push($groups, $subtype);
				}
			}
			if ($instance ['thesis'] === 'true') {
				// if thesis is true, save subtypes thesis
				$all_thesis = $filter->vectorTesis ();
				// $all_thesis: all subtypes thesis
				foreach ( $all_thesis as $type ) {
                                    array_push($groups, $type);
				}
			}
                return $groups;
              }
            else { return; }   
        }
        function maxResults($max_results){
            if ( $max_results < min_results()) { $max_results = min_results();}
            else { if ( $max_results > total_results()) { $max_results = total_results();} }
            return $max_results;
        }
        function maxLenght($max_lenght){
            if (!is_null($max_lenght)){
		 if ( $max_lenght < min_results()) { $max_lenght = show_text();}
            }
            return $max_lenght;
        }
	function plugin_sedici($atts) {
            $instance = shortcode_atts ( default_shortcode (), $atts );
            $handle = $instance ['handle'] ;
            $author = $instance ['author']; 
            $keywords = $instance ['keywords'];
            $util = new Query();
            
            if ($util->validete($author,$handle,$keywords)){
                    $description = $instance ['description'] === 'true' ? "description" : false;
                    $date = ($instance ['date'] === 'true');
                    $show_author = ($instance ['show_author'] === 'true');
                    $cache = $instance ['cache'];//default value from filer.php
                    $all = ($instance ['all'] === 'true');
                    $max_results = $this->maxResults($instance ['max_results']);
                    $maxlenght = $this->maxLenght($instance ['max_lenght']);
                    $subtypes = $this->querySubtypes($instance,$all);
                    //$subtypes: all selected documents subtypes
                    
                    $queryStandar = $util->standarQuery($handle, $author, $keywords,$max_results);
                    $groups = $util->getPublications($all, $queryStandar, $cache, $subtypes);
                    $attributes = $util->group_attributes ( $description, $date, $show_author, $maxlenght);
                    $util->render ( $all, $groups, $attributes );
            }
        }    
}
