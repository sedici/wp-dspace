<?php
/**
 * Plugin Name: Dspace-Plugin
 * Plugin URI: http://sedici.unlp.edu.ar/
 * Description: This plugin connects the repository SEDICI in wordpress, with the purpose of showing the publications of authors or institutions
 * Version: 1.0
 * Author: SEDICI - Paula Salamone Lacunza
 * Author URI: http://sedici.unlp.edu.ar/
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 */
?>
<?php
require_once 'Shortcode.php';
require_once 'Dspace-config.php';
require_once 'configuration/config.php';
require_once 'util/WidgetFilter.php';
require_once 'util/WidgetValidation.php';
require_once 'util/Query.php';
require_once 'util/XmlOrder.php';
require_once 'view/ShowShortcode.php';
require_once 'model/SimplepieModel.php';
require_once 'configuration/Configuration.php';
foreach ( glob ( "configuration/*_config.php" ) as $app ) { 
    require_once $app;
}

function dspace_styles() {
	//include the style
	wp_register_style ( 'Dspace', plugins_url ( 'media/css/styles.css', __FILE__ ));
	wp_enqueue_style ( 'Dspace' );
}

function dspace_scripts_method() {
	// include js archives
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'Dspace', plugins_url ( 'media/js/scrips.js', __FILE__ ), array ("jquery"), null, true );
	wp_enqueue_script ( 'Dspace' );
}

class Dspace extends WP_Widget {
	protected $filter;
	protected $util;
        protected $validation;
        protected $showShortcode;
        protected $configuration;
                
	function Dspace() {
                $this->filter = new WidgetFilter();
		$this->util = new Query();
                $this->validation = new WidgetValidation();
                $this->showShortcode = new ShowShortcode();
		$option = array ('description' => 'This plugin connects the repository SEDICI in wordpress');
		parent::WP_Widget ( 'Dspace', 'Dspace Plugin', $option );
	}

	function widget($args, $instance) {
		extract ( $args );
		$handle = apply_filters ( 'handle', $instance ['handle'] );
                $author = apply_filters ( 'author', $instance ['author'] ); 
                $keywords = apply_filters ( 'keywords', $instance ['keywords'] ); 	
                if($this->validation->labelValidation($author,$handle,$keywords)){
                        $config = $instance ['config'];
                        $this->configuration = $this->validation->create_configuration($config);
                        $description = $this->validation->description($instance ['description'], $instance ['summary']);
			$maxlenght = $this->validation->limit_text($instance ['limit'],$instance ['maxlenght']);
                        $share = ('on' == $instance ['share']);
			$show_author = ('on' == $instance ['show_author']); // $show_author: if ON, then $show_author = true
                        $date = ('on' == $instance ['date']); // $date: if checkbox date is ON, $date=true
                        $max_results = apply_filters ( 'max_results', $instance ['max_results'] ); //$max_results: total results of subtype
                        $cache = apply_filters ( 'cache', $instance ['cache'] );  //$cache: duration in seconds of cache
                        $show_subtypes = ('on' == $instance ['show_subtype']); //$show_subtypes: if checkbox show_subtype is ON, $show_subtypes=true
                        $all = ('on' == $instance ['all']); //$all: all publications without subtype filter
                        $subtypes_selected = $this->filter->selectedSubtypes($instance,$all); //$subtypes: all selected documents subtypes
                        $attributes = $this->util->group_attributes ( $description, $date, $show_author, $maxlenght, $show_subtypes,$share);
                        $queryStandar = $this->util->standarQuery($handle, $author, $keywords,$max_results,  $this->configuration);
                        $cmp=$this->validation->getOrder($instance ['group_subtype'],$instance ['group_year']);
                        $this->util->setCmp($cmp);
                        $results= $this->util->getPublications($all, $queryStandar, $cache, $subtypes_selected );
                        $this->util->render ($results,$attributes, $cmp,  $this->configuration);        
		} 
        }   

