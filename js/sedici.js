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
