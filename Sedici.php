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
?>
<?php
include_once 'util/config.php';
require_once 'Shortcode.php';
require_once 'util/Filter.php';
require_once 'util/Query.php';
require_once 'model/SimplepieModel.php';

function my_styles() {
	//include the style
	wp_register_style ( 'Sedici', plugins_url ( 'wp-dspace/css/styles.css' ) );
	wp_enqueue_style ( 'Sedici' );
}
function my_scripts_method() {
	// include js archives
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'sedici', plugins_url ( 'js/scrips.js', __FILE__ ), array ("jquery"), null, true );
	wp_enqueue_script ( 'sedici' );
}

class Sedici extends WP_Widget {
	protected $filter;
	protected $util;

	function Sedici() {
		$this->filter = new Filter();
		$this->util = new Query();
		$option = array ('description' => 'This plugin connects the repository SEDICI in wordpress');
		parent::WP_Widget ( 'Sedici', 'Plugin Sedici', $option );
	}

        public function description($description,$summary){
            If ('on' == $description) {
		if ('on' ==$summary ) {
                    return "summary"; 
                    // checkbox description and summary ON
                } else { return "description"; } // checkbox description ON, summary OFF
            } else { return false;}
        }
        
        public function limit_text($limit,$max){
            if ('on' == $limit){
                if ( (empty($max)) || ($max < min_results())){
                    $max =  show_text(); //default lenght
                }
            } else { $max = null; }
            return $max;
        }
        
        
        function selectedSubtypes ($instance,$all) {
            //this function returns all active subtypes
            if (!$all){
             $groups = array ();
             $subtypes = $this->filter->subtypes();
			// $subtypes: all names of subtypes
			foreach ($subtypes as $type){
				// compares the user marked subtypes, if TRUE, save the subtype.
				 if ('on' == $instance [$type]) {
                                    array_push($groups, $type);
				}
			}
                return $groups;
              }
            return;    
        }

	function widget($args, $instance) {
		extract ( $args );
		$handle = apply_filters ( 'handle', $instance ['handle'] );
                $author = apply_filters ( 'author', $instance ['author'] ); 
                $keywords = apply_filters ( 'keywords', $instance ['keywords'] ); 	
                if($this->util->validete($author,$handle,$keywords)){
                        $description = $this->description($instance ['description'], $instance ['summary']);
			$maxlenght = $this->limit_text($instance ['limit'],$instance ['maxlenght']);
			$show_author = ('on' == $instance ['show_author']);
			// $show_author: if ON, then $show_author = true
                        $date = ('on' == $instance ['date']);
			// $date: if checkbox date is ON, $date=true
                        $max_results = apply_filters ( 'max_results', $instance ['max_results'] );
			//$max_results: total results of subtype
                        $cache = apply_filters ( 'cache', $instance ['cache'] ); 
			//$cache: duration in seconds of cache
                        $show_subtypes = ('on' == $instance ['show_subtype']);
                        //$show_subtypes: if checkbox show_subtype is ON, $show_subtypes=true
                        $all = ('on' == $instance ['all']);
			//$all: all publications without subtype filter
                        $group_year= ('on' == $instance ['group_year']);
                        $group_subtype = ('on' == $instance ['group_subtype']);
                        
                        $subtypes_selected = $this->selectedSubtypes($instance,$all);
                        //$subtypes: all selected documents subtypes
                        
                        $attributes = $this->util->group_attributes ( $description, $date, $show_author, $maxlenght, $show_subtypes);
                        $queryStandar = $this->util->standarQuery($handle, $author, $keywords,$max_results);
                        
                        $results= $this->util->getPublications($all, $queryStandar, $cache, $subtypes_selected ,$group_subtype,$group_year);
                        $this->util->render ($results,$attributes,$group_subtype,$group_year);        
		} 
        }   

	/**
	 * @see WP_Widget::update
	 */
	function update($new_instance, $old_instance) {
		$subtypes = $this->filter->subtypes();
		$instance = $old_instance;
		$instance ['handle'] = sanitize_text_field ( $new_instance ['handle'] );
                $instance ['author'] = sanitize_text_field ( $new_instance ['author'] );
                $instance ['keywords'] = sanitize_text_field ( $new_instance ['keywords'] );
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
	
	/**
	 * @see WP_Widget::form
	 */
        function show_imput ($type,$text,$id,$placeholder=""){
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
         
        function show_thesis($instance){
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
        
        function show_shortcode($instance){
        ?>    
            <hr>
            El shortcode de la configuración guardada es:
            <?php 
                echo "[".get_shortcode()." ";
                echo get_label('handle', $instance['handle']);
                echo get_label('author', $instance['author']);
                echo get_label('free', $instance['keywords']);
                $all = ('on' == $instance ['all']);
                if (!$all){
                    $subtypes = $this->filter->vectorSubtypes ();
                    foreach ($subtypes as $key => $subtype){    
                        echo is_on($key, $instance[$subtype]);
                    }
                    $this->show_thesis($instance);
                }
                echo get_label('max_results', $instance['max_results']);
                $limit = ('on' == $instance ['limit']);
                if ($limit){
                    echo get_label('max_lenght', $instance['maxlenght']);
                }
                echo is_on('show_subtype', $instance['show_subtype']);
                echo is_on('group_subtype', $instance['group_subtype']);
                echo is_on('group_date', $instance['group_year']);
                echo is_on('description', $instance['description']);
                echo is_on('date', $instance['date']);
                echo is_on('show_author', $instance['show_author']);
                echo " ]";
            ?>
            <hr>
        <?php    
        return;
        }
        
	function form($instance) {
		$max_results = esc_attr ( $instance ['max_results'] );
		$handle = esc_attr ( $instance ['handle'] );
                $author = esc_attr ( $instance ['author'] );
                $keywords = esc_attr ( $instance ['keywords'] );
		$maxlenght = esc_attr($instance['maxlenght']);
                $this->show_shortcode($instance);
                $this->show_imput($handle, 'Handle:', 'handle', 'Ejemplo: 10915/25293');
                $this->show_imput($author, 'Autores:', 'author','Apellidos, Nombres como en SEDICI');
                $this->show_imput($keywords, 'Palabras claves:', 'keywords','Palabra1; Palabra2; etc');
                
		?>
<p>
    <?php $this->show_checkbox($instance['show_author'], 'Mostrar Autores', 'show_author')?>
</p>

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


<p class="description-ds">
    <?php $this->show_checkbox($instance['description'], 'Mostrar Resumen', 'description') ?>
</p>

<p class="conditionally-description"
<?php echo checked($instance['description'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
     <?php $this->show_checkbox($instance['summary'], 'Mostrar Sumario', 'summary') ?>
</p>

<p>
     <?php $this->show_checkbox($instance['date'], 'Mostrar Fecha', 'date') ?>
</p>
	
<?php $duration = esc_attr ( $instance ['cache'] );
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

<p>
    <?php $this->show_checkbox($instance['show_subtype'], 'Mostrar el tipo de documento', 'show_subtype'); ?>
</p>
<p>
    <?php $this->show_checkbox($instance['group_year'], 'Agrupar por fecha', 'group_year'); ?>
</p>
<p>
    <?php $this->show_checkbox($instance['group_subtype'], 'Agrupar por subtipos de documentos', 'group_subtype'); ?>
</p>
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
	}
}
add_action ( 'admin_enqueue_scripts', 'my_scripts_method' );
add_action ( 'admin_enqueue_scripts', 'my_styles' );
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Sedici");' ) );
print_r( add_shortcode ( 'get_publications', 'DspaceShortcode' ));
