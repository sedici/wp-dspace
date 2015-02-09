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

class SimplepieSedici {
	public function cargarPath($str,$duracion) {
		// recibe un string, lo carga y declara namespaces
		require_once 'simplepie-master/autoloader.php';
		$cache = dirname(__FILE__);
		$cache .="/cacheS";
		$feed = new SimplePie ();
		$feed->set_feed_url ( $str );
		$feed->set_cache_location ($cache);
		$feed->set_cache_duration($duracion);
		$feed->init ();
		$feed->handle_content_type ();
		return ($feed);
	}
	public function cantidadResultados($sxe) {
		// retorna el numero de entrys que hay para la consulta: totalResults
		return ($sxe->get_item_quantity ());
	}
	public function entry($sxe) {
		// retorna todos los entrys del xml
		return ($sxe->get_items ());
	}
	public function tipo($entry){
		//retorna el tipo del documento
		$descripcion = $entry->get_description();
		$filtro = explode ( "\n", $descripcion );
		return ($filtro[0]);
	}
	
	public function totalResults($sxe){
		$resultados=$sxe->get_feed_tags('http://a9.com/-/spec/opensearch/1.1/','totalResults');
		return ($resultados[0]['data']);
	}
}
?>