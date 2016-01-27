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
require_once 'Shortcode.php';
require_once 'view/View.php';
require_once 'util/Filter.php';
require_once 'util/Query.php';
require_once 'model/SimplepieModel.php';

function my_styles() {
	//include the style
	wp_register_style ( 'Sedici', plugins_url ( 'Sedici-Plugin/css/styles.css' ) );
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
	
	function widget($args, $instance) {
		extract ( $args );
		$context = apply_filters ( 'context', $instance ['context'] );
                $type = apply_filters ( 'type', $instance ['type'] ); 
			//$type: handle/author/free for the query
		if (($context != "") && ($type!="")) { 
			$cache = apply_filters ( 'cache', $instance ['cache'] ); 
			//$cache: duration in seconds of cache
			$all = ('on' == $instance ['all']);
			//$all: all publications without subtype
			$max_results = apply_filters ( 'max_results', $instance ['max_results'] );
			//$max_results: total results of subtype
			$subtypes = $this->filter->subtypes();
			// $subtypes: all names of subtypes
			$selected_subtypes = array (); 
			// $selected_subtypes: subtypes selected by the user
			$groups = array ();
			// $groups: groups publications by subtype
			foreach ( $subtypes as $o ) {
				//compares the user marked subtypes, if ON, save the subtype.
				if ('on' == $instance [$o]) {
					array_push ( $selected_subtypes, $o );
					$groups [$o] = array ();
				}
			}
			$groups = $this->util->group_subtypes ( $type, $all, $context, $selected_subtypes, $groups, $cache );
			// $vector_group: elements to view for all publications
			if (! $all) {
				//elements to view publications by subtypes
				$groups = $this->util->view_subtypes ( $groups, $type, $context );
			}
			If ('on' == $instance ['description']) {
				if ('on' == $instance ['summary']) {
					$description = "summary"; 
					// checkbox description and summary ON
				} else {
					$description = "description";
					// checkbox description ON, summary OFF
				}
                        } else { $description = false;}
			$date = ('on' == $instance ['date']);
			// $date: if checkbox date is ON, $date=true
			if ('on' == $instance ['limit']){
				//shorten text
				$maxlenght = $instance ['maxlenght'];
				if ($maxlenght == ""){
					$maxlenght = $this->util->max_lenght_text();
					//default lenght
				}
			} else {
				$maxlenght = 0;
			}
			$show_author = ('on' == $instance ['show_author']);
			// $show_author: if ON, then $show_author = true
			$attributes = $this->util->group_attributes ( $description, $date, $show_author, $max_results, $context  , $maxlenght);
			$this->util->render ( $type, $all, $groups, $attributes );
		} else {
			// $context = null
			echo "Ingrese un filtro y un contexto";
		}
	}
	
	/**
	 * @see WP_Widget::update
	 */
	function update($new_instance, $old_instance) {
		$subtypes = $this->filter->subtypes();
		$instance = $old_instance;
		$instance ['context'] = sanitize_text_field ( $new_instance ['context'] );
		$instance ['type'] = sanitize_text_field ( $new_instance ['type'] );
		$instance ['maxlenght'] = sanitize_text_field ( $new_instance ['maxlenght'] );
		$instance ['description'] = sanitize_text_field ( $new_instance ['description'] );
		$instance ['summary'] = sanitize_text_field ( $new_instance ['summary'] );
		$instance ['date'] = sanitize_text_field ( $new_instance ['date'] );
		$instance ['show_author'] = sanitize_text_field ( $new_instance ['show_author'] );
		$instance ['max_results'] = sanitize_text_field ( $new_instance ['max_results'] );
		$instance ['cache'] = sanitize_text_field ( $new_instance ['cache'] );
		$instance ['all'] = sanitize_text_field ( $new_instance ['all'] );
		$instance ['limit'] = sanitize_text_field ( $new_instance ['limit'] );
		foreach ( $subtypes as $s) {
			$instance [$s] = sanitize_text_field ( $new_instance [$s] );
		}
		return $instance;
	}
	
	/**
	 * @see WP_Widget::form
	 */
	function form($instance) {
		$max_results = esc_attr ( $instance ['max_results'] );
		$context = esc_attr ( $instance ['context'] ); 
		$maxlenght = esc_attr($instance['maxlenght']);
		?>

<p class="show-author">
	<input class="checkbox" type="radio"
		<?php checked($instance['type'], 'handle'); ?>
		id="<?php echo $this->get_field_id('type'); ?>"
		name="<?php echo $this->get_field_name('type'); ?>" value="handle" />
	<label for="<?php echo $this->get_field_id('type'); ?>"> <?php _e('Handle'); ?> </label>
	<input class="checkbox" type="radio"
		<?php checked($instance['type'], 'author'); ?>
		id="<?php echo $this->get_field_id('type'); ?>"
		name="<?php echo $this->get_field_name('type'); ?>" value="author" /> <label
		for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Autor'); ?></label>
        <input class="checkbox" type="radio"
		<?php checked($instance['type'], 'free'); ?>
		id="<?php echo $this->get_field_id('type'); ?>"
		name="<?php echo $this->get_field_name('type'); ?>" value="free" /> <label
		for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Busqueda Libre'); ?></label>
</p>

<p class="conditionally-author"
	<?php echo checked($instance['type'], 'author') === '' ? 'style="display: none;"' : ''; ?>>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['show_author'], 'on'); ?>
		id="<?php echo $this->get_field_id('show_author'); ?>"
		name="<?php echo $this->get_field_name('show_author'); ?>" /> 
		<label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Mostrar Autores'); ?></label>
</p>


<p>
	<label for="<?php echo $this->get_field_id('context'); ?>"><?php _e('Contexto:'); ?> 
       <input class="widefat"
		id="<?php echo $this->get_field_id('context'); ?>"
		name="<?php echo $this->get_field_name('context'); ?>" type="text"
		value="<?php echo $context; ?>" /></label>
</p>


<p class="limit">
	<input class="checkbox" type="checkbox"
		<?php checked($instance['limit'], 'on'); ?>
		id="<?php echo $this->get_field_id('limit'); ?>"
		name="<?php echo $this->get_field_name('limit'); ?>" /> <label
		for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limitar longitud del texto'); ?></label>
</p>
<p class="conditionally-limit"
	<?php echo checked($instance['limit'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
	<label for="<?php echo $this->get_field_id('maxlenght'); ?>"><?php _e('Longitud del texto en caracteres:'); ?> 
       <input class="widefat" type="number" onKeyPress="return justNumbers(event);"
		id="<?php echo $this->get_field_id('maxlenght'); ?>"
		name="<?php echo $this->get_field_name('maxlenght'); ?>" 
		value="<?php echo $maxlenght; ?>" /></label>
</p>


<p class="description">
	<input class="checkbox" type="checkbox"
		<?php checked($instance['description'], 'on'); ?>
		id="<?php echo $this->get_field_id('description'); ?>"
		name="<?php echo $this->get_field_name('description'); ?>" /> <label
		for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Mostrar Resumen'); ?></label>
</p>

<p class="conditionally-description"
	<?php echo checked($instance['description'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['summary'], 'on'); ?>
		id="<?php echo $this->get_field_id('summary'); ?>"
		name="<?php echo $this->get_field_name('summary'); ?>" /> <label
		for="<?php echo $this->get_field_id('summary'); ?>"><?php _e('Mostrar sumario'); ?></label>
</p>

<p>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['date'], 'on'); ?>
		id="<?php echo $this->get_field_id('date'); ?>"
		name="<?php echo $this->get_field_name('date'); ?>" /> <label
		for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Mostrar Fecha'); ?></label>
</p>
	<?php $duration = esc_attr ( $instance ['cache'] );  ?>
<p>
	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Duración de la cache:'); ?> <select class='widefat' type="text"
		id="<?php echo $this->get_field_id('cache'); ?>"
		name="<?php echo $this->get_field_name('cache'); ?>">
		<?php
		$one_day= $this->util->one_day();
		$all_days = $this->util->cache_days();
		foreach ($all_days as $d){
			?>
			<option value=<?php echo $d * $one_day;?>
				<?php echo ($duration==($d * $one_day))?'selected':''; ?>><?php echo $d;?> <?php _e('días'); ?></option>
		<?php } //end while?>
	</select>
	</label>
</p>

<p class="show-filter">
	<input class="checkbox" type="checkbox"
		<?php checked($instance['all'], 'on'); ?>
		id="<?php echo $this->get_field_id('all'); ?>"
		name="<?php echo $this->get_field_name('all'); ?>" /> <label
		for="<?php echo $this->get_field_id('all'); ?>"> <?php _e('Todas las publicaciones sin filtros'); ?></label>
</p>

<hr>
<hr>

<p class="conditionally-filter"
	<?php echo checked($instance['all'], 'on') === '' ? '' : 'style="display: none;"'; ?>>
	<?php
		$subtypes = $this->filter->subtypes();
		foreach ( $subtypes as $s ) {
			?>
	<input class="checkbox" type="checkbox"
		<?php checked($instance[$s], 'on'); ?>
		id="<?php echo $this->get_field_id($s); ?>"
		name="<?php echo $this->get_field_name($s); ?>" /> <label
		for="<?php echo $this->get_field_id($s); ?>"><?php echo $s; ?></label>
	<br />
<?php
		}
		?> 
	<br>
	<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Cantidad de Resultados por filtro:'); ?> <select class='widefat'
		id="<?php echo $this->get_field_id('max_results'); ?>"
		name="<?php echo $this->get_field_name('max_results'); ?>" type="text">
		<?php
		
		$results = $this->util->total_results();
		foreach ( $results as $c ) {
			?>
			<option value=<?php echo $c;?>
				<?php echo ($max_results==$c)?'selected':''; ?>>
				<?php  echo ($c==0) ? "Todos":$c; ?>
			</option>
		<?php
		}// end for	
		?>
	</select>
	</label>
</p>
<?php
	}
}
add_action ( 'admin_enqueue_scripts', 'my_scripts_method' );
add_action ( 'admin_enqueue_scripts', 'my_styles' );
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Sedici");' ) );
add_shortcode ( 'get_publications_by_author', 'AuthorShortcode' );
add_shortcode ( 'get_publications_by_handle', 'HandleShortcode' );
add_shortcode ( 'get_publications_by_free_search', 'FreeShortcode' );
