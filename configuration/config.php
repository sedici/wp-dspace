<?php
define ('S_TEXT' , 150);
define ('S_CACHE',86400);
define('S_MAXRESULT', 100);
define('S_MINRESULT',1);
define ('S_MEDRESULT',10);
define ( '_PROTOCOL', "http://" );
define ( '_BASE_PATH', "/open-search/discover" );
define ( 'SQ_HANDLE', "scope");
define ('S_START', 0);
define ( 'Q_RPP', '100' );
define ( 'Q_FORMAT', 'atom' );
define ( 'Q_SORTBY', '2' );
define ( 'Q_ORDER', 'desc' );
define ( 'Q_QUERY', "query");
define ('S_REPOSITORY',"sedici");

function medium_results(){
    return S_MEDRESULT;
}

function default_repository(){
    return S_REPOSITORY;
}
function cache_days(){
    return array (3,7,14);
 }
function total_results() {
    return  array(10,25,50,100);
}                
                
function defaultCache(){
    return S_CACHE * 7 ;
}
function conector() {
	return S_CONECTOR2 . '+';
}
function get_conector() {
	return (S_CONECTOR2 . S_CONECTOR3 . S_CONECTOR2 . S_CONECTOR3 . S_CONECTOR2 . S_CONECTOR3);
}
function show_text() {
    return  S_TEXT;
}
function max_results(){
    return S_MAXRESULT;
}
function min_results(){
    return S_MINRESULT;
}
function one_day(){
    return S_CACHE;
}
