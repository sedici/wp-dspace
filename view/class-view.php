<?php
namespace Wp_dspace\View;

class View {
        protected $configuration;

	function __construct() {
		// Register style sheet.
	    wp_register_style ( 'Vista', plugin_dir_url (__FILE__).'../media/css/styles.css' );
		wp_enqueue_style ( 'Vista' );
        wp_register_script( 'jquery.Pagination',plugin_dir_url (__FILE__ ).'../media/js/jquery.pajinate.js', array ("jquery"), null, true );
        wp_register_script( 'scripspagination',plugin_dir_url (__FILE__ ).'../media/js/scripspagination.js' , array (), null, true);
        wp_enqueue_script ('jquery.Pagination');
        wp_enqueue_script ('scripspagination');
	}


    public function set_configuration($config){
            $this->configuration = $config;
    }
	public function html_especial_chars($texto){
		return (htmlspecialchars_decode($texto));
	}
	
	public function link_author( $author){
            return $this->configuration->print_author($author);
	}
	
	public function author($authors){ 
            $names = array ();
            foreach ( $authors as $author ) {
            if( isset($author ) && ($author != FALSE)){
            //    if(!empty($author->get_name ())){
                    array_push ($names, "<author><name>".$this->link_author($author->get_name ())."</name></author>");
                }
            }//end foreach autores
            if (!empty($names)){
            
            $stringHtml='<div id="sedici-title">'.
            __('Autores: ').
                implode("-", $names).
            '</div>';
        
            }
            return $stringHtml;
	}
	public function is_description($des){
		return  ( ($des == "description" || $des == "summary"  ));
	}
        
	public function show_text($text,$maxlenght){
            if (!is_null($maxlenght)){
		$stringHtml=($this->html_especial_chars(substr($text, 0, $maxlenght).'...'));
            }
            else {
               $stringHtml=  $this->html_especial_chars($text);
            }
            return $stringHtml;
	}
	
	public function show_description ($description,$item,$maxlenght){
		if ($description == "description") { 
                     $stringHtml=' <div id="sedici-title">'.__("Resumen:");  
                        $show_text = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
                        $show_text = $show_text[0]['data'];

                } else {  
                     $stringHtml='<div id="sedici-title">'.__('Sumario:'); 
                        $show_text = $item->get_description ();
                } 

                $stringHtml=$stringHtml.'<span class="sedici-content">'. 
                    $this->show_text($show_text,$maxlenght).
                    
                '</span></div>';
                
		return $stringHtml;
	}
	
        public function dctype($entry){
		//return subtype document
		$description = $entry->get_description();
		$dctype = explode ( "\n", $description );
		return ($dctype[0]);
	}
        
	public function description($description,$item,$maxlenght){
		$stringHtml="";
        if($this->is_description($description)){
			
			$stringHtml='<summary>'.
			$this->show_description($description, $item,$maxlenght).
			'</summary>';
		}
		return $stringHtml;
	}
        function share($link,$title){
        $stringHtml='<div class="a_unline">'.
            __('Compartir: ').
             '<a href="https://www.facebook.com/sharer/sharer.php?p[title]='.$title.'&p[url]='.$link.'" target="_blank">'.
                 '<img src="' . plugins_url( 'media/img/share-facebook.png', dirname(__FILE__) ) . '" alt="Facebook logo" title="Compartir en Facebook">'.'
             </a>
             <a href="https://twitter.com/?status='.$title." ".$link." via @sedici_unlp".'" target="_blank">'.
                  '<img src="' . plugins_url( 'media/img/share-twitter.png', dirname(__FILE__) ) . '" alt="Twitter logo" title="Compartir en Twitter">'.
             '</a>
             <a href="https://plus.google.com/share?url='.$link.'" target="_blank">'
                  .'<img src="' . plugins_url( 'media/img/share-plus.png', dirname(__FILE__) ) . '" alt="Google+ logo" title="Compartir en Google+">'.'
             </a>
             <a href="http://www.linkedin.com/shareArticle?url='.$link.'" target="_blank">'.
                  '<img src="' . plugins_url( 'media/img/share-linkedin.png', dirname(__FILE__) ) . '" alt="Linkedin logo" title="Compartir en Linkedin">'.'
             </a>
        </div>';    
        return $stringHtml;   
        }
	public function document($item,$attributes){
		$link = $item->get_link ();	
		
		$stringHtml = '<li><article>
			<title>' . $item->get_title () . '</title>
                        <div id="sedici-title">
                            <a href="' . $link . '" target="_blank">' . 
                            ($this->html_especial_chars($item->get_title ())) .  
                            '</a>
                        </div>';  
				if ($attributes['show_author']){ $stringHtml=$stringHtml . $this->author($item->get_authors ()); }
				if ($attributes['date']) 
                                { 
                                   $stringHtml= $stringHtml.'<published>
                                        <div id="sedici-title">'.__('Fecha: ') .  
                                        '<span class="sedici-content">' .$item->get_date ( 'Y-m-d' ) . '</span></div>
                                    </published>';
				} //end if fecha  
                                if ($attributes['show_subtypes']) 
                                { 
                                    $stringHtml=$stringHtml . '<dc:type>
                                        <div id="sedici-title">' . __('Tipo de documento: '). 
                                            '<span class="sedici-content">' . $this->dctype($item) . '</span></div>
                                    </dc:type>';
				} //end if fecha
				$stringHtml=$stringHtml . $this->description($attributes['description'], $item,$attributes['max_lenght']);
                                if ($attributes['share']){ $stringHtml=$stringHtml . $this->share($link,$item->get_title ()); }
				
		return $stringHtml . '</article></li>';;

	}
	
