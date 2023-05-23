function justNumbers(e) {
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 9)) return true;
    return /\d/.test(String.fromCharCode(keynum));
}

// jQuery(".checkSupport").live('change', do_onchange);

// function do_onchange() {
//     support_subtype(this);
// }

// function support_subtype(item){
//     var $item = jQuery(item);
//     var id = $item.attr('id');
    
//     var elem = jQuery(`input[name='${id}[subtype]']`);
//     var campo = jQuery(`.${id}.subtype`);
//     if ($item.is(':checked')) {
//         if (elem.val() == "false")
//             elem.attr("value", "");
//         campo.show();
//     }
//     else {
//         campo.hide();
//         elem.attr("value", false);
//     }


// }

(function( $ ) {
	'use strict';

	$(document).ready(function(){
        var divConditionalDescription = 'div.conditionally-description';
        var divDescription = 'div.description-ds';
        var conditionalDescription = 'p.conditionally-description';
        var description = 'p.description-ds input:checkbox';
        var conditionalFilter = 'p.conditionally-filter';
        var checkFilter = 'p.show-filter';
    
        var conditionalLimit = 'p.conditionally-limit';
        var checkLimit = 'p.limit';
    
        var filters = new Array({ selector: description, conditional: conditionalDescription },
            { selector: divDescription, conditional: divConditionalDescription },
            { selector: checkFilter, conditional: conditionalFilter },
            { selector: checkLimit, conditional: conditionalLimit });
            // binding
        $.each($(".checkSupport"),function(){
            
        })
        $.each(filters, function (i, value) {
            $(document.body).on('change',value.selector, function () {
                $(value.conditional).toggle();
            });
        });
        
    
        $(document.body).on('change', "#origen select",function () {
            var selection = $(this).val();
            if ($(`#option_${selection}`).attr('support')){
                $("div.conditional_config").show();
            }else {
                $("div.conditional_config").hide();
            }
        });

        $(".btn-dspace-show").on('click', function () {
        });
    
		
	});
})( jQuery );