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
?>
<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2><?php _e( 'Solo Solis Product Importer &raquo; Upload', 'sols-product-importer' ); ?></h2>

    <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'admin.php?page=sols-product-importer&action=preview'; ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="import_json_url"><?php _e( 'URL to Import', 'sols-product-importer' ); ?></label></th>
                    <td>
                        <input type="text" name="import_json_url" id="import_json_url" class="regular-text code" required>
                        <p class="description"><?php _e( 'Enter the full URL of Json.', 'sols-product-importer' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <button class="button-primary" type="submit"><?php _e( 'Submit and Preview', 'sols-product-importer' ); ?></button>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field( 'sols_import_nonce', 'sols_upload_nonce' ); ?>
    </form>
</div>
