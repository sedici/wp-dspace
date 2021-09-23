<?php
namespace Wp_dspace\Model;

class SimplepieModel {
	protected $cache;
	public function __construct(){
		$this->cache = "/cache";
	}
	/**
	 *   @return SimpleXMLElement of result  OpenSearch. 
	 */
	public function loadPath($str,$duration) {
		
		$result=wp_remote_get($str);
		$units_body = wp_remote_retrieve_body( $result );	
		$xml = simplexml_load_string( $units_body, 'SimpleXMLElement' );
		return ($xml);
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
		$description = $entry->summary;
		$filter = explode ( "\n", $description );
		return ($filter[0]);
	}
	public function date_utf_fotmat($entry){
			
		$dc_values= $entry->children('dc', TRUE);
		$date=date_create($dc_values->date);
		return  date_format($date,"Y-m-d");
	}
        public function date($entry){
			
			$dc_values= $entry->children('dc', TRUE);
       		$date=date_create($dc_values->date);
            return date_format($date,"d/m/Y");
        }
        public function year($entry){
			$dc_values= $entry->children('dc', TRUE);
       		$date=date_create($dc_values->date);
            return date_format($date,"Y");
           
        }
	// TODO eliminar
	/*public function totalResults($sxe){
		$results=$sxe->get_feed_tags('http://a9.com/-/spec/opensearch/1.1/','totalResults');
		return ($results[0]['data']);
	}*/
}