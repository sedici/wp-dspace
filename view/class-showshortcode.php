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
                echo "thesis=true ";
            }
        }
    public function show_label($instance){
        echo $this->get_label('config', $instance['config']);
        echo $this->get_label('handle', $instance['handle']);
        echo $this->get_label('author', $instance['author']);
        echo $this->get_label('keywords', $instance['keywords']);
        echo $this->get_label('max_results', $instance['max_results']);
        if ('on' == $instance ['limit']){
            echo $this->get_label('max_lenght', $instance['maxlenght']);
        }
        return;
    }
    public function show_subtypes($instance){
        if (!('on' == $instance ['all'])){
            $subtypes = $this->filter->vectorSubtypes();
            foreach ($subtypes as $key => $subtype){    
                echo $this->is_on($key, $instance[$subtype]);
            }
            $this->show_thesis($instance);
        }
        return;        
    }
    public function show_checkbox($instance){
        echo $this->is_on('share', $instance['share']);
        echo $this->is_on('show_subtype', $instance['show_subtype']);
        echo $this->is_on('group_subtype', $instance['group_subtype']);
        echo $this->is_on('group_date', $instance['group_year']);
        echo $this->is_on('description', $instance['description']);
        echo $this->is_on('date', $instance['date']);
        echo $this->is_on('show_author', $instance['show_author']);
        return;
    }    
    
    public function is_conicet($instance){
        if (strcmp($instance["config"], "conicet") == 0) {
            $instance['all'] = 'on';
            $instance['show_subtype'] = 'off';
            $instance['group_subtype']= 'off';        
        }
    }
    
    public function show_shortcode($instance){
            $instace_copy = $instance;
        ?>    
            <hr>
            El shortcode de la configuración guardada es:
            <?php 
                $this->is_conicet($instance);
                echo "[".$this->get_shortcode()." ";
                $this->show_label($instance);
                $this->show_subtypes($instance);
                $this->show_checkbox($instance);
                echo " ]";
            ?>
            <hr>
        <?php   
            $instace = $instace_copy;
        return;
        }
    
}