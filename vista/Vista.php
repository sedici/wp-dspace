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

define ( URL, 'http://sedici.unlp.edu.ar' );
define (FILTER , '/discover?fq=author_filter%3A');
define (CON1 , '%2C');
define (CON2, '\+');
define (SEPARADOR, '\|\|\|');
class Vista {
	function Vista() {
		// Register style sheet.
		wp_register_style ( 'Vista', plugins_url ( 'Sedici-Plugin/css/sedici.css' ) );
		wp_enqueue_style ( 'Vista' );
	}

	public function subtipo($sub){
		return ucfirst($sub);
	}
	public function cantidad($cant,$lista){
		if ($cant == 0){ return (count($lista)); }
		else return $cant;
	}
	
	public function html_especial_chars($texto){
		return (htmlspecialchars_decode($texto));
	}
	public function minuscula($cadena){
		return strtolower(implode(CON2, $cadena));
	}
	public function mayuscula($cadena){
		$cadena = implode(" ", $cadena);
		$cadena = ucwords($cadena);
		$cadena = explode ( " ", $cadena );
		return implode(CON2, $cadena);
	}
	public function link_autor( $autor){
		$link = URL.FILTER;
		$nombreCompleto = explode ( ",", $autor );
		$apellido = explode(" ", $nombreCompleto[0]);
		$nombre = explode(" ", $nombreCompleto[1]);
		$link .= $this->minuscula($apellido).CON1;
		$link.=$this->minuscula($nombre).SEPARADOR;
		$link .= $this->mayuscula($apellido).CON1.$this->mayuscula($nombre);
		?>
				<a href="<?php echo $link; ?>"> <?php echo $autor;?></a>
		<?php 
				return;
			}
	
	public function autores($autores){
		?>			<br>
					<span class="title sedici-style">Autor:</span>
					<?php
					$cantidad = count($autores); $i = 1;
					foreach ( $autores as $au ) {
						?>
					<author> <name>	
						<?php 
							$this->link_autor($au->get_name ());
						?>
					</name>
					</author>
					<?php
						if ($i != $cantidad) echo " - ";
						$i ++;
					}//end foreach autores
		return;
	}
	public function is_description($des){
		return  ( ($des == "description" || $des == "summary"  ));
	}
	public function acortar($text,$maxlenght){
		return ($this->html_especial_chars(substr($text, 0, $maxlenght).'...'));
	}
	
	public function show_description ($descripcion,$item,$maxlenght){
		if ($descripcion == "description") {
			?>
			<span class="title sedici-style">Resumen:</span> 
			<?php
					$des= $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
					if ($maxlenght != 0){
						echo $this->acortar($des[0]['data'],$maxlenght);
					} else {
						echo $this->html_especial_chars($des[0]['data']);
					}
					?>
			<?php 
		}else if($descripcion == "summary") {
			?>
			 <span class="title sedici-style">Sumario:</span>
			 <?php 
			 if ($maxlenght != 0){
				 echo $this->acortar($item->get_description (),$maxlenght);
				} else {
					echo $this->html_especial_chars($item->get_description ());
				}
			}
			 ?> 
	<?php 
		return;
	}
	
	
	public function descripcion($descripcion,$item,$maxlenght){
		if($this->is_description($descripcion)){
			?>
			<div class="summary">
			<summary>
			<?php $this->show_description($descripcion, $item,$maxlenght); ?>
			</summary>
			</div>
		<?php 
		}
		return;
	}
	
	public function articulo($item,$a){
		$link = $item->get_link ();	
		?>
		<li><article>
			<title><?php echo $item->get_title ();?></title>
			<span class="title sedici-style">T&iacute;tulo:</span> <a href="<?php echo $link; ?>">
			<?php echo ($this->html_especial_chars($item->get_title ())); ?> 
			</a>
				<?php 
				if ($a['mostrar']){ $this->autores($item->get_authors ()); }
				if ($a['fecha']) { ?>
				<br><span class="title sedici-style"><published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published></span>
				<?php } //end if fecha  
				$this->descripcion($a['descripcion'], $item,$a['max_lenght']);
				 ?>
		</article></li>
		<?php 
		return;
	}
	public function is_handle($type){
		return ($type == 'handle');
	}
	
	public function nombre_autor($type, $nombre){
		if (!$this->is_handle($type)){ ?>
			 <h2> <?php echo $nombre;?> </h2>
		<?php 	 
		}	 
		return;
	}
	public function go_to_sedici($type,$url){
		if ($this->is_handle($type)){ ?> 
		<span class="go-to"> <a href='<?php echo $url; ?>'>Ir a SEDICI</a></span><br><br>
		<?php }
	}
	
	function publicaciones($feed, $a, $type) {
		$this->nombre_autor($type, $a['context']);
		foreach ( $feed as $i ) {
			?>
		<h3><?php echo $this->subtipo($i ['filtro']);?></h3><!-- El subtipo de publicacion -->
		<ol class="sedici-style">
		<?php
				$lista = $i ['vista']; $j=0;
				$fin = $this->cantidad($a['max_results'], $lista);//fin tiene la cantidad de resultados a mostrar
				foreach ( $lista as $item ) {
					$this->articulo($item,$a);
					$j++;
					if($j == $fin) break;
				}
		?>
		</ol>
		<?php 
			$this->go_to_sedici($type, $i['url']);
			} 
		return;
	}
	
	function todos($vector, $a,$type) {
		/*
		 * Es la vista para todos los resultados
		*/
		$this->nombre_autor($type, $a['context']);
		?><ol class="sedici-style">
			<?php 
			foreach ( $vector as $feed ) {
				foreach ($feed as $item){
					$this->articulo($item, $a);
					}
			}
			?>
			</ol>
			<?php 
			return ;
				}
	
} // end de la class

?>