	/**
	 * @see WP_Widget::update
	 */
	function update($new_instance, $old_instance) {
		$subtypes = $this->filter->subtypes();
		$instance = $old_instance;
                $instance ['config'] = sanitize_text_field ( $new_instance ['config'] );
		$instance ['handle'] = sanitize_text_field ( $new_instance ['handle'] );
                $instance ['author'] = sanitize_text_field ( $new_instance ['author'] );
                $instance ['keywords'] = sanitize_text_field ( $new_instance ['keywords'] );
                $instance ['share'] = sanitize_text_field ( $new_instance ['share'] );
		$instance ['maxlenght'] = sanitize_text_field ( $new_instance ['maxlenght'] );
		$instance ['description'] = sanitize_text_field ( $new_instance ['description'] );
		$instance ['summary'] = sanitize_text_field ( $new_instance ['summary'] );
		$instance ['date'] = sanitize_text_field ( $new_instance ['date'] );
		$instance ['show_author'] = sanitize_text_field ( $new_instance ['show_author'] );
		$instance ['max_results'] = sanitize_text_field ( $new_instance ['max_results'] );
		$instance ['cache'] = sanitize_text_field ( $new_instance ['cache'] );
		$instance ['all'] = sanitize_text_field ( $new_instance ['all'] );
		$instance ['limit'] = sanitize_text_field ( $new_instance ['limit'] );
                $instance ['group_subtype'] = sanitize_text_field ( $new_instance ['group_subtype'] );
                $instance ['group_year'] = sanitize_text_field ( $new_instance ['group_year'] );
                $instance ['show_subtype'] = sanitize_text_field ( $new_instance ['show_subtype'] );
		foreach ( $subtypes as $s) {
			$instance [$s] = sanitize_text_field ( $new_instance [$s] );
		}
		return $instance;
	}

    function show_input ($type,$text,$id,$placeholder=""){
        ?>    
        <p>
        <label for="<?php echo $this->get_field_id($id); ?>"><?php _e($text); ?> 
        <input class="widefat"
                placeholder="<?php echo $placeholder; ?>"
		id="<?php echo $this->get_field_id($id); ?>"
		name="<?php echo $this->get_field_name($id); ?>" type="text"
		value="<?php echo $type; ?>" /></label>
        </p>
        <?php
        }
        function show_checkbox($instance,$text,$id){
        ?>    
            <input class="checkbox" type="checkbox"
            <?php checked($instance, 'on'); ?>
            id="<?php echo $this->get_field_id($id); ?>"
            name="<?php echo $this->get_field_name($id); ?>" /> 
            <label for="<?php echo $this->get_field_id($id); ?>"><?php _e($text); ?></label>
        <?php
        }
        
        public function show_cache($duration){ 
            if (empty($duration)) { $duration = defaultCache();}
            ?>
        <p>
	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Duración de la cache:'); ?> 
            <select class='widefat'
		id="<?php echo $this->get_field_id('cache'); ?>"
		name="<?php echo $this->get_field_name('cache'); ?>">
		<?php
		$one_day= one_day();
		$all_days = cache_days();
		foreach ($all_days as $day){
			?>
			<option value=<?php echo $day * $one_day;?>
			<?php echo ($duration==($day * $one_day))?'selected':''; ?>>
                        <?php echo $day;?> <?php _e('días'); ?></option>
		<?php } //end foreach?>
            </select>
	</label>
        </p>  
        <?php
            return;
        }
        
