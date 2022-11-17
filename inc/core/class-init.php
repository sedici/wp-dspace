<?php
namespace Wp_dspace\Inc\Core;
use Wp_dspace as DS;
use Wp_dspace\Inc\Admin as Admin;
use Wp_dspace\Inc\Frontend as Frontend;


/**
 * Clase para administar el plugin, los hook, internacionalizacion.
 *
 * @author Sedici-Manzur Ezequiel
 */
class Init {

	/**
	 *
	 * @var      Loader    $loader    es el encargado de mantener y administar los hooks.
	 */
	protected $loader;
	/**

	 * @var      string    $plugin_base_name    string para identificar al plugin
	 */
	protected $plugin_basename;
	/**
	 * @var      string    $version   Version actual del plugin.
	 */
	protected $version;
	/**
	 * @var      string    $plugin_text_domain    Text domain del plugin.
	 */
	protected $plugin_text_domain;
	//Define la funcionalidad del plugin
	public function __construct() {

		$this->plugin_name = DS\PLUGIN_NAME;
		$this->version = DS\PLUGIN_VERSION;
		$this->plugin_basename = DS\PLUGIN_BASENAME;
		$this->plugin_text_domain = DS\PLUGIN_TEXT_DOMAIN;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		//$this->define_public_hooks();
	}
	/**
	 *
	 * - Loader - Administra  los hooks del plugin.
	 * - Internationalization_i18n - Define la funcionalidad de internacionalización.
	 * - Admin - Define todos los hooks de admin.
	 * - Frontend - Defines los hooks de la parte publica del sitio.
	 *
	 * @access    private
	 */
	private function load_dependencies() {
		$this->loader = new Loader();

	}
	/**
	 * Defina la configuración regional del plugin.
	 *
	 * @access    private
	 */
	private function set_locale() {

		$plugin_i18n = new Internationalization_i18n( $this->plugin_text_domain );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * Callbacks are documented in inc/admin/class-admin.php
	 *
	 * @access    private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin\Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

		
		//Registro de estilos y scripts
		$this->loader->add_action('init',$plugin_admin,'register_styles');
		$this->loader->add_action('init',$plugin_admin,'register_scripts');
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//Add a top-level admin menu for our plugin
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		
		// request ajax para admin. 
		$this->loader->add_action( 'wp_ajax_get_repositorios', $plugin_admin, 'get_repositorios');
		$this->loader->add_action( 'wp_ajax_edit_repo', $plugin_admin, 'edit_repo');
		$this->loader->add_action( 'wp_ajax_get_repo_support', $plugin_admin, 'get_repo_support');
		$this->loader->add_action( 'wp_ajax_new_repo', $plugin_admin, 'new_repo');
		$this->loader->add_action( 'wp_ajax_add_repo', $plugin_admin, 'add_repo');
		$this->loader->add_action( 'wp_ajax_notice_result', $plugin_admin, 'notice_result');
		$this->loader->add_action( 'wp_ajax_delete_repo', $plugin_admin, 'delete_repo');
		$this->loader->add_action( 'wp_ajax_update_repo', $plugin_admin, 'update_repo');
		$this->loader->add_action( 'wp_ajax_show_shortcode', $plugin_admin, 'show_shortcode');

		// View function
		$this->loader->add_action('wp_ajax_get_videos', $plugin_admin ,'get_videos');
		$this->loader->add_action('wp_ajax_nopriv_get_videos', $plugin_admin ,'get_videos');


		// Register admin notices
		// $this->loader->add_action( 'admin_notices', $plugin_admin, 'print_plugin_admin_notices');
		// Inicio widget
		$this->loader->add_action( 'widgets_init',$plugin_admin,'load_widget_dspace' );
		//Creo filtro para pasar repositorios.
        $this->loader->add_action('init',$plugin_admin,'createFilterGetRepositorios');
		// Incio shorcode
		add_shortcode ( 'get_publications', array($plugin_admin,'DspaceShortcode' ));

	}
	/**
	 * Registrar hooks parte pública. 
	 *
	 * @access    private
	 */
	private function define_public_hooks() {
		//Fixme Definir la clase frontend
		// $plugin_public = new Frontend\Frontend( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Ejecuta el cargado Loader. 
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Nombre del complemento utilizado para identificarlo de forma única en el contexto de
	 * WordPress y para definir la funcionalidad de internacionalización.
	 */
	public function get_plugin_name() {
		return $this->plugin_basename;
	}

	/**
	 *
	 * @return    Loader   
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 *
	 * @return    string   Número de version del plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 *
	 * @since     1.0.0
	 * @return    string    Text domain del plugin.
	 */
	public function get_plugin_text_domain() {
		return $this->plugin_text_domain;
	}

}
