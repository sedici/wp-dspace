<?php

namespace Wp_dspace\Inc\Core;

/**
 * 
 *
 * Esta clase se ejecuta cuando se activa el plugin

 * 
 * @since      1.0.0
 *
 * @author    Sedici-Manzur Ezequiel
 */

class Activator
{

    /**
     * Agrega los repositorios de cic,sedici y conicet por defecto.
     * 
     */
    public static function activate()
    {

        $args = array( 'repositorios' => array());

        array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'sedici',
            'domain' => 'sedici.unlp.edu.ar','support'=>true,'protocol'=>'http','subtype' =>'sedici.subtype:','queryMethod'=>false,'handle'=>'scope','author'=>'author:','subject'=>'subject:','degree'=>'thesis.degree.name','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query','default_query'=>"")
        );
        array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'cic',
            'domain' => 'digital.cic.gba.gob.ar','support'=>true,'protocol'=>'https','subtype' =>'dc.type:','queryMethod'=>true, 'apiUrl'=>'https://host170.sedici.unlp.edu.ar/server/api' ,'handle'=>'scope','author'=>'author:','subject'=>'subject:','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query','default_query'=>"" )
		);
		array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'conicet',
            'domain' => 'ri.conicet.gov.ar','support'=>false,'protocol'=>'https','subtype' =>'','queryMethod'=>false,'handle'=>'scope','author'=>'dc.contributor.author:','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query','default_query'=>"*" )
        );

        update_option('config_repositorios',$args);

    }

}
