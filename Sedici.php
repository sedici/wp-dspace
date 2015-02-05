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
require_once 'shortcodeSedici.php';
require_once 'vista/Vista.php';
require_once 'util/Filtros.php';
require_once 'util/Consulta.php';
require_once 'modelo/SimplepieSedici.php';
function my_styles_sedici() {
	// incluye el estilo sedici.css
	wp_register_style ( 'Sedici', plugins_url ( 'Sedici-Plugin/css/sedici.css' ) );
	wp_enqueue_style ( 'Sedici' );
}
function my_scripts_method_sedici() {
	// incluye el js sedici.js
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'sedici', plugins_url ( 'js/sedici.js', __FILE__ ), array (
			"jquery" 
	), null, true );
	wp_enqueue_script ( 'sedici' );
}

/**
 * Sedici Class
 */
class Sedici extends WP_Widget {
	protected $filtro;
	protected $util;
	
	/**
	 * constructor
	 */
	function Sedici() {
		// constructor
		$this->filtro = new Filtros ();
		$this->util = new Consulta ();
		$opciones = array (
				'description' => 'Plugin Sedici' 
		);
		parent::WP_Widget ( 'Sedici', 'Plugin Sedici', $opciones );
	}
	function widget($args, $instance) {
		extract ( $args );
		$context = apply_filters ( 'filtro', $instance ['filtro'] );
		if ($context != "") { // $context no puede venir vacio
			$cache = apply_filters ( 'cache', $instance ['cache'] ); // duracion en segundos de la cache
			                                                         // por defecto, una semana
			$all = ('on' == $instance ['mostrar_todos']); // checkbox para mostrar todos los resultados
			                                              // si esta en "on", $mostrar_todos queda en true, sino en false
			
			$max_results = apply_filters ( 'resultado', $instance ['resultado'] );
			// La variable $max_results, es la cantidad de publicaciones para cada filtro que se desea mostrar.
			// por defecto, todos. Si es que el checkbox de mostrar_todos no esta en ON.
			$opciones = $this->filtro->vectorPublicaciones ();
			/* Opciones es un vector que tiene todos los tipos de archivos, es decir, articulos, tesis, etc */
			$filtros = array (); // tendra todos los tipos de archivos seleccionados
			$vectorAgrupar = array ();
			/* vectorAgrupar, agrupara todas las publicaciones mediante su tipo */
			$agrupar_publicaciones = array ();
			foreach ( $opciones as $o ) {
				/*
				 * Itera sobre opciones, y compara con las opciones marcadas del usuario. Si esta en ON, entonces, guarda el nombre en filtros ($o) y en vectorAgrupar pone $o como clave
				 */
				if ('on' == $instance [$o]) {
					array_push ( $filtros, $o );
					$vectorAgrupar [$o] = array ();
				}
			}
			$type = apply_filters ( 'tipo', $instance ['tipo'] ); // contiene el autor o el handle
			$vectorAgrupar = $this->util->agruparSubtipos ( $type, $all, $context, $filtros, $vectorAgrupar, $cache );
			if (! $all) {
				$agrupar_publicaciones = $this->util->armarVista ( $vectorAgrupar, $type, $context );
			}
			If ('on' == $instance ['descripcion']) {
				if ('on' == $instance ['summary']) {
					$descripcion = "summary"; // si esta en on el checkbox de la descripcion y summary
				} else {
					$descripcion = "description"; // solo on el checkbox de la descripcion
				}
			}
			$fecha = ('on' == $instance ['fecha']);
			// siDescripción esta marcado el checkbox de fecha, $fecha esta en TRUE
			$mostrar_autor = ('on' == $instance ['mostrar_autor']);
			// si esta marcado el checkbox de mostrar_autor, $mostrar_autor esta en TRUE
			$atributos = $this->util->agruparAtributos ( $descripcion, $fecha, $mostrar_autor, $max_results, $context );
			$this->util->render ( $type, $all, $vectorAgrupar, $atributos, $agrupar_publicaciones );
		} else {
			// no se ingreso un autor o handle
			echo "Ingrese un filtro";
		}
	}
	
