<?php
/*
 * Plugin Name: Sedici Plugin URI: http://sedici.unlp.edu.ar/ Description: Este plugin permite mostrar publicaciones de un autor/handle en SEDICI al sitio Version: 1.0 Author: SEDICI - Paula Salamone Lacunza Author URI: http://sedici.unlp.edu.ar/ License:
 */
require_once 'shortcodeSedici.php';
require_once 'vista/Vista.php';
require_once 'filtro/Filtros.php';
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
	protected $model;
	protected $filtro;
	protected $vista;
	
	/**
	 * constructor
	 */
	function Sedici() {
		// constructor
		$this->model = new SimplepieSedici ();
		$this->filtro = new Filtros ();
		$this->vista = new Vista ();
		$opciones = array (
				'description' => 'Plugin Sedici' 
		);
		parent::WP_Widget ( 'Sedici', 'Plugin Sedici', $opciones );
	}
	function widget($args, $instance) {
		extract ( $args );
		$filtro = apply_filters ( 'filtro', $instance ['filtro'] );
		
		if ($filtro != "") { // filtro no puede venir vacio
			
			$duracion = apply_filters ( 'cache', $instance ['cache'] ); // duracion en segundos de la cache
			                                                            // por defecto, una semana
			
			$mostrar_todos = ('on' == $instance ['mostrar_todos']); // checkbox para mostrar todos los resultados
			                                                        // si esta en "on", $mostrar_todos queda en true, sino en false
			
			$resultado = apply_filters ( 'resultado', $instance ['resultado'] );
			// La variable resultado, es la cantidad de publicaciones para cada filtro que se desea mostrar.
			// por defecto, todos. Si es que el checkbox de mostrar_todos no esta en ON.
			
			$opciones = $this->filtro->vectorPublicaciones ();
			/* Opciones es un vector que tiene todos los tipos de archivos, es decir, articulos, tesis, etc */
			
			$filtros = array (); // tendra todos los tipos de archivos seleccionados
			$vectorAgrupar = array ();
			/* vectorAgrupar, agrupara todas las publicaciones mediante su tipo */
			
			foreach ( $opciones as $o ) {
				/*
				 * Itera sobre opciones, y compara con las opciones marcadas del usuario. Si esta en ON, entonces, guarda el nombre en filtros ($o) y en vectorAgrupar pone $o como clave
				 */
				if ('on' == $instance [$o]) {
					array_push ( $filtros, $o );
					$vectorAgrupar [$o] = array ();
				}
			}
			
			$tipo = apply_filters ( 'tipo', $instance ['tipo'] ); // contiene el autor o el handle
			
			$start = 0; // la variable start es para paginar la consulta
			
			$agrupar_publicaciones = array (); // es un array que tendra la informacion para la vista
			
			$cantidadFiltros = count ( $filtros ); // cantidadFiltros tiene la el numero de filtros marcados por el usuario
			$cantidad = 0;
			
			do {
				if ($tipo == 'autor') {
					$consulta = "http://sedici.unlp.edu.ar/open-search/discover?rpp=100&format=atom&sort_by=0&order=desc&start=" . $start . "&query=sedici.creator.person:\"$filtro\"";
				} else { // es un handle
					if ($mostrar_todos) {
						// La consulta tendra todas las publicaciones
						$consulta = "http://sedici.unlp.edu.ar/open-search/discover?rpp=100&format=atom&sort_by=0&order=desc&scope=" . $filtro . "&start=" . $start;
					} else {
						// Se arma una consulta, dependiendo los tipos de archivos marcados
						$i = 1;
						$consulta = "http://sedici.unlp.edu.ar/open-search/discover?rpp=100&format=atom&sort_by=0&order=desc&scope=" . $filtro . "&start=" . $start . "&query=sedici.subtype:";
						// en este for, se arma la consulta
						foreach ( $filtros as $f ) {
							$consulta .= "\"" . $f . "\"";
							if ($i != $cantidadFiltros) {
								$consulta .= "%20OR%20sedici.subtype:";
								// concateno los filtros en la consulta
							}
							$i ++;
						}
					}
				}
				$xpath = $this->model->cargarPath ( $consulta, $duracion ); // cargo la consulta con cache=duracion
				$cantidad += $this->model->cantidadResultados ( $xpath ); // cantidad tiene el numero de entrys resultados
				$totalResultados = $this->model->totalResults ( $xpath ); // Numero total de todas las publicaciones
				$entry = $this->model->entry ( $xpath ); // entry tiene todos los documentos de la consulta
				
				if ($mostrar_todos) {
					// si muestro todos los resultados, no agrupo por tipo de archivo
					array_push ( $vectorAgrupar, $entry );
				} else {
					foreach ( $entry as $e ) {
						$subtipo = $this->model->tipo ( $e ); // el metodo tipo retorna el subtipo de documento
						array_push ( $vectorAgrupar [$subtipo], $e );
						// agrego el documento en vectorAgrupar dependiendo el tipo de documento
					}
				}
				$start += 100;
			} while ( $cantidad < $totalResultados );
			// itera mientras que la cantidad de resultados que levante sea menor que la cantidad total de resultados
			
			if (! $mostrar_todos) {
				while ( list ( $key, $val ) = each ( $vectorAgrupar ) ) {
					// $val tiene las publicaciones de un tipo
					$elementos = count ( $val ); // elementos tiene la cantidad de resultados dado un tipo de archivo
					if ($elementos > 0) {
						// $key tiene la clave de vectorAgrupar, es decir, el tipo de documento
						
						// coleccion tiene para cada filtro, los entrys a mostrar y su url
						if ($tipo == 'autor') {
							$coleccion = array (
									'vista' => $val,
									'filtro' => $key 
							);
						} else {
							$url = $this->vista->armarUrl ( $key, $filtro );
							$coleccion = array (
									'vista' => $val,
									'url' => $url,
									'filtro' => $key 
							);
						}
						array_push ( $agrupar_publicaciones, $coleccion );
						// $agrupar_publicaciones es el vector para iterar en la vista
					}
				}
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
			if ($tipo == 'autor') {
				if ($mostrar_todos) {
					$this->vista->todos ( $vectorAgrupar, $descripcion, $fecha, $mostrar_autor );
				} else {
					$this->vista->autor ( $agrupar_publicaciones, $descripcion, $fecha, $resultado, $mostrar_autor );
				}
			} else { // es un handle
				
				if ($mostrar_todos) {
					$mostrar_autor = true;
					$this->vista->todos ( $vectorAgrupar, $descripcion, $fecha, $mostrar_autor );
				} else {
					$this->vista->articulos ( $agrupar_publicaciones, $descripcion, $fecha, $resultado );
				}
			}
		} else {
			// no se ingreso un
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
		Autor</label>
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
			<option value='604800'
				<?php echo ($duracion=='604800')?'selected':''; ?>>Duración en días
			</option>
			<option value='86400'
				<?php echo ($duracion=='86400')?'selected':''; ?>>1 día</option>
			<option value='259200'
				<?php echo ($duracion=='259200')?'selected':''; ?>>3 días</option>
			<option value='604801'
				<?php echo ($duracion=='604800')?'selected':''; ?>>7 días</option>
			<option value='1209600'
				<?php echo ($duracion=='1209600')?'selected':''; ?>>14 días</option>
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
	<?php echo checked($instance['mostrarfiltro'], 'on') === '' ? '' : 'style="display: none;"'; ?>>
	<label for="<?php echo $this->get_field_id('text'); ?>">Cantidad de
		Resultados por filtro: <select class='widefat'
		id="<?php echo $this->get_field_id('resultado'); ?>"
		name="<?php echo $this->get_field_name('resultado'); ?>" type="text">
			<option value='0' <?php echo ($resultado=='0')?'selected':''; ?>>Todos
			</option>
			<option value='10' <?php echo ($resultado=='10')?'selected':''; ?>>10</option>
			<option value='25' <?php echo ($resultado=='25')?'selected':''; ?>>25</option>
			<option value='50' <?php echo ($resultado=='50')?'selected':''; ?>>50</option>
			<option value='100' <?php echo ($resultado=='100')?'selected':''; ?>>100</option>
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