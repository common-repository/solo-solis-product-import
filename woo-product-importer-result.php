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
    if ( ! isset( $_POST['sols_preview_nonce'] ) 
        || ! wp_verify_nonce( $_POST['sols_preview_nonce'], 'sols_import_nonce' ) 
    ) {
        wp_redirect( get_admin_url().'admin.php?page=sols-product-importer');
    }
    
    if(!isset($_POST['import_json_url'])) wp_redirect( get_admin_url().'admin.php?page=sols-product-importer');
    
    $json_url                           = esc_url_raw($_POST['import_json_url']);
    $map_to                             = sols_recursive_sanitize_text_field($_POST['map_to']);
    $custom_field_name                  = sols_recursive_sanitize_text_field($_POST['custom_field_name']);
    $custom_field_visible               = sols_recursive_sanitize_text_field($_POST['custom_field_visible']);
    $product_image_set_featured         = sols_recursive_sanitize_text_field($_POST['product_image_set_featured']);
    $product_image_skip_duplicates      = sols_recursive_sanitize_text_field($_POST['product_image_skip_duplicates']);
    $post_meta_key                      = sols_recursive_sanitize_text_field($_POST['post_meta_key']);

    $post_data = array(
        'import_json_url'               => $json_url,
        'map_to'                        => $map_to,
        'custom_field_name'             => $custom_field_name,
        'custom_field_visible'          => $custom_field_visible,
        'product_image_set_featured'    => $product_image_set_featured,
        'product_image_skip_duplicates' => $product_image_skip_duplicates,
        'post_meta_key'                 => $post_meta_key,
        'row_count'                     => sanitize_text_field($_POST['row_count']),
    );

    update_option( 'import_solo_solis_json_data', $post_data);

    self::logger('New Json Import Data set');

?>
<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2><?php _e( 'Solo Solis Product Importer &raquo; Results', 'sols-product-importer' ); ?></h2>

    <ul class="import_error_messages">
    </ul>

    <div id="import_status">
        <table>
            <tbody>
                <tr>
                    <th colspan="2"><?php _e( 'Product Import Cron Set.', 'sols-product-importer' ); ?></th>
                </tr>
                <tr>
                    <th><?php _e( 'Total Product to import: ', 'sols-product-importer' ); ?></th>
                    <td id="row_count"><?php echo esc_html($post_data['row_count'],'sols-product-importer'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>