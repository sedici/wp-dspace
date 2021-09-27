<?php
namespace Wp_dspace\Model;

class SimpleXMLModel {
	
	/**
	 *   @return SimpleXMLElement of result  OpenSearch. 
	 */
	public function loadPath($str,$duration) {
		
		$result=wp_remote_get($str);
		$units_body = wp_remote_retrieve_body( $result );	
		$xml = simplexml_load_string( $units_body, 'SimpleXMLElement' );
		return ($xml);
	}
	
	/**
	 * Se procesa el summary para obtener el tipo de docuemnto. 
	 *
	 * @param SimpleXMLElement $entry
	 * @return string
	 */
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
	
}