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

	var conditionalDescription = 'p.conditionally-description';
	var description = 'p.description-ds input:checkbox';
	
	var conditionalFilter = 'p.conditionally-filter';
	var checkFilter = 'p.show-filter';
	
	var conditionalLimit = 'p.conditionally-limit';
	var checkLimit = 'p.limit';
        
        var filters = new Array ( {selector:description, conditional:conditionalDescription } ,
                                  {selector:checkFilter, conditional:conditionalFilter},
                                  {selector:checkLimit, conditional:conditionalLimit});
        
	
	// binding
	jQuery.each( filters, function( i, value ) {
            jQuery(value.selector).live('change', function() {
		jQuery(value.conditional).toggle();
         }); 
        });

});

function justNumbers(e) {
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 9)) return true;
	return /\d/.test(String.fromCharCode(keynum));
}
