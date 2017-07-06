<div class="content table-responsive table-full-width" style="margin-top: 20px;">

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text"><?php _e( 'Select bulk action', 'best-contact-form' ); ?></label>
            <select name="action" id="bulk-action-selector-top">
                <option value="-1"><?php _e( 'Bulk Actions', 'best-contact-form' ); ?></option>
            </select>

            <input type="submit" id="doaction" class="button action" value="Apply">
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'best-contact-form' ); ?></label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col" class="col-form-name"><?php _e( 'Name', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php _e( 'Shortcode', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-entries"><?php _e( 'Entries', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-views"><?php _e( 'Views', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php _e( 'Conversion', 'best-contact-form' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="6"><?php _e( 'Loading...', 'best-contact-form' ); ?></td>
            </tr>
            <tr v-if="!forms.length && !loading">
                <td colspan="6"><?php _e( 'No form found!', 'best-contact-form' ); ?></td>
            </tr>
            <tr v-for="(form, index) in forms">
                <th scope="row" class="check-column">
                    <input id="cb-select-664" type="checkbox" name="post[]" value="">
                </th>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><a :href="'<?php echo admin_url( 'admin.php?page=wpuf-contact-forms&action=edit&id=') ?>' + form.ID">{{ form.post_title }}</a> <span v-if="form.post_status != 'publish'">({{ form.post_status }})</span></strong>

                    <div class="row-actions">
                        <span class="edit"><a :href="'<?php echo admin_url( 'admin.php?page=wpuf-contact-forms&action=edit&id=') ?>' + form.ID"><?php _e( 'Edit', 'best-contact-form' ); ?></a> | </span>
                        <span class="trash"><a href="#" v-on:click.prevent="deleteForm(index)" class="submitdelete"><?php _e( 'Delete', 'best-contact-form' ); ?></a> | </span>
                        <span class="duplicate"><a href="#" v-on:click.prevent="duplicate(form.ID, index)"><?php _e( 'Duplicate', 'best-contact-form' ); ?></a> <template v-if="form.entries">|</template> </span>
                        <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.ID }}"><?php _e( 'View Entries', 'best-contact-form' ); ?></router-link>
                    </div>
                </td>
                <td><code>[wpuf_contact_form id="{{ form.ID }}"]</code></td>
                <td>
                    <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.ID }}">{{ form.entries }}</router-link>
                    <span v-else>&mdash;</span>
                </td>
                <td>{{ form.views }}</td>
                <td>
                    <span v-if="form.views">{{ ((form.entries/form.views) * 100).toFixed(2) }}%</span>
                    <span v-else>0%</span>
                </td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'best-contact-form' ); ?></label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col" class="col-form-name"><?php _e( 'Name', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-shortcode"><?php _e( 'Shortcode', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-entries"><?php _e( 'Entries', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-views"><?php _e( 'Views', 'best-contact-form' ); ?></th>
                <th scope="col" class="col-form-conversion"><?php _e( 'Conversion', 'best-contact-form' ); ?></th>
            </tr>
        </tfoot>
    </table>
</div>