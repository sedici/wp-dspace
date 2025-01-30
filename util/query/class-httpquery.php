<?php
namespace Wp_dspace\Util\Query;

use SimpleXMLElement;
use Wp_dspace\Model\SimpleXMLModel;


class HttpQuery{    

    /** 
	 * Ejecuta una consulta Http y devuelve el resultado como XML
	 * @param String $url Url a consultar
     * @param Boolean $parseXML False si debemos devolver el resultado plano o TRUE para transformalo a XML Object
	 * @return String/XmlObject  Devuelve la respuesta a la consulta, como Html o XML
	*/
    public function executeQuery($url,$parseXML=false){
        //Ejecuta la consulta
        $data = file_get_contents($url);
        if($parseXML){
            $data = simplexml_load_string($data);
        }
        return $data;
    }

     /** 
	 * Obtiene los tags meta de una URL
	 * @param String $url Url a consultar
     * @param Array $tag_values Contiene los posibles nombres del meta tag  
	 * @return String  Devuelve el valor del meta tag correspondiente.
	*/
    public function getMetaTag($url,$tag_values){
        $tags = get_meta_tags($url);
        foreach ($tag_values as $value){
            if (!empty($tags[$value])){
                return $tags[$value];
            }
        }
        return "";

    }


}

?> 