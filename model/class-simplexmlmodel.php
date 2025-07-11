<?php

namespace Wp_dspace\Model;

class SimpleXMLModel
{

	const PREFIJO_TRANSIENT = 'wp_dspace_';

	/**
	 * Guarda en caché los datos obtenidos de una URL para optimizar consultas repetidas.
	 * 
	 * @param string $str URL de la que se obtendrán los datos.
	 * @param int $duration Duración en semanas para mantener los datos en caché (por defecto, 1 semana).
	 * @return SimpleXMLElement of result  OpenSearch. 
	 */

	public function saveData($str, $duration = 1){
		// Genero el id para guardar la consulta en la caché de wordpress. Aplico un hash a la url de consulta. 
		$transient_data_id = self::PREFIJO_TRANSIENT . hash('md5', $str);
		$transient_data_permanent_id = $transient_data_id . '_permanent';
		$transient_data = get_transient($transient_data_id);
		if (empty($transient_data)) {
			  $args = [
      			  'sslverify' => false,
        			'headers'   => [
            		'Referer' => get_site_url(), // <-- AÑADIMOS LA CABECERA AQUÍ
        ],
    ];

			$request = wp_remote_get($str, $args);
			if (is_wp_error($request)){
				// Guardo un resultado de forma permanente por si hay algún error en el repositorio a la hora de devolver los datos
				$transient_data = get_transient($transient_data_permanent_id);
			}
			else {
				// La conexión fue exitosa, ahora validamos el cuerpo de la respuesta.
            	$body = wp_remote_retrieve_body($request);

				libxml_use_internal_errors(true);

				$xml = simplexml_load_string($body);
				if ($xml === false) {
					// el cuerpo no es un XML valido
					//no lo cacheamos y en su lugar buscamos el resultado permanente
					$transient_data = get_transient($transient_data_permanent_id);
				} else {
					// El cuerpo es un XML valido, lo guardamos en la caché
					$transient_data = $body;
					set_transient($transient_data_id, $transient_data, $duration * WEEK_IN_SECONDS);
					set_transient($transient_data_permanent_id, $transient_data);

				
				// Guardamos los datos en el transient
				set_transient($transient_data_id, $transient_data, $duration * WEEK_IN_SECONDS);
				set_transient($transient_data_permanent_id, $transient_data);
			}
			libxml_clear_errors();
			libxml_use_internal_errors(false);



		}
		return $transient_data;
		} 
	}

	/**
     * Elimina una entrada de caché específica basada en la URL de la consulta.
     * @param string $url La URL de la consulta cuya caché se va a eliminar.
     */
    public function deleteCacheByUrl($url)
    {
        $transient_data_id = self::PREFIJO_TRANSIENT . hash('md5', $url);
        
        // Elimina el transient normal
        delete_transient($transient_data_id);

        // Elimina también el transient de respaldo permanente
        $transient_data_permanent_id = $transient_data_id . '_permanent';
        delete_transient($transient_data_permanent_id);
    }





	
	/**
	 * Carga datos desde una URL, los limpia de caracteres especiales y los convierte en un objeto SimpleXMLElement.
	 * 
	 * @param string $str URL de la cual se obtendrán los datos.
	 * @param int $duration Duración en semanas para mantener los datos en caché (no se utiliza directamente, siempre se fija en 1).
	 * @return SimpleXMLElement Objeto XML generado a partir de los datos obtenidos de la URL.
	 */
	public function loadPath($str, $duration = 1)
	{
		# Fixme : cuando se envia el parametro duration, tiene un valor muy alto que no se de dónde sale
		// Por eso lo seteo aca a mano
		$duration = 1;
	
        $transient_data = $this->saveData($str, $duration);
		
		// FIXME: Sería mejor incorporar una libreria, esta solución no considera muchos caracteres especiales
		$some_special_chars = array("√");
		$transient_data = str_replace($some_special_chars, "", $transient_data);
		$xml = simplexml_load_string($transient_data, 'SimpleXMLElement');
		return ($xml);
	}

	/**
	 * Carga datos desde una URL, los decodifica desde formato JSON y los retorna como un array asociativo.
	 * 
	 * @param string $str URL de la cual se obtendrán los datos.
	 * @param int $duration Duración en semanas para mantener los datos en caché (por defecto, 1 semana).
	 * @return array|null Datos decodificados en un array asociativo, o null si la decodificación falla.
	 */
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
		if(!empty($date)){
			return  date_format($date, "Y-m-d");

		}
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
