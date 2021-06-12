<?php
$result = '';
if(isset($_POST['add_url'])){
    $url = $_POST['newurl'];
    $result = $this->junu_add_url_callback($url);
}

?>
<div id="export" class="moresell-contents tabs">
    <div id="post-body" class="post-contents">
        <span style="<?php echo ($result == 'Added Successfull.'?'color: #2271b1':'color: red') ?>" class="notices"><?php echo ($result !== ""?$result:'') ?></span>
        <form action="" method="post">
            <button name="export_products" class="button button-primary">Export Product</button>
        </form>
        <div class="urls">
            <table id="urls_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Url</th>
                        <th>Published</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rows = $this->moresell_urls_table();

                    if($rows){
                        $i = 1;
                        foreach($rows as $row){
                            ?>
                            <tr>
                                <td><?php echo __($i, 'moresell') ?></td>
                                <td><?php echo __($row->site_url, 'moresell') ?></td>
                                <td>
                                    <?php echo $this->moresell_published_urls($row->site_url); ?>
                                </td>
                                <td><?php echo __($row->create_at, 'moresell') ?></td>
                                <td><button data-id="<?php echo intval($row->ID) ?>" class="delete_url">❌</button></td>
                            </tr>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="sidebararea" id="postbox-container">
        <div id="postimagediv" class="postbox">
            <div class="postbox-header">
                <h2>Add Site</h2>
            </div>
            <div class="inside">
                <div class="widefat">
                    <form action="" class="urladdbox" method="post">
                        <input type="url" placeholder="Site Url" name="newurl" class="addurlinp">
                        <button name="add_url" class="createbtn button-secondary"><span class="savebtn">✓</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>