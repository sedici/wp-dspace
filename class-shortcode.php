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
        protected $view;
        protected $configuration;
        public function __construct(){
            $this->view = new \Wp_dspace\View\View();
            $this->filter = new Util\ShortcodeFilter();
            $this->validation = new Util\ShortcodeValidation();
        }   
    
        
    /**
     * Procesa un shortcode para obtener y mostrar publicaciones obtenidas de algun repositorio.
     *
     * Este método valida los parámetros del shortcode, configura las opciones del plugin,
     * construye la consulta correspondiente y obtiene los resultados.
     * Si se encuentran resultados, los renderiza; de lo contrario, muestra un mensaje de error.
     *
     *  @param array $atts Atributos del shortcode proporcionados por el usuario.
     * @return void
     */
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
                    $queryMethod = $this->configuration->get_query_method();
                    
                    if ($queryMethod == "api"){
                        $this->util = new Util\Query\apiQuery();
                    }
                    else{
                        $this->util = new Util\Query\opensearchQuery();
                    }
        
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
                        $show_videos= ($instance ['show_videos'] === 'true');
                        $show_subtypes = ($instance ['show_subtype'] === 'true');
                        $show_subtypes= $this->configuration->is_label_true($show_subtypes);
                        $share=($instance ['share'] === 'true');
                        $group_subtype = ($instance ['group_subtype'] === 'true');
                        $group_subtype = $this->configuration->is_label_true( $group_subtype);
                        $cmp=$this->validation->getOrder($group_subtype,$instance ['group_date']);
                        $this->util->setCmp($cmp);
                        $attributes = $this->util->group_attributes ( $description, $date, $show_author, $maxlenght, $show_subtypes,$share, $show_videos, $max_results);
                        $queryStandar = $this->util->buildQuery($handle, $author, $keywords, $subject,$degree,$max_results,$this->configuration);
                        $results= $this->util->getPublications($all, $queryStandar, $cache, $subtypes, $max_results );
                        if (!empty($results)){
                             echo $this->view->render ($results,$attributes,$cmp,$this->configuration); 
                        }

                        else{
                                echo "<p> <strong>No se encontraron resultados.</strong></p>";
                        }
                        }
            }
        }    
}