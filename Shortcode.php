<?php
define('SHORTCODE', 'get_publications');

function get_shortcode(){
    return SHORTCODE;
}

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
require_once 'util/ShortcodeFilter.php';
require_once 'util/ShortcodeValidation.php';
class Shortcode {
        protected $filter;
        protected $validation;
        protected $util;
        public function Shortcode(){
            $this->filter = new ShortcodeFilter();
            $this->validation = new ShortcodeValidation();
            $this->util = new Query();
        }   
        
	function plugin_sedici($atts) {
            $instance = shortcode_atts ($this->filter->default_shortcode (), $atts );
            $handle = $instance ['handle'] ;
            $author = $instance ['author']; 
            $keywords = $instance ['keywords'];
            if ($this->validation->labelValidation($author,$handle,$keywords)){
                    $subtypes="";
                    $description = $instance ['description'] === 'true' ? "description" : false;
                    $date = ($instance ['date'] === 'true');
                    $show_author = ($instance ['show_author'] === 'true');
                    $cache = $instance ['cache'];//default value from filer.php
                    $max_results = $this->validation->maxResults($instance ['max_results']);
                    $maxlenght = $this->validation->maxLenght($instance ['max_lenght']);
                    $all = $this->filter->selectedSubtypes($instance, $subtypes);
                    $show_subtypes=($instance ['show_subtype'] === 'true');
                    $share=($instance ['share'] === 'true');
                    $cmp=$this->validation->getOrder($instance ['group_subtype'],$instance ['group_date']);
                    $this->util->setCmp($cmp);
                    $attributes = $this->util->group_attributes ( $description, $date, $show_author, $maxlenght, $show_subtypes,$share);
                    $queryStandar = $this->util->standarQuery($handle, $author, $keywords,$max_results);
                    $results= $this->util->getPublications($all, $queryStandar, $cache, $subtypes );
                    $this->util->render ($results,$attributes,$cmp);   
            }
        }    
}
