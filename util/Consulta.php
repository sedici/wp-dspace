<?php
define ( 'RPP', '100' );
define ( 'FORMAT', 'atom' );
define ( 'SORTBY', '0' );
define ( 'ORDER', 'desc' );
define ( CONECTOR2, '%5C' );
define ( CONECTOR3, '%7C' );
define ( _PROTOCOL, "http://" );
define ( _DOMAIN, "sedici.unlp.edu.ar" );
define ( _BASE_PATH, "/open-search/discover" );
function conector() {
	return CONECTOR2 . '+';
}
function get_conector() {
	return (CONECTOR2 . CONECTOR3 . CONECTOR2 . CONECTOR3 . CONECTOR2 . CONECTOR3);
}
function get_base_url() {
	return _PROTOCOL . _DOMAIN . _BASE_PATH;
}
function get_protocol_domain() {
	return _PROTOCOL . _DOMAIN;
}
class Consulta {
	protected $consulta;
	public function Consulta() {
		$this->consulta = get_base_url () . "?rpp=" . RPP . "&format=" . FORMAT . "&sort_by=" . SORTBY . "&order=" . ORDER . "&start=";
	}
	public function valores_cache() {
		//Esta funcion contiene los valores para la eleccion de los dias en que se va a actualizar la cache para el widget
		$atributos = array (
				'604801' => '7',
				'86400' => '1',
				'259200' => '3',
				'1209600' => '14' 
		);
		return $atributos;
	}
	public function cantidad_resultados() {
		//Esta funcion contiene los valores para la eleccion de cantidad de resultados a mostrar por subtipo en el widget
		$atributos = array (
				'0',
				'10',
				'25',
				'50',
				'100' 
		);
		return $atributos;
	}
	function armarUrl($filtro, $handle) {
		/*
		 * Esta funcion, arma la url para el "ir a sedici" dependiendo de cada filtro. 
		 * Ejemplo: http://sedici.unlp.edu.ar/handle/10915/25293/discover?fq=type_filter%3Atesis%5C+de%5C+doctorado%5C%7C%5C%7C%5C%7CTesis%5C+de%5C+doctorado
		 */
		$filtro = strtolower ( $filtro ); // lo convierto todo a minuscula
		$palabras = explode ( " ", $filtro ); // palabras es un array que tiene cada palabra del filtro
		$url = get_protocol_domain () . "/handle/" . $handle . "/discover?fq=type_filter%3A";
		$cant = count ( $palabras ); // cant tiene la cantidad de elementos de palabras
		$url = $url . $palabras [0]; // concateno la primera palabra
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . conector () . $palabras [$i]; // concateno el resto de las palabras, si es que existen, anteponiendo %5c+
		}
		$mayuscula = ucfirst ( $filtro );
		$palabras = explode ( " ", $mayuscula );
		$url = $url . get_conector ();
		$cant = count ( $palabras );
		$url = $url . $palabras [0];
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . conector () . $palabras [$i];
		}
		return $url;
	}
	function armarConsultaAllHandle($start, $context) {
		//Esta funcion arma la consulta opensearch para todos los resultados de un handle/autor
		$consulta = $this->consulta;
		$consulta .= $start . "&scope=" . $context;
		return $consulta;
	}
	function armarConsultaHandle($start, $context, $filtros) {
		//Esta funcion arma la consulta para las publicaciones de determinado subtipo de un handle
		$i = 1;
		$consulta = $this->consulta;
		$consulta .= $start . "&scope=" . $context . "&query=sedici.subtype:";
		// en este for, se arma la consulta
		$cantidadFiltros = count ( $filtros );
		foreach ( $filtros as $f ) {
			$consulta .= "\"" . $f . "\"";
			if ($i != $cantidadFiltros) {
				$consulta .= "%20OR%20sedici.subtype:";
				// concateno los filtros en la consulta
			}
			$i ++;
		}
		return $consulta;
	}
	function armarConsultaAutor($start, $context) {
		//Esta funcion arma la consulta opensearch para un autor
		$consulta = $this->consulta;
		$consulta .= $start . "&query=sedici.creator.person:\"$context\"";
		return $consulta;
	}
	function agruparSubtipos($type, $all, $context, $filtros, $vectorAgrupar,$cache) {
		/*
		 * Esta funcion agrupa las publicaciones mediante subtipos, en el caso de ser todos los resultados solo realiza las consultas paginando
		*/
		$start = 0; // la variable start es para paginar la consulta
		$cantidad = 0;
		$model = new SimplepieSedici ();
		do {
			if ($type == "handle") {
				if ($all) {
					$consulta = $this->armarConsultaAllHandle ( $start, $context );
				} else {
					$consulta = $this->armarConsultaHandle ( $start, $context, $filtros );
				}
			} else {
				$consulta = $this->armarConsultaAutor ( $start, $context );
			}
			$xpath = $model->cargarPath ( $consulta, $cache );
			$cantidad += $model->cantidadResultados ( $xpath ); // cantidad tiene el numero de entrys resultados
			$totalResultados = $model->totalResults ( $xpath );
			$entry = $model->entry ( $xpath ); // entry tiene todos los documentos
			$start += 100;
			
			if ($all) {
				array_push ( $vectorAgrupar, $entry );
			} else {
				foreach ( $entry as $e ) {
					$subtipo = $model->tipo ( $e ); // el metodo tipo retorna el subtipo de documento
					if (array_key_exists ( $subtipo, $vectorAgrupar )) {
						array_push ( $vectorAgrupar [$subtipo], $e );
						// agrego el documento en vectorAgrupar dependiendo el tipo de documento
					}
				}
			}
		} while ( $cantidad < $totalResultados );
		return ($vectorAgrupar);
	}
	function armarVista($vectorAgrupar, $type ,$context) {
		$enviar = array (); // es un array que tendra la informacion para la vista
		while ( list ( $key, $val ) = each ( $vectorAgrupar ) ) {
			// $val tiene las publicaciones de un tipo
			$elementos = count ( $val );
			if ($elementos > 0) {
				// $key tiene la clave de vectorAgrupar, es decir, el tipo de documento
				// coleccion tiene para cada filtro, los entrys a mostrar y su url
				if ($type == 'handle') {
					$url = $this->armarUrl ( $key, $context );
					$coleccion = array (
							'vista' => $val,
							'url' => $url,
							'filtro' => $key 
					);
				} else { // es autor
					$coleccion = array (
							'vista' => $val,
							'filtro' => $key 
					);
				}
				array_push ( $enviar, $coleccion );
				// $enviar es el vector para iterar en la vista
			}
		}
		return ($enviar);
	}
	function agruparAtributos($descripcion, $fecha, $mostrar, $max_results, $context) {
		//Esta funcion agrupa los distintos valores en un array que serviran para tomar desiciones en las vistas
		$atributos = array (
				'descripcion' => $descripcion,
				'mostrar' => $mostrar,
				'max_results' => $max_results,
				'context' => $context,
				'fecha' => $fecha 
		);
		return $atributos;
	}
	function render($type, $all, $vectorAgrupar, $atributos, $enviar) {
		//Dependiendo si es handle/autor o si son todos los resultado, llama a determinada vista
		$vista = new Vista ();
		if ($type == 'handle') {
			$atributos['mostrar'] = TRUE;
			if ($all) {
				return ($vista->todos ( $vectorAgrupar, $atributos,$type ));
			} else {
				return ($vista->publicaciones( $enviar, $atributos,$type ));
			}
		} else {
			// es un autor
			if ($all) {
				return ($vista->todos ( $vectorAgrupar, $atributos,$type ));
			} else {
				return ($vista->publicaciones ( $enviar, $atributos,$type ));
			}
		}
	}
}
?>