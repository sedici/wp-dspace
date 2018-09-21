<?php
namespace Wp_dspace;
 include_once('configuration/config.php');
class Dspace_Widget extends \WP_Widget {
	protected $filter;
	protected $util;
    protected $validation;
    protected $showShortcode;
    protected $configuration;
	function __construct(){
        $this->filter = new Util\WidgetFilter();
		$this->util = new Util\Query();
        $this->validation = new Util\WidgetValidation();
        $this->showShortcode = new View\ShowShortcode();
		$option = array ('description' => 'Allows to displace contents from DSpace repositories in Wordpress sites by using OpenSearch protocol');
		parent::__construct( 'Dspace', 'Dspace Plugin', $option );
	}

	/** 
	 * @overrides
	 * Executes when the widget is displayed
	 *
	 * FIXME : OMG such a long method
	 */
	public function widget($args, $instance) {
//		var_dump($this);die;
		extract ( $args );
		$handle = apply_filters ( 'handle', $instance ['handle'] );
        $author = apply_filters ( 'author', $instance ['author'] );
        $keywords = apply_filters ( 'keywords', $instance['keywords'] );

        if($this->validation->labelValidation($author,$handle,$keywords)){

            $config = $instance ['config'];
            $this->configuration = $this->validation->create_configuration($config);
            $description = ('on' == $instance ['description']);
            $description = $this->configuration->is_description($description);
            $description = $this->validation->description($description, $instance ['summary']);
			$maxlenght = $this->validation->limit_text($instance ['limit'],$instance ['maxlenght']);
            $share = ('on' == $instance ['share']);
			$show_author = ('on' == $instance ['show_author']); // $show_author: if ON, then $show_author = true
            $date = ('on' == $instance ['date']); // $date: if checkbox date is ON, $date=true
            $max_results = apply_filters ( 'max_results', $instance ['max_results'] ); //$max_results: total results of subtype
            $cache = apply_filters ( 'cache', $instance ['cache'] );  //$cache: duration in seconds of cache
            $show_subtypes = ('on' == $instance ['show_subtype']); //$show_subtypes: if checkbox show_subtype is ON, $show_subtypes=true
            $show_subtypes= $this->configuration->is_label_true($show_subtypes);
            $all = ('on' == $instance ['all']); //$all: all publications without subtype filter
            $all = $this->configuration->instance_all($all);
            if ($this->configuration->all_documents()){
                $subtypes_selected = $this->filter->selectedSubtypes($instance,$all); //$subtypes: all selected documents subtypes
            }
            $attributes = $this->util->group_attributes ( $description, $date, $show_author, $maxlenght, $show_subtypes,$share);
            $queryStandar = $this->util->standarQuery($handle, $author, $keywords,$max_results,  $this->configuration);
            $group_subtype = ($instance ['group_subtype'] === 'on');
            $group_subtype = $this->configuration->is_label_true( $group_subtype);
            $cmp=$this->validation->getOrder($group_subtype,$instance ['group_year']);
            $this->util->setCmp($cmp);
            $results= $this->util->getPublications($all, $queryStandar, $cache, $subtypes_selected );
            echo $this->util->render ($results,$attributes, $cmp,  $this->configuration);
		}
    }


     function sanitizar($key,$instance) {
        $instance[$key] = sanitize_text_field($instance[$key]);
     }
	/**
	 * @see WP_Widget::update
	 */
	function update($new_instance, $old_instance) {
		$subtypes = $this->filter->subtypes();
		$instance = $old_instance;
       foreach ($new_instance as $key => $value) {
            $new_instance[$key]= sanitize_text_field($new_instance[$key]);
        }
        foreach ( $this->filter->subtypes() as $s) {
            $new_instance [$s] = sanitize_text_field ( $new_instance [$s] );
        }
        /*$new_instance ['config'] = sanitize_text_field ( $new_instance ['config'] );
		$new_instance ['handle'] = sanitize_text_field ( $new_instance ['handle'] );
        $new_instance ['author'] = sanitize_text_field ( $new_instance ['author'] );
        $new_instance ['keywords'] = sanitize_text_field ( $new_instance ['keywords'] );
        $new_instance ['share'] = sanitize_text_field ( $new_instance ['share'] );*/
		/*$instance ['maxlenght'] = sanitize_text_field ( $new_instance ['maxlenght'] );
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
        $instance ['show_subtype'] = sanitize_text_field ( $new_instance ['show_subtype'] ); */
		return $new_instance;
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
            <?php
                echo  checked($instance, 'on');
            ?>
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
        <div class="conditional_config"
            <?php echo selected($instance['config'], 'conicet') ? 'style="display: none;"' : ''; ?>>
        <p>
            <?php $this->show_checkbox($instance['group_subtype'], 'Agrupar por subtipos de documentos', 'group_subtype'); ?>
        </p>
        </div>
        <p>
            <?php $this->show_checkbox($instance['show_author'], 'Mostrar Autores', 'show_author')?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['share'], 'Compartir', 'share')?>
        </p>
        <p>
            <?php $this->show_checkbox($instance['date'], 'Mostrar Fecha', 'date') ?>
        </p>
        <div class="conditional_config"
            <?php echo selected($instance['config'], 'conicet') ? 'style="display: none;"' : ''; ?>>
        <p>
            <?php $this->show_checkbox($instance['show_subtype'], 'Mostrar el tipo de documento', 'show_subtype'); ?>
        </p>
        </div>
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
        <div class="conditional_config"
            <?php echo selected($instance['config'], 'conicet') ? 'style="display: none;"' : ''; ?>>
            <p class="conditionally-description"
            <?php echo checked($instance['description'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
            <?php $this->show_checkbox($instance['summary'], 'Mostrar Sumario', 'summary') ?>
            </p>
        </div>
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
        <div class="conditional_config"
            <?php echo selected($instance['config'], 'conicet') ? 'style="display: none;"' : ''; ?>>
        <p class="show-filter">
                <?php $this->show_checkbox($instance['all'], 'Todas las publicaciones sin filtros', 'all'); ?>
        </p>
            <hr>
            <hr>
        <p class="conditionally-filter"
            <?php echo checked($instance['all'], 'on') !== '' ? 'style="display: none;"' : ''; ?>>
            <?php
		$subtypes = $this->filter->subtypes();
		foreach ( $subtypes as $subtype ) {
                    $this->show_checkbox($instance[$subtype], $subtype, $subtype);
			?>
                    <br />
                <?php
		}//end foreach subtypes
        ?></p>
        </div>
        <?php
            return;
        }
        function show_configs($config){
            if (empty($config)) { $config = default_repository();}
        ?>
        <div class="config">
        <label id="origen" for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Configuración'); ?>
            <select autocomplete="off" class='widefat'
		id="<?php echo $this->get_field_id('config'); ?>"
		name="<?php echo $this->get_field_name('config'); ?>" type="text">
		<?php
		$directorio = get_configuration_directory();
		foreach (glob($directorio."*.ini") as $value) {
                    $ini_array = parseFile($value);
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
        </div>
        <?php
            return;
        }

        /**
	 * @see WP_Widget::form
	 */
	function form($instance) {

        if (empty($instance))
        {
            $instance = array('all'=>'on');
        }

                $this->showShortcode->show_shortcode($instance);
                $this->show_configs($instance['config']);
                $this->show_options($instance);
                $this->show_description($instance);
                $this->show_cache(esc_attr ( $instance ['cache'] ));
		$this->show_totalResults(esc_attr ( $instance ['max_results'] ));
		$this->show_subtypes($instance);
	}

}//end class
