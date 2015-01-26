<?php
class Filtros {
	public function vectorPublicaciones() {
		$array = array (
				"Documento de trabajo",
				"Articulo",
				"Contribucion a revista",
				"Documento de trabajo",
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
	public function vectorTesis (){
		$array = array (
				"Tesis de doctorado",
				"Tesis de grado",
				"Tesis de maestria"
		);
		return ($array);
	}
	
	public function convertirEspIng($filtro) {
		switch ($filtro) {
			case "Articulo" :
				$valor = "article";
				break;
			case "Libro" :
				$valor = "book";
				break;
		}
		return ($valor);
	}
}
?>