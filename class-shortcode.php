<?php
namespace Wp_dspace;


/*
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
}*/
require_once 'util/class-shortcodefilter.php';
require_once 'util/class-showshortcodevalidation.php';
class Shortcode {
        protected $filter;
        protected $validation;
        protected $util;
        protected $configuration;
        public function __construct(){
            $this->filter = new Util\ShortcodeFilter();
            $this->validation = new Util\ShortcodeValidation();
            $this->util = new Util\Query();
        }   
        
	function plugin_sedici($atts) {
            $instance = shortcode_atts ($this->filter->default_shortcode (), $atts );
            $handle = $instance ['handle'] ;
            $author = $instance ['author']; 
            $keywords = $instance ['keywords'];
            $subject = $instance ['subject'];
            $degree = $instance ['degree'];
            if ($this->validation->labelValidation($author,$handle,$keywords,$subject)){
                    $subtypes="";
                    $config  = $instance ['config'];
                    $this->configuration = $this->validation->create_configuration($config);
                    if (!is_null($this->configuration)){ 
                        $description = ($instance ['description'] === 'true');
                        $description = $this->configuration->is_description($description);
                        $date = ($instance ['date'] === 'true');
                        $show_author = ($instance ['show_author'] === 'true');
                        $cache = $instance ['cache'];//default value from filter.php
                        $max_results = $this->validation->maxResults($instance ['max_results']);
                        $maxlenght = $this->validation->maxLenght($instance ['max_lenght']);
                        $all = $instance ['all'];
                        $all = $this->configuration->instance_all($all);
                        if ($this->configuration->all_documents()){
                            $all = $this->filter->selectedSubtypes($instance, $subtypes);
                        }
                        $show_subtypes = ($instance ['show_subtype'] === 'true');
                        $show_subtypes= $this->configuration->is_label_true($show_subtypes);
                        $share=($instance ['share'] === 'true');
                        $group_subtype = ($instance ['group_subtype'] === 'true');
                        $group_subtype = $this->configuration->is_label_true( $group_subtype);
                        $cmp=$this->validation->getOrder($group_subtype,$instance ['group_date']);
                        $this->util->setCmp($cmp);
                        $attributes = $this->util->group_attributes ( $description, $date, $show_author, $maxlenght, $show_subtypes,$share);
                        $queryStandar = $this->util->standarQuery($handle, $author, $keywords, $subject,$degree,$max_results,$this->configuration);
                        $results= $this->util->getPublications($all, $queryStandar, $cache, $subtypes );
                        if (!empty($results))
                                echo $this->util->render ($results,$attributes,$cmp,$this->configuration); 
                        else
                                echo "<p> <strong>No se encontraron resultados.</strong></p>";
                    }
            }
        }    
}