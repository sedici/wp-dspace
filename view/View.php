<?php
/**
 * Plugin Name: Sedici-Plugin
 * Plugin URI: http://sedici.unlp.edu.ar/
 * Description: This plugin connects the repository SEDICI in wordpress, with the purpose of showing the publications of authors or institutions
 * Version: 1.0
 * Author: SEDICI - Paula Salamone Lacunza
 * Author URI: http://sedici.unlp.edu.ar/
 * Copyright (c) 2015 SEDICI UNLP, http://sedici.unlp.edu.ar
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 */
?>
<?php
class View {
	function View() {
		// Register style sheet.
		wp_register_style ( 'Vista', plugins_url ( 'Sedici-Plugin/css/styles.css' ) );
		wp_enqueue_style ( 'Vista' );
	}

	public function subtype($sub){
		return ucfirst($sub);
	}

	public function html_especial_chars($texto){
		return (htmlspecialchars_decode($texto));
	}
	public function remplace($text){
		return str_replace(" ", S_CONECTOR5, $text);
	}
	public function link_author( $author){
		$link = get_protocol_domain().S_FILTER;
                $name = str_replace(",", S_CONECTOR4, $author);
                $name = $this->remplace($name);
                $link .= strtolower($name). S_SEPARATOR . $name;
		return  ('<a href='.$link.'>'.$author.'</a>') ;
	}
	
	public function author($authors){ ?>
            <br>
            <span class="title sedici-style"><?php _e('Autor:'); ?></span>
            <?php
                $names = array ();
		foreach ( $authors as $author ) {
                    array_push ($names, "<author><name>".$this->link_author($author->get_name ())."</name></author>");
		}//end foreach autores
            print_r(implode("-", $names));
            return;
	}
	public function is_description($des){
		return  ( ($des == "description" || $des == "summary"  ));
	}
        
	public function show_text($text,$maxlenght){
            if (!is_null($maxlenght)){
		echo ($this->html_especial_chars(substr($text, 0, $maxlenght).'...'));
            }
            else {
               echo  $this->html_especial_chars($text);
            }
            return;
	}
	
	public function show_description ($description,$item,$maxlenght){
		if ($description == "description") {
                        $title = "Resumen:";
                        $show_text = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
                        $show_text = $show_text[0]['data'];
                } else {
                        $title = 'Sumario:';
                        $show_text = $item->get_description ();
                } ?>
		<span class="title sedici-style"><?php _e($title); ?></span>
                <?php 
                    $this->show_text($show_text,$maxlenght);
		return;
	}
	
	public function description($description,$item,$maxlenght){
		if($this->is_description($description)){
			?>
			<div class="summary">
			<summary>
			<?php $this->show_description($description, $item,$maxlenght); ?>
			</summary>
			</div>
		<?php 
		}
		return;
	}
	
	public function document($item,$attributes){
		$link = $item->get_link ();	
		?>
		<li><article>
			<title><?php echo $item->get_title ();?></title>
			<span class="title sedici-style"><?php _e('T&iacute;tulo:'); ?></span> <a href="<?php echo $link; ?>">
			<?php echo ($this->html_especial_chars($item->get_title ())); ?> 
			</a>
				<?php 
				if ($attributes['show_author']){ $this->author($item->get_authors ()); }
				if ($attributes['date']) 
                                { ?>
                                    <br><published><span class="title sedici-style"><?php _e('Fecha:'); ?> </span> <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published>
				<?php } //end if fecha  
				$this->description($attributes['description'], $item,$attributes['max_lenght']);
				?>
		</article></li>
		<?php 
		return;
	}
	
	public function publications($groups, $attributes) {
		 foreach ($groups as $key => $entrys){
		?>
                    <h3><?php echo $key;?></h3><!-- publication subtype -->
		<?php
                    $this->all_publications($entrys,$attributes);
                }
		return;
	}
	
	public function all_publications($groups, $attributes) {
	?>
            <ol class="sedici-style">
		<?php 
			foreach ($groups as $item){
                            $this->document($item, $attributes);
			}
		?>
            </ol>
        <?php 
            return ;
	}	
} // end class