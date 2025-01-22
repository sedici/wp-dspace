<?php
namespace Wp_dspace\View;
define('ACTIVE_SUBTYPE', "subtype");
define('ACTIVE_DATE', "date");
class View {
        protected $configuration;
    

	function __construct() {
		// Register style sheet.

  
        
	    wp_register_style ( 'Vista', plugin_dir_url (__FILE__).'../media/css/styles.css' );
		wp_enqueue_style ( 'Vista' );
        wp_register_script('embedVideo', plugin_dir_url (__FILE__).'../media/js/embedVideo.js',array('jquery'));
        wp_enqueue_script('embedVideo');
        $params = array('ajaxurl' => admin_url('admin-ajax.php'));
        wp_localize_script('embedVideo', 'params', $params);
//        wp_register_script( 'jquery.Pagination',plugin_dir_url (__FILE__ ).'../media/js/jquery.pajinate.js', array ("jquery"), null, true );
//        wp_enqueue_script ('jquery.Pagination');
//        wp_enqueue_script ('scripspagination');
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
                    array_push ($names, "<author><name>".$this->link_author($author)."</name></author>");
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
       
	/*	if ($description == "description") { 
                     $stringHtml=' <div id="sedici-title">'.__("Resumen:");  
                        $show_text = explode("\n", $item->summary );
                        $show_text = $show_text[2];

                } else {  
                     $stringHtml='<div id="sedici-title">'.__('Sumario:'); 
                        $show_text = $item->summary;
                }*/ 
                $stringHtml=' <div id="sedici-title">'.__("Resumen:");  
                $show_text = $item->get_abstract();
                
                $stringHtml=$stringHtml.'<span class="sedici-content">'. 
                    $this->show_text($show_text,$maxlenght).   
                '</span></div>';
                
		return $stringHtml;
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
        $link = $item->get_link();  #Listo
        $date= $item->get_date();
         	
		$stringHtml = '<li><article style="padding:10px;border: 2px solid black;">
			<title>' . $item->get_title() . '</title>
                        <div id="sedici-title"> 
                            <a class="link" href="' . $link . '" target="_blank">' . 
                            ($this->html_especial_chars($item->get_title())) .  
                            '</a>
                        </div>';   
				if ($attributes['show_author']){  $stringHtml=$stringHtml . $this->author($item->get_authors()); }

                if ($attributes['show_videos']){
                    $stringHtml = $stringHtml . '<a class="btn-dspace-show"><i style="font-size: 3em;" class="displayDesc fas fa-angle-down" aria-hidden="true"></i></a>
                    <a class="btn-dspace-hide" style="display:none;"><i style="font-size: 3em;" class="displayDesc fas fa-angle-up" aria-hidden="true"></i></a>
                    <div class="avancedDescription" style="display:none;">';
                }   
				if ($attributes['date']) 
                                { 
                                   $stringHtml= $stringHtml.'
                                   <p id="'. $link . '"> </p>
                                   <published>
                                        <div id="sedici-title" >'.__('Fecha: ') .  
                                        '<span class="sedici-content">' . $date . '</span></div>
                                    </published>';
				}
                else{
                    $stringHtml = $stringHtml.'<div>';
                } //end if fecha
                                if ($attributes['show_subtypes'] ) 
                                { 
                                    $stringHtml=$stringHtml . '<dc:type>
                                        <div id="sedici-title">' . __('Tipo de documento: '). 
                                            '<span class="sedici-content">' . $item->get_subtype() . '</span></div> 
                                    </dc:type>';  
				} //end if fecha
				$stringHtml=$stringHtml . $this->description($attributes['description'], $item,$attributes['max_lenght']);
                                if ($attributes['share']){ $stringHtml=$stringHtml . $this->share($link,$item->title ); }
        if ($attributes['show_videos']){
            $stringHtml = $stringHtml .'</div>';
        }
		return $stringHtml . '</article></li><br>';;

	}
	
        public function group($item,$group){
           if ($group == "date") {
                $date = $item->get_raw_date();
                if(!empty($date)){
                    return (int) date_format($date,"Y") ;

                }
            } elseif ( $group == "subtype" ) {
                return $item->get_subtype();
            }
            return true;
        }
    

       

        public function publicationsByGroup($entrys, $attributes, $group) {
                    $position=0;
                    $stringItem=""; 
                    $stringHtml='<div class="wpDspace itemsPagination '.$this->classPagination($entrys).'" id="'. uniqid('page_container_') .'"> <ul class="content">';
                    $array_for_groups = array();
                    foreach ($entrys as $entry) {

                        $title = $this->group($entry, $group);
                        if (empty($array_for_groups[$title]))
                            $array_for_groups[$title]= array($entry);
                        else
                            array_push( $array_for_groups[$title],$entry);
                        
                    }
                   
                    foreach ($array_for_groups as $title => $values) {
                        $stringHtml=$stringHtml .'<li class="noList"><h2>' . $title . '</h2></li>';
                        foreach ($values as  $value) {
                            $stringHtml=$stringHtml.$this->document($value, $attributes);
                        }
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

        /**
         *  @param String $link web page URL to extract youtube video from
         */
        public function get_videos($link){
            $html = file_get_contents($link);
            preg_match_all("#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#", $html, $matches);
            $matches = array_unique($matches[0]);
            $youtubeLinks = array_map(function($match){
                return "https://www.youtube.com/embed/{$match}?feature=oembed";
            },$matches );
            return $youtubeLinks;
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
            $array_for_groups = array();
            foreach ($entrys as $entry) {
                $title = $this->group($entry, $group);
                $subtitle = $this->group($entry, $subgroup);
                if (empty($array_for_groups[$title]) or empty($array_for_groups[$title][$subtitle]))
                    $array_for_groups[$title][$subtitle]= array($entry);
                else   
                    array_push( $array_for_groups[$title][$subtitle],$entry);
            }
            krsort($array_for_groups);
            foreach ($array_for_groups as $year => $values_for_date) {
                $stringHtml=$stringHtml .'<li class="noList"><h2>' . $year . '</h2></li>';
                foreach ($values_for_date as $subtype => $values) {
                    $stringHtml=$stringHtml .
                    '<li class="noList"><h3>' . $subtype . '</h3></li>';
                    foreach ($values as  $value) {
                        $stringHtml=$stringHtml.$this->document($value, $attributes);
                    }
                }
            }
        
          
            
                
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

    function render($results, $attributes, $cmp, $configuration)
    {
        $this->set_configuration($configuration);
        if($cmp){
        if (strcmp($cmp, CMP_DATE_SUBTYPE) == 0) {
            return ($this->publicationsByDateSubtype($results, $attributes, ACTIVE_DATE, ACTIVE_SUBTYPE));
        }
        if (strcmp($cmp, CMP_DATE) == 0) {
            return ($this->publicationsByGroup($results, $attributes, ACTIVE_DATE));
        }
        if (strcmp($cmp, CMP_SUBTYPE) == 0) {
            return ($this->publicationsByGroup($results, $attributes, ACTIVE_SUBTYPE));
        }
        }
        return $this->allPublications($results, $attributes);
    }
        
} // end class
