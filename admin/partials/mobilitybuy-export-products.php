<div class="exportpproduct_wrap tabs" id="export">

    <div class="product_select">
        <label for="select_all">
            <input type="checkbox" name="select_all" class="select_all" id="select_all">
            Select Products
        </label>
        <ul>
        <?php
        $products = get_posts( ['post_type' => 'product', 'status' => 'publish', 'nopaging' => true] );
        if($products){
            global $wpdb;
            $i = 1;
            foreach($products as $product){
                $bonusrate = $wpdb->get_var("SELECT bonus_rate FROM {$wpdb->prefix}mobilitybuy_published_urls_v1 WHERE product_id = {$product->ID}");
                ?>
                <li>
                    <span class="serial"><?php echo ($i) ?>.</span>
                    <input data-id="<?php echo $bonusrate ?>" type="checkbox" name="select_product_item[]" class="select_product_item" value="<?php echo $product->ID; ?>">
                    <span class="productName"><?php echo __($product->post_title, 'mobilitybuy'); ?></span>
                </li>
                <?php
                $i++;
            }
        }
        ?> 
        </ul>
    </div>

    <div class="publishingrool">
        <table id="export_table">
            <thead>
                <tr>
                    <th><label for="select__site">Site</label></th>
                    <th>
                        <select name="select__site" id="select__site">
                            <option value="-1">Select site</option>
                            <?php
                            $rows = $this->mobilitybuy_urls_table();
                            $i = 1;
                            if($rows){
                                foreach($rows as $row){
                                    ?>
                                    <option value="<?php echo intval($row->ID) ?>"><?php echo __($row->site_url, 'mobilitybuy') ?></option>
                                    <?php
                                }
                            }
                        ?> 
                        </select>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th><label for="bonus_rate">Bonus rate</label></th>
                    <td><input type="number" name="bonus_rate" id="bonus_rate" placeholder="10%"></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <select name="select__status" id="select__status">
                            <option value="publish">Publish</option>
                            <option value="draft">Draft</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <button id="exportbtn" class="button button-success"><span class="btntxt">Export</span> <span class="loading">↻</span></button>
    </div>
</div>