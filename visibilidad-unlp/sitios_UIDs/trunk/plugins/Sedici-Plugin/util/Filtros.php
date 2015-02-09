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

class Filtros {
	protected $subtipos;
	protected $tesis;
	public function Filtros(){
		$this->subtipos = array ("Documento de trabajo","Articulo","Contribucion a revista",
				"Informe tecnico","Libro","Objeto de conferencia","Preprint","Revision",
				"Tesis de doctorado","Tesis de grado","Tesis de maestria","Trabajo de especializacion" 
		);
		$this->tesis= array ( "Tesis de doctorado",	"Tesis de grado","Tesis de maestria" );
	}
	
	public function vectorPublicaciones() {
		return $this->subtipos;
	}
	public function vectorTesis() {
		return $this->tesis;
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