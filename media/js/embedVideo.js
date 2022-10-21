;
(function($) {

html=""
function get_data_and_template(method, data_params, divVideo) {
    if (divVideo.children('p .notFound').length == 0){
        divVideo.append('<div class="searching"><div class="loader"></div><p>Buscando videos...</p></div>');
    }
    jQuery.ajax({
        url: params.ajaxurl,
        type: method,
        data: data_params,
        success: function (response) {
                var videos = response;
                divVideo.children('.searching').hide();
                if ((videos.length >0) && (divVideo.children('iframe').length == 0)) {
                    for (var i = 0; i < videos.length; ++i){
                        divVideo.append('<iframe  width="350" height="370" src="'+ videos[i]+ '"></iframe>');                    }
                    ;
                }
                else if ((videos.length == 0) && (divVideo.children('p .notFound').length == 0)) {
                    divVideo.append('&nbsp<p class="notFound" style="color:red;font-weight:bold;">No se encontraron videos.</p>');
                }
            }
            })
    }


$(document).ready(function (){
$(".btn-dspace-show").on('click', function (e) {

    btnUp = $(this).next().show(); // Mostrar boton para colapsar

    btnDown = $(this).first().hide(); // Ocultar boton para mostrar descripción
    link = $(this).parent().children()[1].lastElementChild.href; // Link al elemento en sedici
    var data_params = { action: 'get_videos', instanceData: link};
    description = $(this).next().next(); // Obtener la descripción que esta oculta
    $addVideo = description.children(":first");
    get_data_and_template('GET', data_params, $addVideo);
    description = description.show(); // Mostrar descripción



});

$(".btn-dspace-hide").on('click', function (e) {
    btnUp = $(this).first().hide(); //Oculto el boton para ocultar la descripcion

    description = $(this).next().hide(); //Oculto la descripcion

    btnDown = $(this).prev().first().show(); //Muestro el boton para mostrar la descripcion
});

}) 


})(jQuery);
