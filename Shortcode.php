<?php
function LoadShortcode($atts, $type) {
	$shortcode = new Shortcode ();
        ob_start();
	$shortcode->plugin_sedici ( $atts, $type );
        $res =ob_get_contents();
        ob_clean();
        return $res;
}
function AuthorShortcode($atts) {
	return LoadShortcode ( $atts, 'author' );
}
function HandleShortcode($atts) {
	return LoadShortcode ( $atts, 'handle' );
}
function FreeShortcode($atts) {
	return LoadShortcode ( $atts, 'free' );
}

class Shortcode {
	function plugin_sedici($atts, $type) {
		$filter = new Filter ();
		$a = shortcode_atts ( $filter->default_shortcode (), $atts );
		if (is_null ( $a ['context'] )) {
			echo "Ingrese un context";
		} else {
			$a ['type'] = $type;
			$description = $a ['description'] === 'true' ? "description" : false;
			$date = ($a ['date'] === 'true');
			$show_author = ($a ['show_author'] === 'true');
			$cache = $a ['cache'];//default value from filer.php
			$context = $a ['context'];
			$all = ($a ['all'] === 'true');
			$max_results = $a ['max_results'];
			$maxlenght = $a ['max_lenght'];
			
			$util = new Query ();
			$subtypes = $filter->subtypes ();
			// $subtypes: all names of subtypes
			$selected_subtypes = array ();
			$groups = array ();
			// $groups: groups publications by subtype
			foreach ( $subtypes as $o ) {
				// compares the user marked subtypes, if TRUE, save the subtype.
				$subtype = $filter->convertirEspIng ( $o );
				if ('true' === $a [$subtype]) {
					array_push ( $selected_subtypes, $o );
					$groups [$o] = array ();
				}
			}
			$thesis = ($a ['thesis'] === 'true');
			if ($thesis) {
				// if thesis is true, save subtypes thesis
				$all_thesis = $filter->vectorTesis ();
				// $all_thesis: all subtypes thesis
				foreach ( $all_thesis as $o ) {
					array_push ( $selected_subtypes, $o );
					$groups [$o] = array ();
				}
			}
			$groups = $util->group_subtypes ( $type, $all, $context, $selected_subtypes, $groups, $cache );
			if (! $all) {
				// elements to view publications by subtypes
				$groups = $util->view_subtypes ( $groups, $type, $context );
			}
			$attribute = $util->group_attributes ( $description, $date, $show_author, $max_results, $context, $maxlenght );
			$util->render ( $type, $all, $groups, $attribute );
		}
	}
}
