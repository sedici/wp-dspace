<?php
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
		?>
					Autor:
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
	public function descripcion($descripcion,$item){
		if ($descripcion == "description") {
		?>
			<summary type="text">Resumen: <?php
					$des= $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
					echo $this->html_especial_chars($des[0]['data']);
					?>
			</summary>
			<?php 
		}else if($descripcion == "summary") {
			?>
			 <summary type="text">Sumario: <?php 
			 echo $this->html_especial_chars($item->get_description ());
			 ?> 
			 </summary>
		<?php }
		return;
	}
	
	public function articulo($item,$a){
		$link = $item->get_link ();	
		?>
		<article>
			<header>
				<title><?php echo $item->get_title ();?></title>
			</header>
			<li>T&iacute;tulo: <a href="<?php echo $link; ?>">
			<?php echo ($this->html_especial_chars($item->get_title ())); ?> 
			</a></li>
				<?php 
				if ($a['mostrar']){ $this->autores($item->get_authors ()); }
				if ($a['fecha']) { ?>
				<br /><published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published><br />
				<?php } //end if fecha  
				$this->descripcion($a['descripcion'], $item);
				 ?>
		</article>
		<?php 
		return;
	}
	
	public function nombre_autor($type, $nombre){
		if ($type != 'handle') echo "<h1> $nombre </h1>";//El nombre del autor
		return;
	}
	
	function publicaciones($feed, $a, $type) {
		$this->nombre_autor($type, $a['context']);
		foreach ( $feed as $i ) {
			?>
		<h2><?php echo $this->subtipo($i ['filtro']);?></h1><!-- El subtipo de publicacion -->
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
		<?php if ($type == 'handle'){ ?> <a href='<?php echo $i[url]; ?>'>Ir a SEDICI</a><br/><br/>
		<?php }
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

