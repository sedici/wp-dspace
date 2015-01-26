<?php
class Vista {
	function todos($vector, $descripcion, $fecha,$mostrar) {
		/*
		 * Es la vista para todos los resultados
		*/
		
		?><ol>
		<?php 
		foreach ( $vector as $feed ) {
			foreach ($feed as $item){
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
			<br /><published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published><br />
			<?php } ?>
			<?php if ($descripcion == "description") { 
				?><summary type="text">Resumen: <?php 
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
		return ;
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
					
				
			
			<?php if ($fecha) { ?>
			<br /><published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published><br />
			<?php } ?>
			<?php if ($descripcion == "description") { 
				?><summary type="text">Resumen: <?php 
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
	<br /><br/>
	<?php
			} ?>  <?php 
			return;
		}
	
	function autor($feed, $descripcion, $fecha, $resultado,$mostrar, $context) {
		/*
		 * Es la vista para los articulos
		 */?> 
		 <?php 
		echo "<h1> $context </h1>";
		foreach ( $feed as $i ) {
			?>

<h2><?php $filtro= ucfirst ($i [filtro]); echo $filtro;?></h2>
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
		<published>Fecha: <?php  echo $item->get_date ( 'Y-m-d' ); ?> </published><br />
		<?php } ?>
		<?php if ($descripcion == "description") { 
			?><summary type="text">Resumen: <?php 
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
		} ?>  <?php 
		return;
	}
} // end de la class

?>