<div class="wpuf-contact-form-entry">
    <h1 class="wp-heading-inline"><?php _e( 'Entry Details', 'weforms' ); ?></h1>
    <router-link class="page-title-action" :to="{ name: 'formEntries', params: { id: $route.params.id }}"><?php _e( 'Back to Entries', 'weforms' ); ?></router-link>

    <div v-if="loading"><?php _e( 'Loading...', 'weforms' ); ?></div>
    <div v-else class="wpuf-contact-form-entry-wrap">

        <div class="wpuf-contact-form-entry-left">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span>{{ entry.meta_data.form_title }} : Entry # {{ $route.params.entryid }}</span></h2>

                <div class="main">
                    <table v-if="hasFormFields" class="wp-list-table widefat fixed striped posts">
                        <tbody>
                            <template v-for="(field, index) in entry.form_fields">
                                <tr class="field-label">
                                    <th><strong>{{ field.label }}</strong></th>
                                </tr>
                                <tr class="field-value">
                                    <td>
                                        <weforms-entry-gmap :lat="entry.meta_data[index]['lat']" :long="entry.meta_data[index]['long']" v-if="field.type == 'map'"></weforms-entry-gmap>
                                        <div v-else-if="field.type === 'checkbox_field' || field.type === 'multiple_select'">
                                            <ul style="margin: 0;">
                                                <li v-for="item in field.value">- {{ item }}</li>
                                            </ul>
                                        </div>
                                        <div v-else v-html="field.value"></div>
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
                                <span class="value">{{ entry.meta_data.ip_address }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Page', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value"><a :href="entry.meta_data.referer">{{ entry.meta_data.referer }}</a></span>
                            </li>
                            <li v-if="entry.meta_data.user">
                                <span class="label"><?php _e( 'From', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.user }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Submitted On', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.meta_data.created }}</span>
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

        <div class="wpuf-contact-form-entry-right" v-if="entry.payment_data" style=" clear: right;">
            <div class="postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php _e( 'Payment Info', 'weforms' ); ?></span></h2>
                <div class="inside">
                    <div class="main">

                        <ul>
                            <li>
                                <span class="label"><?php _e( 'Payment ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">#{{ entry.payment_data.id }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Gateway', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.gateway }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Status', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.status }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Total', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.total }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Transaction ID', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.transaction_id ? entry.payment_data.transaction_id : 'N/A' }}</span>
                            </li>
                            <li>
                                <span class="label"><?php _e( 'Created at', 'weforms' ); ?></span>
                                <span class="sep"> : </span>
                                <span class="value">{{ entry.payment_data.created_at }}</span>
                            </li>

                            <li v-if="entry.payment_data.payment_data">

                                <template v-if="show_payment_data" class="value" v-for="(val,key) in entry.payment_data.payment_data">
                                    <template v-if="key && (val === false || val)">
                                        <li>
                                            <span class="label">{{ key }}</span>
                                            <span class="sep"> : </span>
                                            <span class="value"> {{ val }}</span>
                                        </li>
                                    </template>
                                </template>

                                <span class="value"> <a href="#" @click.prevent="show_payment_data = !show_payment_data"> {{ show_payment_data ? 'Hide' : 'Show More' }} </a> </span>

                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>