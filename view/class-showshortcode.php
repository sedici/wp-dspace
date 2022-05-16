<?php
namespace Wp_dspace\View;
define('SHORTCODE', 'get_publications');


class ShowShortcode {
    protected $filter;
    public function __construct(){
        $this->filter = new \Wp_dspace\Util\WidgetFilter();
    }
    
    public function get_label($label,$value){
        if(!empty($value)){
            $text = $label.'="'.$value.'" ';
            return $text;
        }
        return;
    }

    public function is_on($label,$value){
        if('on' == $value){
            $text = $label.'=true ';
            return $text;
        }
        return;
    }
    public function get_shortcode(){
        return SHORTCODE;
    }
    public function show_thesis($instance){
            $show_thesis=false;
            $thesis = $this->filter->vectorTesis();
            foreach ($thesis as $t){    
                if ('on' == $instance [$t]) {
                    $show_thesis = true;
                }
            }
            if ($show_thesis) {
                return "thesis=true ";
            }
        }
    public function show_label($instance){
        $shortcode_aux="";
        $shortcode_aux= $shortcode_aux . $this->get_label('config', $instance['config']);
        $shortcode_aux= $shortcode_aux . $this->get_label('handle', $instance['handle']);
        $shortcode_aux= $shortcode_aux . $this->get_label('author', $instance['author']);
        $shortcode_aux= $shortcode_aux . $this->get_label('keywords', $instance['keywords']);
        $shortcode_aux= $shortcode_aux . $this->get_label('subject', $instance['subject']);
        $shortcode_aux= $shortcode_aux . $this->get_label('max_results', $instance['max_results']);
        if ('on' == $instance ['limit']){
            $shortcode_aux= $shortcode_aux . $this->get_label('max_lenght', $instance['maxlenght']);
        }
        return $shortcode_aux;
    }
    public function show_subtypes($instance){
        if (!('on' == $instance ['all'])){
            $shortcode_aux="";
            $subtypes = $this->filter->vectorSubtypes();
            foreach ($subtypes as $key => $subtype){    
                $shortcode_aux= $shortcode_aux . $this->is_on($key, $instance[$subtype]);
            }
            $shortcode_aux= $shortcode_aux . $this->show_thesis($instance);
        }
        return $shortcode_aux;        
    }
    public function show_checkbox($instance){
        $shortcode_aux="";
        $shortcode_aux= $shortcode_aux . $this->is_on('share', $instance['share']);
        $shortcode_aux= $shortcode_aux . $this->is_on('show_subtype', $instance['show_subtype']);
        $shortcode_aux= $shortcode_aux . $this->is_on('group_subtype', $instance['group_subtype']);
        $shortcode_aux= $shortcode_aux . $this->is_on('group_date', $instance['group_year']);
        $shortcode_aux= $shortcode_aux . $this->is_on('description', $instance['description']);
        $shortcode_aux= $shortcode_aux . $this->is_on('date', $instance['date']);
        $shortcode_aux= $shortcode_aux . $this->is_on('show_author', $instance['show_author']);
        return $shortcode_aux;
    }    
    public function search_Widget_Number($form_array){
        $aux_find="";
        $aux_num=0;
        foreach ( $form_array as $element ) {
            if ( $element['name'] == "widget_number"){
               $aux_num = $element['value'];
               $aux_find="widget-dspace[" . $aux_num . "][config]";
            }
            if ($aux_find == $element['name']){
                return $aux_num;
            }
        }
        return;
    }
    
    public function get_Elements($name,$form_array){
       foreach ( $form_array as $element ) {
            if ( $name == $element['name']){
                return $element['value'];
            }
        }
        return false;
    }
    public function is_conicet(&$instance){
        if (strcmp($instance["config"], "conicet") == 0) {
            $instance['all'] = 'on';
            $instance['show_subtype'] = 'off';
            $instance['group_subtype']= 'off';        
        }
    }

    private function buildSearchString($numeroDeWidget,$name) {
        return "widget-dspace[" .$numeroDeWidget. "][$name]";
    } 

    // La funcion usa 2 parametros:
    // $form_array -> Parámetro utilizado en  Jquery, es un arreglo serializado que debe transformarse en una instancia ($instance).
    // $instance es la instancia con la que se construye el shortcode, al invocarse desde PHP se envia pero desde jquery no, por eso se inicializa en null.
    public function show_shortcode($form_array, $instance = null){
        if ($instance == null) { 
            $instance = array();
          //Aca convierto el array de objetos en un $instance que acepte la función
          $numeroDeWidget = $this->search_Widget_Number($form_array);
          $keywords = ['config','handle','author','keywords','description','subject','max_results','group_year','show_author','maxlenght','cache','max_results','all'];
          foreach($keywords as $keyword){
              $instance[$keyword] = $this->get_Elements( $this->buildSearchString($numeroDeWidget,$keyword)  ,$form_array);
          }
        }
        $this->is_conicet($instance);
        $shortcode= "[".$this->get_shortcode()." ";
                $shortcode= $shortcode . $this->show_label($instance);
                $shortcode= $shortcode .$this->show_subtypes($instance);
                $shortcode= $shortcode . $this->show_checkbox($instance);
                $shortcode= $shortcode . "]";
        return $shortcode;
        }
    
} ?>