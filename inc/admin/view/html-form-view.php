<h1>Repositorios</h1>


<?php $canciones=get_option('eze_canciones')['Canciones'] ; ?>




    <form  action="/wp-admin/admin-post.php" method="post"> <?php
	foreach ($canciones as $key =>  $value) {
		?>
        <h3><?php echo $value['title'] ?></h3>
        <label for="Title">Título</label>
        <input type="text" name="titulo[]" value="<?php echo $value['title']?>" required > <br>
        <label for="Autor">Autor</label>
        <input type="text" name="autor[]" value="<?php echo $value['autor']?>" required > <br> <br>
        <input type="checkbox" name="delete_repositorios[]" value="<?php echo $key;    ?>">
        <input type="hidden" name="action" value="form_config">
		<?php
	}

	?>
        <hr>
        <h3>Nuevo</h3
        <label for="Title">Título</label>
        <input type="text" name="titulo_nuevo" value=""> <br>
        <label for="Autor">Autor</label>
        <input type="text" name="autor_nuevo" value=""> <br>
	     <?php @submit_button()?>
    </form>