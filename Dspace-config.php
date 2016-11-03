<?php
/**
 * Description of Dspace-config
 *
 * @author SEDICI - Paula Salamone
 */
function dspace_config() {
    add_options_page(
        'Dspace Plugin',
        'Dspace configuration',
        'manage_options',
        'dspace_extra_information',
        'dspace_options_page' 
    );
}
 
/**
 * Register the settings
 */
function dspace_register_settings() {
     register_setting(
        'dspace_settings',  // settings section
        'configuration' // setting name
     );
}
add_action( 'admin_init', 'dspace_register_settings' ); 

function dsmenu_scripts() {
	// include js archives
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'Dspace-config', plugins_url ( 'media/js/ds-menu.js', __FILE__ ), array ("jquery"), null, true );
	wp_enqueue_script ( 'Dspace-config' );
}
add_action ( 'admin_enqueue_scripts', 'dsmenu_scripts' );
function dsmenu_styles() {
	//include the style
	wp_register_style ( 'Dspace-config', plugins_url ('media/css/ds-menu.css', __FILE__ ));
	wp_enqueue_style ( 'Dspace-config' );
}
add_action ( 'admin_enqueue_scripts', 'dsmenu_styles' );
/**
 * Build the options page
 */
function dspace_options_page() {
    $cant=3;
    if(isset($_POST['config'])){
        precargar($_POST['config']);
    }
?> 
<div id="menu-selector" class="fade-transition">
   <ul> 
   <?php for ($i = 1; $i <= $cant; $i++) { 
       $menu="menu".$i;
       ?> 
   <li><a href="#<?php echo $menu; ?>" title="Configuración <?php echo $i; ?>"><?php echo _e('Modulo de configuración ');echo $i; ?></a></li>

   <?php } ?>
   </ul>
    
<?php for ($i = 1; $i <= $cant; $i++) {
       $menu="menu".$i;
       settings_fields( 'dspace_settings' );
       do_settings_sections( 'dspace_settings' );
       $options = get_option( 'configuration' );
?>  
<div class="wrap" id="<?php echo $menu; ?>">
<form method="post" action="">  
<h2><?php echo _e('Modulo ');echo $i; ?></h2>
    <input type="hidden"  name="configuration[<?php echo $i; ?>][id]" value="<?php echo $i; ?>" />
    <table class="form-table">
            <tr valign="top">
                <th scope="row">Extra post info:</th>
                <td><input type="text" name="configuration[<?php echo $i; ?>][prueba]" value="<?php echo  $options[$i]['prueba']; ?>"/></td>
            </tr>
    </table>
    <?php submit_button(); ?>
</form>
</div>    
<?php } ?>    
</div>

<div class="Dspace-configuration">
<form method="post" action="options-general.php?page=dspace_extra_information">  
     <p>
        <label for="config"><?php _e('Seleccionar configuración: '); ?> 
            <select class='widefat' id="algo" name="config" type="text"  style="width: 200px" >
		<option value="Sedici">
                       Sedici
		</option>
                <option value="Cic">
                       Cic
		</option>
            </select>
        </label>
        </p>
    <?php submit_button('Pre-cargar'); ?>
</form>
    </div>
</div>
<?php
}

function precargar($id){
    echo $id;
}