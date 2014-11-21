<?php
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