<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Moresell
 * @subpackage Moresell/admin/partials
 */

require_once MORESELL_PATH.'admin/class-moresell-admin.php';
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php

if(isset($_POST['keys_save'])){
    if(!empty($_POST['consumar_key'])){
        $consumar_key = sanitize_key( $_POST['consumar_key'] );
        update_option('moresell_consumar_key', $consumar_key);
    }
    if(!empty($_POST['consumer_secret'])){
        $consumer_secret = sanitize_key( $_POST['consumer_secret'] );
        update_option('moresell_consumer_secret', $consumer_secret);
    }
}

?>

<div id="moresell-wrapp">
    <div class="tab_btns">
        <button style="background-color:#fff" class="button-tab export" onclick="openCity('export')">Manage Site</button>
        <button class="button-tab settings" onclick="openCity('settings')">Settings</button>
    </div>

    <?php
    if(isset($_POST['export_products'])){
        require_once 'moresell-export-products.php';
    }else{
        require_once 'morell-exported-content.php';
    }
    ?>
    <div id="settings" class="settings tabs" style="display:none">
        <div class="setting__content">
            <form action="" method="post">
                <table id="wookeys">
                    <tbody>
                        <tr>
                            <th><label for="consumar_key">Consumer key</label></th>
                            <td><input type="text" name="consumar_key" id="consumar_key" value="<?php echo get_option('moresell_consumar_key','') ?>"></td>
                        </tr>
                        <tr>
                            <th><label for="consumer_secret">Consumer secret</label></th>
                            <td><input type="text" name="consumer_secret" id="consumer_secret" value="<?php echo get_option('moresell_consumer_secret','') ?>"></td>
                        </tr>
                    </tbody>
                </table>
                
                <button name="keys_save" class="button button-secondary">Save</button>
            </form>
        </div>
    </div>
</div>

<script>
function openCity(elem) {
    var g;
    var x = document.getElementsByClassName('button-tab');
    for (g = 0; g < x.length; g++) {
        x[g].style.backgroundColor = "transparent"; 
    }
    document.getElementsByClassName(elem)[0].style.backgroundColor = "#fff";

    var i;
    var x = document.getElementsByClassName("tabs");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";  
    }
    document.getElementById(elem).style.display = "flex";  
}
</script>