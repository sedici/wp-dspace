<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WidgetFilter
 *
 * @author paw
 */
require_once 'Filter.php';
class WidgetFilter extends Filter {
    public function WidgetFilter(){
         parent::__construct();
    }
    
    function selectedSubtypes ($instance,$all) {
            //this function returns all active subtypes
            if (!$all){
             $groups = array ();
             $subtypes = $this->subtypes();
			// $subtypes: all names of subtypes
			foreach ($subtypes as $type){
				// compares the user marked subtypes, if TRUE, save the subtype.
				 if ('on' == $instance [$type]) {
                                    array_push($groups, $type);
				}
			}
                return $groups;
              }
            return false;    
        }
}
