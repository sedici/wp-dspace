<h1>Repositorios</h1>


<?php $repositorios=get_option('config_repositorios')['repositorios'] ;


function print_text_field($label, $id, $field,$value,$description="",$required="") {
    return "<tr>
                <th scope='row'>
                    <label for='$field'><h3>$label</h3></label>
                </th>
                <td>
                    <input type='text' name='{$id}[{$field}]' value='$value' {$required} >
                    <p class='description'>$description</p>    
                </td>
            </tr>" ;
}
?>
    <form  action="/wp-admin/admin-post.php" method="post"> <?php
	foreach ($repositorios as $key =>  $value) {
		?>
        <h1><?php echo ucfirst($value['name']); ?></h1>
        
        <input type="hidden" name="id[]" value="<?php echo 'id_'.$value['id']?>">
        <input type="hidden"  name="id_<?php echo $value['id']?>[id]" value="<?php echo $value['id']?>">
        <input type="hidden" name="action" value="form_config">
        <table class="form-table">
            <tbody>
                <?php echo print_text_field('Nombre del repositorio',"id_{$value['id']}",'name',$value['name'],'Descripción','required');
                echo print_text_field('Protocolo', "id_{$value['id']}",'protocol',$value['protocol'],'Descripción','required');
                echo print_text_field('Dominio', "id_{$value['id']}",'domain',$value['domain'],'Descripción','required');
                echo print_text_field('Path OpenSearch', "id_{$value['id']}",'base_path',$value['base_path'],'Descripción','required');
                echo print_text_field('Formato', "id_{$value['id']}",'format',$value['format'],'Descripción','required');
                echo print_text_field('Query', "id_{$value['id']}",'query',$value['query'],'Descripción','required');
                echo print_text_field('Handle', "id_{$value['id']}",'handle',$value['handle'],'Descripción','required');
                echo print_text_field('Subtipo de documento', "id_{$value['id']}",'subtype',$value['subtype'],'Descripción','required');
                echo print_text_field('Autor',"id_{$value['id']}",'author',$value['author'],'Descripción','required');
                ?>
                <tr>
                <th scope='row'>
                    <label for='delete'><h3>Eliminar repositorio</h3></label>
                </th>
                <td>
                <input type="checkbox" name="delete_repositorios[]" value="<?php echo 'id_'.$value['id'];    ?>">
                    <p class='description'>descripción</p>    
                </td>
                </tr>
                
                </tbody>
        </table>
        
		<?php
	}

	?>
        <hr>
        <h3>Nuevo Repositorio</h3>
        <table class="form-table">
            <tbody>
            <?php echo print_text_field('Nombre del repositorio',"nuevo",'name','','Descripción');
                echo print_text_field('Protocolo', "nuevo",'protocol','','Descripción');
                echo print_text_field('Dominio', "nuevo",'domain','','Descripción');
                echo print_text_field('Path OpenSearch', "nuevo",'base_path','','Descripción');
                echo print_text_field('Formato', "nuevo",'format','','Descripción');
                echo print_text_field('Query', "nuevo",'query','','Descripción');
                echo print_text_field('Handle', "nuevo",'handle','','Descripción');
                echo print_text_field('Subtipo de documento', "nuevo",'subtype','','Descripción');
                echo print_text_field('Autor',"nuevo",'author','','Descripción');
            ?>
             </tbody>
        </table>
         
	     <?php @submit_button()?>
    </form>