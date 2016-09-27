jQuery(document).ready(function() {
     
        jQuery('#menu-selector div:gt(0)').hide();
        jQuery('#menu-selector ul li:first a').addClass('aqui');
        jQuery('#menu-selector div:first').addClass('seleccionada');
        jQuery('#menu-selector a').click(function(){
        jQuery('#menu-selector div').removeClass('seleccionada');
        jQuery('#menu-selector a').removeClass('aqui');
        jQuery(this).addClass('aqui');
        jQuery('#menu-selector div').fadeOut(350).filter(this.hash).fadeIn(350);
        jQuery('#menu-selector div').filter(this.hash).addClass('seleccionada');
        //alert(imprimir);
        return false;
         });

});

