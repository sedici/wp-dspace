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
function LoadShortcode($atts, $type) {
	$shortcode = new Shortcode ();
	$shortcode->plugin_sedici ( $atts, $type );
}
function AuthorShortcode($atts) {
	LoadShortcode ( $atts, 'author' );
}
function HandleShortcode($atts) {
	LoadShortcode ( $atts, 'handle' );
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
			$date = $a ['date'] === 'true' ? true : false;
			$show_author = $a ['show_author'] === 'true' ? true : false;
			$cache = $a ['cache'];
			$context = $a ['context'];
			$all = $a ['all'] === 'true' ? true : false;
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
			$thesis = $a ['thesis'] === 'true' ? true : false;
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