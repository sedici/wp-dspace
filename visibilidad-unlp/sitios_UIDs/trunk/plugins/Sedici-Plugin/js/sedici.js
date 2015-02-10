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
	var conditionalAuthor = 'p.conditionally-author';
	var checkAuthor = 'p.show-author input:radio[value="author"]';
	
	var conditionaDescription = 'p.conditionally-description';
	var description = 'p.description input:checkbox';
	
	var conditionalFilter = 'p.conditionally-filter';
	var checkFilter = 'p.show-filter';
	
	var conditionalLimit = 'p.conditionally-limit';
	var checkLimit = 'p.limit';
	
	
	
	// binding
	jQuery('p.show-author input:radio').live('change', function() {
		jQuery(conditionalAuthor).toggle(jQuery(checkAuthor).is(':checked'));
	});
	jQuery(description).live('change', function() {
		jQuery(conditionaDescription).toggle();
	});
	jQuery(checkFilter).live('change', function() {
		jQuery(conditionalFilter).toggle();
	});
	jQuery(checkLimit).live('change', function() {
		jQuery(conditionalLimit).toggle();
	});

});

function justNumbers(e) {
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 9)) return true;
	return /\d/.test(String.fromCharCode(keynum));
}
