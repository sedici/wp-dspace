function get_data_and_template(method, data_params, selector, refreshCallback, function_params) {
	jQuery.ajax({
		url: params.ajaxurl,
		type: method,
		data: data_params,
		success: function (response) {
			if (typeof response['template'] !== 'undefined'){
			  var source = response['template'];
		      var template = Handlebars.compile(source);
			  var result = response['result'];
			  var html = template({ result });
			}
			else {
			  var html = response;
			}
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


function do_onchange() {
	support_subtype(this);
}

function check_v(){
	checkVersion(this);
}

function get_form_repo(){
	var queryMethodVal = jQuery('input[name=queryMethod]');
	if( queryMethodVal.is(':checked')){
       var apiMethod = true;

	}
	else{
		var apiMethod = false;
	}
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
	subject: jQuery('input[name=subject]').val(),
	degree : jQuery('input[name=degree]').val(),
	queryMethod : apiMethod,
    //FIXME : Lo tengo que cambiar por las funciones get api repo y get opensearch repo
	apiUrl: jQuery('input[name=apiUrl]').val(),
	default_query: jQuery('input[name=default_query]').val(),
	id: jQuery('input[name=id_repo]').val(),
};
}

function get_api_repo(){
	return{
		name: jQuery('input[name=name]').val(),
	    apiUrl: jQuery('input[name=dspaceUrl]').val()
	}
}

function get_opensearch_repo(){
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
		subject: jQuery('input[name=subject]').val(),
		degree : jQuery('input[name=degree]').val(),
		queryMethod : false,
		default_query: jQuery('input[name=default_query]').val(),
		id: jQuery('input[name=id_repo]').val(),
	}
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

function checkVersion(item) {
	var $item = jQuery(`input[name=queryMethod]`);
	if ($item.is(':checked') | ($item.checked == true)) {
		document.getElementById("openSearchG").disabled = true;
		document.getElementById("apiRestG").disabled = false;
		document.getElementById("openSearchG").hidden= true;
		document.getElementById("apiRestG").hidden = false;

	}
	else {
		document.getElementById("openSearchG").disabled = false;
		document.getElementById("apiRestG").disabled = true;
		document.getElementById("openSearchG").hidden= false;
		document.getElementById("apiRestG").hidden = true;
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

function updateSubtype(repo_name){
	jQuery.ajax({
		url: params.ajaxurl,
		type: 'GET',
		data: {action: 'get_repo_support',name: repo_name},
		success: function (response) {
					  var respuesta = response['result'];
					  var div =document.getElementById("subtypeDiv");
					  respuesta = respuesta[0];
					  respuesta = respuesta[Object.keys(respuesta)[0]];
					  if(!respuesta['support']){
						if (!div.classList.contains("noDisplayDspace")){
                            div.classList.add("noDisplayDspace");
						}						
					  }
					  else{
						if(div.classList.contains("noDisplayDspace")){
                          div.classList.remove("noDisplayDspace");
						}
					  }
				}
	});
}


(function( $ ) {
	'use strict';

	$(document).ready(function(){
		$(document.body).on('click','.agregar-nuevo-repo', function () {
			var data_params = { action: 'new_repo', template: 'form-repo' };
			get_data_and_template('GET', data_params, 'form-repo');
		});

	   var $WidgetForm = jQuery("input[name^='widget-dspace']").parents("form");
		$(document.body).on('change',$WidgetForm,function (e){
			var $target = jQuery(e.target);
			var targetForm = $target.parents('form').serializeArray();
			var data_params = { action: 'show_shortcode', instanceData: targetForm};
			
			//Get widget number
			let obj = targetForm.find(o => o.name === 'widget_number');

			let search = "widget-dspace["+obj.value+"][config]";
			let obj2 = targetForm.find(o => o.name === search);

			updateSubtype(obj2.value);		
			get_data_and_template('POST', data_params, 'view-Shortcode');			

		});

		
		$(document.body).on('click','.editar-repo' ,function () {
			var repo_id = jQuery(this).attr('id_repo');
			var data_params = { action: 'edit_repo', template: 'form-repo', id: repo_id };
			get_data_and_template('GET', data_params, 'form-repo');
		});
		$(document.body).on('click','.emilinar-repo', function () {
			if (confirm("¿Estas seguro?") ) {
			var repo_id = jQuery(this).attr('id_repo');
			
			var data_params = { action: 'delete_repo', template: 'notice', id: repo_id };
			get_data_and_template('POST', data_params, 'notice_result',after_delete_repo);
		}
		});
		
		
		$(document.body).on('submit','#form-new-repo', function (e) {
			var repo =  get_form_repo();
			var data_params = { action: 'add_repo', template: 'notice', repo: repo };
			var params_fuction ={id_element: '#template-repo'}
			get_data_and_template('POST', data_params, 'notice_result',after_add_and_update_repo,params_fuction);
			e.preventDefault();
		});
		$(document.body).on('submit','#form-update-repo', function (e) {
			var repo =  get_form_repo();
			var data_params = { action: 'update_repo', template: 'notice', repo: repo };
			var params_fuction ={id_element: '#template-repo'}
			get_data_and_template('POST', data_params, 'notice_result',after_add_and_update_repo,params_fuction);
			e.preventDefault();
		});
		
		$(document.body).on('change', ".checkSupport",do_onchange);
		$(document.body).on('change', ".checkVersion",check_v);
		$(".editar-repo button button-primary").on('click', ".checkVersion",check_v);
		
	});
})( jQuery );