        public function show_options($instance){
            $handle = esc_attr ( $instance ['handle'] );
            $author = esc_attr ( $instance ['author'] );
            $keywords = esc_attr ( $instance ['keywords'] );
            $this->show_input($handle, 'Handle:', 'handle', 'Ejemplo: 10915/25293');
            $this->show_input($author, 'Autores:', 'author','Apellidos, Nombres como en SEDICI');
            $this->show_input($keywords, 'Palabras claves:', 'keywords','Palabra1; Palabra2; etc');
            ?>
        <p>
             <?php $this->show_checkbox($instance['group_year'], 'Agrupar por fecha', 'group_year'); ?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['group_subtype'], 'Agrupar por subtipos de documentos', 'group_subtype'); ?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['show_author'], 'Mostrar Autores', 'show_author')?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['share'], 'Compartir', 'share')?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['date'], 'Mostrar Fecha', 'date') ?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['show_subtype'], 'Mostrar el tipo de documento', 'show_subtype'); ?>
        </p>
        <?php
            return;
        }
        
        public function show_description($instance){ 
            $maxlenght = esc_attr($instance['maxlenght']);
            ?>
            <div class="description-ds">
            <p class="description-ds">
                <?php $this->show_checkbox($instance['description'], 'Mostrar Resumen', 'description') ?>
            </p>
            </div>
            <p class="conditionally-description"
            <?php echo checked($instance['description'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
            <?php $this->show_checkbox($instance['summary'], 'Mostrar Sumario', 'summary') ?>
            </p>
            
        <div class="conditionally-description"
            <?php echo checked($instance['description'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
            <p class="limit">
                <?php $this->show_checkbox($instance['limit'], 'Limitar longitud del texto', 'limit') ?>
            </p>
        <p class="conditionally-limit"
        <?php echo checked($instance['limit'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
            <label for="<?php echo $this->get_field_id('maxlenght'); ?>"><?php _e('Longitud del texto en caracteres:'); ?> 
            <input class="widefat" type="number" onKeyPress="return justNumbers(event);"
                min="10"
		id="<?php echo $this->get_field_id('maxlenght'); ?>"
		name="<?php echo $this->get_field_name('maxlenght'); ?>" 
		value="<?php echo $maxlenght; ?>" /></label>
        </p>
        </div>
        <?php
            return;
        }
        
        public function show_totalResults($max_results){ ?>
        <p>
        <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Cantidad de Resultados a mostrar'); ?> 
            <select class='widefat'
		id="<?php echo $this->get_field_id('max_results'); ?>"
		name="<?php echo $this->get_field_name('max_results'); ?>" type="text">
		<?php
		$results = total_results();
		foreach ( $results as $result ) {
			?>
			<option value=<?php echo $result;?>
				<?php echo ($max_results==$result)?'selected':''; ?>>
				<?php echo $result; ?>
			</option>
		<?php
		}// end for	
		?>
            </select>
        </label>
        </p>
        <?php
           return;
        }
        
        public function show_subtypes($instance){?>
            <p class="show-filter">
                <?php $this->show_checkbox($instance['all'], 'Todas las publicaciones sin filtros', 'all'); ?>
            </p>
            <hr>
            <hr>
        <p class="conditionally-filter"
            <?php echo checked($instance['all'], 'on') === '' ? '' : 'style="display: none;"'; ?>>
            <?php
		$subtypes = $this->filter->subtypes();
		foreach ( $subtypes as $subtype ) {
                    $this->show_checkbox($instance[$subtype], $subtype, $subtype);
			?>
                    <br />
                <?php
		}//end foreach subtypes
        ?></p>
        <?php
            return;
        }
        
        function show_configs($config){
            if (empty($config)) { $config = default_repository();}
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Configuración'); ?> 
            <select class='widefat'
		id="<?php echo $this->get_field_id('config'); ?>"
		name="<?php echo $this->get_field_name('config'); ?>" type="text">
		<?php
		$directorio =  WP_CONTENT_DIR."/plugins/wp-dspace/config-files/";
		foreach (glob($directorio."*.ini") as $value) {
                    $ini_array = parse_ini_file($value);
                    ?>
                    <option value=<?php echo $ini_array['name'];?>
                    <?php echo (strcmp($ini_array['name'], $config) == 0)?'selected':''; ?>>
				<?php echo $ini_array['name']; ?>
                    </option>
		<?php
		}// end for	
		?>
            </select>
        </label>
        </p>  
        <?php
            return;
        }
        
        /**
	 * @see WP_Widget::form
	 */         
	function form($instance) {
                $this->showShortcode->show_shortcode($instance);
                $this->show_configs($instance['config']);
                $this->show_options($instance);
                $this->show_description($instance);
                $this->show_cache(esc_attr ( $instance ['cache'] )); 
		$this->show_totalResults(esc_attr ( $instance ['max_results'] ));
		$this->show_subtypes($instance);
	}
        
}//end class
add_action ( 'admin_enqueue_scripts', 'dspace_scripts_method' );
add_action ( 'admin_enqueue_scripts', 'dspace_styles' );
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Dspace");' ) );
add_action( 'admin_menu', 'dspace_config' );
print_r( add_shortcode ( 'get_publications', 'DspaceShortcode' ));
