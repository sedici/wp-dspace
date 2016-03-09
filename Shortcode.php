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
                    $maxlenght = $instance ['max_lenght'];
                    if (!$all){
                        $subtypes = $filter->subtypes ();
			// $subtypes: all names of subtypes
			$selected_subtypes = array ();
			$groups = array ();
			// $groups: groups publications by subtype
			foreach ( $subtypes as $suptype ) {
				// compares the user marked subtypes, if TRUE, save the subtype.
				$subtype = $filter->convertirEspIng ( $suptype );
				if ('true' === $instance [$subtype]) {
					array_push ( $selected_subtypes, $suptype );
					$groups [$suptype] = array ();
				}
			}
			if ($instance ['thesis'] === 'true') {
				// if thesis is true, save subtypes thesis
				$all_thesis = $filter->vectorTesis ();
				// $all_thesis: all subtypes thesis
				foreach ( $all_thesis as $thesis ) {
					array_push ( $selected_subtypes, $thesis );
					$groups [$thesis] = array ();
				}
			}
                    }	
                    $util = new Query ();	
                    $query = $util->standarQuery($handle, $author, $keywords,$all,$selected_subtypes);
                    $entrys = $util->createQuery( $query,  $cache, $groups, $all );
                    if (!$all)  { $entrys = $util->view_subtypes ( $entrys); }
                    $attributes = $util->group_attributes ( $description, $date, $show_author, $max_results, $maxlenght);
                    $util->render ( $all, $entrys, $attributes );
		}
	}
}
