<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('SHORTCODE', 'get_publications');


function get_shortcode(){
    return SHORTCODE;
}

function get_label($label,$value){
    if(!empty($value)){
        $text = $label.'="'.$value.'" ';
        return $text;
    }
    return;
}

function is_on($label,$value){
    if('on' == $value){
        $text = $label.'=true ';
        return $text;
    }
    return;
}