	/**
	 *
	 * @see WP_Widget::update
	 */
	function update($new_instance, $old_instance) {
		$tipos_archivos = $this->filtro->vectorPublicaciones ();
		$instance = $old_instance;
		$instance ['filtro'] = sanitize_text_field ( $new_instance ['filtro'] );
		$instance ['tipo'] = sanitize_text_field ( $new_instance ['tipo'] );
		$instance ['descripcion'] = sanitize_text_field ( $new_instance ['descripcion'] );
		$instance ['summary'] = sanitize_text_field ( $new_instance ['summary'] );
		$instance ['fecha'] = sanitize_text_field ( $new_instance ['fecha'] );
		$instance ['mostrar_autor'] = sanitize_text_field ( $new_instance ['mostrar_autor'] );
		$instance ['resultado'] = sanitize_text_field ( $new_instance ['resultado'] );
		$instance ['cache'] = sanitize_text_field ( $new_instance ['cache'] );
		$instance ['mostrar_todos'] = sanitize_text_field ( $new_instance ['mostrar_todos'] );
		
		foreach ( $tipos_archivos as $filtro ) {
			$instance [$filtro] = sanitize_text_field ( $new_instance [$filtro] );
		}
		return $instance;
	}
	
	/**
	 *
	 * @see WP_Widget::form
	 */
	function form($instance) {
		$resultado = esc_attr ( $instance ['resultado'] ); // cantidad de resultados a mostrar
		$filtro = esc_attr ( $instance ['filtro'] ); // ingresa un autor o un handle
		$duracion = esc_attr ( $instance ['cache'] ); // duracion de la cache
		$tipos_archivos = $this->filtro->vectorPublicaciones (); // contiene los distintos tipos de archivos
		?>

<!-- Eleccion entre autor y handle -->
<p class="mostrar-autor">
	<input class="checkbox" type="radio"
		<?php checked($instance['tipo'], 'handle'); ?>
		id="<?php echo $this->get_field_id('tipo'); ?>"
		name="<?php echo $this->get_field_name('tipo'); ?>" value="handle" />
	<label for="<?php echo $this->get_field_id('tipo'); ?>">Handle </label>
	<input class="checkbox" type="radio"
		<?php checked($instance['tipo'], 'autor'); ?>
		id="<?php echo $this->get_field_id('tipo'); ?>"
		name="<?php echo $this->get_field_name('tipo'); ?>" value="autor" /> <label
		for="<?php echo $this->get_field_id('tipo'); ?>">Autor</label>
</p>

<!-- Checkbox de mostrar autor, solo si tipo=autor -->
<p class="conditionally-autor"
	<?php echo checked($instance['tipo'], 'autor') === '' ? 'style="display: none;"' : ''; ?>>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['mostrar_autor'], 'on'); ?>
		id="<?php echo $this->get_field_id('mostrar_autor'); ?>"
		name="<?php echo $this->get_field_name('mostrar_autor'); ?>" /> <label
		for="<?php echo $this->get_field_id('mostrar_autor'); ?>">Mostrar
		Autores</label>
</p>


<!-- Imput para ingresar un autor o un handle -->
<p>
	<label for="<?php echo $this->get_field_id('filtro'); ?>"><?php _e('Filtro:'); ?> 
       <input class="widefat"
		id="<?php echo $this->get_field_id('filtro'); ?>"
		name="<?php echo $this->get_field_name('filtro'); ?>" type="text"
		value="<?php echo $filtro; ?>" /></label>
</p>


<!-- Checkbox de la descripcion -->
<p class="description">
	<input class="checkbox" type="checkbox"
		<?php checked($instance['descripcion'], 'on'); ?>
		id="<?php echo $this->get_field_id('descripcion'); ?>"
		name="<?php echo $this->get_field_name('descripcion'); ?>" /> <label
		for="<?php echo $this->get_field_id('descripcion'); ?>">Mostrar
		Resumen</label>
</p>

<!-- Si descripcion esta marcado, entonces se habilita el checkbox del summary -->
<p class="conditionally-loaded"
	<?php echo checked($instance['descripcion'], 'on') === '' ? 'style="display: none;"' : ''; ?>>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['summary'], 'on'); ?>
		id="<?php echo $this->get_field_id('summary'); ?>"
		name="<?php echo $this->get_field_name('summary'); ?>" /> <label
		for="<?php echo $this->get_field_id('summary'); ?>">Mostrar sumario</label>
