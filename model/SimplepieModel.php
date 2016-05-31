<?php
class SimplepieModel {
	protected $cache;
	public function SimplepieModel(){
		$this->cache = "/cache";
	}
	public function loadPath($str,$duration) {
		// declare the namespaces
		require_once 'simplepie-master/autoloader.php';
		$cache = dirname(__FILE__);
		$cache .=$this->cache;
		$feed = new SimplePie ();
		$feed->set_feed_url ( $str );
		$feed->set_cache_location ($cache);
		$feed->set_cache_duration($duration);
		$feed->init ();
		$feed->handle_content_type ();
		return ($feed);
	}
	public function itemQuantity($sxe) {
		//return number of entrys
		return ($sxe->get_item_quantity ());
	}
	public function entry($sxe) {
		// return all documents
		return ($sxe->get_items ());
	}
	public function type($entry){
		//return subtype document
		$description = $entry->get_description();
		$filter = explode ( "\n", $description );
		return ($filter[0]);
	}
        public function date($entry){
            return $entry->get_date ( 'Y' );
        }
	public function totalResults($sxe){
		$results=$sxe->get_feed_tags('http://a9.com/-/spec/opensearch/1.1/','totalResults');
		return ($results[0]['data']);
	}
}