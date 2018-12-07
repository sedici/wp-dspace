<?php

namespace Wp_dspace\Inc\Core;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.

 * @link       https://www.nuancedesignstudio.in
 * @since      1.0.0
 *
 * @author    Sedici-Manzur Ezequiel
 */

class Activator
{

    /**
     * Short Description.
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {

        $args = array( 'repositorios' => array());

        array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'sedici',
            'domain' => 'sedici.unlp.edu.ar','support'=>true,'protocol'=>'http://','subtype' =>'sedici.subtype:','handle'=>'scope','author'=>'author:','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query','default_query'=>"" )
        );
        array_push($args['repositorios'],array('id'=>uniqid(), 'name' => 'cic',
            'domain' => 'digital.cic.gba.gob.ar','support'=>true,'protocol'=>'https://','subtype' =>'dc.type:','handle'=>'scope','author'=>'author:','base_path'=>'/open-search/discover','format' => 'atom','query'=>'query','default_query'=>"" )
        );

        update_option('config_repositorios',$args);

    }

}
