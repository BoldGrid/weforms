<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline"><?php _e( 'Entry Details', 'weforms' ); ?></h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}"><?php _e( 'Back to Entries', 'weforms' ); ?></router-link>

    <div v-if="loading"><?php _e( 'Loading...', 'weforms' ); ?></div>
    <div v-else class="wpuf-contact-form-entry-wrap">

        <div class="wpuf-contact-form-entry-left">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span>{{ entry.info.form_title }} : Entry # {{ $route.params.entryid }}</span></h2>

                <div class="main">
                    <table v-if="hasFormFields" class="wp-list-table widefat fixed striped posts">
                        <tbody>
                            <template v-for="(field, index) in entry.form_fields">
                                <tr class="field-label">
                                    <th><strong>{{ field.label }}</strong></th>
                                </tr>
                                <tr class="field-value">
                                    <td>
                                        <weforms-entry-gmap v-if="field.type === 'map'" :lat="entry.meta_data[index]['lat']" :long="entry.meta_data[index]['long']"></weforms-entry-gmap>
                                        <div v-else-if="field.type === 'checkbox' || field.type === 'multiselect'">
                                            <ul style="margin: 0;">
                                                <li v-for="item in entry.meta_data[index]">{{ field.options[item] }}</li>
                                            </ul>
                                        </div>
                                        <div v-else-if="field.type === 'select' || field.type === 'radio'">
                                            {{ field.options[ entry.meta_data[index] ]}}
                                        </div>
                                        <div v-else v-html="entry.meta_data[index]"></div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div v-else><div class="inside"><?php _e( 'Loading...', 'weforms' ); ?></div></div>

                </div>
            </div>
        </div>

        <div class="wpuf-contact-form-entry-right">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php _e( 'Submission Info', 'weforms' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php _e( 'Entry ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ $route.params.entryid }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'User IP', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.ip }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Page', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value"><a :href="entry.info.referer">{{ entry.info.referer }}</a></span>
                            </li>
                            <li v-if="entry.info.user">
                                <span class="label"><?php _e( 'From', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.user }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Submitted On', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.created }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="major-publishing-actions">
                    <div id="publishing-action">
                        <button class="button button-large button-secondary" v-on:click.prevent="trashEntry"><span class="dashicons dashicons-trash"></span><?php _e( ' Delete', 'weforms' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>