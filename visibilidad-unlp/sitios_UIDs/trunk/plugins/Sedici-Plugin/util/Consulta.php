<?php
define('RPP', '100');
define('FORMAT', 'atom');
define('SORTBY', '0');
define('ORDER', 'desc');

define(_PROTOCOL,"http://");
define(_DOMAIN , "sedici.unlp.edu.ar");
define(_BASE_PATH,"/open-search/discover");

function get_base_url() {
	return _PROTOCOL._DOMAIN._BASE_PATH;
}
function get_protocol_domain(){
	return _PROTOCOL._DOMAIN;
}
class Consulta {
	protected $consulta;
	
	public function Consulta()
	{
		$this->consulta= get_base_url()."?rpp=" . RPP ."&format=".FORMAT."&sort_by=".SORTBY."&order=".ORDER."&start=";
	}
	
	function armarUrl($filtro, $handle) {
		/*
		 * Esta funcion, arma la url para el "ir a sedici" dependiendo de cada filtro. Ejemplo: http://sedici.unlp.edu.ar/handle/10915/25293/discover?fq=type_filter%3Atesis%5C+de%5C+doctorado%5C%7C%5C%7C%5C%7CTesis%5C+de%5C+doctorado
		*/
		$filtro = strtolower($filtro);//lo convierto todo a minuscula
		$palabras = explode ( " ", $filtro ); // palabras es un array que tiene cada palabra del filtro
		$url = get_protocol_domain()."/handle/" . $handle . "/discover?fq=type_filter%3A";
		$cant = count ( $palabras ); // cant tiene la cantidad de elementos de palabras
		$url = $url . $palabras [0]; // concateno la primera palabra
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . "%5C+" . $palabras [$i]; // concateno el resto de las palabras, si es que existen, anteponiendo %5c+
		}
		$mayuscula = ucfirst ( $filtro );
		$palabras = explode ( " ", $mayuscula );
		$url = $url . "%5C%7C%5C%7C%5C%7C";
		$cant = count ( $palabras );
		$url = $url . $palabras [0];
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . "%5C+" . $palabras [$i];
		}
		return $url;
	}
	function armarConsultaAllHandle($start,$context){
				$consulta = $this->consulta;
				$consulta .= $start."&scope=" . $context;
				return $consulta;
			} 
	function armarConsultaHandle($start,$context,$filtros) {
				$i = 1;
				$consulta = $this->consulta;
				$consulta .= $start."&scope=". $context  . "&query=sedici.subtype:";
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

	function armarConsultaAutor($start,$context){
			$consulta = $this->consulta;
			$consulta .= $start . "&query=sedici.creator.person:\"$context\"";
			return $consulta;
		}
		
	
}
?>