        public function group($item,$group){
            if ($group == "date") {
                return $item->get_date ( 'Y' );
            } else if ( $group == "subtype") {
                return $this->dctype($item);
            }
            return true;
        }
        public function corte($elem,$comparator,$value){
            if($comparator=="date"){
                return ($elem->get_date ( 'Y' )==$value);
            }
            elseif ($comparator=="subtype") {
                return ($this->dctype($elem)==$value);
            }
            return true;
        }
        public function corteControl($anArray,$attributes,$position,$corte,$corte2=""){
            $c=true; $c2=true; 
            $stringHtml="";
            $condition= $this->group($anArray[$position],$corte);
            $condition2= $this->group($anArray[$position],$corte2);
            while ( ($position != count($anArray)) && ( $c ) && ($c2)) {
                 $c = $this->corte($anArray[$position], $corte, $condition);
                 $c2 = $this->corte($anArray[$position], $corte2, $condition2);
                 if (($c) && ($c2)) {
                    $stringHtml=$stringHtml.$this->document($anArray[$position], $attributes);
                    $position++;
                 }   
             }
             return array("stringHtml" => $stringHtml,"position" => $position);
        }

        public function publicationsByGroup($entrys, $attributes, $group) {
                    $position=0;
                    $stringItem=""; 
                    $stringHtml='<div class="wpDspace itemsPagination '.$this->classPagination($entrys).'" id="'. uniqid('page_container_') .'"> <ul class="content">';
                    while ($position != count($entrys)){
                        $currentElem= $entrys[$position];
                        $title = $this->group($currentElem, $group);
                        $stringHtml=$stringHtml .'<li class="noList"><h2>' . $title . '</h2></li>';
                        $arrayCorteControl = $this->corteControl($entrys,$attributes,$position,$group);
                        $position=$arrayCorteControl['position'];
                        $stringHtml=$stringHtml.$arrayCorteControl['stringHtml'];   
                    }           
        return $stringHtml .' </ul><div class="page_navigation " ></div></div>' ; // end div=group
	}
        public function printTitle($title,$lastTitle){
            $stringHtml="";
            if (strcmp($title,$lastTitle)!== 0) { 
               // Div open in function printTitle 
                $stringHtml='<li class="noList"><h2>' . $title . '</h2></li>';
            
            }//end if
            return $stringHtml ;
        }
        
        public function closeDiv($actualTitle,$entrys,$position,$group){
            if ($position < count($entrys)) {
                $titleEntry = $this->group($entrys[$position], $group); 
                return (strcmp($actualTitle, $titleEntry)!== 0);
            }
            return true;
        }
        public function classPagination($entrys){
            $stringHtml="";
            if(count($entrys) < 20){
                $stringHtml=' noPagination';
            }
            return $stringHtml;
        }
        public function publicationsByDateSubtype($entrys, $attributes,$group,$subgroup) {
           $position=0; $title=""; $stringItem=""; 
           $stringHtml='<div class="wpDspace itemsPagination'.$this->classPagination($entrys).'" id="'. uniqid('page_container_') .'">
                <ul class="content">';  
           while ($position != count($entrys)){
                $currentElem= $entrys[$position];
                $lastTitle = $title;
                $title = $this->group($currentElem, $group);
                $subtitle = $this->group($currentElem, $subgroup);
                $stringHtml=$stringHtml . $this->printTitle($title, $lastTitle) .
                '<li class="noList"><h3>' . $subtitle . '</h3></li>';
            
                $arrayCorteControl = $this->corteControl($entrys,$attributes,$position,$group,$subgroup);
                $position=$arrayCorteControl['position'];
                $stringItem=$arrayCorteControl['stringHtml'];
                $stringHtml=$stringHtml . $stringItem ;   
            
               /* if($this->closeDiv($title, $entrys, $position, $group)){ 
                    
                    $stringHtml= $stringHtml .'</div>'; 
                    //<!-- Close the Div open in function printTitle  -->   
                }// end if(cerrarDiv)*/
            }//end while
          
            
                
            return $stringHtml . '</ul><div class="page_navigation " ></div></div>'; //<!-- close div=DateSubtype --> ;
	}
        
        
        public function allPublications($entrys, $attributes) {
            $stringHtml='<div class="wpDspace itemsPagination '.$this->classPagination($entrys).'" id="'. uniqid('page_container_') .'"><ul class="content">';
            $stringItem="";
			foreach ($entrys as $item){
                          $stringItem=$stringItem . $this->document($item, $attributes);
			}
            $stringHtml=$stringHtml . $stringItem . '</ul>
            <div class="page_navigation " ></div>
            </div>';          
            return $stringHtml;
	}
        
} // end class
