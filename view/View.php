<?php
class View {
	function View() {
		// Register style sheet.
		wp_register_style ( 'Vista', plugins_url ( 'wp-dspace/css/styles.css' ) );
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
            <div class="sedici-title"><?php _e('Autores:'); ?></div>
            <div class="sedici-content">
            <?php
                $names = array ();
		foreach ( $authors as $author ) {
                    array_push ($names, "<author><name>".$this->link_author($author->get_name ())."</name></author>");
		}//end foreach autores
            print_r(implode("-", $names));
            ?>
            </div>
            <?php
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
                <div class="sedici-title"><?php _e($title); ?></div>
                <div class="sedici-content">
                <?php 
                    $this->show_text($show_text,$maxlenght);
                ?>
                </div>
                <?php
		return;
	}
	
	public function description($description,$item,$maxlenght){
		if($this->is_description($description)){
			?>
			<summary>
			<?php $this->show_description($description, $item,$maxlenght); ?>
			</summary>
		<?php 
		}
		return;
	}
	
	public function document($item,$attributes){
		$link = $item->get_link ();	
		?>
		<li><article>
			<title><?php echo $item->get_title ();?></title>
                        <div class="sedici-title"><?php _e('T&iacute;tulo:'); ?></div>
                        <div class="sedici-content"><a href="<?php echo $link; ?>">
			<?php echo ($this->html_especial_chars($item->get_title ())); ?> 
			</a>
                        </div>  
				<?php 
				if ($attributes['show_author']){ $this->author($item->get_authors ()); }
				if ($attributes['date']) 
                                { ?>
                                    <published>
                                        <div class="sedici-title"><?php _e('Fecha:'); ?> </div>
                                        <div class="sedici-content"><?php  echo $item->get_date ( 'Y-m-d' ); ?></div>
                                    </published>
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
                <div class="sedici-title"><?php echo $key;?></div><!-- publication subtype -->
		<?php
                    $this->all_publications($entrys,$attributes);
                }
		return;
	}
	
	public function all_publications($groups, $attributes) {
	?>
            <ol>
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