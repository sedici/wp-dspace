<?php
function plugin_sedici($atts) {
	$a = shortcode_atts ( array (
			'type' => null,
			'context' => null,
			'max_results' => 20,
			'all' => false,
			'description' => false,
			'date' => false,
			'show_author' => false,
			'cache' => 604800,
			'article' => false,
			'book' => false,
			'thesis' => false 
	), $atts );
	
	if ((is_null ( $a ['type'] )) && (is_null ( $a ['context'] ))) {
		return "Ingrese un type y un context";
	}
	$type = $a ['type'];
	if ((strcmp ( $type, "handle" ) !== 0) && (strcmp ( $type, "author" ) !== 0)) {
		return "El type debe ser handle o author";
	}

	$descripcion = $a ['description'] === 'true' ? true : false;
	$fecha = $a ['date'] === 'true' ? true : false;
	$mostrar = $a ['show_author'] === 'true' ? true : false;
	$cache = $a ['cache'];
	$context = $a ['context'];
	$all = $a ['all'] === 'true' ? true : false;
	$max_results = $a ['max_results'];
	
	$vectorAgrupar = array ();
	$model = new SimplepieSedici ();
	$vista = new Vista ();
	$filtro = new Filtros ();
	$util = new Consulta ();
	$opciones = $filtro->vectorPublicaciones ();
	/* Opciones es un vector que tiene todos los filtros, es decir, articulos, tesis, etc */
	
	$filtros = array ();
	$vectorAgrupar = array ();
	/* vectorAgrupar, agrupara todas las publicaciones mediante su tipo */
	foreach ( $opciones as $o ) {
		/*
		 * Itera sobre opciones, y compara con las opciones marcadas del usuario. Si esta en ON, entonces, guarda el nombre en filtros ($o) y en vectorAgrupar pone $o como clave
		 */
		$valor = $filtro->convertirEspIng ( $o );
		if ('true' === $a [$valor]) {
			array_push ( $filtros, $o );
			$vectorAgrupar [$o] = array ();
		}
	}
	$tesis = $a ['thesis'] === 'true' ? true : false;
	if ($tesis) {
		$vectorTesis = $filtro->vectorTesis ();
		foreach ( $vectorTesis as $o ) {
			array_push ( $filtros, $o );
			$vectorAgrupar [$o] = array ();
		}
	}
	
	$start = 0; // la variable start es para paginar la consulta
	$enviar = array (); // es un array que tendra la informacion para la vista
	$cantidad = 0;
	do {
		if ($type == "handle") {
			if ($all) {
				$consulta = $util->armarConsultaAllHandle ( $start, $context );
			} else {
				$consulta = $util->armarConsultaHandle ( $start, $context, $filtros );
			}
		} else {
			$consulta = $util->armarConsultaAutor ( $start, $context );
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
	
	if (! $all) {
		while ( list ( $key, $val ) = each ( $vectorAgrupar ) ) {
			// $val tiene las publicaciones de un tipo
			$elementos = count ( $val );
			if ($elementos > 0) {
				// $key tiene la clave de vectorAgrupar, es decir, el tipo de documento
				
				// coleccion tiene para cada filtro, los entrys a mostrar y su url
				if ($type == 'author') {
					$coleccion = array (
							'vista' => $val,
							'filtro' => $key 
					);
				} else {
					$url = $util->armarUrl ( $key, $context );
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
	
	
	if ($type == 'author') {
		if ($all) {
			return ($vista->todos ( $vectorAgrupar, $descripcion, $fecha, $mostrar ));
		} else {
			return ($vista->autor ( $enviar, $descripcion, $fecha, $max_results, $mostrar, $context ));
		}
	} else { // es un handle
		
		if ($all) {
			$mostrar = true;
			return ($vista->todos ( $vectorAgrupar, $descripcion, $fecha, $mostrar ));
		} else {
			return ($vista->articulos ( $enviar, $descripcion, $fecha, $max_results ));
		}
	}
}