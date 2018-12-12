<?php

namespace Wp_dspace\Inc\Core;

/**
 * Define la funcionalidad de internalizacion.
 *
 *
 *
 * @author     Sedici - Manzur Ezequiel
 */
class Internationalization_i18n {

	private $text_domain;

	/**
	 *
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_text_domain ) {

		$this->text_domain = $plugin_text_domain;

	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->text_domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
