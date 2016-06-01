jQuery(document).ready(function() {
	// inicializacion
        var divConditionalDescription = 'div.conditionally-description';
        var divDescription = 'div.description-ds';
	var conditionalDescription = 'p.conditionally-description';
	var description = 'p.description-ds input:checkbox';
	
	var conditionalFilter = 'p.conditionally-filter';
	var checkFilter = 'p.show-filter';
	
	var conditionalLimit = 'p.conditionally-limit';
	var checkLimit = 'p.limit';
        
        var filters = new Array ( {selector:description, conditional:conditionalDescription },
                                  {selector:divDescription, conditional:divConditionalDescription },
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
