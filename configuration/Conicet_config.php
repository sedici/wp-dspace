<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conicet_config
 *
 * @author paw
 */
class Conicet_config extends Configuration {
    public function is_description($instance){
           return ($instance === 'true' ? "summary" : false);
    }
    public function is_label_true($instance){
           return false;
    } 
    public function all_documents(){
        return false; 
    }
}
