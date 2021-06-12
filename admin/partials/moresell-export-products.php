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
            $i = 1;
            foreach($products as $product){
                ?>
                <li>
                    <span class="serial"><?php echo ($i) ?>.</span>
                    <input type="checkbox" name="select_product_item[]" class="select_product_item" value="<?php echo $product->ID; ?>">
                    <span class="productName"><?php echo __($product->post_title, 'moresell'); ?></span>
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
                            $rows = $this->moresell_urls_table();
                            $i = 1;
                            if($rows){
                                foreach($rows as $row){
                                    ?>
                                    <option value="<?php echo intval($row->ID) ?>"><?php echo __($row->site_url, 'moresell') ?></option>
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
                    <th><label for="select__cat">Category</label></th>
                    <td>
                        <?php
                        $args = [
                            'show_option_all'   => '',
                            'show_option_none'  => 'Select Category',
                            'orderby'           => 'id',
                            'order'             => 'ASC',
                            'show_count'        => 0,
                            'hide_empty'        => 1,
                            'child_of'          => 0,
                            'exclude'           => '',
                            'echo'              => 1,
                            'selected'          => 0,
                            'hierarchical'      => 0,
                            'name'              => 'select__cat',
                            'id'                => 'select__cat',
                            'class'             => 'select__cat',
                            'depth'             => 0,
                            'tab_index'         => 0,
                            'taxonomy'          => 'product_cat',
                            'hide_if_empty'     => false,
                            'option_none_value' => -1,
                            'value_field'       => 'name',
                            'required'          => true,
                        ];
                        wp_dropdown_categories( $args );
                        ?>
                    </td>
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