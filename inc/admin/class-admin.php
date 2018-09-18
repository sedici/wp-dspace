<?php

namespace Wp_dspace\Inc\Admin;

/**
 * Funcionalidad del administrador del plugin
 *
 *
 * @author    Manzur Ezequiel
 */
class Admin {

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
	 * @param    string $plugin_name	The name of this plugin.
	 * @param    string $version	The version of this plugin.
	 * @param	 string $plugin_text_domain	The text domain of this plugin
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_styles(){
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-dspace-admin.css', array(), $this->version, 'all' );

	}
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name);

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts(){
		wp_register_script (  $this->plugin_name, plugin_dir_url(  __FILE__ ).'js/wp-dspace-admin.js',  array ("jquery"), null, true);

	}
	public function enqueue_scripts() {
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script($this->plugin_name );
	}

	/**
	 * Callback for the admin menu
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(	__( 'Dspace', $this->plugin_text_domain ), //page title
			__( 'Dspace', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_name, //menu_slug
			array( $this, 'wp_dspace_pagina' )
		);
		$html_form_page_hook = add_submenu_page(
			$this->plugin_name, //parent slug
			__( 'Administración form repo ', $this->plugin_text_domain ), //page title
			__( 'Configurar repositorios', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			'theme-op-settings', //menu_slug
			array( $this, 'html_form_page_content' ) //callback for page content
		);
		add_action( 'load-'.$html_form_page_hook, array( $this, 'loaded_html_form_submenu_page' ) );
	}
	public function wp_dspace_pagina(){
		echo "<h1> Pagina de descripción del plugin</h1>";
	}
	/*
	 * Callback for the add_submenu_page action hook
	 *
	 * The plugin's HTML form is loaded from here
	 *
	 * @since	1.0.0
	 */
	public function html_form_page_content() {
		//show the form

		include_once (dirname(__DIR__). '/admin/view/html-form-view.php' );
	}

	/*
	 * Callback for the add_submenu_page action hook
	 *
	 * The plugin's HTML Ajax is loaded from here
	 *
	 * @since	1.0.0
	 */
	public function ajax_form_page_content() {
		include_once( 'views/partials-ajax-form-view.php' );
	}

	/*
	 * Callback for the load-($html_form_page_hook)
	 * Called when the plugin's submenu HTML form page is loaded
	 *
	 * @since	1.0.0
	 */
	public function loaded_html_form_submenu_page() {
		// called when the particular page is loaded.
	}

	/*
	 * Callback for the load-($ajax_form_page_hook)
	 * Called when the plugin's submenu Ajax form page is loaded
	 *
	 * @since	1.0.0
	 */
	public function loaded_ajax_form_submenu_page() {
		// called when the particular page is loaded.
	}

	/**
	 *
	 * @since    1.0.0
	 */
	public function the_form_response() {

		if (isset($_POST['delete_repositorios'])) {
			foreach ($_POST['delete_repositorios'] as $item)
				unset($_POST['titulo'][$item]);
			unset($_POST['autor'][$item]);
		}
		if(isset($_POST['titulo']) & isset($_POST['autor']))
		{
			$args = array( 'Canciones' => array());
			$array=array_merge($_POST['titulo'] , $_POST['autor']);
			foreach ($_POST['titulo'] as $key=>  $item)
				array_push($args['Canciones'],array('title' =>$item,
				                                    'autor' => $_POST['autor'][$key])
				);
		}
		if( !empty($_POST['titulo_nuevo']) & !empty($_POST['autor_nuevo'])){
			array_push($args['Canciones'],array('title' => $_POST['titulo_nuevo'],
			                                    'autor' => $_POST['autor_nuevo'])
			);
			$type    = 'update_repository';
			$message = 'TODO OK';

		}
		else{
			if( !empty($_POST['titulo_nuevo']) | !empty($_POST['autor_nuevo'])) {
				$type    = 'error';
				$message = 'ERRORRRRR LPM';
			}
		}
		update_option('eze_canciones',$args);
		$this->custom_redirect($type,$message);


	}

	/**
	 * Redirect
	 *
	 * @since    1.0.0
	 */
	public function custom_redirect( $type, $message ) {
		wp_redirect( esc_url_raw( add_query_arg( array(
			$type => $message,
			'dspace_response' => $_POST,
		),
			admin_url('admin.php?page=theme-op-settings')
		) ) );


	}


	/**
	 * Print Admin Notices
	 *
	 * @since    1.0.0
	 */
	public function print_plugin_admin_notices() {
		if (isset($_REQUEST['error']) || isset($_REQUEST['update_repository'])) {
			if (isset($_REQUEST['error'])){ ?>
				<div class="notice notice-error"><p><?php echo $_REQUEST['error']?></p></div> <?php }
			if (isset($_REQUEST['update_repository'])){ ?>
				<div class="notice notice-success"><p><?php echo $_REQUEST['update_repository']?></p></div> <?php }
		}
		else {
			return;
		}

	}


}
