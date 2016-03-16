<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of config
 *
 * @author paw
 */
define ( Q_RPP, '100' );
define ( Q_FORMAT, 'atom' );
define ( Q_SORTBY, '3' );
define ( Q_ORDER, 'desc' );
define ( S_CONECTOR2, '%5C' );
define ( S_CONECTOR3, '%7C' );
define (S_CONECTOR4 , '%2C');
define (S_CONECTOR5, '\+');
define ( _PROTOCOL, "http://" );
define ( _DOMAIN, "sedici.unlp.edu.ar" );
define ( _BASE_PATH, "/open-search/discover" );
define ( SQ_HANDLE, "scope");
define ( Q_QUERY, "query");
define ( SQ_AUTHOR, "sedici.creator.person:");
define (SQ_SUBTYPE, "sedici.subtype:");
define (S_TEXT , 150);
define (S_CACHE,86400);
define (S_START, 0);
define (S_FILTER , '/discover?fq=author_filter%3A');
define (S_SEPARATOR, '\|\|\|');

function defaultCache(){
    return S_CACHE * 7 ;
}
function conector() {
	return S_CONECTOR2 . '+';
}
function get_conector() {
	return (S_CONECTOR2 . S_CONECTOR3 . S_CONECTOR2 . S_CONECTOR3 . S_CONECTOR2 . S_CONECTOR3);
}
function get_base_url() {
	return get_protocol_domain() . _BASE_PATH;
}
function get_protocol_domain() {
	return _PROTOCOL . _DOMAIN;
}
function get_standar_query($results){
    return ("?rpp=" . $results . "&format=" . Q_FORMAT . "&sort_by=" . Q_SORTBY . "&order=" . Q_ORDER . "&start=".S_START);
}
function standar_query($results){
   return get_base_url () . get_standar_query($results);
}
function total_results() {
    return  S_TEXT;
}
function one_day(){
    return S_CACHE;
}
function default_shortcode(){
	return ( array (
		'handle' => null,
		'author' => null,
                'keywords' => null,
		'max_results' => 10,
		'max_lenght' => 0,
		'all' => false,
		'description' => false,
		'date' => false,
		'show_author' => false,
		'cache' => defaultCache(),
		'article' => false,
		'preprint' => false,
		'book' => false,
		'working_paper' => false,
		'technical_report' => false,
		'conference_object' => false,
		'revision' => false,
		'work_specialization' => false,
                'learning_object'=>false,
		'thesis' => false 
	));
}