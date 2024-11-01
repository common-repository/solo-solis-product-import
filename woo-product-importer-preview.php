<?php /*
    This file is part of Solo Solis Product Importer.

    Solo Solis Product Importer is Copyright 2012-2013 Web Presence Partners LLC.

    Solo Solis Product Importer is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Solo Solis Product Importer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Solo Solis Product Importer.  If not, see <http://www.gnu.org/licenses/>.
*/ 

    if ( ! isset( $_POST['sols_upload_nonce'] ) 
        || ! wp_verify_nonce( $_POST['sols_upload_nonce'], 'sols_import_nonce' ) 
    ) {
        wp_redirect( get_admin_url().'admin.php?page=sols-product-importer');
    }
    
    /* If Direct Access this URL Redirect to First Step */
    if(!isset($_POST['import_json_url'])) wp_redirect( get_admin_url().'admin.php?page=sols-product-importer');
    
    $args = array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode("solosolis:Bright_737")
        )
    );
    $response         = wp_remote_get( esc_url_raw( $_POST['import_json_url'] ), $args );
    $product_json     = wp_remote_retrieve_body( $response );

    $product_obj      = json_decode($product_json,true);

    $error_messages = array();

    if(empty($product_obj)) 
        $error_messages[] = __( 'No data to import.', 'sols-product-importer' );


    $header_row = array();

    foreach ($product_obj[0] as $key => $value) {
        array_push($header_row, $key);
    }
        
    $row_count = sizeof($product_obj);
    //'mapping_hints' should be all lower case
    //(a strtolower is performed on header_row when checking)
    $col_mapping_options = array(

        'do_not_import' => array(
            'label' => __( 'Do Not Import', 'sols-product-importer' ),
            'mapping_hints' => array()),

        'optgroup_general' => array(
            'optgroup' => true,
            'label' => 'General'),

        'post_title' => array(
            'label' => __( 'Name', 'sols-product-importer' ),
            'mapping_hints' => array('title', 'product name')),
        '_sku' => array(
            'label' => __( 'SKU', 'sols-product-importer' ),
            'mapping_hints' => array()),
        'post_content' => array(
            'label' => __( 'Description', 'sols-product-importer' ),
            'mapping_hints' => array('desc', 'content')),
        'post_excerpt' => array(
            'label' => __( 'Short Description', 'sols-product-importer' ),
            'mapping_hints' => array('short desc', 'excerpt')),

        'optgroup_status' => array(
            'optgroup' => true,
            'label' => 'Status and Visibility'),

        'post_status' => array(
            'label' => __( 'Status (Valid: publish/draft/trash/[more in Codex])', 'sols-product-importer' ),
            'mapping_hints' => array('status', 'product status', 'post status')),
        'menu_order' => array(
            'label' => __( 'Menu Order', 'sols-product-importer' ),
            'mapping_hints' => array('menu order')),
        '_visibility' => array(
            'label' => __( 'Visibility (Valid: visible/catalog/search/hidden)', 'sols-product-importer' ),
            'mapping_hints' => array('visibility', 'visible')),
        '_featured' => array(
            'label' => __( 'Featured (Valid: yes/no)', 'sols-product-importer' ),
            'mapping_hints' => array('featured')),
        '_stock' => array(
            'label' => __( 'Stock', 'sols-product-importer' ),
            'mapping_hints' => array('qty', 'quantity')),
        '_stock_status' => array(
            'label' => __( 'Stock Status (Valid: instock/outofstock)', 'sols-product-importer' ),
            'mapping_hints' => array('stock status', 'in stock')),
        '_backorders' => array(
            'label' => __( 'Backorders (Valid: yes/no/notify)', 'sols-product-importer' ),
            'mapping_hints' => array('backorders')),
        '_manage_stock' => array(
            'label' => __( 'Manage Stock (Valid: yes/no)', 'sols-product-importer' ),
            'mapping_hints' => array('manage stock')),
        'comment_status' => array(
            'label' => __( 'Comment/Review Status (Valid: open/closed)', 'sols-product-importer' ),
            'mapping_hints' => array('comment status')),
        'ping_status' => array(
            'label' => __( 'Pingback/Trackback Status (Valid: open/closed)', 'sols-product-importer' ),
            'mapping_hints' => array('ping status', 'pingback status', 'pingbacks', 'trackbacks', 'trackback status')),

        'optgroup_pricing' => array(
            'optgroup' => true,
            'label' => 'Pricing, Tax, and Shipping'),

        '_regular_price' => array(
            'label' => __( 'Regular Price', 'sols-product-importer' ),
            'mapping_hints' => array('price', '_price', 'msrp')),
        '_sale_price' => array(
            'label' => __( 'Sale Price', 'sols-product-importer' ),
            'mapping_hints' => array()),
        '_tax_status' => array(
            'label' => __( 'Tax Status (Valid: taxable/shipping/none)', 'sols-product-importer' ),
            'mapping_hints' => array('tax status', 'taxable')),
        '_tax_class' => array(
            'label' => __( 'Tax Class', 'sols-product-importer' ),
            'mapping_hints' => array()),
        'product_shipping_class_by_id' => array(
            'label' => __( 'Shipping Class By ID (Separated by "|")', 'sols-product-importer' ),
            'mapping_hints' => array()),
        'product_shipping_class_by_name' => array(
            'label' => __( 'Shipping Class By Name (Separated by "|")', 'sols-product-importer' ),
            'mapping_hints' => array('product_shipping_class', 'shipping_class', 'product shipping class', 'shipping class')),
        '_weight' => array(
            'label' => __( 'Weight', 'sols-product-importer' ),
            'mapping_hints' => array('wt')),
        '_length' => array(
            'label' => __( 'Length', 'sols-product-importer' ),
            'mapping_hints' => array('l')),
        '_width' => array(
            'label' => __( 'Width', 'sols-product-importer' ),
            'mapping_hints' => array('w')),
        '_height' => array(
            'label' => __( 'Height', 'sols-product-importer' ),
            'mapping_hints' => array('h')),

        'optgroup_product_types' => array(
            'optgroup' => true,
            'label' => 'Special Product Types'),

        '_downloadable' => array(
            'label' => __( 'Downloadable (Valid: yes/no)', 'sols-product-importer' ),
            'mapping_hints' => array('downloadable')),
        '_virtual' => array(
            'label' => __( 'Virtual (Valid: yes/no)', 'sols-product-importer' ),
            'mapping_hints' => array('virtual')),
        '_product_type' => array(
            'label' => __( 'Product Type (Valid: simple/variable/grouped/external)', 'sols-product-importer' ),
            'mapping_hints' => array('product type', 'type')),
        '_button_text' => array(
            'label' => __( 'Button Text (External Product Only)', 'sols-product-importer' ),
            'mapping_hints' => array('button text')),
        '_product_url' => array(
            'label' => __( 'Product URL (External Product Only)', 'sols-product-importer' ),
            'mapping_hints' => array('product url', 'url')),
        '_file_paths' => array(
            'label' => __( 'File Path (Downloadable Product Only)', 'sols-product-importer' ),
            'mapping_hints' => array('file path', 'file', 'file_path', 'file paths')),
        '_download_expiry' => array(
            'label' => __( 'Download Expiration (in Days)', 'sols-product-importer' ),
            'mapping_hints' => array('download expiration', 'download expiry')),
        '_download_limit' => array(
            'label' => __( 'Download Limit (Number of Downloads)', 'sols-product-importer' ),
            'mapping_hints' => array('download limit', 'number of downloads')),

        'optgroup_taxonomies' => array(
            'optgroup' => true,
            'label' => 'Categories and Tags'),

        'product_cat_by_name' => array(
            'label' => __( 'Categories By Name (Separated by "|")', 'sols-product-importer' ),
            'mapping_hints' => array('category', 'categories', 'product category', 'product categories', 'product_cat')),
        'product_cat_by_id' => array(
            'label' => __( 'Categories By ID (Separated by "|")', 'sols-product-importer' ),
            'mapping_hints' => array()),
        'product_tag_by_name' => array(
            'label' => __( 'Tags By Name (Separated by "|")', 'sols-product-importer' ),
            'mapping_hints' => array('tag', 'tags', 'product tag', 'product tags', 'product_tag')),
        'product_tag_by_id' => array(
            'label' => __( 'Tags By ID (Separated by "|")', 'sols-product-importer' ),
            'mapping_hints' => array()),

        'optgroup_custom' => array(
            'optgroup' => true,
            'label' => 'Custom Attributes and Post Meta'),

        'custom_field' => array(
            'label' => __( 'Custom Field / Product Attribute (Set Name Below)', 'sols-product-importer' ),
            'mapping_hints' => array('custom field', 'custom')),
        'post_meta' => array(
            'label' => __( 'Post Meta', 'sols-product-importer' ),
            'mapping_hints' => array('postmeta')),

        'optgroup_images' => array(
            'optgroup' => true,
            'label' => 'Product Images'),

        'product_image_by_url' => array(
            'label' => __( 'Images (By URL, Separated by ";")', 'sols-product-importer' ),
            'mapping_hints' => array('image', 'images', 'image url', 'image urls', 'product image url', 'product image urls', 'product images')),
        'product_image_by_path' => array(
            'label' => __( 'Images (By Local File Path, Separated by ";")', 'sols-product-importer' ),
            'mapping_hints' => array('image path', 'image paths', 'product image path', 'product image paths'))
    );

