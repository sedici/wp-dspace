<?php

namespace Wp_dspace\Model;

class SimpleXMLModel
{

	const PREFIJO_TRANSIENT = 'wp_dspace_';

	/**
	 *   @return SimpleXMLElement of result  OpenSearch. 
	 */

	public function saveData($str, $duration = 1){
		// Genero el id para guardar la consulta en la caché de wordpress. Aplico un hash a la url de consulta. 
		$transient_data_id = self::PREFIJO_TRANSIENT . hash('md5', $str);
		$transient_data_permanent_id = $transient_data_id . '_permanent';
		$transient_data = get_transient($transient_data_id);
		if (empty($transient_data)) {
			$request = wp_remote_get($str,array('sslverify' => FALSE));
			if (is_wp_error($request)){
				// Guardo un resultado de forma permanente por si hay algún error en el repositorio a la hora de devolver los datos
				$transient_data = get_transient($transient_data_permanent_id);
			}
			else {
				$transient_data = wp_remote_retrieve_body($request);
				// Guardamos los datos en el transient
				set_transient($transient_data_id, $transient_data, $duration * HOUR_IN_SECONDS);
				set_transient($transient_data_permanent_id, $transient_data);
			}
		}
		return $transient_data;
	}

	public function loadPath($str, $duration = 1)
	{
        $transient_data = $this->saveData($str, $duration);
		try{
			$xml = simplexml_load_string($transient_data, 'SimpleXMLElement');
		}
		catch(Exception $e){
			echo "Lo sentimos, esta solicitud posee caracteres inválidos.";
		}
		return ($xml);
	}

    public function loadJsonPath($str, $duration = 1)
	{
        $transient_data = $this->saveData($str, $duration);
		$json = json_decode($transient_data,true);
		return ($json);
	}
     	 
	/**
	 * Se procesa el summary para obtener el tipo de docuemnto. 
	 *
	 * @param SimpleXMLElement $entry
	 * @return string
	 */
	public function type($entry)
	{
		//return subtype document
		$description = $entry->get_abstract();
		return $description;
	}


	public function date_utf_fotmat($entry)
	{
		$date = $entry->get_raw_date();
		return  date_format($date, "Y-m-d");
	}
	public function date($entry)
	{
		$date = $entry->get_raw_date();
		return date_format($date, "d/m/Y");
	}
	public function year($entry)
	{
		$date = $entry->get_raw_date();
		return date_format($date, "Y");
	}



}
