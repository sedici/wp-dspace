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
define ( Q_SORTBY, '0' );
define ( Q_ORDER, 'desc' );
define ( CONECTOR2, '%5C' );
define ( CONECTOR3, '%7C' );
define ( _PROTOCOL, "http://" );
define ( _DOMAIN, "sedici.unlp.edu.ar" );
define ( _BASE_PATH, "/open-search/discover" );
define ( SQ_HANDLE, "scope");
define ( Q_QUERY, "query");
define ( SQ_AUTHOR, "sedici.creator.person:");
define (SQ_SUBTYPE, "sedici.subtype:");

function conector() {
	return CONECTOR2 . '+';
}
function get_conector() {
	return (CONECTOR2 . CONECTOR3 . CONECTOR2 . CONECTOR3 . CONECTOR2 . CONECTOR3);
}
function get_base_url() {
	return _PROTOCOL . _DOMAIN . _BASE_PATH;
}
function get_protocol_domain() {
	return _PROTOCOL . _DOMAIN;
}
function get_standar_query(){
    return ("?rpp=" . Q_RPP . "&format=" . Q_FORMAT . "&sort_by=" . Q_SORTBY . "&order=" . Q_ORDER);
}