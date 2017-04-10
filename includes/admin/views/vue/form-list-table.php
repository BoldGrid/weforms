<div class="content table-responsive table-full-width" style="margin-top: 20px;">

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
            <select name="action" id="bulk-action-selector-top">
                <option value="-1">Bulk Actions</option>
                    <option value="trash">Move to Trash</option>
            </select>

            <input type="submit" id="doaction" class="button action" value="Apply">
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col" class="col-form-name">Name</th>
                <th scope="col" class="col-form-shortcode">Shortcode</th>
                <th scope="col" class="col-form-entries">Entries</th>
                <th scope="col" class="col-form-views">Views</th>
                <th scope="col" class="col-form-conversion">Conversion</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="6">Loading...</td>
            </tr>
            <tr v-if="!forms.length && !loading">
                <td colspan="6">No form found!</td>
            </tr>
            <tr v-for="(form, index) in forms">
                <th scope="row" class="check-column">
                    <input id="cb-select-664" type="checkbox" name="post[]" value="">
                </th>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><a :href="'<?php echo admin_url( 'admin.php?page=wpuf-contact-forms&action=edit&id=') ?>' + form.ID">{{ form.post_title }}</a></strong>

                    <div class="row-actions">
                        <span class="edit"><a href="#">Edit</a> | </span>
                        <span class="trash"><a href="#" v-on:click.prevent="deleteForm(index)" class="submitdelete">Trash</a> | </span>
                        <span class="duplicate"><a href="#">Duplicate</a> | </span>
                        <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.ID }}">View Entries</router-link>
                    </div>
                </td>
                <td><code>[wpuf_contact_form id="{{ form.ID }}"]</code></td>
                <td>
                    <router-link v-if="form.entries" :to="{ name: 'formEntries', params: { id: form.ID }}">{{ form.entries }}</router-link>
                    <span v-else>&mdash;</span>
                </td>
                <td>0</td>
                <td>0%</td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col" class="col-form-name">Name</th>
                <th scope="col" class="col-form-shortcode">Shortcode</th>
                <th scope="col" class="col-form-entries">Entries</th>
                <th scope="col" class="col-form-views">Views</th>
                <th scope="col" class="col-form-conversion">Conversion</th>
            </tr>
        </tfoot>
    </table>
</div>