?>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $("select.map_to").change(function(){

            if($(this).val() == 'custom_field') {
                $(this).closest('th').find('.custom_field_settings').show(400);
            } else {
                $(this).closest('th').find('.custom_field_settings').hide(400);
            }

            if($(this).val() == 'product_image_by_url' || $(this).val() == 'product_image_by_path') {
                $(this).closest('th').find('.product_image_settings').show(400);
            } else {
                $(this).closest('th').find('.product_image_settings').hide(400);
            }

            if($(this).val() == 'post_meta') {
                $(this).closest('th').find('.post_meta_settings').show(400);
            } else {
                $(this).closest('th').find('.post_meta_settings').hide(400);
            }
        });

        //to show the appropriate settings boxes.
        $("select.map_to").trigger('change');

        $(window).resize(function(){
            $("#import_data_preview").addClass("fixed").removeClass("super_wide");
            $("#import_data_preview").css("width", "100%");

            var cell_width = $("#import_data_preview tbody tr:first td:last").width();
            if(cell_width < 60) {
                $("#import_data_preview").removeClass("fixed").addClass("super_wide");
                $("#import_data_preview").css("width", "auto");
            }
        });

        //set table layout
        $(window).trigger('resize');
    });
</script>

<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2><?php _e( 'Solo Solis Product Importer &raquo; Preview', 'sols-product-importer' ); ?></h2>
    <p><?php _e( 'We are showing only first 10 products from JSON Data', 'sols-product-importer' ); ?></p>
    <?php if(sizeof($error_messages) > 0): ?>
        <ul class="import_error_messages">
            <?php foreach($error_messages as $message):?>
                <li><?php echo $message; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if($row_count > 0): ?>
        <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'admin.php?page=sols-product-importer&action=result'; ?>">
            <input type="hidden" name="import_json_url" value="<?php echo esc_url($_POST['import_json_url']); ?>">
            <input type="hidden" name="row_count" value="<?php echo esc_html( $row_count ); ?>">
            <p>
                <button class="button-primary" type="submit"><?php _e( 'Import', 'sols-product-importer' ); ?></button>
            </p>

            <table id="import_data_preview" class="wp-list-table widefat fixed pages" cellspacing="0">
                <thead>
                    <tr class="header_row">
                        <th colspan="<?php echo sizeof($header_row); ?>"><?php _e( 'CSV Header Row', 'sols-product-importer' ); ?></th>
                    </tr>
                    <tr class="header_row">
                        <?php foreach($header_row as $col): ?>
                            <th><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php
                            reset($product_obj);
                            $first_row = current($product_obj);
                            foreach($first_row as $key => $col):
                        ?>
                            <th>
                                <div class="map_to_settings">
                                    <?php _e( 'Map to:', 'sols-product-importer' ); ?> <select name="map_to[<?php echo esc_html($key,'sols-product-importer'); ?>]" class="map_to">
                                        <optgroup>
                                        <?php foreach($col_mapping_options as $value => $meta): ?>
                                            <?php if(array_key_exists('optgroup', $meta) && $meta['optgroup'] === true): ?>
                                                </optgroup>
                                                <optgroup label="<?php echo esc_html($meta['label'],'sols-product-importer'); ?>">
                                            <?php else: ?>
                                                <option value="<?php echo esc_html($value,'sols-product-importer'); ?>" <?php
                                                    $header_value = $key;
                                                    if( $header_value == strtolower($value) ||
                                                        $header_value == strtolower($meta['label']) ||
                                                        in_array($header_value, $meta['mapping_hints']) ) {

                                                        echo 'selected="selected"';
                                                    }
                                                    
                                                ?>><?php echo esc_html($meta['label'],'sols-product-importer'); ?></option>
                                            <?php endif;?>
                                        <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="custom_field_settings field_settings">
                                    <h4><?php _e( 'Custom Field Settings', 'sols-product-importer' ); ?></h4>
                                    <p>
                                        <label for="custom_field_name_<?php echo esc_html($key,'sols-product-importer'); ?>"><?php _e( 'Name', 'sols-product-importer' ); ?></label>
                                        <input type="text" name="custom_field_name[<?php echo esc_html($key,'sols-product-importer'); ?>]" id="custom_field_name_<?php echo esc_html($key,'sols-product-importer'); ?>" value="<?php echo esc_html($key,'sols-product-importer'); ?>" />
                                    </p>
                                    <p>
                                        <input type="checkbox" name="custom_field_visible[<?php echo esc_html($key,'sols-product-importer'); ?>]" id="custom_field_visible_<?php echo esc_html($key,'sols-product-importer'); ?>" value="1" checked="checked" />
                                        <label for="custom_field_visible_<?php echo esc_html($key,'sols-product-importer'); ?>"><?php _e( 'Visible?', 'sols-product-importer' ); ?></label>
                                    </p>
                                </div>
                                <div class="product_image_settings field_settings">
                                    <h4><?php _e( 'Image Settings', 'sols-product-importer' ); ?></h4>
                                    <p>
                                        <input type="checkbox" name="product_image_set_featured[<?php echo esc_html($key,'sols-product-importer'); ?>]" id="product_image_set_featured_<?php echo esc_html($key,'sols-product-importer'); ?>" value="1" checked="checked" />
                                        <label for="product_image_set_featured_<?php echo esc_html($key,'sols-product-importer'); ?>"><?php _e( 'Set First Image as Featured', 'sols-product-importer' ); ?></label>
                                    </p>
                                    <p>
                                        <input type="checkbox" name="product_image_skip_duplicates[<?php echo esc_html($key,'sols-product-importer'); ?>]" id="product_image_skip_duplicates_<?php echo esc_html($key,'sols-product-importer'); ?>" value="1" checked="checked" />
                                        <label for="product_image_skip_duplicates_<?php echo esc_html($key,'sols-product-importer'); ?>"><?php _e( 'Skip Duplicate Images', 'sols-product-importer' ); ?></label>
                                    </p>
                                </div>
                                <div class="post_meta_settings field_settings">
                                    <h4><?php _e( 'Post Meta Settings', 'sols-product-importer' ); ?></h4>
                                    <p>
                                        <label for="post_meta_key_<?php echo esc_html($key,'sols-product-importer'); ?>"><?php _e( 'Meta Key', 'sols-product-importer' ); ?></label>
                                        <input type="text" name="post_meta_key[<?php echo esc_html($key,'sols-product-importer'); ?>]" id="post_meta_key_<?php echo esc_html($key,'sols-product-importer'); ?>" value="<?php echo esc_html($key,'sols-product-importer'); ?>" />
                                    </p>
                                </div>
                            </th>
                        <?php
                            endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    foreach($product_obj as $row_id => $row): ?>
                        <tr>
                            <?php foreach($row as $col): ?>
                                <td><?php echo htmlspecialchars($col); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php 
                    $counter++;
                    if($counter > 10) break;
                    endforeach; ?>
                </tbody>
            </table>
            <?php wp_nonce_field( 'sols_import_nonce', 'sols_preview_nonce' ); ?>
        </form>
    <?php endif; ?>
</div>