function get_data_and_template(method, data_params, selector, refreshCallback, function_params) {
	jQuery.ajax({
		url: params.ajaxurl,
		type: method,
		data: data_params,
		success: function (response) {
			console.log(response);
			var source = response['template'];
			var template = Handlebars.compile(source);
			var result = response['result'];
			var html = template({ result });
			jQuery('#' + selector).html(html);
			if ( refreshCallback ) {
				refreshCallback(function_params);
			}
		}
	});
}
jQuery(document).ready(function ($) {
	init();
	function init() {
		list_repositorios();
	}
	

	Handlebars.registerHelper('toUpperCase', function (str) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	});

});

jQuery('.editar-repo').live('click', function () {
	var repo_id = jQuery(this).attr('id_repo');
	var data_params = { action: 'edit_repo', template: 'form-repo', id: repo_id };
	get_data_and_template('GET', data_params, 'form-repo');

});
jQuery('.emilinar-repo').live('click', function () {
	if (confirm("¿Estas seguro?") ) {
	var repo_id = jQuery(this).attr('id_repo');
	
	var data_params = { action: 'delete_repo', template: 'notice', id: repo_id };
	get_data_and_template('POST', data_params, 'notice_result',after_delete_repo);
}
});

jQuery('.agregar-nuevo-repo').live('click', function () {
	var data_params = { action: 'new_repo', template: 'form-repo' };
	get_data_and_template('GET', data_params, 'form-repo');
});
jQuery('#form-new-repo').live('submit', function (e) {
	var repo =  get_form_repo();
	var data_params = { action: 'add_repo', template: 'notice', repo: repo };
	var params_fuction ={id_element: '#template-repo'}
	get_data_and_template('POST', data_params, 'notice_result',after_add_and_update_repo,params_fuction);
	e.preventDefault();
});
jQuery('#form-update-repo').live('submit', function (e) {
	var repo =  get_form_repo();
	var data_params = { action: 'update_repo', template: 'notice', repo: repo };
	var params_fuction ={id_element: '#template-repo'}

	get_data_and_template('POST', data_params, 'notice_result',after_add_and_update_repo,params_fuction);
	e.preventDefault();
});


jQuery(".checkSupport").live('change', do_onchange);

function do_onchange() {
	support_subtype(this);
}
function get_form_repo(){
 return {
	name: jQuery('input[name=name]').val(),
	protocol: jQuery('input[name=protocol]').val(),
	domain: jQuery('input[name=domain]').val(),
	base_path: jQuery('input[name=base_path]').val(),
	format: jQuery('input[name=format]').val(),
	query: jQuery('input[name=query]').val(),
	handle: jQuery('input[name=handle]').val(),
	subtype: jQuery('input[name=subtype]').val(),
	author: jQuery('input[name=author]').val(),
	default_query: jQuery('input[name=default_query]').val(),
	id: jQuery('input[name=id_repo]').val(),
};
}

function support_subtype(item) {
	var $item = jQuery(item);
	var elem = jQuery(`input[name=subtype]`);
	if ($item.is(':checked')) {
		elem.prop('disabled', false);
		elem.prop('required', true);
	}
	else {
		elem.prop('disabled', true);
		elem.prop('required', false);
		elem.prop('value','');
	}


}
function tasks_after_all(){
	list_repositorios();
	jQuery("#notice").fadeOut(5000);
}
function after_add_and_update_repo(params){
	tasks_after_all();
	jQuery(params.id_element).remove();
	
}
function after_delete_repo(params){
	tasks_after_all();
}
function list_repositorios() {
	var data_params = { action: 'get_repositorios', template: 'list_repo' };
	get_data_and_template('GET', data_params, 'list_repo');
}
// $(document).on('click', '#prueba', function (e) {
// 	e.preventDefault();
// 	link = $(this);
// 	id = 1

// 	$.ajax({
// 		url: params.ajaxurl,
// 		type: 'post',
// 		data: {
// 			action: 'prueba',
// 			id_post: id
// 		},
// 		beforeSend: function () {
// 			link.html('Cargando ...');
// 		},
// 		success: function (resultado) {
// 			link.html(resultado);
// 		}

// 	});

// });