<div class="content table-responsive table-full-width" style="margin-top: 20px;">

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col">Name</th>
                <th scope="col">Shortcode</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="4">Loading...</td>
            </tr>
            <tr v-if="!forms.length && !loading">
                <td colspan="4">No form found!</td>
            </tr>
            <tr v-for="(form, index) in forms">
                <th scope="row" class="check-column">
                    <input id="cb-select-664" type="checkbox" name="post[]" value="">
                </th>
                <td class="title column-title has-row-actions column-primary page-title">
                    <strong><router-link :to="{ name: 'form', params: { id: form.ID }}">{{ form.post_title }}</router-link></strong>

                    <div class="row-actions">
                        <span class="edit"><a href="#">Edit</a> | </span>
                        <span class="trash"><a href="#" v-on:click.prevent="deleteForm(index)" class="submitdelete">Trash</a> | </span>
                        <span class="duplicate"><a href="#">Duplicate</a></span>
                    </div>
                </td>
                <td><code>[wpuf_contact_form id="{{ form.ID }}"]</code></td>
                <td><router-link :to="{ name: 'formEntries', params: { id: form.ID }}">Entries</router-link></td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <th scope="col">Name</th>
                <th scope="col">Shortcode</th>
                <th scope="col">Action</th>
            </tr>
        </tfoot>
    </table>
</div>