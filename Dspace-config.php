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
        'values' // setting name
     );
}
add_action( 'admin_init', 'dspace_register_settings' ); 

/**
 * Build the options page
 */
function dspace_options_page() {
?>
     <div class="wrap">
          <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
        <div id="post-body-content">
        <form method="post" action="options.php">
            <?php settings_fields( 'dspace_settings' ); ?>
            <?php do_settings_sections( 'dspace_settings' ); ?>
            <?php $options = get_option( 'values' ); ?>
            <table class="form-table">
            <tr valign="top">
                <th scope="row">Extra post info:</th>
                <td><input type="text" name="values[prueba]" value="<?php echo  $options['prueba']; ?>"/></td>
            </tr>
            </table>
        <?php submit_button(); ?>
        </form>
        </div> <!-- end post-body-content -->
     </div>
<?php
}