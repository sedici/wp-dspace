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
define ( URL, 'http://sedici.unlp.edu.ar' );
define (FILTER , '/discover?fq=author_filter%3A');
define (CON1 , '%2C');
define (CON2, '\+');
define (SEPARATOR, '\|\|\|');
class View {
	function View() {
		// Register style sheet.
		wp_register_style ( 'Vista', plugins_url ( 'Sedici-Plugin/css/styles.css' ) );
		wp_enqueue_style ( 'Vista' );
	}

	public function subtype($sub){
		return ucfirst($sub);
	}
	public function max_results($cant,$list){
		if ($cant == 0){ return (count($list)); }
		else return $cant;
	}
	
	public function html_especial_chars($texto){
		return (htmlspecialchars_decode($texto));
	}
	public function strtolower_text($text){
		return strtolower(implode(CON2, $text));
	}
	public function ucwords_text($text){
		$text = implode(" ", $text);
		$text = ucwords($text);
		$text = explode ( " ", $text );
		return implode(CON2, $text);
	}
	public function link_author( $author){
		$link = URL.FILTER;
		$fullname = explode ( ",", $author );
		$lastname = explode(" ", $fullname[0]);
		if(count($fullname) >1) $name = explode(" ", $fullname[1]);
                else $name= $lastname;
		$link .= $this->strtolower_text($lastname).CON1;
		$link.=$this->strtolower_text($name).SEPARATOR;
		$link .= $this->ucwords_text($lastname).CON1.$this->ucwords_text($name);
		?>
				<a href="<?php echo $link; ?>"> <?php echo $author;?></a>
		<?php 
				return;
			}
	
	public function author($authors){
		?>			<br>
					<span class="title sedici-style"><?php _e('Autor:'); ?></span>
					<?php
					$count = count($authors); $i = 1;
					foreach ( $authors as $au ) {
						?>
					<author> <name>	
						<?php 
							$this->link_author($au->get_name ());
						?>
					</name>
					</author>
					<?php
						if ($i != $count) echo " - ";
						$i ++;
					}//end foreach autores
		return;
	}
	public function is_description($des){
		return  ( ($des == "description" || $des == "summary"  ));
	}
	public function shorten_text($text,$maxlenght){
		return ($this->html_especial_chars(substr($text, 0, $maxlenght).'...'));
	}
	
	public function show_description ($description,$item,$maxlenght){
		if ($description == "description") {
			?>
			<span class="title sedici-style"><?php _e('Resumen:'); ?></span> 
			<?php
					$des= $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
					if ($maxlenght != 0){
						echo $this->shorten_text($des[0]['data'],$maxlenght);
					} else {
						echo $this->html_especial_chars($des[0]['data']);
					}
					?>
			<?php 
		}else if($description == "summary") {
			?>
			 <span class="title sedici-style"><?php _e('Sumario:'); ?></span>
			 <?php 
			 if ($maxlenght != 0){
				 echo $this->shorten_text($item->get_description (),$maxlenght);
				} else {
					echo $this->html_especial_chars($item->get_description ());
				}
			}
			 ?> 
	<?php 
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
	
	public function document($item,$a){
		$link = $item->get_link ();	
		?>
		<li><article>
			<title><?php echo $item->get_title ();?></title>
			<span class="title sedici-style"><?php _e('T&iacute;tulo:'); ?></span> <a href="<?php echo $link; ?>">
			<?php echo ($this->html_especial_chars($item->get_title ())); ?> 
			</a>
				<?php 
				if ($a['show_author']){ $this->author($item->get_authors ()); }
				if ($a['date']) { ?>
				<br><published><span class="title sedici-style"><?php _e('Fecha:'); ?> </span> <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published>
				<?php } //end if fecha  
				$this->description($a['description'], $item,$a['max_lenght']);
				 ?>
		</article></li>
		<?php 
		return;
	}
	public function is_handle($type){
		return ($type == 'handle');
	}
        public function is_author($type){
		return ($type == 'author');
	}
	
	public function author_name($type, $name){
		if ($this->is_author($type)){ ?>
			 <h2> <?php echo $name;?> </h2>
		<?php 	 
		}	 
		return;
	}
	public function go_to_sedici($type,$url){
		?> 
		<span class="go-to"> <a href='<?php echo $url; ?>'><?php _e('Ir a SEDICI'); ?></a></span><br><br>
		<?php
	}
	
	function publications($feed, $a, $type) {
		$this->author_name($type, $a['context']);
		foreach ( $feed as $i ) {
			?>
		<h3><?php echo $this->subtype($i ['filter']);?></h3><!-- publication subtype -->
		<ol class="sedici-style">
		<?php
				$list = $i ['view']; $j=0;
				$totalresults = $this->max_results($a['max_results'], $list);
				foreach ( $list as $item ) {
					$this->document($item,$a);
					$j++;
					if($j == $totalresults) break;
				}
		?>
		</ol>
		<?php 
                    if ($this->is_handle($type))
			$this->go_to_sedici($type, $i['url']);
			} 
		return;
	}
	
	function all_publications($groups, $a,$type) {
		$this->author_name($type, $a['context']);
		?><ol class="sedici-style">
			<?php 
			foreach ( $groups as $feed ) {
				foreach ($feed as $item){
					$this->document($item, $a);
					}
			}
			?>
			</ol>
			<?php 
			return ;
				}
	
} // end class