<?php
class Filtros {
	public function vectorPublicaciones() {
		//Contiene los subtipos de publicaciones
		$array = array (
				"Documento de trabajo",
				"Articulo",
				"Contribucion a revista",
				"Informe tecnico",
				"Libro",
				"Objeto de conferencia",
				"Preprint",
				"Revision",
				"Tesis de doctorado",
				"Tesis de grado",
				"Tesis de maestria",
				"Trabajo de especializacion" 
		);
		return ($array);
	}
	public function vectorTesis() {
		//Esta funcion es utilizada para el shortcode, cuando thesis=true, son los 3 subtipos
		$vector = array (
				"Tesis de doctorado",
				"Tesis de grado",
				"Tesis de maestria" 
		);
		return ($vector);
	}
	public function convertirEspIng($filtro) {
		//Pasa los subtipos al ingles para la comparacion con el shortcode
		switch ($filtro) {
			case "Articulo" :
				$valor = "article";
				break;
			case "Libro" :
				$valor = "book";
				break;
			case "Preprint" :
				$valor = "preprint";
				break;
			case "Documento de trabajo" :
				$valor = "working_paper";
				break;
			case "Informe tecnico" :
				$valor = "technical_report";
				break;
			case "Objeto de conferencia" :
				$valor = "conference_object";
				break;
			case "Revision" :
				$valor = "revision";
				break;
			case "Trabajo de especializacion" :
				$valor = "work_specialization";
				break;
		}
		return ($valor);
	}
}
?>