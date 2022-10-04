<?php

namespace Wp_dspace\Inc\Admin;

/**
 * Funcionalidad del administrador del plugin
 *
 *
 * @author  Sedici-  Manzur Ezequiel
 */
class Admin
{

    /**
     * ID plugin.
     * @var      string    $plugin_name    ID plugin.
     */
    private $plugin_name;

    /**
     * @var      string    $version     version actual del plugin.
     */
    private $version;

    /**
     * @var      string    $plugin_text_domain    Text domain del plugin.
     */
    private $plugin_text_domain;

    /**
     * @param    string $plugin_name    Nombre plugin.
     * @param    string $version version del plugin.
     * @param     string $plugin_text_domain     text domain Del plugin
     */
    public function __construct($plugin_name, $version, $plugin_text_domain)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;

    }

    /**
     * Registrar estilos en stylesheets admin area.
     *
     * 
     */
    public function register_styles()
    {
        wp_register_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-dspace-admin.css', array(), $this->version, 'all');

    }
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name);

    }

    /**
     * Registrar scripts admin area.
     *
     * 
     */
    public function register_scripts()
    {
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-dspace-admin.js', array("jquery"), null, true);
           
    }
    public function enqueue_scripts($hook){
        /**  scripts para realizar y procesar peticiones ajax*/
         if (('conector-opensearch_page_config-repo'==  $hook) or ('widgets.php' == $hook)) {
            $params = array('ajaxurl' => admin_url('admin-ajax.php'));
            wp_enqueue_script('dspace_ajax_handle', plugin_dir_url(__FILE__) . 'js/ajax_dspace.js', array('jquery'), $this->version, false);
            wp_localize_script('dspace_ajax_handle', 'params', $params);
            wp_enqueue_script('handlebars_dspace', 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.min.js', array('jquery'), '4.0.12', true);
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script($this->plugin_name);

    }
    /**
     * Menu del plugin en el area de admin 
     *
     * 
     */
    public function add_plugin_admin_menu()
    {
        add_menu_page(__('Conector OpenSearch', $this->plugin_text_domain), //page title
            __('Conector OpenSearch', $this->plugin_text_domain), //menu title
            'manage_options', //capability
            $this->plugin_name, //menu_slug
            array($this, 'wp_dspace_pagina')
        ); // pagina para Descripción del plugin
        $ajax_form_page_hook = add_submenu_page(
            $this->plugin_name, //parent slug
            __('Repositorios', $this->plugin_text_domain), //page title
            __('Repositorios', $this->plugin_text_domain), //menu title
            'manage_options', //capability
            'config-repo', //menu_slug
            array($this, 'ajax_form_page_content') // pagina para configurar repositorios.
        );
        //Todo eliminar
      //  add_action('load-' . $html_form_page_hook, array($this, 'loaded_html_form_submenu_page'));
    }

    /**
     * Vistas para cada opción del menu en el area de admin
     * 
     */
    public function wp_dspace_pagina()
    {
        //Fixme include_once dirname(__DIR__) . '/admin/view/html-descrition-plugin.php';
        $url=plugins_url();
        echo "<h1> Pagina de descripción del plugin</h1>
        <p>Este plugin de Wordpress permite a cualquier sitio web hecho en WP recuperar 
        contenidos alojados en repositorios DSpace y mostrarlos dentro de Widgets o como 
        shortcodes dentro de páginas y posts. La recuperación de los contenidos puede realizarse 
        mediante una expresión de búsqueda, que se transforma a OpenSearch, o a partir de una 
        colección particular del repositorio</p>

        
        <a href=$url/wp-dspace/UtilizaciondelPLuginWP-Dspace.docx>Descargar Manual</a>
        
        ";
    }

    
    public function ajax_form_page_content()
    {
        include_once dirname(__DIR__) . '/admin/view/html-form-view-ajax.php';
    }

    /**  
     * Funciones para los request ajax.
     * 
     */
    /**
     * Retorna el html de los template para la libreria handlebarsjs
     */
    public function get_template($template)
    {
        return file_get_contents(dirname(__DIR__) . '/admin/view/templates/' . $template . '.html');
    }
    public function get_repo($id)
    {
        $repo = array_filter($this->get_option_repositorios(), function ($array) use ($id) {
            return ($array['id'] == $id);
        });
        return array_pop($repo);
    }
    public function edit_repo()
    {
        $repo = $this->get_repo($_GET['id']);
        $checked = ($repo['support']) ? 'checked' : '';
        $disabled = ($repo['support']) ? '' : 'disabled';
        $config_subtype = ($repo['support']) ? array('checked' => 'checked', 'disabled' => '', 'required' => 'required') : array('checked' => '', 'disabled' => 'disabled', 'required' => '');
        //var_dump($repo['queryMethod']);
        $config_queryMethod = ($repo['queryMethod'] == "true") ? array('checked' => 'checked', 'disabled' => '', 'required' => 'required', 'hiddenOpen' => 'hidden', 'hiddenApi' => '') : array('checked' => '', 'disabled' => 'disabled', 'hiddenOpen' => '', 'hiddenApi' => 'hidden');
        //var_dump($config_queryMethod);
        $response['template'] = $this->get_template($_GET['template']);
        $response['result'] = array('titulo' => 'Editar', 'repo' => $repo, 'action' => 'form-update-repo', 'config_subtype' => $config_subtype, 'config_queryMethod' => $config_queryMethod);
        
        wp_send_json($response);
    }
    public function delete_repo()
    {
        $id = $_POST['id'];
        $repositorios = array_filter($this->get_option_repositorios(), function ($array) use ($id) {
            return ($array['id'] != $id);
        });
        $args['repositorios'] = $repositorios;
        update_option('config_repositorios', $args);
        $response['template'] = $this->get_template($_POST['template']);
        $notificacion = array("mensaje" => 'Repositorio elminado', 'type_notice' => 'notice-success');
        $response['result'] = array('notificacion' => $notificacion);
        wp_send_json($response);

    }
    public function new_repo()
    {
        $response['template'] = $this->get_template($_GET['template']);
        $config_subtype = array('checked' => 'checked', 'disabled' => '', 'required' => 'required');
        $response['result'] = array('titulo' => 'Agregar nuevo', 'action' => 'form-new-repo', 'config_subtype' => $config_subtype);
        wp_send_json($response);
    }
    public function add_form()
    {
        $config_subtype = array( 'required' => 'required');
        $response['template'] = $this->get_template($_GET['template']);
        $respose['result'] = array('config_subtype' => $config_subtype);
        wp_send_json($response);
    }
    public function get_option_repositorios()
    {
        return get_option('config_repositorios')['repositorios'];
    }
    public function get_repositorios()
    {
        $response['result'] = $this->get_option_repositorios();
        $response['template'] = $this->get_template($_GET['template']);
        wp_send_json($response);
    }
    

    public function add_repo()
    {
        $repo = $this->add_repo_in_option($_POST['repo']);
        $response['template'] = $this->get_template($_POST['template']);
        if($repo){
            $mensaje= 'Se agrego de forma correcta';
            $type_notice = 'notice-success';
        }
        else{
            $mensaje= 'Ya existe un repositorio con ese nombre ';
            $type_notice = 'notice-error';
        }
        $notificacion = array("mensaje" => $mensaje, 'type_notice' => $type_notice);
        $response['result'] = array('notificacion' => $notificacion);
        wp_send_json($response);

    }
    public function validate_option_params($repo)
    {
        if (isset($repo['subtype']) and $repo['subtype'] != "") {
            $repo['support'] = true;
        } else {
            $repo['support'] = false;
        }

        return $repo;
    }


    private function validate_name_in_repo($name_repo,$repositorios){
        $repos_name=array_column($repositorios, 'name');
        return in_array($name_repo,$repos_name);

    }
    private function add_repo_in_option($repo)
    {
        $repositorios = $this->get_option_repositorios();
        if(! $this->validate_name_in_repo($repo['name'],$repositorios)){
            $repo['id'] = uniqid();
            $repo = $this->validate_option_params($repo);
            array_push($repositorios, $repo);
            $args['repositorios'] = $repositorios;
            update_option('config_repositorios', $args);
        }
        else
            $repo=false;
        return $repo;

    }
    public function update_repo_in_options($repo)
    {
        $repositorios = $this->get_option_repositorios();
        $repo = $this->validate_option_params($repo);
        foreach ($repositorios as $key => $value) {
            if ($value['id'] == $repo['id']) {
                $repositorios[$key] = $repo;
            }
        }

        $args['repositorios'] = $repositorios;
        update_option('config_repositorios', $args);
        return $repo;
    }
    public function update_repo()
    {
        $repo = $this->update_repo_in_options($_POST['repo']);

        $response['template'] = $this->get_template($_POST['template']);
        $notificacion = array("mensaje" => "El repositorio " . $repo['name'] . " se actualizo correctamente", 'type_notice' => 'notice-success');
        $response['result'] = array('notificacion' => $notificacion);
        wp_send_json($response);
    }
    public function notice_result()
    {
        $response['template'] = $this->get_template($_GET['template']);
        $response['result'] = array('notificacion' => $_GET['notificacion']);
        wp_send_json($response);
    }

    public function load_widget_dspace()
    {
        register_widget('\Wp_dspace\Dspace_Widget');
    }

    public function LoadShortcode($atts)
    {
        $shortcode = new \Wp_dspace\Shortcode();
        ob_start();
        $shortcode->plugin_sedici($atts);
        $res = ob_get_clean();
        return $res;
    }
    public function DspaceShortcode($atts)
    {
        return $this->LoadShortcode($atts);
    }
    

    public function createFilterGetRepositorios(){
        $repositorios= $this->get_option_repositorios();
        apply_filters( 'get_repositorios', $repositorios );
    }

    private function buildInstance($post_data) {
        $instance = array();
        $str_to_remove = array("widget-dspace[__2__]","[","]");
        foreach($post_data as $values) {
            $clave = "";
            $valor = "";
            foreach($values as $key=>$value) { 
                //echo " K: $key ==> $value";
                if ($key == 'name')
                    $clave= str_replace($str_to_remove,"",$value);
                elseif ($key == 'value')
                    $valor =$value;
                
            }
            $instance[$clave] = $valor;
        }

        return $instance;
    }

    public function show_shortcode(){
        $instance= (isset($_GET['instanceData']) ? 
                    $_GET['instanceData'] : 
                    $_POST['instanceData']);
        $shortcode_Gen= new \Wp_dspace\View\ShowShortcode();
        $shortcode = $shortcode_Gen->show_shortcode($instance);
        wp_send_json($shortcode);
    }

    // Function to get a repository by Name
    public function get_repo_support(){
        $name = $_GET['name'];
        $array[0]= array_filter($this->get_option_repositorios(), function ($array) use ($name) {
      return ($array['name'] == $name);
    });
    $response['result'] = $array;
    wp_send_json($response);
}

}
