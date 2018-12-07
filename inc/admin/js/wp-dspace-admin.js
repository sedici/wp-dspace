jQuery(document).ready(function () {
    
    // inicializacion
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
    jQuery.each(jQuery(".checkSupport"),function(){
        
    })
    jQuery.each(filters, function (i, value) {
        jQuery(value.selector).live('change', function () {
            jQuery(value.conditional).toggle();
        });
    });

    jQuery("#origen select").live('change', function () {
        var selection = jQuery(this).val();
        if (jQuery(`#option_${selection}`).attr('support')){
            jQuery("div.conditional_config").show();
        }else {
            jQuery("div.conditional_config").hide();
        }
    });

});


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