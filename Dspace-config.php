<?php
/**
 * Description of Dspace-config
 *
 * @author paw
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

function dsmenu_styles() {
	//include the style
	wp_register_style ( 'Dspace-config', plugins_url ('media/css/ds-menu.css', __FILE__ ));
	wp_enqueue_style ( 'Dspace-config' );
}
add_action ( 'admin_enqueue_scripts', 'dsmenu_styles' );

function dsmenu_scripts_method() {
	// include js archives
	wp_enqueue_script ( 'jquery' );
	wp_register_script ( 'Dspace-config', plugins_url ( 'media/js/ds-menu.js', __FILE__ ), array ("jquery"), null, true );
	wp_enqueue_script ( 'Dspace-config' );
}
add_action ( 'admin_enqueue_scripts', 'dsmenu_scripts_method' );
/**
 * Build the options page
 */
function dspace_options_page() {
?>
<div id="menu-selector">
   <ul> 
   <?php for ($i = 1; $i < 4; $i++) { 
       $menu="menu".$i;
    ?> 
   <li><a href="#<?php echo $menu; ?>" title="Opción <?php echo $i; ?>">Modulo <?php echo $i; ?></a></li>
   <?php } ?> <!--  End for  -->
   </ul>

<form method="post" action="options.php">    
<?php for ($i = 1; $i < 4; $i++) { 
       $menu="menu".$i;
?>
<div id="<?php echo $menu; ?>">
<h1>Modulo <?php echo $i; ?></h1>

    <?php settings_fields( 'dspace_settings' ); ?>
    <?php do_settings_sections( 'dspace_settings' ); ?>
    <?php $options = get_option( 'configuration' ); ?>
    <input type="hidden"  name="<?php echo 'configuration['.$i.'][prueba]'; ?>" value="<?php echo $options[$i]['id']; ?>" />
    <table class="form-table">
            <tr valign="top">
                <th scope="row">Extra post info:</th>
                <td><input type="text" name="<?php echo 'configuration['.$i.'][prueba]'; ?>" value="<?php echo  $options[$i]['prueba']; ?>"/></td>
            </tr>
    </table>
    <?php submit_button(); ?>
</div> <!-- end id menu -->
<?php }  ?> <!-- end for -->
</div> <!-- end id=menu-selector -->
</form>
<?php
}
