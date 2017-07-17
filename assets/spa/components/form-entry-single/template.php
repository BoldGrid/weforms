<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline"><?php _e( 'Entry Details', 'best-contact-form' ); ?></h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}"><?php _e( 'Back to Entries', 'best-contact-form' ); ?></router-link>

    <div v-if="loading"><?php _e( 'Loading...', 'best-contact-form' ); ?></div>
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
                                        <div v-html="entry.meta_data[index]"></div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div v-else><div class="inside"><?php _e( 'Loading...', 'best-contact-form' ); ?></div></div>

                </div>
            </div>
        </div>

        <div class="wpuf-contact-form-entry-right">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php _e( 'Submission Info', 'best-contact-form' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php _e( 'Entry ID', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ $route.params.entryid }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'User IP', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.ip }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Page', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value"><a :href="entry.info.referer">{{ entry.info.referer }}</a></span>
                            </li>
                            <li v-if="entry.info.user">
                                <span class="label"><?php _e( 'From', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.user }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Submitted On', 'best-contact-form' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.info.created }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="major-publishing-actions">
                    <div id="publishing-action">
                        <button class="button button-large button-secondary" v-on:click.prevent="trashEntry"><span class="dashicons dashicons-trash"></span><?php _e( ' Delete', 'best-contact-form' ); ?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>