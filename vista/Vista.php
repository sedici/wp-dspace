<?php
class Vista {
	function todos($vector, $descripcion, $fecha, $resultado,$mostrar) {
		/*
		 * Es la vista para todos los resultados
		*/
		
		?><ol>
		<?php 
		foreach ( $vector as $feed ) {
			foreach ($feed as $item){
			?>
	
	
		<article>
			<header>
				<title><?php echo $item->get_title ();?></title>
			</header>
			<li>T&iacute;tulo: <a href="<?php echo $link; ?>"><?php $titulo= $item->get_title ();
			//echo ( $titulo);
			print (htmlspecialchars_decode($titulo));
			?> </a></li>
			
			
				<?php 
				if($mostrar){
				$autores = $item->get_authors ();?>
					Autor:
					
					<?php
					foreach ( $autores as $a ) {
						?>
					<author> <name>	
						<?php echo $a->get_name (); ?>
					</name>
					</author>
			
					<?php
						echo " - ";
					}
					}
					?>
					
				
			<?php if ($fecha) { ?>
			<published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published>
			<?php } ?>
			<?php if ($descripcion == "description") { 
				?><summary type="text">Descripci&oacute;n:<?php 
					$des= $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
					print (htmlspecialchars_decode($des[0]['data']));
					?></summary>
					<?php 
				} 
					else if($descripcion == "summary") {
				 ?>
				 
			 <summary type="text">Sumario: <?php $des=$item->get_description ();
			 print (htmlspecialchars_decode($des)); ?> </summary>
			
				 <?php } ?>
		</article>
	
	<?php
				}
				
				?>
				
	<?php
			}
			?>
			
			</ol>
		<?php 
			return;
		}
	
	
	
	function armarUrl($filtro, $handle) {
		/*
		 * Esta funcion, arma la url para el "ir a sedici" dependiendo de cada filtro. Ejemplo: http://sedici.unlp.edu.ar/handle/10915/25293/discover?fq=type_filter%3Atesis%5C+de%5C+doctorado%5C%7C%5C%7C%5C%7CTesis%5C+de%5C+doctorado
		*/
		$filtro = strtolower($filtro);//lo convierto todo a minuscula
		$palabras = explode ( " ", $filtro ); // palabras es un array que tiene cada palabra del filtro
		$url = "http://sedici.unlp.edu.ar/handle/" . $handle . "/discover?fq=type_filter%3A";
		$cant = count ( $palabras ); // cant tiene la cantidad de elementos de palabras
		$url = $url . $palabras [0]; // concateno la primera palabra
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . "%5C+" . $palabras [$i]; // concateno el resto de las palabras, si es que existen, anteponiendo %5c+
		}
		$mayuscula = ucfirst ( $filtro );
		$palabras = explode ( " ", $mayuscula );
		$url = $url . "%5C%7C%5C%7C%5C%7C";
		$cant = count ( $palabras );
		$url = $url . $palabras [0];
		for($i = 1; $i < $cant; $i ++) {
			$url = $url . "%5C+" . $palabras [$i];
		}
		return $url;
	}
	function articulos($feed, $descripcion, $fecha, $resultado) {
		/*
		 * Es la vista para los articulos
		*/
		foreach ( $feed as $i ) {
			?>
	
	<h1><?php $filtro= ucfirst ($i [filtro]); echo $filtro;?></h1>
	<ol>
	<?php
				
				$lista = $i ['vista'];
				$j=0;
				//fin tiene la cantidad de resultados a mostrar
				if($resultado==0) { $fin = count($lista);}
				else { $fin= $resultado; }
				foreach ( $lista as $item ) {
					
					$link = $item->get_link ();
					?>
	
		<article>
			<header>
				<title><?php echo $item->get_title ();?></title>
			</header>
			<li>T&iacute;tulo: <a href="<?php echo $link; ?>"><?php $titulo= $item->get_title ();
			//echo ( $titulo);
			print (htmlspecialchars_decode($titulo));
			?> </a></li>
				<?php $autores = $item->get_authors ();?>
					Autor:
					
					<?php
					foreach ( $autores as $a ) {
						?>
					<author> <name>	
						<?php echo $a->get_name (); ?>
					</name>
					</author>
			
					<?php
						echo " - ";
					}
					?>
					
				
			<br />
			<?php if ($fecha) { ?>
			<published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published>
			<br />
			<?php } ?>
			<?php if ($descripcion == "description") { 
				?><summary type="text">Descripci&oacute;n:<?php 
					$des= $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
					print (htmlspecialchars_decode($des[0]['data']));
					?></summary>
					<?php 
				} 
					else if($descripcion == "summary") {
				 ?>
				 
			 <summary type="text">Sumario: <?php $des=$item->get_description ();
			 print (htmlspecialchars_decode($des)); ?> </summary>
			
				 <?php } ?>
		</article>
	
	<?php
			$j++;
			if($j == $fin) break;
				}
				
				?>
				</ol>
	<a href='<?php echo $i[url]; ?>'>Ir a SEDICI</a>
	<br />
	<?php
			}
			return;
		}
	
	function autor($feed, $descripcion, $fecha, $resultado,$mostrar) {
		/*
		 * Es la vista para los articulos
		 */
		foreach ( $feed as $i ) {
			?>

<h1><?php $filtro= ucfirst ($i [filtro]); echo $filtro;?></h1>
<ol>
<?php
			
			$lista = $i ['vista'];
			$j=0;
			//fin tiene la cantidad de resultados a mostrar
			if($resultado==0) { $fin = count($lista);}
			else { $fin= $resultado; }
			foreach ( $lista as $item ) {
				
				$link = $item->get_link ();
				?>

	<article>
		<header>
			<title><?php echo $item->get_title ();?></title>
		</header>
		<li>T&iacute;tulo: <a href="<?php echo $link; ?>"><?php $titulo= $item->get_title ();
		//echo ( $titulo);
		print (htmlspecialchars_decode($titulo));
		?> </a></li>
		
		
			<?php 
			if($mostrar){
			$autores = $item->get_authors ();?>
				Autor:
				
				<?php
				foreach ( $autores as $a ) {
					?>
				<author> <name>	
					<?php echo $a->get_name (); ?>
				</name>
				</author>
		
				<?php
					echo " - ";
				}
				}
				?>
				
			
		<?php if ($fecha) { ?>
		<published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published>
		<?php } ?>
		<?php if ($descripcion == "description") { 
			?><summary type="text">Descripci&oacute;n:<?php 
				$des= $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11,'description') ;
				print (htmlspecialchars_decode($des[0]['data']));
				?></summary>
				<?php 
			} 
				else if($descripcion == "summary") {
			 ?>
			 
		 <summary type="text">Sumario: <?php $des=$item->get_description ();
		 print (htmlspecialchars_decode($des)); ?> </summary>
		
			 <?php } ?>
	</article>

<?php
		$j++;
		if($j == $fin) break;
			}
			
			?>
			</ol>
<?php
		}
		return;
	}
} // end de la class

?>