</p>

<!-- Checkbox de la fecha -->
<p>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['fecha'], 'on'); ?>
		id="<?php echo $this->get_field_id('fecha'); ?>"
		name="<?php echo $this->get_field_name('fecha'); ?>" /> <label
		for="<?php echo $this->get_field_id('fecha'); ?>">Mostrar Fecha</label>
</p>

<!-- Duracion de la cache -->
<p>
	<label for="<?php echo $this->get_field_id('text'); ?>">Duración de la
		cache: <select class='widefat' type="text"
		id="<?php echo $this->get_field_id('cache'); ?>"
		name="<?php echo $this->get_field_name('cache'); ?>">
		<?php
		
		$dias = $this->util->valores_cache ();
		
		while ( list ( $key, $val ) = each ( $dias ) ) {
			
			?>
			<option value=<?php echo $key;?>
				<?php echo ($duracion==$key)?'selected':''; ?>><?php echo $val;?> días</option>
		<?php } //end while?>
	</select>
	</label>
</p>

<!-- Checkbox para mostrar todas las publicaciones del handle/autor -->
<p class="mostrarfiltro">
	<input class="checkbox" type="checkbox"
		<?php checked($instance['mostrar_todos'], 'on'); ?>
		id="<?php echo $this->get_field_id('mostrar_todos'); ?>"
		name="<?php echo $this->get_field_name('mostrar_todos'); ?>" /> <label
		for="<?php echo $this->get_field_id('mostrar_todos'); ?>">Todas las
		publicaciones sin filtros</label>
</p>

<hr>
<hr>
<!-- Si no esta marcada la opcion de todos los resultado (mostrar_todos), se habilitan los checkbox de tipos de archivos -->
<p class="conditionally-filtro"
	<?php echo checked($instance['mostrar_todos'], 'on') === '' ? '' : 'style="display: none;"'; ?>>
	<!-- Checkbox de opciones -->
<?php
		
		foreach ( $tipos_archivos as $filtro ) {
			?>
	<input class="checkbox" type="checkbox"
		<?php checked($instance[$filtro], 'on'); ?>
		id="<?php echo $this->get_field_id($filtro); ?>"
		name="<?php echo $this->get_field_name($filtro); ?>" /> <label
		for="<?php echo $this->get_field_id($filtro); ?>"><?php echo $filtro; ?></label>
	<br />
<?php
		}
		?> </p>
<!-- Imput para cantidad de resultados a mostrar (si no esta marcada la opcion de mostrar_todos) -->
<p class="conditionally-filtro"
	<?php echo checked($instance['mostrar_todos'], 'on') === '' ? '' : 'style="display: none;"'; ?>>
	<label for="<?php echo $this->get_field_id('text'); ?>">Cantidad de
		Resultados por filtro: <select class='widefat'
		id="<?php echo $this->get_field_id('resultado'); ?>"
		name="<?php echo $this->get_field_name('resultado'); ?>" type="text">
		<?php
		
		$cantidad = $this->util->cantidad_resultados ();
		foreach ( $cantidad as $c ) {
			?>
			<option value=<?php echo $c;?>
				<?php echo ($resultado==$c)?'selected':''; ?>>
			<?php  echo ($c==0) ? "Todos":$c; ?>
			</option>
		<?php
		}
		// end for		?>
	</select>
	</label>
<p>
<?php
	}
}
add_action ( 'admin_enqueue_scripts', 'my_scripts_method_sedici' );
add_action ( 'admin_enqueue_scripts', 'my_styles_sedici' );
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Sedici");' ) );
add_shortcode ( 'sedici', 'plugin_sedici' );