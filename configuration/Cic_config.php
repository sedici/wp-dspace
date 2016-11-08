<?php
/**
 * Description of Sedici-config
 *
 * @author Paula Salamone
 */
define ('C_FILTER' , '/discover?filtertype_1=author&filter_relational_operator_1=equals&filter_1=');
class Cic_config extends Configuration{

    
    public function Cic_config($conf){
       parent::__construct ($conf);
    }
    function print_author($author){
        $link = $this->get_protocol_domain();
	$link = $link. C_FILTER;
        $link .= $author;
	return  ('<a href="'.$link.'" target="_blank">'.$author.'</a>') ;
            
    }
}
