/**
 * Plugin Name: Sedici-Plugin
 * Plugin URI: http://sedici.unlp.edu.ar/
 * Description: This plugin connects the repository SEDICI in wordpress, with the purpose of showing the publications of authors or institutions
 * Version: 1.0
 * Author: SEDICI - Paula Salamone Lacunza
 * Author URI: http://sedici.unlp.edu.ar/
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 */


jQuery(document).ready(function() {
	// inicializacion
	var conditionala = 'p.conditionally-autor';
	var checka = 'p.mostrar-autor input:radio[value="autor"]';
	// jQuery(conditionala).toggle(jQuery(checka).is(':checked'));
	var conditional = 'p.conditionally-loaded';
	var check = 'p.description input:checkbox';
	// jQuery(conditional).toggle(jQuery(check).is(':checked'));
	var conditionalf = 'p.conditionally-filtro';
	var checkf = 'p.mostrarfiltro';
	// jQuery(conditionalf).toggle(jQuery(checkf).is(':not(:checked)'));

	var conditionall = 'p.conditionally-limitar';
	var checkl = 'p.limitar';
	jQuery(checkl).live('change', function() {
		jQuery(conditionall).toggle();
	});
	
	
	// binding
	jQuery('p.mostrar-autor input:radio').live('change', function() {
		jQuery(conditionala).toggle(jQuery(checka).is(':checked'));
	});
	jQuery(check).live('change', function() {
		jQuery(conditional).toggle();
	});
	jQuery(checkf).live('change', function() {
		jQuery(conditionalf).toggle();
	});

});

function justNumbers(e) {
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 9)) return true;
	return /\d/.test(String.fromCharCode(keynum));
}
