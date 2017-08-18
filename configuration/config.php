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
define ( 'Q_SORTBY', '3' );
define ( 'Q_ORDER', 'desc' );
define ( 'Q_QUERY', "query");
define ('S_REPOSITORY',"sedici");
define ('Q_CONFIGURATION',"/plugins/wp-dspace/config-files/");

function get_configuration_directory(){
    return WP_CONTENT_DIR.Q_CONFIGURATION;
}

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
function parse_ini_files($f) {
    $r=$null;
    $sec=$null;
    $f=@file($f);
    for ($i=0;$i<@count($f);$i++) {
      $newsec=0; $w=@trim($f[$i]);
      if ($w) { if ((!$r) or ($sec)) {
        if ((@substr($w,0,1)=="[") and (@substr($w,-1,1))=="]") {
          $sec=@substr($w,1,@strlen($w)-2);$newsec=1;}
        }
      if (!$newsec) {
        $w=@explode("=",$w);
        $k=@trim($w[0]);unset($w[0]);
        $v=@trim(@implode("=",$w));
        if ((@substr($v,0,1)=="\"") and (@substr($v,-1,1)=="\"")) {
          $v=@substr($v,1,@strlen($v)-2);}
        if ($sec) {$r[$sec][$k]=$v;}
        else {$r[$k]=$v;} } } }
        return $r; }
function parseFile($f){
  if (!function_exists('parse_ini_file'))
  {
    $arrayFiles= parse_ini_files($f)['config'];

    }
  else {
    $arrayFiles=  parse_ini_file($f);
  }
  return $arrayFiles;
}
