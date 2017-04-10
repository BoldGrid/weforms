<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline">Entry Details</h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}">Back to Entries</router-link>



    <div class="wpuf-contact-form-entry-wrap">

        <div class="wpuf-contact-form-entry-left">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span>Contact Form : Entry # 3</span></h2>

                <div class="main">
                    <table v-if="hasFormFields" class="wp-list-table widefat fixed striped posts">
                        <tbody>
                            <template v-for="(label, index) in entry.form_fields">
                                <tr>
                                    <th><strong>{{ label }}</strong></th>
                                </tr>
                                <tr>
                                    <td>{{ entry.meta_data[index] }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div v-else><div class="inside">Loading...</div></div>

                </div>
            </div>
        </div>

        <div class="wpuf-contact-form-entry-right">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span>Submission Info</span></h2>
                <div class="inside">
                    <div class="main">
                        hello

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <pre>{{ entry }}</pre> -->

</div>