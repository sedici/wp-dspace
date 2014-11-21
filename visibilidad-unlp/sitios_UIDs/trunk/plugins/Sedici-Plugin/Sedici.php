<?php
/*
 * Plugin Name: Sedici Plugin URI: http://sedici.unlp.edu.ar/ Description: Este plugin permite mostrar publicaciones de un autor/handle en SEDICI al sitio Version: 1.0 Author: SEDICI - Paula Salamone Lacunza Author URI: http://sedici.unlp.edu.ar/ License:
 */
require_once 'vista/Vista.php';
require_once 'filtro/Filtros.php';
require_once 'modelo/SimplepieSedici.php';

function my_styles_sedici() {
	wp_register_style ( 'Sedici', plugins_url ( 'Sedici-Plugin/css/sedici.css' ) );
	wp_enqueue_style ( 'Sedici' );
}
function my_scripts_method_sedici() {
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'sedici', plugins_url ( 'js/sedici.js', __FILE__ ), array (
			"jquery" 
	), null, true );
	wp_enqueue_script ( 'sedici' );
}
function header_widgets_init() {
	$args = array (
			'name' => 'ZONA SEDICI WIDGET',
			'id' => 'sedici-widget',
			'description' => '',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '' 
	);
	
	register_sidebar ( $args );
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
		
		if ($filtro != "") {
			$duracion = apply_filters ( 'duracion', $instance ['duracion'] );
			
			$mostrarfiltro = ('on' == $instance ['mostrarfiltro']);
			
			$resultado = apply_filters ( 'resultado', $instance ['resultado'] );
			/*
			 * La variable resultado, es la cantidad de publicaciones para cada filtro que se desea mostrar. Si viene en blanco, el defecto es 10
			 */
			
			$opciones = $this->filtro->vectorPublicaciones ();
			/* Opciones es un vector que tiene todos los filtros, es decir, articulos, tesis, etc */
			
			$filtros = array ();
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
			$tipo = apply_filters ( 'tipo', $instance ['tipo'] );
			$start = 0; // la variable start es para paginar la consulta
			$enviar = array (); // es un array que tendra la informacion para la vista
			$cantidadFiltros = count ( $filtros ); // cantidadFiltros tiene la el numero de filtros marcados por el usuario
			$cantidad = 0;
			do {
				if ($tipo == 'autor') {
					$consulta = "http://sedici.unlp.edu.ar/open-search/discover?rpp=100&format=atom&sort_by=0&order=desc&start=" . $start . "&query=sedici.creator.person:\"$filtro\"";
				} else { // es un handle
					if ($mostrarfiltro) {
						$consulta = "http://sedici.unlp.edu.ar/open-search/discover?rpp=100&format=atom&sort_by=0&order=desc&scope=" . $filtro . "&start=" . $start;
					} else {
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
				$xpath = $this->model->cargarPath ( $consulta, $duracion );
				$cantidad += $this->model->cantidadResultados ( $xpath ); // cantidad tiene el numero de entrys resultados
				$totalResultados = $this->model->totalResults ( $xpath );
				$entry = $this->model->entry ( $xpath ); // entry tiene todos los documentos
				
				if ($mostrarfiltro) {
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
			
			if (! $mostrarfiltro) {
				while ( list ( $key, $val ) = each ( $vectorAgrupar ) ) {
					// $val tiene las publicaciones de un tipo
					$elementos = count ( $val );
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
						array_push ( $enviar, $coleccion );
						// $enviar es el vector para iterar en la vista
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
			$mostrar = ('on' == $instance ['mostrar']);
			// si esta marcado el checkbox de mostrar, $mostrar esta en TRUE
			if ($tipo == 'autor') {
				if ($mostrarfiltro) {
					$this->vista->todos ( $vectorAgrupar, $descripcion, $fecha, $resultado, $mostrar );
				} else {
					$this->vista->autor ( $enviar, $descripcion, $fecha, $resultado, $mostrar );
				}
			} else { // es un handle
				
				if ($mostrarfiltro) {
					$mostrar = true;
					$this->vista->todos ( $vectorAgrupar, $descripcion, $fecha, $resultado, $mostrar );
				} else {
					$this->vista->articulos ( $enviar, $descripcion, $fecha, $resultado );
				}
			}
		} else {
			echo "Ingrese un filtro";
		}
	}
	
	/**
	 *
	 * @see WP_Widget::update
	 */
	function update($new_instance, $old_instance) {
		$array = $this->filtro->vectorPublicaciones ();
		
		$instance = $old_instance;
		// Con sanitize_text_field elimiamos HTML de los campos
		$instance ['filtro'] = sanitize_text_field ( $new_instance ['filtro'] );
		$instance ['tipo'] = sanitize_text_field ( $new_instance ['tipo'] );
		$instance ['descripcion'] = sanitize_text_field ( $new_instance ['descripcion'] );
		$instance ['summary'] = sanitize_text_field ( $new_instance ['summary'] );
		$instance ['fecha'] = sanitize_text_field ( $new_instance ['fecha'] );
		$instance ['mostrar'] = sanitize_text_field ( $new_instance ['mostrar'] );
		$instance ['resultado'] = sanitize_text_field ( $new_instance ['resultado'] );
		$instance ['duracion'] = sanitize_text_field ( $new_instance ['duracion'] );
		$instance ['mostrarfiltro'] = sanitize_text_field ( $new_instance ['mostrarfiltro'] );
		foreach ( $array as $filtro ) {
			$instance [$filtro] = sanitize_text_field ( $new_instance [$filtro] );
		}
		return $instance;
	}
	
	/**
	 *
	 * @see WP_Widget::form
	 */
	function form($instance) {
		$mostrarfiltro = esc_attr ( $instance ['mostrarfiltro'] );
		$resultado = esc_attr ( $instance ['resultado'] );
		$tipo = esc_attr ( $instance ['tipo'] );
		$filtro = esc_attr ( $instance ['filtro'] );
		$descripcion = esc_attr ( $instance ['descripcion'] );
		$summary = esc_attr ( $instance ['summary'] );
		$mostrar = esc_attr ( $instance ['mostrar'] );
		$fecha = esc_attr ( $instance ['fecha'] );
		$vectorFiltros = $this->filtro->vectorPublicaciones ();
		$duracion = esc_attr ( $instance ['duracion'] );
		?>

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

<!-- Checkbox de mostrar autor -->
<p class="conditionally-autor"
	<?php echo checked($instance['tipo'], 'autor') === '' ? 'style="display: none;"' : ''; ?>>
	<input class="checkbox" type="checkbox"
		<?php checked($instance['mostrar'], 'on'); ?>
		id="<?php echo $this->get_field_id('mostrar'); ?>"
		name="<?php echo $this->get_field_name('mostrar'); ?>" /> <label
		for="<?php echo $this->get_field_id('mostrar'); ?>">Mostrar Autor</label>
</p>


<!-- Imput del filtro -->
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
		Descripci&oacute;n</label>
</p>
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
		id="<?php echo $this->get_field_id('duracion'); ?>"
		name="<?php echo $this->get_field_name('duracion'); ?>">
			<option value='604800'
				<?php echo ($duracion=='604800')?'selected':''; ?>>Duración en días
			</option>
			<option value='86400'
				<?php echo ($duracion=='86400')?'selected':''; ?>>1 día</option>
			<option value='259200'
				<?php echo ($duracion=='259200')?'selected':''; ?>>3 días</option>
			<option value='604800'
				<?php echo ($duracion=='604800')?'selected':''; ?>>7 días</option>
			<option value='1209600'
				<?php echo ($duracion=='1209600')?'selected':''; ?>>14 días</option>
	</select>
	</label>
</p>


<p class="mostrarfiltro">
	<input class="checkbox" type="checkbox"
		<?php checked($instance['mostrarfiltro'], 'on'); ?>
		id="<?php echo $this->get_field_id('mostrarfiltro'); ?>"
		name="<?php echo $this->get_field_name('mostrarfiltro'); ?>" /> <label
		for="<?php echo $this->get_field_id('mostrarfiltro'); ?>">Todas las
		publicaciones sin filtros</label>
</p>

<hr>
<hr>
<p class="conditionally-filtro"
	<?php echo checked($instance['mostrarfiltro'], 'on') === '' ? '' : 'style="display: none;"'; ?>>
	<!-- Checkbox de opciones -->
<?php
		
		foreach ( $vectorFiltros as $filtro ) {
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
<!-- Imput para cantidad de resultados a mostrar -->
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
add_action ( 'widgets_init', 'header_widgets_init' );
add_action ( 'admin_enqueue_scripts', 'my_scripts_method_sedici' );
add_action ( 'admin_enqueue_scripts', 'my_styles_sedici' );
add_action ( 'widgets_init', create_function ( '', 'return register_widget("Sedici");' ) );