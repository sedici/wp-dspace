<?php
require_once 'Filter.php';
class ShortcodeFilter extends Filter {

    function default_shortcode(){
	return ( array (
		'handle' => null,
		'author' => null,
                'keywords' => null,
                'config' =>default_repository(),
                'share' => false,
		'max_results' => medium_results(),
		'max_lenght' => null,
                'show_subtype' =>false,
		'all' => true,
                'group_subtype' =>false,
                'group_date' =>false,
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
    
    function selectedSubtypes ($instance,&$groups) {
        //this function returns all active subtypes
        $all=true;
        $groups = array ();
        $subtypes = $this->subtypes ();
	// $subtypes: all names of subtypes
	foreach ($subtypes as $key => $subtype){
            // compares the user marked subtypes, if TRUE, save the subtype.
            if ('true' === $instance [$key]) {
                array_push($groups, $subtype);
                $all=false;
            }
	}
	if ($instance ['thesis'] === 'true') {
        // if thesis is true, save subtypes thesis
            $all_thesis = $this->vectorTesis ();
            // $all_thesis: all subtypes thesis
            foreach ( $all_thesis as $thesis ) {
                array_push($groups, $thesis);
                $all=false;
            }
        }
            return $all;   
    }
    
}
